<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Size;

class SizeController extends Controller
{
    // แสดงรายการขนาดทั้งหมด
    public function index()
    {
        // ✅ โหลดความสัมพันธ์เพื่อนับจำนวนสินค้า (ใช้ตาราง product_sizes)
        $sizes = Size::withCount('productColorSizes')->orderBy('id')->get();
        return view('sizes.index', compact('sizes'));
    }

    // บันทึกขนาดใหม่
    public function store(Request $request)
    {
        $request->validate([
            'size_name' => 'required|unique:sizes,size_name|max:50',
        ], [
            'size_name.required' => 'กรุณากรอกชื่อขนาด',
            'size_name.unique' => 'ชื่อขนาดนี้มีอยู่ในระบบแล้ว',
            'size_name.max' => 'ชื่อขนาดต้องไม่เกิน 50 ตัวอักษร',
        ]);

        Size::create([
            'size_name' => $request->size_name,
        ]);

        return redirect()->route('sizes.index')->with('success', 'เพิ่มขนาดเรียบร้อยแล้ว');
    }

    // อัปเดตข้อมูลขนาด
    public function update(Request $request, Size $size)
    {
        // ✅ ตรวจสอบว่ามีสินค้าใช้ขนาดนี้อยู่หรือไม่ (จากตาราง product_sizes)
        $productCount = $size->productColorSizes()->count();
        
        if ($productCount > 0) {
            return redirect()->back()->with('error', 'ไม่สามารถแก้ไขขนาดได้ เนื่องจากมีสินค้า ' . $productCount . ' รายการใช้ขนาดนี้อยู่');
        }

        $request->validate([
            'size_name' => 'required|max:50|unique:sizes,size_name,' . $size->id,
        ], [
            'size_name.required' => 'กรุณากรอกชื่อขนาด',
            'size_name.unique' => 'ชื่อขนาดนี้มีอยู่ในระบบแล้ว',
            'size_name.max' => 'ชื่อขนาดต้องไม่เกิน 50 ตัวอักษร',
        ]);

        $size->update([
            'size_name' => $request->size_name,
        ]);

        return redirect()->route('sizes.index')->with('success', 'แก้ไขขนาดเรียบร้อยแล้ว');
    }

    // ลบขนาด
    public function destroy(Size $size)
    {
        // ✅ ตรวจสอบว่ามีสินค้าใช้ขนาดนี้อยู่หรือไม่ (จากตาราง product_sizes)
        $productCount = $size->productColorSizes()->count();
        
        if ($productCount > 0) {
            return redirect()->back()->with('error', 'ไม่สามารถลบขนาดได้ เนื่องจากมีสินค้า ' . $productCount . ' รายการใช้ขนาดนี้อยู่');
        }

        $size->delete();
        return redirect()->route('sizes.index')->with('success', 'ลบขนาดเรียบร้อยแล้ว');
    }
}