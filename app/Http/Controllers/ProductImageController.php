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
     * อัปโหลดรูปภาพใหม่ (Store) - รองรับหลายไฟล์
     */
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        // ตรวจสอบว่ามีไฟล์หรือไม่
        if (!$request->hasFile('images')) {
            return back()->with('error', 'กรุณาเลือกไฟล์รูปภาพ');
        }

        $uploadCount = 0;
        $skipCount = 0;

        foreach ($request->file('images') as $file) {
            // ตรวจสอบชนิดไฟล์
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $skipCount++;
                continue; 
            }

            $filename = $file->getClientOriginalName();

            // ตรวจสอบไฟล์ซ้ำ
            $isDuplicate = $product->productImages()
                ->where('image_url', 'like', '%product_images/' . $filename)
                ->exists();

            if ($isDuplicate) {
                $skipCount++;
                continue;
            }

            // อัปโหลด
            $path = $file->storeAs('product_images', $filename, 'public');

            // ถ้ายังไม่มีรูปหลัก ให้รูปแรกเป็นรูปหลักอัตโนมัติ
            $hasMain = $product->productImages()->where('is_main', true)->exists();

            $product->productImages()->create([
                'image_url' => $path,
                'is_main' => !$hasMain 
            ]);

            $uploadCount++;
        }

        // แจ้งผลลัพธ์
        if ($uploadCount > 0) {
            $message = "อัปโหลดรูปภาพสำเร็จ {$uploadCount} ไฟล์";
            if ($skipCount > 0) {
                $message .= " (ข้าม {$skipCount} ไฟล์)";
            }
            return back()->with('success', $message);
        } else {
            return back()->with('error', 'ไม่สามารถอัปโหลดไฟล์ได้ (ไฟล์ซ้ำหรือไม่ถูกต้อง)');
        }
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
    public function destroy($productId, $imageId)
    {
        try {
            $image = ProductImage::findOrFail($imageId);

            // ลบไฟล์จาก Storage
            if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
                Storage::disk('public')->delete($image->image_url);
            }

            $image->delete();

            return back()->with('success', 'ลบรูปภาพเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * ตั้งรูปนี้เป็นรูปหลัก (Set Main)
     */
    public function setMain($productId, $imageId)
    {
        try {
            // Reset รูปทั้งหมดของสินค้านี้
            ProductImage::where('product_id', $productId)->update(['is_main' => false]);

            // ตั้งรูปนี้เป็นรูปหลัก
            $image = ProductImage::findOrFail($imageId);
            $image->is_main = true;
            $image->save();

            return back()->with('success', 'ตั้งค่ารูปหลักเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}