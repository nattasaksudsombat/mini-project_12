<?php
// app/Http/Controllers/StockController.php

namespace App\Http\Controllers;

use App\Services\StockService;
use App\Models\ProductColorSize;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Color;
use App\Models\Size;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Models\CustomerAddress;

class StockController extends Controller
{
    protected StockService $stockService;
public function store(Request $request)
{
    // 1) Validate
    $request->validate([
        'customer.name'             => 'required|string|max:255',
        'customer.phone'            => 'nullable|string|max:20',
        'customer.purchase_channel' => 'required|string',
        'customer.payment_method'   => 'required|string',
        'items_json'                => 'required|json',
        'shipping_fee'              => 'required|numeric|min:0',
        'discount'                  => 'nullable|numeric|min:0',

        // ✅ ลูกค้าเก่า/ใหม่
        'customer_id'               => 'nullable|integer|exists:customers,id',
        'existing_address_id'       => 'nullable|integer|exists:customer_addresses,id',

        // ✅ ที่อยู่ใหม่ (optional)
        'new_address.name'          => 'nullable|string',
        'new_address.address'       => 'nullable|string',
        'new_address.district'      => 'nullable|string',
        'new_address.province'      => 'nullable|string',
        'new_address.postal_code'   => 'nullable|string',

        // ✅ จะถูกเติมโดย JS ก่อน submit
        'shipping_address'          => 'nullable|string',
    ]);

    return DB::transaction(function () use ($request) {

        // ---------------- 2) เตรียมสินค้า / ยอดเงิน ----------------
        $items = json_decode($request->items_json, true) ?: [];
        if (empty($items)) {
            throw new \Exception('ต้องเพิ่มสินค้าอย่างน้อย 1 รายการ');
        }

        $subtotal    = collect($items)->sum(fn($i) => (float)$i['unit_price'] * (int)$i['quantity']);
        $discount    = (float)($request->discount ?? 0);
        $shippingFee = (float)$request->shipping_fee;
        $totalPrice  = $subtotal + $shippingFee - $discount;

        // ---------------- 3) ลูกค้าเก่า/ใหม่ ----------------
        $customerId  = (int)($request->input('customer_id', 0));
        $existingAddressId = (int)$request->input('existing_address_id', 0);
        $newAddr = $request->input('new_address', []);
        $hasNewAddress = !empty(trim($newAddr['address'] ?? ''));

        if ($customerId > 0) {
            // --- ✅ ลูกค้าเก่า: อัปเดตข้อมูลพื้นฐาน ---
            $customer = Customer::findOrFail($customerId);

            $customer->name             = $request->input('customer.name', $customer->name);
            $customer->phone            = $request->input('customer.phone', $customer->phone);
            $customer->purchase_channel = $request->input('customer.purchase_channel', $customer->purchase_channel);
            $customer->payment_method   = $request->input('customer.payment_method', $customer->payment_method);
            $customer->save();

        } else {
            // --- ✅ ลูกค้าใหม่: สร้างเลย ---
            $customer = Customer::create([
                'name'             => $request->input('customer.name'),
                'phone'            => $request->input('customer.phone'),
                'purchase_channel' => $request->input('customer.purchase_channel'),
                'payment_method'   => $request->input('customer.payment_method'),
                'address'          => '', // เก็บว่างไว้ (ใช้ customer_addresses แทน)
            ]);

            $customerId = $customer->id;
        }

        // ---------------- 4) ที่อยู่จัดส่ง (CustomerAddress + shipping_address) ----------------
        $shippingAddress = trim((string)$request->input('shipping_address', ''));

        // Helper รวมที่อยู่เต็มบรรทัดจาก CustomerAddress array
        $buildFullAddress = function (array $addrRow) {
            $parts = [];
            if (!empty($addrRow['address']))      $parts[] = $addrRow['address'];
            if (!empty($addrRow['district']))     $parts[] = $addrRow['district'];
            if (!empty($addrRow['province']))     $parts[] = $addrRow['province'];
            if (!empty($addrRow['postal_code']))  $parts[] = $addrRow['postal_code'];
            return trim(implode(' ', $parts));
        };

        // ถ้า shipping_address ยังไม่มี (เช่น JS ไม่ได้เติม) ให้เราเติม logic นี้
        if ($shippingAddress === '') {

            if ($customerId > 0 && $existingAddressId > 0) {
                // ✅ ใช้ที่อยู่เดิมของลูกค้า
                $addr = CustomerAddress::where('customer_id', $customerId)
                    ->where('id', $existingAddressId)
                    ->first();

                if ($addr) {
                    $shippingAddress = $buildFullAddress([
                        'address'      => $addr->address,
                        'district'     => $addr->district,
                        'province'     => $addr->province,
                        'postal_code'  => $addr->postal_code,
                    ]);
                }

            } elseif ($hasNewAddress) {
                // ✅ เพิ่มที่อยู่ใหม่ลง customer_addresses แล้วใช้เป็น shipping
                $addr = CustomerAddress::create([
                    'customer_id' => $customerId,
                    'name'        => $newAddr['name']        ?? null,
                    'address'     => $newAddr['address']     ?? null,
                    'district'    => $newAddr['district']    ?? null,
                    'province'    => $newAddr['province']    ?? null,
                    'postal_code' => $newAddr['postal_code'] ?? null,
                ]);

                $shippingAddress = $buildFullAddress([
                    'address'      => $addr->address,
                    'district'     => $addr->district,
                    'province'     => $addr->province,
                    'postal_code'  => $addr->postal_code,
                ]);

                // อัปเดต address หลักของลูกค้าด้วย (ถ้ายังว่าง)
                if (empty($customer->address)) {
                    $customer->address = $shippingAddress;
                    $customer->save();
                }

            } else {
                // ไม่มีทั้ง existing / new ให้ fallback ใช้ customer->address เดิม
                $shippingAddress = (string)($customer->address ?? '');
            }
        }

        // ---------------- 5) สร้างเลขออเดอร์ ----------------
        $latestOrder = Order::latest('id')->first();
        $number      = $latestOrder ? ((int) str_replace('ORD', '', $latestOrder->order_number)) + 1 : 1;
        $orderNumber = 'ORD' . str_pad($number, 4, '0', STR_PAD_LEFT);

        // ---------------- 6) สร้าง Order ----------------
        $order = Order::create([
            'customer_id'       => $customerId,
            'shipping_address'  => $shippingAddress,
            'subtotal'          => $subtotal,
            'shipping_fee'      => $shippingFee,
            'discount'          => $discount,
            'total_price'       => $totalPrice,
            'total_amount'      => $totalPrice,
            'notes'             => $request->notes,
            'status'            => 'pending',
            'payment_status'    => 'pending',
            'order_number'      => $orderNumber,
            'stock_reserved_at' => now(),
        ]);

        // ---------------- 7) วนลูปสร้าง OrderItem + จองสต็อค ----------------
        foreach ($items as $item) {
            $productId   = (int)$item['product_id'];
            $productName = $item['product_name'] ?? ($item['name'] ?? '');
            $qty         = (int)$item['quantity'];
            $unitPrice   = (float)$item['unit_price'];

            // หาข้อมูล variant (color/size)
            [$colorId, $sizeId, $variant, $hasVariants] = $this->resolveVariant(
                $productId,
                $item['color_id']     ?? null,
                $item['size_id']      ?? null,
                $item['color_name']   ?? null,
                $item['size_name']    ?? null,
                $item['variant_name'] ?? null
            );

            if ($hasVariants) {
                if (!$variant) {
                    throw new \Exception("กรุณาเลือกสี/ไซส์ให้ครบถ้วนสำหรับสินค้า: {$productName}");
                }

                // จองสต็อกผ่าน StockService
                $this->stockService->reserveNewForOrderVariant(
                    $variant->id,
                    $order->id,
                    $qty,
                    $orderNumber,
                    'สร้างออเดอร์ใหม่'
                );
            }

            // สร้างชื่อ Variant
            $colorName = $colorId ? (Color::find($colorId)->name ?? ($item['color_name'] ?? null)) : ($item['color_name'] ?? null);
            $sizeName  = $sizeId  ? (Size::find($sizeId)->size_name ?? ($item['size_name'] ?? null)) : ($item['size_name'] ?? null);
            $variantName = $this->createVariantName($colorName, $sizeName);

            // บันทึกรายการสินค้า
            OrderItem::create([
                'order_id'               => $order->id,
                'product_id'             => $productId,
                'product_name'           => $productName,
                'product_color_size_id'  => $variant ? $variant->id : null,
                'color_id'               => $colorId,
                'size_id'                => $sizeId,
                'variant_name'           => $variantName,
                'quantity'               => $qty,
                'unit_price'             => $unitPrice,
                'total_price'            => $qty * $unitPrice,
            ]);
        }

        return redirect()
            ->route('orders.index')
            ->with('success', 'สร้างออเดอร์และจองสต็อคเรียบร้อยแล้ว');
    });
}
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * เลือกชื่อคอลัมน์คีย์หลักของ v_current_stock แบบยืดหยุ่น
     * 1) variant_id
     * 2) product_color_size_id
     * 3) id (fallback สุดท้าย)
     */
    private function vStockPk(): string
    {
        foreach (['variant_id', 'product_color_size_id', 'id'] as $col) {
            if (Schema::hasColumn('v_current_stock', $col)) {
                return $col;
            }
        }
        // ถ้าไม่มีจริง ๆ (ไม่น่ามีเคสนี้) ก็คืน 'id' ไว้ก่อน เพื่อไม่ให้ล่ม
        return 'id';
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
     * - อ่านจาก v_current_stock แบบไม่ lock
     * - join ไปที่ product_color_size ด้วยคีย์ที่ถูกต้อง (v.<pk> = pcs.id)
     */
    public function productStock(int $productId)
    {
        $product = DB::table('products')->find($productId);
        if (!$product) { abort(404); }

        $pk = $this->vStockPk();

        $rows = DB::table('v_current_stock as v')
            ->join('product_color_size as pcs', function ($j) use ($pk) {
                // v.<pk> == pcs.id  (ไม่ต้อง DB::raw ก็ได้เพราะเป็น column-to-column)
                $j->on("v.$pk", '=', 'pcs.id');
            })
            ->leftJoin('colors as c', 'c.id', '=', 'pcs.color_id')
            ->leftJoin('sizes  as s', 's.id', '=', 'pcs.size_id')
            ->selectRaw("v.$pk as variant_id, c.name as color_name, s.size_name, v.current_stock, v.reserved_stock, v.available_stock")
            ->where('v.product_id', $productId)
            ->orderBy('c.name')
            ->orderBy('s.size_name')
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
     * - อ่าน summary จาก v_current_stock โดย where ด้วยคีย์ที่ถูกต้อง
     * - ไม่ lock วิว
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

        $pk = $this->vStockPk();
        $v  = DB::table('v_current_stock')->where($pk, $variantId)->first();
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
     * - ตัว lock/เช็คสต๊อก ให้ไปทำใน StockService (ที่ table จริง product_color_size) เท่านั้น
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
     * ดูประวัติ/ไทม์ไลน์ของ Variant + Holds ปัจจุบัน
     * - อ่าน summary จาก v_current_stock ด้วยคีย์ที่ถูกต้อง (ไม่ lock)
     */
    public function variantHistory(Request $request, $variant)
    {
        // รับค่า scope
        $scope = $request->input('scope', 'all');
        $variantId = (int) $variant;

        // 1. ดึงข้อมูล Variant (สินค้า/สี/ไซส์)
        $variantObj = DB::table('product_color_size as pcs')
            ->join('products as p', 'p.id', '=', 'pcs.product_id')
            ->leftJoin('colors as c', 'c.id', '=', 'pcs.color_id')
            ->leftJoin('sizes  as s', 's.id', '=', 'pcs.size_id')
            ->selectRaw('pcs.id, pcs.product_id, p.name as product_name, c.name as color_name, s.size_name')
            ->where('pcs.id', $variantId)
            ->first();

        if (!$variantObj) {
            abort(404, 'ไม่พบสินค้านี้');
        }

        // 2. ดึง Summary (จาก v_current_stock)
        // ✅✅✅ แก้ไขจุดที่ Error: เปลี่ยนจาก 'id' เป็น 'variant_id' ✅✅✅
        $v = DB::table('v_current_stock')->where('variant_id', $variantId)->first();
        
        // กรณีหาไม่เจอใน View (เช่นเพิ่งสร้างสินค้า) ให้ default เป็น 0
        $summary = (object)[
            'current'   => (int)($v->current_stock ?? 0),
            'reserved'  => (int)($v->reserved_stock ?? 0),
            'available' => (int)($v->available_stock ?? 0),
        ];

        // 3. ดึงประวัติ (Stock Transactions)
        $query = DB::table('stock_transactions')
            ->where('product_color_size_id', $variantId)
            ->orderByDesc('created_at');

        // Filter ตาม Scope
        if ($scope === 'holds') {
            $query->whereIn('type', ['reserve', 'release']);
        } elseif ($scope === 'physical') {
            $query->whereIn('type', ['in', 'out']);
        } else {
            // all
            $query->whereIn('type', ['reserve', 'release', 'in', 'out']);
        }

        $rows = $query->limit(100)->get();

        // แปลงข้อมูลเพื่อแสดงผล
        $mapTH = ['reserve'=>'จอง', 'release'=>'ปล่อย', 'in'=>'เข้า', 'out'=>'ออก'];
        $history = $rows->map(function($r) use ($mapTH) {
            $delta = (int)$r->quantity;
            return (object)[
                'created_at' => $r->created_at,
                'type'       => $r->type,
                'type_th'    => $mapTH[$r->type] ?? $r->type,
                'before'     => (int)$r->quantity_before,
                'delta'      => $delta,
                'delta_str'  => ($delta >= 0 ? '+' : '') . $delta,
                'after'      => (int)$r->quantity_after,
                'reason'     => $r->reason,
                'user_name'  => $r->user_name ?? '-',
                'order_id'   => $r->order_id,
                'ref'        => $r->reference_number,
            ];
        });

        // 4. ดึงรายการ Holds (ถ้ามีตาราง)
        $holds = collect();
        if (Schema::hasTable('stock_holds')) {
            $holds = DB::table('stock_holds as sh')
                ->leftJoin('orders as o', 'o.id', '=', 'sh.order_id')
                ->where('sh.product_color_size_id', $variantId)
                ->where('sh.status', 'active')
                ->select('sh.*', 'o.status as order_status', 'o.order_number')
                ->orderByDesc('sh.updated_at')
                ->get();
        }

        // ส่งตัวแปรไป View
        return view('stock.variant-history', [
            'variant'   => $variantObj,
            'summary'   => $summary,
            'scope'     => $scope,
            'history'   => $history,
            'holds'     => $holds,
            'variantId' => $variantId
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
    private function createVariantName($colorName, $sizeName): string
    {
        $c = trim((string)($colorName ?? ''));
        $s = trim((string)($sizeName ?? ''));
        if ($c === '' && $s === '') return 'ไม่ระบุ';
        if ($c !== '' && $s === '') return $c;
        if ($c === '' && $s !== '') return $s;
        return "{$c} - {$s}";
    }

    private function resolveVariant(
        int $productId,
        ?int $colorId,
        ?int $sizeId,
        ?string $colorName,
        ?string $sizeName,
        ?string $variantName
    ): array {
        // พยายามหา ID จากชื่อ ถ้าไม่มีส่งมา
        $colorId = $colorId ?: ($colorName ? \App\Models\Color::where('name', trim($colorName))->value('id') : null);
        $sizeId  = $sizeId  ?: ($sizeName ? \App\Models\Size::where('size_name', trim($sizeName))->value('id') : null);

        // ถ้ายังไม่มี ID แต่มีชื่อรวม (เช่น "แดง - XL") ลองแตก string ดู
        if ((!$colorId || !$sizeId) && $variantName) {
            $parts = array_map('trim', explode('-', $variantName));
            if (count($parts) >= 2) {
                $colorId = $colorId ?: \App\Models\Color::where('name', $parts[0])->value('id');
                $sizeId  = $sizeId  ?: \App\Models\Size::where('size_name', $parts[1])->value('id');
            }
        }

        $variant = null;
        $hasVariants = \App\Models\ProductColorSize::where('product_id', $productId)->exists();

        if ($hasVariants && $colorId && $sizeId) {
            $variant = \App\Models\ProductColorSize::where([
                'product_id' => $productId,
                'color_id'   => $colorId,
                'size_id'    => $sizeId,
            ])->first();
        }
        return [$colorId, $sizeId, $variant, $hasVariants];
    }
}
