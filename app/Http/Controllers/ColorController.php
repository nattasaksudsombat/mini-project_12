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
        'color_name' => 'required|unique:colors,name',
        'hex_code' => 'string',
    ]);

    // ปรับการส่งข้อมูลไปยังฐานข้อมูล
    Color::create([
        'name' => $request->color_name,  // แก้เป็น 'name' ไม่ใช่ 'color_name'
        'hex_code' => $request->hex_code ?? '#000000',
    ]);

    return redirect()->route('colors.index')->with('success', 'เพิ่มสีเรียบร้อยแล้ว');
}

    
// อัปเดตสี
public function update(Request $request, Color $color)
{
    $request->validate([
        'color_name' => 'required|unique:colors,name,' . $color->id,
        'hex_code' => 'required',
    ]);

    // แก้ไขค่าที่ตรงกับฐานข้อมูล
    $color->update([
        'name' => $request->color_name, // แก้เป็น 'name' ไม่ใช่ 'color_name'
        'hex_code' => $request->hex_code,
    ]);

    return redirect()->back()->with('success', 'อัปเดตสีเรียบร้อย');
}


public function destroy(Color $color)
{
    $color->delete();
    return redirect()->back()->with('success', 'ลบสีเรียบร้อย');
}
}