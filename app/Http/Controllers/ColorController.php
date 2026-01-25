<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    // แสดงรายการสีทั้งหมด
    public function index()
    {
        $colors = Color::withCount('products')->get();
        return view('colors.index', compact('colors'));
    }

    // เพิ่มสีใหม่
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:colors,name',
            'hex_code' => 'required|string|max:7',
        ], [
            'name.required' => 'กรุณากรอกชื่อสี',
            'name.unique' => 'ชื่อสีนี้มีอยู่ในระบบแล้ว',
            'hex_code.required' => 'กรุณาเลือกรหัสสี',
        ]);

        Color::create([
            'name' => $request->name,
            'hex_code' => $request->hex_code,
        ]);

        return redirect()->route('colors.index')->with('success', 'เพิ่มสีเรียบร้อยแล้ว');
    }

    
    // อัปเดตสี
    public function update(Request $request, Color $color)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:colors,name,' . $color->id,
            'hex_code' => 'required|string|max:7',
        ], [
            'name.required' => 'กรุณากรอกชื่อสี',
            'name.unique' => 'ชื่อสีนี้มีอยู่ในระบบแล้ว',
            'hex_code.required' => 'กรุณาเลือกรหัสสี',
        ]);

        $color->update([
            'name' => $request->name,
            'hex_code' => $request->hex_code,
        ]);

        return redirect()->back()->with('success', 'อัปเดตสีเรียบร้อย');
    }


    // ลบสี
    public function destroy(Color $color)
    {
        // ตรวจสอบว่ามีสินค้าใช้สีนี้อยู่หรือไม่
        $productCount = $color->products()->count();
        
        if ($productCount > 0) {
            return redirect()->back()->with('error', 'ไม่สามารถลบสีได้ เนื่องจากมีสินค้า ' . $productCount . ' รายการใช้สีนี้อยู่');
        }

        $color->delete();
        return redirect()->back()->with('success', 'ลบสีเรียบร้อย');
    }
}