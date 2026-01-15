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
use App\Services\StockService; // ✅ เรียกใช้ StockService

class OrderController extends Controller
{
    protected $stockService;

    // ✅ Inject StockService เข้ามาใช้งาน
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $query = Order::with(['customer', 'orderItems']);

        // ค้นหา
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        // กรองสถานะ
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('payment_status')) $query->where('payment_status', $request->payment_status);

        // กรองวันที่
        if ($request->filled('start_date')) $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('created_at', '<=', $request->end_date);

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        foreach ($orders as $order) {
            if ($order->orderItems === null) {
                $order->setRelation('orderItems', collect([]));
            }
        }

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::with(['colorSizes.color', 'colorSizes.size'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        try {
            // 1. Validate เบื้องต้น (ยังไม่บังคับ customer.address)
            $rules = [
                'customer_id' => 'nullable|exists:customers,id',
                'customer.name' => 'required|string|max:255', // บังคับชื่อเสมอ (ทั้งเก่าและใหม่)
                'customer.phone' => 'nullable|string|max:20',
                'customer.email' => 'nullable|email|max:255',
                'customer.purchase_channel' => 'required',
                'customer.payment_method' => 'required',
                // ตัด customer.address ออก เพราะเราจะดูจาก new_address แทนถ้าเป็นลูกค้าใหม่
                
                'items_json' => 'required|json',
                'shipping_fee' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ];

            // ถ้าไม่ได้เลือกลูกค้าเดิม (คือสร้างใหม่) ต้องบังคับให้กรอกที่อยู่ใหม่
            if (!$request->filled('customer_id')) {
                $rules['new_address.address'] = 'required|string'; // บ้านเลขที่
                $rules['new_address.subdistrict'] = 'required|string';
                $rules['new_address.district'] = 'required|string';
                $rules['new_address.province'] = 'required|string';
                $rules['new_address.postal_code'] = 'required|string';
            }

            $validated = $request->validate($rules, [
                'items_json.required' => 'กรุณาเลือกสินค้าอย่างน้อย 1 รายการ',
                'new_address.address.required' => 'กรุณากรอกที่อยู่ (บ้านเลขที่)',
                'customer.name.required' => 'กรุณากรอกชื่อลูกค้า',
            ]);

            DB::beginTransaction();

            $customer = null;
            $customerAddressId = null;
            $shippingAddressText = '';

            // ---------------------------------------------------------
            // 2. จัดการลูกค้า (Customer)
            // ---------------------------------------------------------
            if ($request->filled('customer_id')) {
                // A. กรณีลูกค้าเก่า
                $customer = \App\Models\Customer::findOrFail($request->customer_id);
                
                // ถ้ามีการเลือกที่อยู่เดิม
                if ($request->filled('existing_address_id')) {
                    $addr = \App\Models\CustomerAddress::find($request->existing_address_id);
                    if ($addr) {
                        $customerAddressId = $addr->id;
                        $shippingAddressText = $addr->full_address; // ใช้ Accessor ที่มีใน Model หรือต่อ String เอง
                    }
                } 
                // ถ้ามีการเพิ่มที่อยู่ใหม่ให้ลูกค้าเก่า
                elseif ($request->filled('new_address.address')) {
                    $newAddrData = $request->input('new_address');
                    $newAddrData['customer_id'] = $customer->id;
                    // ล้างค่า null ใน soi/road เพื่อป้องกัน error
                    $newAddrData['soi'] = $newAddrData['soi'] ?? '';
                    $newAddrData['road'] = $newAddrData['road'] ?? '';
                    
                    $newAddress = \App\Models\CustomerAddress::create($newAddrData);
                    $customerAddressId = $newAddress->id;
                    // รวมข้อความที่อยู่สำหรับ shipping_address
                    $shippingAddressText = $this->formatFullAddress($newAddress);
                }
            } else {
                // B. กรณีลูกค้าใหม่ (สร้าง Customer + Address)
                $customer = \App\Models\Customer::create([
                    'name' => $validated['customer']['name'],
                    'phone' => $validated['customer']['phone'] ?? null,
                    'email' => $validated['customer']['email'] ?? null,
                    'purchase_channel' => $validated['customer']['purchase_channel'],
                    'payment_method' => $validated['customer']['payment_method'],
                ]);

                // สร้างที่อยู่ใหม่ผูกกับลูกค้าใหม่ทันที
                $newAddrData = $request->input('new_address');
                $newAddrData['customer_id'] = $customer->id;
                $newAddrData['name'] = $newAddrData['name'] ?? 'ที่อยู่จัดส่ง'; // ตั้งชื่อ Default
                $newAddrData['soi'] = $newAddrData['soi'] ?? '';
                $newAddrData['road'] = $newAddrData['road'] ?? '';

                $newAddress = \App\Models\CustomerAddress::create($newAddrData);
                $customerAddressId = $newAddress->id;
                $shippingAddressText = $this->formatFullAddress($newAddress);
            }

            // ---------------------------------------------------------
            // 3. สร้าง Order
            // ---------------------------------------------------------
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(\App\Models\Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            
            $order = \App\Models\Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $customer->id,
                'customer_address_id' => $customerAddressId, // เก็บ ID อ้างอิง (ถ้ามี)
                'shipping_address' => $shippingAddressText,  // ✅ เก็บเป็นข้อความ (สำคัญ!)
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_fee' => $validated['shipping_fee'],
                'discount' => $validated['discount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'subtotal' => 0,
                'total_amount' => 0,
                'total_price' => 0,
            ]);

            // 4. เพิ่มสินค้า & จองสต็อก (Hold) -- ส่วนนี้คงเดิม ไม่แตะต้อง
            $items = json_decode($validated['items_json'], true);
            $subtotal = 0;

            foreach ($items as $item) {
                $variant = \App\Models\ProductColorSize::where('product_id', $item['product_id'])
                    ->where('color_id', $item['color_id'])
                    ->where('size_id', $item['size_id'])
                    ->first();

                if (!$variant) throw new \Exception("ไม่พบสินค้า: {$item['product_name']} ({$item['variant_name']})");

                $this->stockService->reserveNewForOrderVariant(
                    $variant->id, $order->id, $item['quantity'], 
                    $orderNumber, 'สร้างออเดอร์ใหม่'
                );

                $orderItem = \App\Models\OrderItem::create([
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
            }

            $totalAmount = $subtotal + $order->shipping_fee - $order->discount;
            $order->update(['subtotal' => $subtotal, 'total_amount' => $totalAmount, 'total_price' => $totalAmount]);

            DB::commit();
            return redirect()->route('orders.show', $order)->with('success', 'สร้างออเดอร์สำเร็จ (จองสต็อกเรียบร้อย)');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error($e);
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
        }
    }

    // Helper Function สำหรับรวมข้อความที่อยู่ (ใส่เพิ่มใน Controller)
    private function formatFullAddress($addr) {
        $text = ($addr->name ? "({$addr->name}) " : "") . $addr->address;
        if($addr->soi) $text .= " ซ." . $addr->soi;
        if($addr->road) $text .= " ถ." . $addr->road;
        $text .= " ต." . $addr->subdistrict . " อ." . $addr->district . " จ." . $addr->province . " " . $addr->postal_code;
        return $text;
    }
    public function show(Order $order)
    {
        $order->load(['customer', 'customerAddress', 'orderItems.product', 'orderItems.color', 'orderItems.size']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['customer', 'customerAddress', 'orderItems.product', 'orderItems.color', 'orderItems.size']);

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
                'color_name' => $item->color->name ?? '-',
                'size_name' => $item->size->size_name ?? '-',
                'variant_name' => $item->variant_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
                'is_existing_item' => true,
            ];
        })->toArray();

        return view('orders.edit', compact('order', 'products', 'items'));
    }

    public function update(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'customer.name' => 'required',
                'customer.phone' => 'nullable',
                'customer.email' => 'nullable',
                'customer.purchase_channel' => 'required',
                'customer.payment_method' => 'required',

               'ship_name' => 'nullable|string',
            'ship_address' => 'required|string', 
            'ship_soi' => 'nullable|string',
            'ship_road' => 'nullable|string',
            'ship_subdistrict' => 'required|string',
            'ship_district' => 'required|string',
            'ship_province' => 'required|string',
            'ship_postal_code' => 'required|string',
                'status' => 'required',
                'payment_status' => 'required',
                'items_json' => 'required|json',
                'shipping_fee' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'tracking_number' => 'nullable',
                'notes' => 'nullable',
            ]);

            DB::beginTransaction();
            $customerData = $validated['customer'];
            unset($customerData['address']);
            $order->customer->update($validated['customer']);

            // 1. คืนสต็อก (Release) ของเดิมทั้งหมดก่อน
            foreach ($order->orderItems as $oldItem) {
                $variant = ProductColorSize::where('product_id', $oldItem->product_id)
                    ->where('color_id', $oldItem->color_id)
                    ->where('size_id', $oldItem->size_id)
                    ->first();

                if ($variant) {
                    // ✅ ใช้ Service ปล่อยจอง (คืนโควต้า)
                    $this->stockService->releaseAllForOrderVariant(
                        $variant->id,
                        $order->id,
                        $order->order_number,
                        'แก้ไขออเดอร์ (Release Old)'
                    );
                }
            }
            $order->orderItems()->delete();

            // 2. เพิ่มสินค้าใหม่ & จองใหม่ (Reserve)
            $items = json_decode($validated['items_json'], true);
            $subtotal = 0;

            foreach ($items as $item) {
                $variant = ProductColorSize::where('product_id', $item['product_id'])
                    ->where('color_id', $item['color_id'])
                    ->where('size_id', $item['size_id'])
                    ->first();

                if (!$variant) throw new \Exception("ไม่พบสินค้าในระบบ");

                // ✅ ใช้ Service จองใหม่
                $this->stockService->reserveNewForOrderVariant(
                    $variant->id,
                    $order->id,
                    $item['quantity'],
                    $order->order_number,
                    'แก้ไขออเดอร์ (Reserve New)'
                );

                $newItem = OrderItem::create([
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
                $subtotal += $newItem->total_price;
            }
$fullAddress = ($validated['ship_name'] ?? '') ? "({$validated['ship_name']}) " : "";
        $fullAddress .= $validated['ship_address'];
        if(!empty($validated['ship_soi'])) $fullAddress .= " ซ." . $validated['ship_soi'];
        if(!empty($validated['ship_road'])) $fullAddress .= " ถ." . $validated['ship_road'];
        $fullAddress .= " ต." . $validated['ship_subdistrict'];
        $fullAddress .= " อ." . $validated['ship_district'];
        $fullAddress .= " จ." . $validated['ship_province'];
        $fullAddress .= " " . $validated['ship_postal_code'];
            $totalAmount = $subtotal + $validated['shipping_fee'] - ($validated['discount'] ?? 0);

            $order->update([
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'shipping_fee' => $validated['shipping_fee'],
                'discount' => $validated['discount'],
                'tracking_number' => $validated['tracking_number'],
                'notes' => $validated['notes'],
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'total_price' => $totalAmount,
               'shipping_address' => $fullAddress
            ]);

            // จัดการสถานะ Shipped/Cancelled
            if ($validated['status'] === 'shipped') {
                $this->stockService->shipConsumeAll($order->id, $order->order_number);
            } elseif ($validated['status'] === 'cancelled') {
                $this->stockService->cancelOrderReleaseAll($order->id, $order->order_number);
            }

            DB::commit();
            return redirect()->route('orders.show', $order)->with('success', 'แก้ไขออเดอร์สำเร็จ');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error($e);
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();

            // ✅ ใช้ Service คืนสต็อก (Release)
            $this->stockService->cancelOrderReleaseAll($order->id, $order->order_number);

            $order->orderItems()->delete();
            $order->delete();

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'ลบออเดอร์และคืนยอดจองแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // --- Action Methods ---

    public function cancel(Order $order)
    {
        try {
            DB::beginTransaction();

            // ✅ ใช้ Service คืนยอดจอง (Release)
            $this->stockService->cancelOrderReleaseAll($order->id, $order->order_number);

            $order->update(['status' => 'cancelled']);

            DB::commit();
            return back()->with('success', 'ยกเลิกออเดอร์และคืนยอดจองแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function ship(Order $order)
    {
        try {
            DB::beginTransaction();

            // ✅ ใช้ Service ตัดสต็อกจริง (เปลี่ยน Hold -> Out)
            $this->stockService->shipConsumeAll($order->id, $order->order_number);

            $order->update(['status' => 'shipped']);

            DB::commit();
            return back()->with('success', 'จัดส่งแล้ว (ตัดสต็อกจริงเรียบร้อย)');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // --- Helper / AJAX Methods ---

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

    public function updateTracking(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'tracking_number' => 'required|string|max:100',
            ], [
                'tracking_number.required' => 'กรุณากรอกเลข Tracking',
            ]);

            // อัปเดตเลขพัสดุและเปลี่ยนสถานะเป็น Shipped
            // หมายเหตุ: ถ้าจะให้ตัดสต็อกด้วย ควรเรียก ship() หรือเพิ่ม logic shipConsumeAll() ที่นี่

            DB::beginTransaction();

            $order->update([
                'tracking_number' => $validated['tracking_number'],
                'status' => 'shipped',
            ]);

            // ✅ ตัดสต็อกจริงเมื่อใส่เลข Tracking (ถือว่าส่งของแล้ว)
            $this->stockService->shipConsumeAll($order->id, $order->order_number);

            DB::commit();

            return back()->with('success', 'อัปเดต Tracking Number สำเร็จ');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Tracking Error: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

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
}
