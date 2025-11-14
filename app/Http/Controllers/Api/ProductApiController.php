<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductApiController extends Controller
{
    /**
     * GET /api/products/search?q=
     * returns: id, name, sku, price
     */
    public function search(Request $request)
    {
        $q = trim($request->query('q', ''));

        $rows = DB::table('products')
            ->when($q !== '', function ($qq) use ($q) {
                $like = '%' . $q . '%';
                $qq->where(function ($w) use ($like) {
                    $w->where('name', 'LIKE', $like)
                      ->orWhere('id_stock', 'LIKE', $like);
                });
            })
            ->select('id', 'name', 'id_stock as sku', 'price')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json($rows);
    }

    /**
     * GET /api/products/{product}/variants
     * returns: [{id, color_id, size_id, quantity, color_name, size_name}]
     */
    public function variants($productId)
    {
        $rows = DB::table('product_color_size as pcs')
            ->join('colors as c', 'c.id', '=', 'pcs.color_id')
            ->join('sizes  as s', 's.id', '=', 'pcs.size_id')
            ->where('pcs.product_id', $productId)
            ->select([
                'pcs.id',
                'pcs.color_id',
                'pcs.size_id',
                'pcs.quantity',
                'c.name as color_name',     // ใช้ colors.name ตามสคีมา
                's.size_name as size_name', // หลีกเลี่ยง s.name
            ])
            ->orderBy('c.name')
            ->orderBy('s.id')
            ->get();

        return response()->json($rows);
    }

    /**
     * GET /api/products/{product}/reserved
     * returns: { variant_id: qty }
     * นับเฉพาะสถานะ open = [pending, processing]
     */
    public function reserved(Request $request, $productId)
    {
        $excludeOrderId = (int) $request->query('exclude_order_id', 0);

        $rows = DB::table('stock_holds as h')
            ->join('product_color_size as pcs', 'pcs.id', '=', 'h.product_color_size_id')
            ->leftJoin('orders as o', 'o.id', '=', 'h.order_id')
            ->where('pcs.product_id', $productId)
            ->where('h.status', 'active')
            ->where(function ($q) use ($excludeOrderId) {
                // holds แบบไม่มีออเดอร์ (manual hold) และยังไม่หมดอายุ
                $q->where(function ($w) {
                    $w->whereNull('h.order_id')
                      ->where(function ($w2) {
                          $w2->whereNull('h.expires_at')
                             ->orWhere('h.expires_at', '>', now());
                      });
                })
                // หรือ holds แบบมีออเดอร์ที่สถานะเปิด (pending/processing)
                ->orWhere(function ($w) use ($excludeOrderId) {
                    $w->whereNotNull('h.order_id')
                      ->whereIn('o.status', ['pending', 'processing']);
                    if ($excludeOrderId) {
                        $w->where('h.order_id', '!=', $excludeOrderId);
                    }
                });
            })
            ->groupBy('pcs.id')
            ->select('pcs.id as variant_id', DB::raw('SUM(h.quantity) AS qty'))
            ->get();

        $map = [];
        foreach ($rows as $r) {
            $map[(string)$r->variant_id] = (int)$r->qty;
        }
        return response()->json($map);
    }
    /**
     * GET /api/products/{product}/holds
     * returns: { variant_id: [{order_id, order_number, customer_name, status, quantity}] }
     * อิงสถานะ open = [pending, processing]
     */
    public function holds(Request $request, $productId)
    {
        $excludeOrderId = (int) $request->query('exclude_order_id', 0);

        $rows = DB::table('stock_holds as h')
            ->join('product_color_size as pcs', 'pcs.id', '=', 'h.product_color_size_id')
            ->leftJoin('orders as o', 'o.id', '=', 'h.order_id')
            ->leftJoin('customers as c', 'c.id', '=', 'o.customer_id')
            ->where('pcs.product_id', $productId)
            ->where('h.status', 'active')
            ->where(function ($q) use ($excludeOrderId) {
                $q->where(function ($w) {
                    $w->whereNull('h.order_id')
                      ->where(function ($w2) {
                          $w2->whereNull('h.expires_at')
                             ->orWhere('h.expires_at', '>', now());
                      });
                })
                ->orWhere(function ($w) use ($excludeOrderId) {
                    $w->whereNotNull('h.order_id')
                      ->whereIn('o.status', ['pending', 'processing']);
                    if ($excludeOrderId) {
                        $w->where('h.order_id', '!=', $excludeOrderId);
                    }
                });
            })
            ->orderByDesc('h.created_at')
            ->select([
                'pcs.id as variant_id',
                'h.order_id',
                'o.order_number',
                DB::raw('COALESCE(c.name, "Hold ชั่วคราว") as customer_name'),
                'o.status',
                'h.quantity',
            ])
            ->get();

        $map = [];
        foreach ($rows as $r) {
            $vid = (string)$r->variant_id;
            if (!isset($map[$vid])) $map[$vid] = [];
            $map[$vid][] = [
                'order_id'      => $r->order_id ? (int)$r->order_id : null,
                'order_number'  => $r->order_number,
                'customer_name' => $r->customer_name,
                'status'        => $r->status, // อาจเป็น null ถ้าเป็น manual hold
                'quantity'      => (int)$r->quantity,
            ];
        }
        return response()->json($map);
    }
}
