<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get total income
        $totalIncome = Income::sum('amount');
        
        // Get total expense
        $totalExpense = Expense::sum('amount');
        
        // Calculate balance
        $balance = $totalIncome - $totalExpense;
        
        // Get recent incomes (last 5)
        $recentIncomes = Income::orderBy('date', 'desc')->take(5)->get();
        
        // Get recent expenses (last 5)
        $recentExpenses = Expense::orderBy('date', 'desc')->take(5)->get();
        
        // Get all income categories for dropdown
        $incomeCategories = Income::select('category')->distinct()->pluck('category');
        
        // Get all expense categories for dropdown
        $expenseCategories = Expense::select('category')->distinct()->pluck('category');
        
        // Filter data based on request parameters
        $filteredIncomes = null;
        $filteredExpenses = null;
        
        // Start building queries
        $incomeQuery = Income::query();
        $expenseQuery = Expense::query();
        
        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            $incomeQuery->whereBetween('date', [$startDate, $endDate]);
            $expenseQuery->whereBetween('date', [$startDate, $endDate]);
        }
        
        // Filter by income category
        if ($request->has('income_category') && !empty($request->income_category)) {
            $incomeQuery->where('category', $request->income_category);
        }
        
        // Filter by expense category
        if ($request->has('expense_category') && !empty($request->expense_category)) {
            $expenseQuery->where('category', $request->expense_category);
        }
        
        // Filter by minimum amount
        if ($request->has('min_amount') && is_numeric($request->min_amount)) {
            $minAmount = (float) $request->min_amount;
            $incomeQuery->where('amount', '>=', $minAmount);
            $expenseQuery->where('amount', '>=', $minAmount);
        }
        
        // Filter by maximum amount
        if ($request->has('max_amount') && is_numeric($request->max_amount)) {
            $maxAmount = (float) $request->max_amount;
            $incomeQuery->where('amount', '<=', $maxAmount);
            $expenseQuery->where('amount', '<=', $maxAmount);
        }
        
        // Execute queries if any filter was applied
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
            'expenseCategories'
        ));
    }
}