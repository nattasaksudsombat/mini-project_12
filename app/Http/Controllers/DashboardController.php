<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\Order; // Added Order model
use App\Models\ProductColorSize; // Added ProductColorSize model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // --- Existing Financial Data ---
        $totalIncome = Income::sum('amount');
        $totalExpense = Expense::sum('amount');
        $balance = $totalIncome - $totalExpense;
        $recentIncomes = Income::orderBy('date', 'desc')->take(5)->get();
        $recentExpenses = Expense::orderBy('date', 'desc')->take(5)->get();
        $incomeCategories = Income::select('category')->distinct()->pluck('category');
        $expenseCategories = Expense::select('category')->distinct()->pluck('category');
        
        // --- New Operational Data ---

        // 1. Today's Sales
        $todaySales = Order::whereDate('created_at', Carbon::today())
            ->whereIn('payment_status', ['paid']) // Only count paid
            ->sum('total_price');

        // 2. Pending Orders Count (To Process)
        $pendingOrdersCount = Order::whereIn('status', ['pending', 'processing'])->count();

        // 3. Low Stock Items Count
        $lowStockCount = ProductColorSize::where('quantity', '<=', 10)->count();

        // --- Filtering Logic (Kept same) ---
        $filteredIncomes = null;
        $filteredExpenses = null;
        $incomeQuery = Income::query();
        $expenseQuery = Expense::query();
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $incomeQuery->whereBetween('date', [$startDate, $endDate]);
            $expenseQuery->whereBetween('date', [$startDate, $endDate]);
        }
        if ($request->has('income_category') && !empty($request->income_category)) {
            $incomeQuery->where('category', $request->income_category);
        }
        if ($request->has('expense_category') && !empty($request->expense_category)) {
            $expenseQuery->where('category', $request->expense_category);
        }
        if ($request->has('min_amount') && is_numeric($request->min_amount)) {
            $minAmount = (float) $request->min_amount;
            $incomeQuery->where('amount', '>=', $minAmount);
            $expenseQuery->where('amount', '>=', $minAmount);
        }
        if ($request->has('max_amount') && is_numeric($request->max_amount)) {
            $maxAmount = (float) $request->max_amount;
            $incomeQuery->where('amount', '<=', $maxAmount);
            $expenseQuery->where('amount', '<=', $maxAmount);
        }
        
        if ($request->has('start_date') || $request->has('income_category') || 
            $request->has('expense_category') || $request->has('min_amount') || 
            $request->has('max_amount')) {
            
            $filteredIncomes = $incomeQuery->orderBy('date', 'desc')->get();
            $filteredExpenses = $expenseQuery->orderBy('date', 'desc')->get();
        }
        
        return view('dashboard', compact(
            'totalIncome', 
            'totalExpense', 
            'balance', 
            'recentIncomes', 
            'recentExpenses',
            'filteredIncomes',
            'filteredExpenses',
            'incomeCategories',
            'expenseCategories',
            'todaySales', // Pass to view
            'pendingOrdersCount', // Pass to view
            'lowStockCount' // Pass to view
        ));
    }
}
