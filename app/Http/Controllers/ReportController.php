<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductColorSize;
use App\Models\Setting; // Added Setting model
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Daily Sales (Last 30 Days)
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(29); // Include today, so go back 29 days

        // Filter orders that are paid
        $dailySalesData = Order::whereBetween('created_at', [$startDate->format('Y-m-d 00:00:00'), $endDate->format('Y-m-d 23:59:59')])
            ->where('payment_status', 'paid')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as total'))
            ->groupBy(DB::raw('DATE(created_at)')) // Fix for Strict Mode
            ->orderBy('date')
            ->get();

        $dailyLabels = [];
        $dailyValues = [];

        // Fill in missing dates with 0
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $dailyLabels[] = $date->format('d/m');

            $sale = $dailySalesData->firstWhere('date', $formattedDate);
            $dailyValues[] = $sale ? $sale->total : 0;
        }

        // 2. Monthly Sales (Current Year)
        $monthlySalesData = Order::whereYear('created_at', Carbon::now()->year)
             ->where('payment_status', 'paid')
             ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_price) as total'))
             ->groupBy('month')
             ->orderBy('month')
             ->get();

        $monthlyLabels = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $monthlyValues = array_fill(0, 12, 0);

        foreach ($monthlySalesData as $data) {
            $monthlyValues[$data->month - 1] = $data->total;
        }

        // 3. Top 5 Best Selling Products
        $topProducts = OrderItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as total_qty'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 4. Low Stock Items (Variant < 10)
        // Use setting if available, default to 10
        $lowStockThreshold = (int) Setting::getValue('low_stock_threshold', 10);

        $lowStockItems = ProductColorSize::with(['product', 'color', 'size'])
            ->where('quantity', '<=', $lowStockThreshold)
            ->orderBy('quantity', 'asc')
            ->limit(50)
            ->get();

        return view('reports.index', compact(
            'dailyLabels', 'dailyValues',
            'monthlyLabels', 'monthlyValues',
            'topProducts',
            'lowStockItems'
        ));
    }
}
