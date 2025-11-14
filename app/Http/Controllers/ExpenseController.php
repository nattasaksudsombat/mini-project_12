<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $expenses = Expense::when($request->start_date, function ($query) use ($request) {
                return $query->where('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                return $query->where('date', '<=', $request->end_date);
            })
            ->when($request->category, function ($query) use ($request) {
                return $query->where('category', $request->category);
            })
            ->orderBy('date', 'asc')
            ->paginate(20); // ✅ ใช้ paginate() 
    
        // คำนวณยอดรวมรายจ่ายตามเดือน
        $expensesByMonth = $expenses->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->date)->format('Y-m'); // แยกตามปี-เดือน
        })->map(function ($group) {
            return [
                'month' => $group->first()->date, 
                'total' => $group->sum('amount'),
            ];
        })->values();
    
        $categories = Expense::distinct()->pluck('category');
    
        return view('expenses.index', compact('expenses', 'expensesByMonth', 'categories'));
    }
    
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('expenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category' => 'required|string|max:100',
        ]);

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'รายจ่ายถูกบันทึกเรียบร้อยแล้ว');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category' => 'required|string|max:100',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'รายจ่ายถูกแก้ไขเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'รายจ่ายถูกลบเรียบร้อยแล้ว');
    }
}