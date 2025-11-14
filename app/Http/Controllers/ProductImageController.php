<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ProductImageController extends Controller
{
    // แสดงหน้าแก้ไขรูปภาพ
    public function edit(Product $product)
    {
        return view('products.edit_images', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                // ✅ ตรวจสอบชนิดไฟล์
                if (!in_array($file->extension(), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    continue; // ข้ามถ้าไม่ใช่รูป
                }

                $filename = $file->getClientOriginalName();

                // ✅ ตรวจสอบซ้ำ
                $isDuplicate = $product->productImages()
                    ->where('image_url', 'like', '%product_images/' . $filename)
                    ->exists();

                if ($isDuplicate) {
                    continue;
                }

                // ✅ อัปโหลด
                $path = $file->storeAs('product_images', $filename, 'public');

                // ✅ ตรวจว่ามีรูปหลักหรือยัง
                $hasMain = $product->productImages()->where('is_main', true)->exists();

                $product->productImages()->create([
                    'image_url' => $path,
                    'is_main' => !$hasMain // รูปแรกจะเป็น main
                ]);
            }
        }

        return back()->with('success', 'อัปโหลดรูปภาพเรียบร้อยแล้ว');
    }



    public function destroy($id)
    {
        $image = ProductImage::findOrFail($id);

        // ลบไฟล์จาก storage
        if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
            Storage::disk('public')->delete($image->image_url);
        }

        $image->delete();

        return back()->with('success', 'ลบรูปภาพเรียบร้อยแล้ว');
    }
    public function setMain($productId, $imageId)
    {
        // Reset รูปภาพทั้งหมดของสินค้านี้ให้ไม่เป็นหลัก
        \App\Models\ProductImage::where('product_id', $productId)->update(['is_main' => false]);

        // ตั้งรูปนี้เป็นรูปหลัก
        $image = \App\Models\ProductImage::findOrFail($imageId);
        $image->is_main = true;
        $image->save();

        return redirect()->back()->with('success', 'ตั้งค่ารูปหลักเรียบร้อยแล้ว');
    }
}
