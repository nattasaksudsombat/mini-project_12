<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Product;
use Carbon\Carbon;

/**
 * ===================================================================
 * ReportController - ระบบรายงานและวิเคราะห์
 * ===================================================================
 * ฟีเจอร์:
 * 1. Dashboard สรุปภาพรวม (ยอดขาย, กำไร, ออเดอร์รอจัดส่ง, สต็อกต่ำ)
 * 2. กราฟวิเคราะห์ (ยอดขายรายวัน, สัดส่วนตามหมวดหมู่, สินค้าขายดี)
 * 3. รายงานตารางรายรับ-รายจ่าย พร้อม Export Excel
 * ===================================================================
 */
class ReportController extends Controller
{
    /**
     * หน้า Dashboard สรุปภาพรวม
     */
    public function index(Request $request)
    {
        // ช่วงวันที่ที่เลือก (default = เดือนนี้)
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // --------------------------------------------------
        // 1. ยอดขายวันนี้, เดือนนี้, ปีนี้
        // --------------------------------------------------
        $salesToday = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $salesThisMonth = Order::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $salesThisYear = Order::whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // --------------------------------------------------
        // 2. กำไรสุทธิ (รายรับจาก Order - ต้นทุน - รายจ่าย + รายรับอื่น)
        // --------------------------------------------------
        // รายรับจาก Order (ที่ชำระแล้ว)
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // ต้นทุนสินค้า (ดึงจาก order_items + cost ใน products)
        $totalCost = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->join('products as p', 'p.id', '=', 'oi.product_id')
            ->whereBetween('o.created_at', [$startDate, $endDate])
            ->where('o.payment_status', 'paid')
            ->sum(DB::raw('oi.quantity * COALESCE(p.cost, 0)'));

        // รายจ่าย (จาก expenses)
        $totalExpense = Expense::whereBetween('date', [$startDate, $endDate])->sum('amount');

        // รายรับอื่น (จาก incomes)
        $totalIncome = Income::whereBetween('date', [$startDate, $endDate])->sum('amount');

        // กำไรสุทธิ = รายรับจาก Order - ต้นทุน - รายจ่าย + รายรับอื่น
        $netProfit = $totalRevenue - $totalCost - $totalExpense + $totalIncome;

        // --------------------------------------------------
        // 3. ออเดอร์รอจัดส่ง
        // --------------------------------------------------
        $pendingOrders = Order::whereIn('status', ['pending', 'processing'])->count();

        // --------------------------------------------------
        // 4. สินค้าที่สต็อกใกล้หมด (available_stock <= 10)
        // --------------------------------------------------
        $lowStockCount = DB::table('product_color_size as pcs')
    ->leftJoin(DB::raw('(SELECT product_color_size_id, SUM(quantity) as reserved FROM stock_holds WHERE status="active" GROUP BY product_color_size_id) as h'), 'h.product_color_size_id', '=', 'pcs.id')
    ->selectRaw('GREATEST(0, pcs.quantity - COALESCE(h.reserved,0)) as available_stock')
    ->havingRaw('available_stock <= 10 AND available_stock > 0')
    ->count();

        // --------------------------------------------------
        // ส่งข้อมูลไป View
        // --------------------------------------------------
        return view('reports.dashboard', compact(
            'salesToday',
            'salesThisMonth',
            'salesThisYear',
            'totalRevenue',
            'totalCost',
            'totalExpense',
            'totalIncome',
            'netProfit',
            'pendingOrders',
            'lowStockCount',
            'startDate',
            'endDate'
        ));
    }

