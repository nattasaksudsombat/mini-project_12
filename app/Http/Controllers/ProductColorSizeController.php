<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use App\Models\ProductColorSize;
use App\Models\StockTransaction; // ✅ เพิ่ม Model นี้
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
 // ✅ เพิ่ม Schema

class ProductColorSizeController extends Controller
{
    /**
     * แสดงฟอร์มเพิ่มสี-ขนาดให้สินค้า
     */
    public function create($product_id)
    {
        $product = Product::findOrFail($product_id);
        $colors = Color::orderBy('name')->get();
        // ถ้า column ใน DB ชื่อ size_name ให้ใช้ size_name ถ้าชื่อ name ให้ใช้ name
        $sizes = Size::orderBy('size_name')->get(); 
        
        $existingVariants = ProductColorSize::where('product_id', $product_id)
            ->with(['color', 'size'])
            ->get();
        
        return view('product_color_size.create', compact('product', 'colors', 'sizes', 'existingVariants'));
    }

    /**
     * บันทึกสี-ขนาดใหม่
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'color_id' => 'required|exists:colors,id',
                'size_id' => 'required|exists:sizes,id',
                'quantity' => 'required|integer|min:0',
            ], [
                'product_id.required' => 'กรุณาเลือกสินค้า',
                'color_id.required' => 'กรุณาเลือกสี',
                'size_id.required' => 'กรุณาเลือกไซส์',
                'quantity.required' => 'กรุณากรอกจำนวน',
            ]);

            DB::beginTransaction();

            $exists = ProductColorSize::where('product_id', $validated['product_id'])
                ->where('color_id', $validated['color_id'])
                ->where('size_id', $validated['size_id'])
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withErrors(['duplicate' => 'สี + ขนาด นี้มีอยู่แล้วในสินค้านี้'])
                    ->withInput();
            }

            ProductColorSize::create($validated);

            DB::commit();

            return redirect()->route('products.show', $validated['product_id'])
                ->with('success', 'เพิ่มสีและขนาดสินค้าเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $colorSize = ProductColorSize::with(['product', 'color', 'size'])->findOrFail($id);
        $colors = Color::orderBy('name')->get();
        $sizes = Size::orderBy('size_name')->get();
        
        return view('product_color_size.edit', compact('colorSize', 'colors', 'sizes'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'color_id' => 'required|exists:colors,id',
                'size_id' => 'required|exists:sizes,id',
                'quantity' => 'required|integer|min:0',
            ]);

            DB::beginTransaction();

            $colorSize = ProductColorSize::findOrFail($id);

            // เช็คซ้ำ
            $exists = ProductColorSize::where('product_id', $colorSize->product_id)
                ->where('color_id', $validated['color_id'])
                ->where('size_id', $validated['size_id'])
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return back()->withErrors(['duplicate' => 'ข้อมูลซ้ำ'])->withInput();
            }

            $colorSize->update($validated);

            DB::commit();
            return redirect()->route('products.show', $colorSize->product_id)->with('success', 'อัปเดตเรียบร้อย');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $colorSize = ProductColorSize::findOrFail($id);
            $pid = $colorSize->product_id;

            // เช็คว่าถูกใช้ใน Order หรือไม่
            if (DB::table('order_items')->where('product_color_size_id', $id)->exists()) {
                 return back()->with('error', 'ลบไม่ได้ สินค้านี้มีประวัติการสั่งซื้อ');
            }

            $colorSize->delete();
            DB::commit();
            return redirect()->route('products.show', $pid)->with('success', 'ลบเรียบร้อย');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // ========================================================
    // ✅ ส่วนที่เพิ่ม/แก้ไข เพื่อให้ระบบ Stock ทำงานได้จริง
    // ========================================================

    /**
     * หน้าประวัติสต๊อก
     */
    public function history($id)
    {
        $variant = ProductColorSize::with(['product', 'color', 'size'])->findOrFail($id);
        
        $history = [];
        if (Schema::hasTable('stock_transactions')) {
            $history = StockTransaction::where('product_color_size_id', $id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return view('stock.variant-history', compact('variant', 'history'));
    }
    /**
     * หน้าปรับสต๊อก (แสดงฟอร์ม)
     */
    public function adjustForm($id)
    {
        $variant = ProductColorSize::with(['product', 'color', 'size'])->findOrFail($id);

        $current = $variant->quantity;
        $reserved = 0;
        
        if (Schema::hasTable('stock_holds')) {
             $reserved = DB::table('stock_holds')
                ->where('product_color_size_id', $id)
                ->where('status', 'active') 
                ->sum('quantity');
        }

        $summary = (object)[
            'current'   => (int)$current,
            'reserved'  => (int)$reserved,
            'available' => (int)($current - $reserved),
        ];

        // ✅ แก้ไข: ดึง 10 รายการล่าสุดเพื่อส่งไปแก้ Error Undefined variable $last10
        $last10 = [];
        if (Schema::hasTable('stock_transactions')) {
            $last10 = StockTransaction::where('product_color_size_id', $id)
                ->latest() // เรียงจากใหม่ไปเก่า
                ->limit(10)
                ->get();
        }

        // ส่ง $last10 ไปด้วย
        return view('stock.adjust', compact('variant', 'summary', 'last10'));
    }
    /**
     * ✅ บันทึกการปรับสต๊อก (ฟังก์ชันนี้แหละที่คุณขาดไป!)
     */
    public function saveAdjustment(Request $request, $id)
    {
        // 1. ตรวจสอบค่าที่ส่งมา
        $request->validate([
            'action'   => 'required|in:in,out', // รับค่า in (เพิ่ม) หรือ out (ลด)
            'quantity' => 'required|integer|min:1',
            'reason'   => 'nullable|string|max:255',
            'ref'      => 'nullable|string|max:50', // รับเลขอ้างอิง
        ]);

        DB::beginTransaction();
        try {
            // 2. ดึงข้อมูลสินค้า (Lock เพื่อกันแย่งกันอัปเดต)
            $variant = ProductColorSize::lockForUpdate()->findOrFail($id);
            
            $qty = (int) $request->quantity;
            $type = $request->action;
            $qtyBefore = $variant->quantity;
            $change = 0;

            // 3. คำนวณการตัด/เพิ่มสต๊อก
            if ($type === 'out') {
                // กรณีตัดของออก เช็คว่าของพอไหม
                if ($variant->quantity < $qty) {
                    throw new \Exception("สินค้าคงเหลือไม่พอให้ตัดออก (มี: {$variant->quantity}, จะตัด: {$qty})");
                }
                $variant->decrement('quantity', $qty);
                $change = -$qty;
            } else {
                // กรณีเพิ่มของเข้า
                $variant->increment('quantity', $qty);
                $change = $qty;
            }

            $variant->refresh(); // ดึงค่าล่าสุด

            // 4. บันทึกประวัติลง stock_transactions (ถ้ามีตารางนี้)
            if (\Illuminate\Support\Facades\Schema::hasTable('stock_transactions')) {
                \App\Models\StockTransaction::create([
                    'product_color_size_id' => $variant->id,
                    'user_id'          => auth()->id() ?? null, // ใส่ ID คนทำรายการ
                    'user_name'        => auth()->user()->name ?? 'System', // ใส่ชื่อคนทำรายการ
                    'type'             => $type,      // in หรือ out
                    'quantity'         => $change,    // เช่น +10 หรือ -5
                    'quantity_before'  => $qtyBefore,
                    'quantity_after'   => $variant->quantity,
                    'reason'           => $request->reason,
                    'reference_number' => $request->ref, // บันทึกเลขอ้างอิง
                    'created_at'       => now(),
                ]);
            }

            DB::commit();
            return back()->with('success', 'บันทึกการปรับสต๊อกเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
        }
    }
}