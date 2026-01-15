<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * แสดงหน้าจัดการรูปภาพ (index)
     */
    public function index($productId)
    {
        // ดึงข้อมูลสินค้า พร้อมรูปภาพ (เรียงลำดับให้รูปหลักขึ้นก่อน)
        $product = Product::with(['productImages' => function($q) {
            $q->orderBy('is_main', 'desc');
        }])->findOrFail($productId);
        
        $colors = Color::all();

        // ส่งตัวแปร $product และ $colors ไปที่หน้า View
        // (ใน View ให้ใช้ $product->productImages แทน $productImages)
        return view('product_images.index', compact('product', 'colors'));
    }

    /**
     * อัปโหลดรูปภาพใหม่ (Store)
     */
    public function store(Request $request, Product $product)
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                // ตรวจสอบชนิดไฟล์
                if (!in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    continue; 
                }

                $filename = $file->getClientOriginalName();

                // ตรวจสอบไฟล์ซ้ำ
                $isDuplicate = $product->productImages()
                    ->where('image_url', 'like', '%product_images/' . $filename)
                    ->exists();

                if ($isDuplicate) continue;

                // อัปโหลด
                $path = $file->storeAs('product_images', $filename, 'public');

                // ถ้ายังไม่มีรูปหลัก ให้รูปแรกเป็นรูปหลักอัตโนมัติ
                $hasMain = $product->productImages()->where('is_main', true)->exists();

                $product->productImages()->create([
                    'image_url' => $path,
                    'is_main' => !$hasMain 
                ]);
            }
            return back()->with('success', 'อัปโหลดรูปภาพเรียบร้อยแล้ว');
        }

        return back()->with('error', 'กรุณาเลือกไฟล์รูปภาพ');
    }

    /**
     * อัปเดตข้อมูลรูปภาพ (เช่น เปลี่ยนสี)
     */
    public function update(Request $request, $productId, $imageId)
    {
        try {
            $image = ProductImage::findOrFail($imageId);
            
            $image->update([
                'color_id' => $request->input('color_id')
            ]);

            return back()->with('success', 'อัปเดตข้อมูลรูปภาพเรียบร้อย');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * ลบรูปภาพ (Destroy)
     */
    public function destroy($id)
    {
        $image = ProductImage::findOrFail($id);

        if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
            Storage::disk('public')->delete($image->image_url);
        }

        $image->delete();

        return back()->with('success', 'ลบรูปภาพเรียบร้อยแล้ว');
    }

    /**
     * ตั้งรูปนี้เป็นรูปหลัก (Set Main)
     * (Route ต้องชี้มาที่นี่ ถ้าต้องการปุ่มตั้งรูปหลัก)
     */
    public function setMain($productId, $imageId)
    {
        // Reset รูปทั้งหมดของสินค้านี้
        ProductImage::where('product_id', $productId)->update(['is_main' => false]);

        // ตั้งรูปนี้เป็นรูปหลัก
        $image = ProductImage::findOrFail($imageId);
        $image->is_main = true;
        $image->save();

        return back()->with('success', 'ตั้งค่ารูปหลักเรียบร้อยแล้ว');
    }
}