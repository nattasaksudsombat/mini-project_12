<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index()
    {
        // ดึงข้อมูลไซส์พร้อมนับจำนวนสินค้าที่ใช้อยู่ (product_color_sizes_count)
        $sizes = Size::withCount('productColorSizes')->get();
        return view('sizes.index', compact('sizes'));
    }

    public function store(Request $request)
    {
        // ✅ Validation: ห้ามชื่อซ้ำ
        $request->validate([
            'size_name' => 'required|string|max:50|unique:sizes,size_name',
        ], [
            'size_name.required' => 'กรุณากรอกชื่อไซส์',
            'size_name.unique' => 'ชื่อไซส์นี้มีอยู่แล้ว',
        ]);

        Size::create($request->all());

        return redirect()->route('sizes.index')->with('success', 'เพิ่มไซส์เรียบร้อยแล้ว');
    }

    public function update(Request $request, Size $size)
    {
        // ✅ Validation: ห้ามชื่อซ้ำ (ยกเว้นตัวมันเอง)
        $request->validate([
            'size_name' => 'required|string|max:50|unique:sizes,size_name,' . $size->id,
        ], [
            'size_name.required' => 'กรุณากรอกชื่อไซส์',
            'size_name.unique' => 'ชื่อไซส์นี้มีอยู่แล้ว',
        ]);

        $size->update($request->all());

        return redirect()->route('sizes.index')->with('success', 'แก้ไขไซส์เรียบร้อยแล้ว');
    }

    public function destroy(Size $size)
    {
        // ✅ เช็คก่อนลบ: ถ้ามีสินค้าใช้ไซส์นี้ ห้ามลบ
        if ($size->productColorSizes()->count() > 0) {
            return redirect()->back()->withErrors(['msg' => 'ไม่สามารถลบได้ เนื่องจากมีสินค้าใช้งานไซส์นี้อยู่']);
        }

        $size->delete();

        return redirect()->route('sizes.index')->with('success', 'ลบไซส์เรียบร้อยแล้ว');
    }
}