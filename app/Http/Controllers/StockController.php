<?php
// app/Http/Controllers/StockController.php

namespace App\Http\Controllers;

use App\Services\StockService;
use App\Models\ProductColorSize;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StockController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * ประวัติปรับเข้า/ออกล่าสุด 10 รายการของ variant
     */
    public function adjustHistory(int $variantId)
    {
        $variant = DB::table('product_color_size as pcs')
            ->join('products as p','p.id','=','pcs.product_id')
            ->leftJoin('colors as c','c.id','=','pcs.color_id')
            ->leftJoin('sizes  as s','s.id','=','pcs.size_id')
            ->selectRaw('pcs.id, p.name as product_name, c.name as color_name, s.size_name')
            ->where('pcs.id',$variantId)
            ->first();

        if (!$variant) { abort(404); }

        $history = DB::table('stock_transactions')
            ->where('product_color_size_id',$variantId)
            ->whereIn('type',['in','out'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function($r){
                return (object)[
                    'created_at' => $r->created_at,
                    'type'       => $r->type === 'in' ? 'เข้า' : 'ออก',
                    'before'     => (int)$r->quantity_before,
                    'delta_str'  => ($r->quantity >= 0 ? '+' : '').(int)$r->quantity,
                    'after'      => (int)$r->quantity_after,
                    'reason'     => $r->reason,
                    'user_name'  => $r->user_name ?? '-',
                ];
            });

        return view('stock.adjust_history', [
            'variant' => $variant,
            'history' => $history,
        ]);
    }

    /**
     * หน้า stock ของสินค้าตัวหนึ่ง (group ตามสี)
     */
    public function productStock(int $productId)
    {
        $product = DB::table('products')->find($productId);
        if (!$product) { abort(404); }

        $rows = DB::table('v_current_stock as v')
            ->join('product_color_size as pcs','pcs.id','=','v.id')
            ->leftJoin('colors as c','c.id','=','pcs.color_id')
            ->leftJoin('sizes  as s','s.id','=','pcs.size_id')
            ->selectRaw('v.id as variant_id, c.name as color_name, s.size_name, v.current_stock, v.reserved_stock, v.available_stock')
            ->where('v.product_id', $productId)
            ->orderBy('c.name')->orderBy('s.size_name')
            ->get();

        return view('stock.product', [
            'product' => $product,
            'grouped' => $rows->groupBy('color_name'),
        ]);
    }

    /**
     * รายการเคลื่อนไหวรวม (มีฟิลเตอร์)
     */
    public function history(Request $request)
    {
        $query = DB::table('v_stock_movements');

        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date.' 00:00:00',
                $request->end_date.' 23:59:59',
            ]);
        }
        if ($request->user_name) {
            $query->where('user_name','like','%'.$request->user_name.'%');
        }

        $transactions = $query->orderByDesc('created_at')->paginate(50);
        $products = Product::select('id','name','id_stock')->get();

        return view('stock.history', compact('transactions','products'));
    }

    /**
     * ฟอร์มปรับสต๊อก (manual)
     */
    public function adjustForm(int $variantId)
    {
        $variant = DB::table('product_color_size as pcs')
            ->join('products as p','p.id','=','pcs.product_id')
            ->leftJoin('colors as c','c.id','=','pcs.color_id')
            ->leftJoin('sizes  as s','s.id','=','pcs.size_id')
            ->selectRaw('pcs.id, pcs.product_id, p.name as product_name, c.name as color_name, s.size_name')
            ->where('pcs.id',$variantId)
            ->first();

        if (!$variant) { abort(404); }

        $v = DB::table('v_current_stock')->where('id',$variantId)->first();
        if (!$v) { abort(500,"ไม่พบ variant id={$variantId} ใน v_current_stock"); }

        $last10 = DB::table('stock_transactions')
            ->where('product_color_size_id',$variantId)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('stock.adjust', [
            'variant' => $variant,
            'summary' => (object)[
                'current'   => (int)$v->current_stock,
                'reserved'  => (int)$v->reserved_stock,
                'available' => (int)$v->available_stock,
            ],
            'last10'  => $last10,
        ]);
    }

    /**
     * บันทึกการปรับสต๊อก (manual)
     * action: in|out, quantity: >=1
     */
    public function adjustSave(int $variantId, Request $request, StockService $svc)
    {
        $request->validate([
            'action'   => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'reason'   => 'nullable|string|max:255',
            'ref'      => 'nullable|string|max:100',
        ], [], [
            'action'   => 'ประเภทการปรับ',
            'quantity' => 'จำนวน',
            'reason'   => 'เหตุผล',
            'ref'      => 'เลขอ้างอิง',
        ]);

        $action = $request->input('action');
        $qty    = (int)$request->input('quantity');
        $reason = $request->input('reason') ?: ($action==='in' ? 'รับสินค้าเข้า (manual)' : 'ตัดสต๊อค (manual)');
        $ref    = $request->input('ref');

        try {
            if ($action === 'in') {
                $svc->increaseStock($variantId, $qty, $reason, $ref);
            } else {
                $svc->decreaseStock($variantId, $qty, $reason, $ref);
            }
            return redirect()->route('stock.adjust.form', $variantId)->with('success','ปรับสต๊อคเรียบร้อย');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * ดูประวัติ/ไทม์ไลน์ของ Variant + Holds ปัจจุบัน (Golden Rule)
     * ส่ง summary/holds/history ให้ view ครบ
     */
    public function variantHistory(int $variantId, Request $request)
    {
        // รายละเอียด variant
        $variant = DB::table('product_color_size as pcs')
            ->join('products as p','p.id','=','pcs.product_id')
            ->leftJoin('colors as c','c.id','=','pcs.color_id')
            ->leftJoin('sizes  as s','s.id','=','pcs.size_id')
            ->selectRaw('pcs.id, pcs.product_id, p.name as product_name, c.name as color_name, s.size_name')
            ->where('pcs.id',$variantId)
            ->first();
        if (!$variant) { abort(404); }

        // summary จาก v_current_stock
        $v = DB::table('v_current_stock')->where('id',$variantId)->first();
        if (!$v) { abort(500,"ไม่พบ variant id={$variantId} ใน v_current_stock"); }

        $summary = (object)[
            'current'   => (int)$v->current_stock,
            'reserved'  => (int)$v->reserved_stock,
            'available' => (int)$v->available_stock,
        ];

        // scope กรองประเภทในไทม์ไลน์
        $scope = $request->query('scope','all');

        // ประวัติจาก stock_transactions
        $q = DB::table('stock_transactions')
            ->where('product_color_size_id',$variantId)
            ->orderByDesc('created_at');

        if ($scope === 'holds') {
            $q->whereIn('type',['reserve','release']);
        } elseif ($scope === 'physical') {
            $q->whereIn('type',['in','out']);
        } else {
            $q->whereIn('type',['reserve','release','in','out']);
        }

        $rows = $q->limit(100)->get();

        $mapTH = ['reserve'=>'จอง','release'=>'ปล่อย','in'=>'เข้า','out'=>'ออก'];
        $history = $rows->map(function($r) use ($mapTH){
            $delta = (int)$r->quantity;
            return (object)[
                'created_at' => $r->created_at,
                'type'       => $r->type,
                'type_th'    => $mapTH[$r->type] ?? $r->type,
                'before'     => (int)$r->quantity_before,
                'delta'      => $delta,
                'delta_str'  => ($delta >= 0 ? '+' : '').$delta,
                'after'      => (int)$r->quantity_after,
                'reason'     => $r->reason,
                'user_name'  => $r->user_name ?? '-',
                'order_id'   => $r->order_id,
                'ref'        => $r->reference_number,
            ];
        });

        // Holds ปัจจุบัน
        $holds = collect();
        if (Schema::hasTable('stock_holds')) {
            $openStatuses = ['pending','processing'];
            $orderNoExpr = Schema::hasColumn('orders','order_number') ? 'o.order_number'
                        : (Schema::hasColumn('orders','code')         ? 'o.code'
                        : (Schema::hasColumn('orders','order_no')     ? 'o.order_no' : 'o.id'));

            $holds = DB::table('stock_holds as sh')
                ->leftJoin('orders as o','o.id','=','sh.order_id')
                ->where('sh.product_color_size_id',$variantId)
                ->where('sh.status','active')
                ->when(Schema::hasTable('orders'), function($qq) use ($openStatuses){
                    $qq->whereIn('o.status',$openStatuses);
                })
                ->orderByDesc('sh.updated_at')
                ->get([
                    'sh.order_id','sh.quantity','o.status',
                    DB::raw("$orderNoExpr as order_number"),
                ]);
        }

        return view('stock.variant-history', [
            'variant' => $variant,
            'summary' => $summary,
            'scope'   => $scope,
            'history' => $history,
            'holds'   => $holds,
        ]);
    }

    /**
     * รายงานรวม + Export
     */
    public function report(Request $request)
    {
        $query = DB::table('v_current_stock');

        if ($request->search) {
            $query->where(function($q) use ($request){
                $q->where('product_name','like','%'.$request->search.'%')
                  ->orWhere('id_stock','like','%'.$request->search.'%');
            });
        }
        if ($request->stock_status === 'out') {
            $query->where('available_stock','<=',0);
        } elseif ($request->stock_status === 'low') {
            $query->where('available_stock','>',0)
                  ->where('available_stock','<=',10);
        }

        $stocks = $query->paginate(50);
        return view('stock.report', compact('stocks'));
    }

    public function export(Request $request)
    {
        $stocks = DB::table('v_current_stock')->get();

        $filename = 'stock_report_'.date('Y-m-d').'.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$filename.'"');

        $output = fopen('php://output','w');

        fputcsv($output, [
            'รหัสสินค้า','ชื่อสินค้า','สี','ไซส์',
            'สต๊อกปัจจุบัน','ถูกจอง','คงเหลือพร้อมขาย'
        ]);

        foreach ($stocks as $s) {
            fputcsv($output, [
                $s->id_stock,
                $s->product_name,
                $s->color_name,
                $s->size_name,
                $s->current_stock,
                $s->reserved_stock,
                $s->available_stock,
            ]);
        }

        fclose($output);
        exit;
    }
}
