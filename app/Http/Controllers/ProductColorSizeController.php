<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use App\Models\ProductColorSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductColorSizeController extends Controller
{
    /**
     * แสดงฟอร์มเพิ่มสี-ขนาดให้สินค้า
     */
    public function create($product_id)
    {
        $product = Product::findOrFail($product_id);
        $colors = Color::orderBy('name')->get();
        
        // ✅ แก้ไขบรรทัดนี้: เปลี่ยน 'name' เป็น 'size_name'
        $sizes = Size::orderBy('size_name')->get();
        
        // ดึง variants ที่มีอยู่แล้ว
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
                'quantity' => 'required|integer|min:0|max:2147483647',
            ], [
                'product_id.required' => 'กรุณาเลือกสินค้า',
                'color_id.required' => 'กรุณาเลือกสี',
                'size_id.required' => 'กรุณาเลือกไซส์',
                'quantity.required' => 'กรุณากรอกจำนวน',
                'quantity.integer' => 'จำนวนต้องเป็นตัวเลข',
                'quantity.min' => 'จำนวนต้องไม่ต่ำกว่า 0',
            ]);

            DB::beginTransaction();

            // ตรวจสอบว่า combination นี้มีอยู่แล้วหรือยัง
            $exists = ProductColorSize::where('product_id', $validated['product_id'])
                ->where('color_id', $validated['color_id'])
                ->where('size_id', $validated['size_id'])
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withErrors(['duplicate' => 'สี + ขนาด นี้มีอยู่แล้วในสินค้านี้'])
                    ->withInput();
            }

            // สร้าง variant ใหม่
            ProductColorSize::create([
                'product_id' => $validated['product_id'],
                'color_id' => $validated['color_id'],
                'size_id' => $validated['size_id'],
                'quantity' => $validated['quantity'],
            ]);

            DB::commit();

            return redirect()->route('products.show', $validated['product_id'])
                ->with('success', 'เพิ่มสีและขนาดสินค้าเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProductColorSize Store Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * แสดงฟอร์มแก้ไข
     */
    public function edit($id)
    {
        $colorSize = ProductColorSize::with(['product', 'color', 'size'])->findOrFail($id);
        $colors = Color::orderBy('name')->get();
        
        // ✅ แก้ไขบรรทัดนี้ด้วยเช่นกัน
        $sizes = Size::orderBy('size_name')->get();
        
        return view('product_color_size.edit', compact('colorSize', 'colors', 'sizes'));
    }
    /**
     * อัปเดตข้อมูล
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'color_id' => 'required|exists:colors,id',
                'size_id' => 'required|exists:sizes,id',
                'quantity' => 'required|integer|min:0|max:2147483647',
            ], [
                'color_id.required' => 'กรุณาเลือกสี',
                'size_id.required' => 'กรุณาเลือกไซส์',
                'quantity.required' => 'กรุณากรอกจำนวน',
                'quantity.integer' => 'จำนวนต้องเป็นตัวเลข',
                'quantity.min' => 'จำนวนต้องไม่ต่ำกว่า 0',
            ]);

            DB::beginTransaction();

            $colorSize = ProductColorSize::findOrFail($id);

            // ตรวจสอบซ้ำ (ยกเว้นตัวเอง)
            $exists = ProductColorSize::where('product_id', $colorSize->product_id)
                ->where('color_id', $validated['color_id'])
                ->where('size_id', $validated['size_id'])
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withErrors(['duplicate' => 'ข้อมูลสีและขนาดนี้มีอยู่แล้วในระบบ'])
                    ->withInput();
            }

            // อัปเดตข้อมูล
            $colorSize->update([
                'color_id' => $validated['color_id'],
                'size_id' => $validated['size_id'],
                'quantity' => $validated['quantity'],
            ]);

            DB::commit();

            return redirect()->route('products.show', $colorSize->product_id)
                ->with('success', 'อัปเดตข้อมูลสีและขนาดเรียบร้อย');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProductColorSize Update Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * ลบ variant
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $colorSize = ProductColorSize::findOrFail($id);
            $productId = $colorSize->product_id;

            // ตรวจสอบว่ามีการใช้งานในออเดอร์หรือไม่
            $usedInOrders = DB::table('order_items')
                ->where('product_id', $colorSize->product_id)
                ->where('color_id', $colorSize->color_id)
                ->where('size_id', $colorSize->size_id)
                ->count();

            if ($usedInOrders > 0) {
                return back()->with('error', "ไม่สามารถลบได้ เนื่องจากมีการใช้งานใน {$usedInOrders} ออเดอร์");
            }

            $colorSize->delete();

            DB::commit();

            return redirect()->route('products.show', $productId)
                ->with('success', 'ลบข้อมูลสีและขนาดเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ProductColorSize Delete Error: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * ดึงข้อมูล variants สำหรับ AJAX (ใช้ในออเดอร์)
     */
    public function getVariants($productId)
    {
        try {
            $product = Product::findOrFail($productId);
            
            $variants = ProductColorSize::where('product_id', $productId)
                ->with(['color', 'size'])
                ->where('quantity', '>', 0) // เฉพาะที่มีสต็อก
                ->get()
                ->map(function($variant) use ($product) {
                    return [
                        'id' => $variant->id,
                        'color_id' => $variant->color_id,
                        'size_id' => $variant->size_id,
                        'color_name' => $variant->color->name ?? 'N/A',
                        'size_name' => $variant->size->name ?? 'N/A',
                        'variant_name' => ($variant->color->name ?? 'N/A') . ' - ' . ($variant->size->name ?? 'N/A'),
                        'quantity' => $variant->quantity,
                        'stock' => $variant->quantity,
                        'price' => $product->price,
                        'display_name' => ($variant->color->name ?? 'N/A') . ' - ' . ($variant->size->name ?? 'N/A') . ' (คงเหลือ: ' . $variant->quantity . ')',
                    ];
                });

            return response()->json([
                'success' => true,
                'variants' => $variants
            ]);

        } catch (\Exception $e) {
            Log::error('Get Variants Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * อัปเดตสต็อกหลายรายการพร้อมกัน
     */
    public function bulkUpdateStock(Request $request, $productId)
    {
        try {
            $validated = $request->validate([
                'variants' => 'required|array',
                'variants.*.id' => 'required|exists:product_color_size,id',
                'variants.*.quantity' => 'required|integer|min:0',
            ]);

            DB::beginTransaction();

            foreach ($validated['variants'] as $variantData) {
                $variant = ProductColorSize::where('id', $variantData['id'])
                    ->where('product_id', $productId)
                    ->first();

                if ($variant) {
                    $variant->quantity = $variantData['quantity'];
                    $variant->save();
                }
            }

            DB::commit();

            return back()->with('success', 'อัปเดตสต็อกสำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk Update Stock Error: ' . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
    // 1. ดึงสีตามสินค้า
    public function getColors($id)
    {
        // ดึงสีที่มีการจับคู่กับสินค้านี้ (ผ่านตาราง product_color_size)
        // หรือจะดึงจาก ProductColor โดยตรงก็ได้ แล้วแต่โครงสร้าง
        
        // ตัวอย่าง: ดึงสีที่มีในสต็อกจริง
        $colors = ProductColorSize::where('product_id', $id)
            ->with('color')
            ->get()
            ->pluck('color')
            ->unique('id')
            ->values();

        return response()->json($colors);
    }

    // 2. ดึงไซส์ตามสินค้าและสี
    public function getSizes(Request $request, $id)
    {
        $colorId = $request->query('color_id');

        $sizes = ProductColorSize::where('product_id', $id)
            ->where('color_id', $colorId)
            ->with('size')
            ->get()
            ->pluck('size')
            ->unique('id')
            ->values();

        return response()->json($sizes);
    }

    // 3. เช็คสต็อก
    public function checkStock(Request $request)
    {
        $variant = ProductColorSize::where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->where('size_id', $request->size_id)
            ->first();

        if ($variant) {
            return response()->json([
                'status' => 'success',
                'quantity' => $variant->quantity,
                'price' => $variant->price // เผื่อราคาต่างกันตามไซส์
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'ไม่พบสินค้า']);
    }
}
