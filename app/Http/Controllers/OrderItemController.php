<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductColorSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderItems = OrderItem::with(['order.customer', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('order_items.index', compact('orderItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::with('customer')->orderBy('created_at', 'desc')->get();
        $products = Product::with(['colors', 'sizes', 'colorSizes'])->get();

        return view('order_items.create', compact('orders', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'product_id' => 'required|exists:products,id',
                'color_id' => 'required|integer',
                'size_id' => 'required|integer',
                'quantity' => 'required|integer|min:1',
                'unit_price' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            // ดึงข้อมูลสินค้า
            $product = Product::findOrFail($validated['product_id']);
            
            // หา ProductColorSize
            $colorSize = ProductColorSize::where('product_id', $validated['product_id'])
                ->where('color_id', $validated['color_id'])
                ->where('size_id', $validated['size_id'])
                ->firstOrFail();

            // ตรวจสอบสต็อก
            if ($colorSize->stock < $validated['quantity']) {
                return back()->withErrors(['quantity' => 'สต็อกไม่เพียงพอ มีเพียง ' . $colorSize->stock . ' ชิ้น'])->withInput();
            }

            // สร้าง OrderItem
            $orderItem = new OrderItem();
            $orderItem->order_id = $validated['order_id'];
            $orderItem->product_id = $validated['product_id'];
            $orderItem->product_name = $product->name;
            $orderItem->color_id = $validated['color_id'];
            $orderItem->size_id = $validated['size_id'];
            $orderItem->variant_name = $colorSize->color->name . ' - ' . $colorSize->size->name;
            $orderItem->quantity = $validated['quantity'];
            $orderItem->unit_price = $validated['unit_price'];
            $orderItem->total_price = $validated['quantity'] * $validated['unit_price'];
            $orderItem->save();

            // ลดสต็อก
            $colorSize->decrement('stock', $validated['quantity']);

            // อัปเดตยอดรวมของ Order
            $this->recalculateOrderTotal($validated['order_id']);

            DB::commit();

            return redirect()
                ->route('orders.show', $validated['order_id'])
                ->with('success', 'เพิ่มสินค้าในออเดอร์สำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('OrderItem Store Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orderItem = OrderItem::with(['order.customer', 'product'])->findOrFail($id);
        return view('order_items.show', compact('orderItem'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $orderItem = OrderItem::with(['order', 'product'])->findOrFail($id);
        $products = Product::with(['colors', 'sizes', 'colorSizes'])->get();

        return view('order_items.edit', compact('orderItem', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'color_id' => 'required|integer',
                'size_id' => 'required|integer',
                'quantity' => 'required|integer|min:1',
                'unit_price' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            $orderItem = OrderItem::findOrFail($id);
            $oldQuantity = $orderItem->quantity;
            $oldColorSizeId = $orderItem->color_id . '-' . $orderItem->size_id;

            // ดึงข้อมูลสินค้า
            $product = Product::findOrFail($validated['product_id']);
            
            // หา ProductColorSize ใหม่
            $newColorSize = ProductColorSize::where('product_id', $validated['product_id'])
                ->where('color_id', $validated['color_id'])
                ->where('size_id', $validated['size_id'])
                ->firstOrFail();

            $newColorSizeId = $validated['color_id'] . '-' . $validated['size_id'];

            // คืนสต็อกเดิม
            if ($oldColorSizeId !== $newColorSizeId) {
                $oldColorSize = ProductColorSize::where('product_id', $orderItem->product_id)
                    ->where('color_id', $orderItem->color_id)
                    ->where('size_id', $orderItem->size_id)
                    ->first();
                
                if ($oldColorSize) {
                    $oldColorSize->increment('stock', $oldQuantity);
                }
            } else {
                // สี-ไซส์เดิม แต่จำนวนเปลี่ยน
                $newColorSize->increment('stock', $oldQuantity);
            }

            // ตรวจสอบสต็อกใหม่
            if ($newColorSize->stock < $validated['quantity']) {
                DB::rollBack();
                return back()->withErrors(['quantity' => 'สต็อกไม่เพียงพอ มีเพียง ' . $newColorSize->stock . ' ชิ้น'])->withInput();
            }

            // ลดสต็อกใหม่
            $newColorSize->decrement('stock', $validated['quantity']);

            // อัปเดต OrderItem
            $orderItem->product_id = $validated['product_id'];
            $orderItem->product_name = $product->name;
            $orderItem->color_id = $validated['color_id'];
            $orderItem->size_id = $validated['size_id'];
            $orderItem->variant_name = $newColorSize->color->name . ' - ' . $newColorSize->size->name;
            $orderItem->quantity = $validated['quantity'];
            $orderItem->unit_price = $validated['unit_price'];
            $orderItem->total_price = $validated['quantity'] * $validated['unit_price'];
            $orderItem->save();

            // อัปเดตยอดรวมของ Order
            $this->recalculateOrderTotal($orderItem->order_id);

            DB::commit();

            return redirect()
                ->route('orders.show', $orderItem->order_id)
                ->with('success', 'แก้ไขสินค้าในออเดอร์สำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('OrderItem Update Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $orderItem = OrderItem::findOrFail($id);
            $orderId = $orderItem->order_id;

            // คืนสต็อก
            $colorSize = ProductColorSize::where('product_id', $orderItem->product_id)
                ->where('color_id', $orderItem->color_id)
                ->where('size_id', $orderItem->size_id)
                ->first();

            if ($colorSize) {
                $colorSize->increment('stock', $orderItem->quantity);
            }

            // ลบ OrderItem
            $orderItem->delete();

            // อัปเดตยอดรวมของ Order
            $this->recalculateOrderTotal($orderId);

            DB::commit();

            return redirect()
                ->route('orders.show', $orderId)
                ->with('success', 'ลบสินค้าออกจากออเดอร์สำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('OrderItem Delete Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * คำนวณยอดรวมของ Order ใหม่
     */
    private function recalculateOrderTotal($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // รวมยอดสินค้าทั้งหมด
        $subtotal = $order->orderItems()->sum('total_price');
        
        $order->subtotal = $subtotal;
        $order->total_price = $subtotal + $order->shipping_fee - $order->discount;
        $order->total_amount = $order->total_price; // Sync ทั้งสองฟิลด์
        $order->save();
    }
}