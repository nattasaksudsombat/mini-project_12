<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\ProductColorSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'orderItems']);

        // ค้นหา
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // กรองตามสถานะ
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // กรองตามสถานะการชำระเงิน
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // กรองตามวันที่
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // ✅ แก้ไขปัญหา: ตรวจสอบให้แน่ใจว่า orderItems มีค่าก่อน sum()
        foreach ($orders as $order) {
            if ($order->orderItems === null) {
                $order->setRelation('orderItems', collect([]));
            }
        }

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $products = Product::with(['colorSizes.color', 'colorSizes.size'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('orders.create', compact('products'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        try {
            // Validation
            $validated = $request->validate([
                // ข้อมูลลูกค้า
                'customer_id' => 'nullable|exists:customers,id',
                'customer.name' => 'required|string|max:255',
                'customer.phone' => 'nullable|string|max:20',
                'customer.email' => 'nullable|email|max:255',
                'customer.purchase_channel' => 'required|in:facebook,line,website,shopee,lazada,offline',
                'customer.payment_method' => 'required|in:bank_transfer,cash_on_delivery,credit_card,e_wallet',
                'customer.address' => 'required|string',
                
                // ที่อยู่จัดส่ง
                'existing_address_id' => 'nullable|exists:customer_addresses,id',
                'new_address.name' => 'nullable|string|max:100',
                'new_address.address' => 'nullable|string',
                'new_address.subdistrict' => 'nullable|string|max:100',
                'new_address.district' => 'nullable|string|max:100',
                'new_address.province' => 'nullable|string|max:100',
                'new_address.postal_code' => 'nullable|string|max:10',
                
                // ข้อมูลออเดอร์
                'items_json' => 'required|json',
                'shipping_fee' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ], [
                'customer.name.required' => 'กรุณากรอกชื่อลูกค้า',
                'customer.purchase_channel.required' => 'กรุณาเลือกช่องทางซื้อ',
                'customer.payment_method.required' => 'กรุณาเลือกวิธีชำระเงิน',
                'customer.address.required' => 'กรุณากรอกที่อยู่จัดส่ง',
                'items_json.required' => 'กรุณาเลือกสินค้า',
            ]);

            DB::beginTransaction();

            // 1. จัดการลูกค้า - ไม่แก้ไขข้อมูลเดิม
            if ($request->filled('customer_id')) {
                // ใช้ลูกค้าเดิม
                $customer = Customer::findOrFail($request->customer_id);
            } else {
                // สร้างลูกค้าใหม่
                $customer = Customer::create([
                    'name' => $validated['customer']['name'],
                    'phone' => $validated['customer']['phone'] ?? null,
                    'email' => $validated['customer']['email'] ?? null,
                    'purchase_channel' => $validated['customer']['purchase_channel'],
                    'payment_method' => $validated['customer']['payment_method'],
                ]);
            }

            // 2. จัดการที่อยู่จัดส่ง
            $shippingAddress = $validated['customer']['address'];
            $customerAddressId = null;

            if ($request->filled('existing_address_id')) {
                // ใช้ที่อยู่เดิม
                $customerAddressId = $request->existing_address_id;
                $addressRecord = CustomerAddress::find($customerAddressId);
                if ($addressRecord) {
                    $shippingAddress = $addressRecord->address . ' ' . 
                                     $addressRecord->subdistrict . ' ' . 
                                     $addressRecord->district . ' ' . 
                                     $addressRecord->province . ' ' . 
                                     $addressRecord->postal_code;
                }
            } elseif ($request->filled('new_address.address')) {
                // สร้างที่อยู่ใหม่
                $newAddress = CustomerAddress::create([
                    'customer_id' => $customer->id,
                    'name' => $request->input('new_address.name'),
                    'address' => $request->input('new_address.address'),
                    'subdistrict' => $request->input('new_address.subdistrict'),
                    'district' => $request->input('new_address.district'),
                    'province' => $request->input('new_address.province'),
                    'postal_code' => $request->input('new_address.postal_code'),
                ]);
                $customerAddressId = $newAddress->id;
                $shippingAddress = $newAddress->address . ' ' . 
                                 $newAddress->subdistrict . ' ' . 
                                 $newAddress->district . ' ' . 
                                 $newAddress->province . ' ' . 
                                 $newAddress->postal_code;
            }

            // 3. สร้างออเดอร์
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(
                Order::whereDate('created_at', today())->count() + 1, 
                4, 
                '0', 
                STR_PAD_LEFT
            );

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $customer->id,
                'customer_address_id' => $customerAddressId,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_fee' => $validated['shipping_fee'],
                'discount' => $validated['discount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'subtotal' => 0,
                'total_amount' => 0,
                'total_price' => 0,
            ]);

            // 4. เพิ่มสินค้าในออเดอร์
            $items = json_decode($validated['items_json'], true);
            $subtotal = 0;

            foreach ($items as $item) {
                // ตรวจสอบสต็อก
                $colorSize = ProductColorSize::where('product_id', $item['product_id'])
                    ->where('color_id', $item['color_id'])
                    ->where('size_id', $item['size_id'])
                    ->first();

                if (!$colorSize || $colorSize->quantity < $item['quantity']) {
                    throw new \Exception("สต็อกไม่เพียงพอสำหรับ {$item['product_name']} ({$item['variant_name']})");
                }

                // สร้าง OrderItem
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'color_id' => $item['color_id'],
                    'size_id' => $item['size_id'],
                    'variant_name' => $item['variant_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);

                $subtotal += $orderItem->total_price;

                // ลดสต็อก
                $colorSize->decrement('quantity', $item['quantity']);
            }

            // 5. อัปเดตยอดรวม
            $totalAmount = $subtotal + $order->shipping_fee - $order->discount;
            
            $order->update([
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'total_price' => $totalAmount,
            ]);

            DB::commit();

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'สร้างออเดอร์สำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Store Error: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'customerAddress', 'orderItems.product', 'orderItems.color', 'orderItems.size']);
        
        // ✅ แก้ไขปัญหา: ตรวจสอบให้แน่ใจว่า orderItems ไม่เป็น null
        if ($order->orderItems === null) {
            $order->setRelation('orderItems', collect([]));
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        $order->load(['customer', 'customerAddress', 'orderItems.product']);
        
        // ✅ แก้ไขปัญหา
        if ($order->orderItems === null) {
            $order->setRelation('orderItems', collect([]));
        }

        $products = Product::with(['colorSizes.color', 'colorSizes.size'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $items = $order->orderItems->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'color_id' => $item->color_id,
                'size_id' => $item->size_id,
                'color_name' => $item->color->name ?? 'N/A',
                'size_name' => $item->size->name ?? 'N/A',
                'variant_name' => $item->variant_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
                'is_existing_item' => true,
            ];
        })->toArray();

        return view('orders.edit', compact('order', 'products', 'items'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        try {
            // Validation
            $validated = $request->validate([
                // ข้อมูลลูกค้า
                'customer.name' => 'required|string|max:255',
                'customer.phone' => 'nullable|string|max:20',
                'customer.email' => 'nullable|email|max:255',
                'customer.purchase_channel' => 'required|in:facebook,line,website,shopee,lazada,offline',
                'customer.payment_method' => 'required|in:bank_transfer,cash_on_delivery,credit_card,e_wallet',
                'customer.address' => 'required|string',
                
                // สถานะออเดอร์
                'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
                'payment_status' => 'required|in:pending,paid,refunded',
                
                // ข้อมูลออเดอร์
                'items_json' => 'required|json',
                'shipping_fee' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'tracking_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // 1. อัปเดตข้อมูลลูกค้า (แค่ในออเดอร์นี้)
            $order->customer->update([
                'name' => $validated['customer']['name'],
                'phone' => $validated['customer']['phone'] ?? null,
                'email' => $validated['customer']['email'] ?? null,
                'purchase_channel' => $validated['customer']['purchase_channel'],
                'payment_method' => $validated['customer']['payment_method'],
            ]);

            // 2. จัดการสินค้า - คืนสต็อกเดิมทั้งหมดก่อน
            foreach ($order->orderItems as $oldItem) {
                ProductColorSize::where('product_id', $oldItem->product_id)
                    ->where('color_id', $oldItem->color_id)
                    ->where('size_id', $oldItem->size_id)
                    ->increment('quantity', $oldItem->quantity);
            }

            // ลบรายการเดิม
            $order->orderItems()->delete();

            // 3. เพิ่มรายการใหม่
            $items = json_decode($validated['items_json'], true);
            $subtotal = 0;

            foreach ($items as $item) {
                // ตรวจสอบสต็อก
                $colorSize = ProductColorSize::where('product_id', $item['product_id'])
                    ->where('color_id', $item['color_id'])
                    ->where('size_id', $item['size_id'])
                    ->first();

                if (!$colorSize || $colorSize->quantity < $item['quantity']) {
                    throw new \Exception("สต็อกไม่เพียงพอสำหรับ {$item['product_name']} ({$item['variant_name']})");
                }

                // สร้าง OrderItem
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'color_id' => $item['color_id'],
                    'size_id' => $item['size_id'],
                    'variant_name' => $item['variant_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);

                $subtotal += $orderItem->total_price;

                // ลดสต็อกใหม่
                $colorSize->decrement('quantity', $item['quantity']);
            }

            // 4. อัปเดตออเดอร์
            $totalAmount = $subtotal + $validated['shipping_fee'] - ($validated['discount'] ?? 0);
            
            $order->update([
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'shipping_fee' => $validated['shipping_fee'],
                'discount' => $validated['discount'] ?? 0,
                'tracking_number' => $validated['tracking_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'total_price' => $totalAmount,
            ]);

            DB::commit();

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'แก้ไขออเดอร์สำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Update Error: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified order
     */
    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();

            // คืนสต็อกทั้งหมด
            foreach ($order->orderItems as $item) {
                ProductColorSize::where('product_id', $item->product_id)
                    ->where('color_id', $item->color_id)
                    ->where('size_id', $item->size_id)
                    ->increment('quantity', $item->quantity);
            }

            // ลบออเดอร์
            $order->delete();

            DB::commit();

            return redirect()
                ->route('orders.index')
                ->with('success', 'ลบออเดอร์สำเร็จ และคืนสต็อกแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Delete Error: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Search customers for AJAX
     */
    public function searchCustomers(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $customers = Customer::where('name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'phone', 'email', 'purchase_channel', 'payment_method']);

        return response()->json($customers);
    }

    /**
     * Get customer addresses for AJAX
     */
    public function getCustomerAddresses($customerId)
    {
        try {
            $customer = Customer::findOrFail($customerId);
            
            $addresses = CustomerAddress::where('customer_id', $customerId)
                ->get()
                ->map(function ($address) {
                    return [
                        'id' => $address->id,
                        'label' => $address->name ?? 'ที่อยู่ #' . $address->id,
                        'full_address' => trim(
                            $address->address . ' ' . 
                            $address->subdistrict . ' ' . 
                            $address->district . ' ' . 
                            $address->province . ' ' . 
                            $address->postal_code
                        ),
                    ];
                });

            return response()->json([
                'success' => true,
                'addresses' => $addresses
            ]);

        } catch (\Exception $e) {
            Log::error('Get Customer Addresses Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด'
            ], 500);
        }
    }

    /**
     * Upload payment slip
     */
    public function pay(Request $request, Order $order)
    {
        try {
            $request->validate([
                'slip_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ], [
                'slip_image.required' => 'กรุณาแนบรูปสลิป',
                'slip_image.image' => 'ไฟล์ต้องเป็นรูปภาพ',
            ]);

            DB::beginTransaction();

            // ลบรูปเดิม (ถ้ามี)
            if ($order->slip_image) {
                Storage::delete('public/' . $order->slip_image);
            }

            // อัปโหลดรูปใหม่
            $path = $request->file('slip_image')->store('slips', 'public');

            // อัปเดตออเดอร์
            $order->update([
                'slip_image' => $path,
                'payment_status' => 'paid',
            ]);

            DB::commit();

            return back()->with('success', 'แนบสลิปสำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Pay Error: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Update tracking number
     */
    public function updateTracking(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'tracking_number' => 'required|string|max:100',
            ], [
                'tracking_number.required' => 'กรุณากรอกเลข Tracking',
            ]);

            $order->update([
                'tracking_number' => $validated['tracking_number'],
                'status' => 'shipped',
            ]);

            return back()->with('success', 'อัปเดต Tracking Number สำเร็จ');

        } catch (\Exception $e) {
            Log::error('Update Tracking Error: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Mark order as paid
     */
    public function markPaid(Order $order)
    {
        try {
            $order->update(['payment_status' => 'paid']);
            return back()->with('success', 'เปลี่ยนสถานะเป็น "ชำระเงินแล้ว" สำเร็จ');
        } catch (\Exception $e) {
            Log::error('Mark Paid Error: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Cancel order
     */
    public function cancel(Order $order)
    {
        try {
            DB::beginTransaction();

            // คืนสต็อก
            foreach ($order->orderItems as $item) {
                ProductColorSize::where('product_id', $item->product_id)
                    ->where('color_id', $item->color_id)
                    ->where('size_id', $item->size_id)
                    ->increment('quantity', $item->quantity);
            }

            // อัปเดตสถานะ
            $order->update(['status' => 'cancelled']);

            DB::commit();

            return back()->with('success', 'ยกเลิกออเดอร์สำเร็จ และคืนสต็อกแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel Order Error: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Mark order as shipped
     */
    public function ship(Order $order)
    {
        try {
            $order->update(['status' => 'shipped']);
            return back()->with('success', 'เปลี่ยนสถานะเป็น "จัดส่งแล้ว" สำเร็จ');
        } catch (\Exception $e) {
            Log::error('Ship Order Error: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}