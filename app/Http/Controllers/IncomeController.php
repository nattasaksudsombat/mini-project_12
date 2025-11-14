<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ดึงข้อมูลรายรับโดยใช้เงื่อนไขการกรอง
        $incomes = Income::when($request->start_date, function ($query) use ($request) {
                return $query->where('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                return $query->where('date', '<=', $request->end_date);
            })
            ->when($request->category, function ($query) use ($request) {
                return $query->where('category', $request->category);
            })
            ->orderBy('date', 'desc')
            ->paginate(20); // ✅ ใช้ paginate() เพื่อให้สามารถเรียก total() ได้

        // ดึงประเภทของรายรับทั้งหมดสำหรับแสดงใน dropdown
        $categories = Income::distinct()->pluck('category');

        // ส่งค่าตัวแปรไปยัง View
        return view('incomes.index', compact('incomes', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ดึงรายการประเภทที่มีอยู่เพื่อแสดงในฟอร์ม
        $categories = Income::select('category')->distinct()->pluck('category');
        
        return view('incomes.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ตรวจสอบข้อมูลที่ส่งมา
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string'
        ]);
        
        // สร้างรายการรายรับใหม่
        Income::create($validated);
        
        return redirect()->route('incomes.index')
            ->with('success', 'บันทึกรายรับเรียบร้อยแล้ว');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Income $income)
    {
        // ดึงรายการประเภทที่มีอยู่เพื่อแสดงในฟอร์ม
        $categories = Income::select('category')->distinct()->pluck('category');
        
        return view('incomes.edit', compact('income', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Income $income)
    {
        // ตรวจสอบข้อมูลที่ส่งมา
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string'
        ]);
        
        // อัปเดตข้อมูลรายรับ
        $income->update($validated);
        
        return redirect()->route('incomes.index')
            ->with('success', 'อัปเดตรายรับเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Income $income)
    {
        // ลบรายการรายรับ
        $income->delete();
        
        return redirect()->route('incomes.index')
            ->with('success', 'ลบรายรับเรียบร้อยแล้ว');
    }
}