    /**
     * หน้ากราฟวิเคราะห์
     */
    public function charts(Request $request)
    {
        $days = (int)($request->input('days', 30)); // จำนวนวันย้อนหลัง (default = 30)

        // --------------------------------------------------
        // 1. กราฟเส้นยอดขายรายวัน (ย้อนหลัง X วัน)
        // --------------------------------------------------
        $dailySales = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->where('created_at', '>=', now()->subDays($days))
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // แปลงเป็น JSON สำหรับ Chart.js
        $salesLabels = $dailySales->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray();
        $salesData = $dailySales->pluck('total')->toArray();

        // --------------------------------------------------
        // 2. กราฟวงกลม: สัดส่วนยอดขายตามหมวดหมู่
        // --------------------------------------------------
        $categorySales = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->join('products as p', 'p.id', '=', 'oi.product_id')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->where('o.payment_status', 'paid')
            ->select('c.category_name', DB::raw('SUM(oi.total_price) as total'))
            ->groupBy('c.id', 'c.category_name')
            ->orderByDesc('total')
            ->get();

        $categoryLabels = $categorySales->pluck('category_name')->toArray();
        $categoryData = $categorySales->pluck('total')->toArray();

        // --------------------------------------------------
        // 3. กราฟแท่ง: สินค้าขายดี 5 อันดับแรก
        // --------------------------------------------------
        $topProducts = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->where('o.payment_status', 'paid')
            ->select('oi.product_name', DB::raw('SUM(oi.quantity) as total_qty'))
            ->groupBy('oi.product_id', 'oi.product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $topProductLabels = $topProducts->pluck('product_name')->toArray();
        $topProductData = $topProducts->pluck('total_qty')->toArray();

        // --------------------------------------------------
        // ส่งข้อมูลไป View
        // --------------------------------------------------
        return view('reports.charts', compact(
            'salesLabels',
            'salesData',
            'categoryLabels',
            'categoryData',
            'topProductLabels',
            'topProductData',
            'days'
        ));
    }

    /**
     * หน้ารายงานตาราง รายรับ-รายจ่าย
     */
    public function financial(Request $request)
    {
        // ช่วงวันที่
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // --------------------------------------------------
        // 1. รายรับจาก Orders
        // --------------------------------------------------
        $orderRevenue = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as amount, "รายรับจาก Order" as type')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->get();

        // --------------------------------------------------
        // 2. รายรับอื่น (Incomes)
        // --------------------------------------------------
        $incomes = Income::selectRaw('date, amount, CONCAT("รายรับอื่น: ", description) as type')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // --------------------------------------------------
        // 3. รายจ่าย (Expenses)
        // --------------------------------------------------
        $expenses = Expense::selectRaw('date, amount, CONCAT("รายจ่าย: ", description) as type')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // --------------------------------------------------
        // รวมทุกรายการ และเรียงตามวันที่
        // --------------------------------------------------
        $transactions = collect()
            ->merge($orderRevenue->map(fn($item) => [
                'date' => $item->date,
                'type' => $item->type,
                'income' => $item->amount,
                'expense' => 0
            ]))
            ->merge($incomes->map(fn($item) => [
                'date' => $item->date,
                'type' => $item->type,
                'income' => $item->amount,
                'expense' => 0
            ]))
            ->merge($expenses->map(fn($item) => [
                'date' => $item->date,
                'type' => $item->type,
                'income' => 0,
                'expense' => $item->amount
            ]))
            ->sortBy('date')
            ->values();

        // สรุปยอดรวม
        $totalIncome = $transactions->sum('income');
        $totalExpense = $transactions->sum('expense');
        $balance = $totalIncome - $totalExpense;

        return view('reports.financial', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'balance',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export Excel (ใช้ Laravel Excel หรือ CSV ธรรมดา)
     * ตัวอย่างนี้ใช้ CSV ง่ายๆ
     */
    public function exportFinancial(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // ดึงข้อมูลเหมือน financial()
        $orderRevenue = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as amount, "รายรับจาก Order" as type')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->get();

        $incomes = Income::selectRaw('date, amount, CONCAT("รายรับอื่น: ", description) as type')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $expenses = Expense::selectRaw('date, amount, CONCAT("รายจ่าย: ", description) as type')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $transactions = collect()
            ->merge($orderRevenue->map(fn($item) => [
                'date' => $item->date,
                'type' => $item->type,
                'income' => $item->amount,
                'expense' => 0
            ]))
            ->merge($incomes->map(fn($item) => [
                'date' => $item->date,
                'type' => $item->type,
                'income' => $item->amount,
                'expense' => 0
            ]))
            ->merge($expenses->map(fn($item) => [
                'date' => $item->date,
                'type' => $item->type,
                'income' => 0,
                'expense' => $item->amount
            ]))
            ->sortBy('date')
            ->values();

        // สร้างไฟล์ CSV
        $filename = 'รายงานการเงิน_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM สำหรับ UTF-8

        // หัวตาราง
        fputcsv($output, ['วันที่', 'รายการ', 'รายรับ (บาท)', 'รายจ่าย (บาท)']);

        // เขียนข้อมูล
        foreach ($transactions as $row) {
            fputcsv($output, [
                $row['date'],
                $row['type'],
                number_format($row['income'], 2),
                number_format($row['expense'], 2)
            ]);
        }

        // สรุปท้าย
        fputcsv($output, ['']);
        fputcsv($output, ['รวมรายรับ', '', number_format($transactions->sum('income'), 2), '']);
        fputcsv($output, ['รวมรายจ่าย', '', '', number_format($transactions->sum('expense'), 2)]);
        fputcsv($output, ['คงเหลือ', '', number_format($transactions->sum('income') - $transactions->sum('expense'), 2), '']);

        fclose($output);
        exit;
    }
}