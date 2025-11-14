<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Size;

class SizeController extends Controller
{
    // แสดงรายการขนาดทั้งหมด
        public function index()
    {
        $sizes = Size::orderBy('id')->get();
        return view('sizes.index', compact('sizes'));
    }

    // บันทึกขนาดใหม่
    public function store(Request $request)
    {
        $request->validate([
            'size_name' => 'required|unique:sizes,size_name|max:50',
        ]);

        Size::create([
            'size_name' => $request->size_name,
        ]);

        return redirect()->route('sizes.index')->with('success', 'เพิ่มขนาดเรียบร้อยแล้ว');
    }

    // อัปเดตข้อมูลขนาด
    public function update(Request $request, Size $size)
    {
        $request->validate([
            'size_name' => 'required|max:50|unique:sizes,size_name,' . $size->id,
        ]);

        $size->update([
            'size_name' => $request->size_name,
        ]);

        return redirect()->route('sizes.index')->with('success', 'แก้ไขขนาดเรียบร้อยแล้ว');
    }

    // ลบขนาด
    public function destroy(Size $size)
    {
        $size->delete();
        return redirect()->route('sizes.index')->with('success', 'ลบขนาดเรียบร้อยแล้ว');
    }
}
