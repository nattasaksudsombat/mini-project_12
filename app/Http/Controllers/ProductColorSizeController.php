<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use App\Models\ProductColorSize;
use Illuminate\Http\Request;

class ProductColorSizeController extends Controller
{
    // แสดงฟอร์มเพิ่มสี-ขนาดให้สินค้า
    public function create($product_id)
    {
        $product = Product::findOrFail($product_id);
        $colors = Color::all();
        $sizes = Size::all();
        return view('product_color_size.create', compact('product', 'colors', 'sizes'));
    }

    // บันทึกสี-ขนาดใหม่
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id' => 'required|exists:colors,id',
            'size_id' => 'required|exists:sizes,id',
            'quantity' => 'required|integer|min:0|max:2147483647',
        ]);
        // ตรวจสอบว่า combination นี้มีอยู่แล้วหรือยัง
        $exists = ProductColorSize::where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->where('size_id', $request->size_id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['ข้อความว่า สี + ขนาด นี้มีอยู่แล้วในสินค้านี้'])
                ->withInput();
        }


        ProductColorSize::create($request->all());

        return redirect()->route('products.show', $request->product_id)
            ->with('success', 'เพิ่มสีและขนาดสินค้าเรียบร้อยแล้ว');
    }

    // แสดงฟอร์มแก้ไข
    public function edit($id)
    {
        $colorSize = ProductColorSize::findOrFail($id);
        $colors = Color::all();
        $sizes = Size::all();
        return view('product_color_size.edit', compact('colorSize', 'colors', 'sizes'));
    }

    // อัปเดตข้อมูล
    public function update(Request $request, $id)
    {
        $request->validate([
            'color_id' => 'required|exists:colors,id',
            'size_id' => 'required|exists:sizes,id',
            'quantity' => 'required|integer|min:0',
        ]);
    
        $colorSize = ProductColorSize::findOrFail($id);
    
        // ตรวจสอบซ้ำ
        $exists = ProductColorSize::where('product_id', $colorSize->product_id)
            ->where('color_id', $request->color_id)
            ->where('size_id', $request->size_id)
            ->where('id', '!=', $id)
            ->exists();
    
        if ($exists) {
            return redirect()->back()
                ->withErrors(['duplicate' => 'ข้อมูลสีและขนาดนี้มีอยู่แล้วในระบบ'])
                ->withInput();
        }
    
        $colorSize->update([
            'color_id' => $request->color_id,
            'size_id' => $request->size_id,
            'quantity' => $request->quantity,
        ]);
    
        return redirect()->route('products.show', $colorSize->product_id)
                         ->with('success', 'อัปเดตข้อมูลสีและขนาดเรียบร้อย');
    }
    public function getVariants($productId)
{
    $variants = ProductColorSize::where('product_id', $productId)
        ->with(['color', 'size'])
        ->where('quantity', '>', 0) // เฉพาะที่มีสต็อก
        ->get()
        ->map(function($variant) {
            return [
                'id' => $variant->id,
                'quantity' => $variant->quantity,
                'display_name' => $variant->color->name . ' - ' . $variant->size->name . ' (คงเหลือ: ' . $variant->quantity . ')'
            ];
        });
    
    return response()->json($variants);
}
}
