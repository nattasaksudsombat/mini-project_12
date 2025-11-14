<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
{
    try {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
        ]);
    
        Category::create($request->all());
    
        return redirect()->route('categories.index')->with('success', 'เพิ่มหมวดหมู่เรียบร้อยแล้ว');
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()
            ->withErrors($e->validator)
            ->withInput()
            ->with('show_create_modal', true); // Flag to show the create modal
    }
}
    
    

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
{
    try {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name,' . $category->id,
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')->with('success', 'แก้ไขหมวดหมู่เรียบร้อยแล้ว');

    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()
        ->withErrors($e->validator)
        ->withInput()
        ->with('edit_id', $category->id); // ส่งค่าของ edit_id
// ใช้ session เพื่อเก็บ edit_id
    }
}


public function destroy(Category $category)
{
    if ($category->products()->count() > 0) {
        return redirect()->route('categories.index')
            ->with('error', 'ไม่สามารถลบหมวดหมู่ที่มีสินค้าได้');
    }

    $category->delete();

    return redirect()->route('categories.index')->with('success', 'ลบหมวดหมู่เรียบร้อยแล้ว');
}

}
