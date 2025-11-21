<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Exception;


class StockService
{
      public function reserveStock(int $variantId, int $qty, int $orderId, string $orderNumber = ''): void
    {
        if ($qty <= 0) return;

        DB::transaction(function () use ($variantId, $qty, $orderId) {
            // lock แถวสต๊อค & hold ที่เกี่ยวข้อง
            DB::table('product_color_size')->where('id', $variantId)->lockForUpdate()->first();

            $sum = $this->getVariantSummary($variantId, $orderId);
            if ($qty > $sum['available_stock']) {
                throw new \Exception("สต๊อคไม่พอ (คงเหลือ {$sum['available_stock']} ชิ้น)");
            }

            // อัปเซ็ต hold แถวเดียวต่อ (variant, order)
            $hold = DB::table('stock_holds')->where([
                'product_color_size_id' => $variantId,
                'order_id' => $orderId,
                'status'   => 'active',
            ])->lockForUpdate()->first();

            if ($hold) {
                DB::table('stock_holds')->where('id', $hold->id)->update([
                    'quantity'   => (int)$hold->quantity + $qty,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('stock_holds')->insert([
                    'product_color_size_id' => $variantId,
                    'order_id' => $orderId,
                    'quantity' => $qty,
                    'status'   => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
    /**
     * อ่านค่าจาก view + นับ hold ของออเดอร์นี้
     */
    public function getVariantSummary(int $variantId, int $orderId = 0): array
    {
        $row = DB::table('product_color_size')->where('id', $variantId)->lockForUpdate()->first();
        $current = (int)($row->quantity ?? 0);

        $reservedAllExceptThis = (int) DB::table('stock_holds')
            ->where('product_color_size_id', $variantId)
            ->where('status', 'active')
            ->when($orderId > 0, fn($q) => $q->where(function($qq) use ($orderId){
                $qq->whereNull('order_id')->orWhere('order_id', '<>', $orderId);
            }))
            ->sum('quantity');

        $reservedByThis = (int) DB::table('stock_holds')
            ->where('product_color_size_id', $variantId)
            ->where('status', 'active')
            ->where('order_id', $orderId)
            ->sum('quantity');

        $available = max(0, $current - $reservedAllExceptThis);

        return [
            'current_stock'       => $current,
            'reserved_stock'      => $reservedAllExceptThis,
            'reserved_by_this'    => $reservedByThis,
            'available_stock'     => $available,
            'max_total_for_order' => $available + $reservedByThis,
        ];
    }
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

        $action   = $request->input('action');
        $qty      = (int)$request->input('quantity');
        $reason   = $request->input('reason') ?: ($action === 'in' ? 'รับสินค้าเข้า (manual)' : 'ตัดสต๊อค (manual)');
        $ref      = $request->input('ref');

        try {
            if ($action === 'in') {
                $svc->increaseStock($variantId, $qty, $reason, $ref);
            } else {
                $svc->decreaseStock($variantId, $qty, $reason, $ref); // ป้องกัน available ติดลบในตัว
            }
            return redirect()->route('stock.adjust.form', $variantId)->with('success', 'ปรับสต๊อคเรียบร้อย');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
    public function adjustForm(int $variantId)
    {
        $variant = DB::table('product_color_size as pcs')
            ->join('products as p', 'p.id', '=', 'pcs.product_id')
            ->leftJoin('colors as c', 'c.id', '=', 'pcs.color_id')
            ->leftJoin('sizes  as s', 's.id', '=', 'pcs.size_id')
            ->selectRaw('pcs.id, pcs.product_id, p.name as product_name, c.name as color_name, s.size_name')
            ->where('pcs.id', $variantId)->first();
        abort_unless($variant, 404);

        $v = DB::table('v_current_stock')->where('id', $variantId)->first();
        if (!$v) abort(500, "ไม่พบ variant id={$variantId} ใน v_current_stock");

        $last10 = DB::table('stock_transactions')
            ->where('product_color_size_id', $variantId)
            ->orderByDesc('created_at')->limit(10)->get();

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
    public function variantHistory(int $variantId, Request $request)
{
    // === โหลดข้อมูลตัวแปร (variant) ===
    $variant = DB::table('product_color_size as pcs')
        ->join('products as p', 'p.id', '=', 'pcs.product_id')
        ->leftJoin('colors as c', 'c.id', '=', 'pcs.color_id')
        ->leftJoin('sizes  as s', 's.id', '=', 'pcs.size_id')
        ->selectRaw('
            pcs.id,
            pcs.product_id,
            p.name as product_name,
            c.name as color_name,
            s.size_name
        ')
        ->where('pcs.id', $variantId)
        ->first();

    if (!$variant) { abort(404); } // แทน abort_unless เพื่อไม่ให้ IDE เตือนเหลือง

    // === สรุปสต็อคจาก v_current_stock (Golden Rule) ===
    $v = DB::table('v_current_stock')->where('id', $variantId)->first();
    if (!$v) { abort(500, "ไม่พบ variant id={$variantId} ใน v_current_stock"); }

    $summary = (object)[
        'current'   => (int)$v->current_stock,
        'reserved'  => (int)$v->reserved_stock,
        'available' => (int)$v->available_stock,
    ];

    // === ตัวกรอง scope สำหรับตารางประวัติ ===
    // all (ค่าเริ่มต้น) | holds (เฉพาะ reserve/release) | physical (เฉพาะ in/out)
    $scope = $request->query('scope', 'all');

    // === โหลดประวัติจาก stock_transactions ===
    $q = DB::table('stock_transactions')
        ->where('product_color_size_id', $variantId)
        ->orderByDesc('created_at');

    if ($scope === 'holds') {
        $q->whereIn('type', ['reserve', 'release']);
    } elseif ($scope === 'physical') {
        $q->whereIn('type', ['in', 'out']);
    } else {
        $q->whereIn('type', ['reserve', 'release', 'in', 'out']);
    }

    $rows = $q->limit(100)->get();

    $mapTH = [
        'reserve' => 'จอง',
        'release' => 'ปล่อย',
        'in'      => 'เข้า',
        'out'     => 'ออก',
    ];

    $history = $rows->map(function ($r) use ($mapTH) {
        return (object)[
            'created_at'   => $r->created_at,
            'type'         => $r->type,
            'type_th'      => $mapTH[$r->type] ?? $r->type,
            'before'       => (int)$r->quantity_before,
            'delta'        => (int)$r->quantity,
            'delta_str'    => ($r->quantity >= 0 ? '+' : '') . (int)$r->quantity,
            'after'        => (int)$r->quantity_after,
            'reason'       => $r->reason,
            'user_name'    => $r->user_name ?? '-',
            'order_id'     => $r->order_id,
            'ref'          => $r->reference_number,
        ];
    });

    // === โหลดออเดอร์ที่กำลังจับ (holds ปัจจุบัน) จาก stock_holds.status='active' ===
    $holds = collect();
    if (Schema::hasTable('stock_holds')) {
        $openStatuses = ['pending', 'processing'];

        // เลือกคอลัมน์เลขออเดอร์ตามที่มีจริงในตาราง orders
        $orderNoExpr = Schema::hasColumn('orders', 'order_number') ? 'o.order_number'
                    : (Schema::hasColumn('orders', 'code')         ? 'o.code'
                    : (Schema::hasColumn('orders', 'order_no')     ? 'o.order_no' : 'o.id'));

        $holds = DB::table('stock_holds as sh')
            ->leftJoin('orders as o', 'o.id', '=', 'sh.order_id')
            ->where('sh.product_color_size_id', $variantId)
            ->where('sh.status', 'active')
            ->when(Schema::hasTable('orders'), function ($qq) use ($openStatuses) {
                $qq->whereIn('o.status', $openStatuses);
            })
            ->orderByDesc('sh.updated_at')
            ->get([
                'sh.order_id',
                'sh.quantity',
                'o.status',
                DB::raw("$orderNoExpr as order_number"),
            ]);
    }

    // === ส่งไปที่ view ===
    return view('stock.variant-history', [
        'variant' => $variant,
        'summary' => $summary,
        'scope'   => $scope,
        'history' => $history,
        'holds'   => $holds,
    ]);
}
 public function decreaseStock(int $variantId, int $quantity, string $reason = 'ตัดสต๊อค (ปรับลด)', ?string $referenceNumber = null): void
    {
        if ($quantity <= 0) return;

        DB::transaction(function () use ($variantId, $quantity, $reason, $referenceNumber) {
            $before = DB::table('product_color_size')
                ->where('id', $variantId)
                ->lockForUpdate()
                ->value('quantity');

            if ($before === null) throw new Exception("ไม่พบ variant id={$variantId}");

            $before = (int)$before;

            $reservedActive = (int) DB::table('stock_holds')
                ->where('product_color_size_id', $variantId)
                ->where('status', 'active')
                ->sum('quantity');

            $after = $before - $quantity;

            if ($after < $reservedActive) {
                $allow = max(0, $before - $reservedActive);
                throw new Exception("ตัดสต๊อคไม่ได้: จะเหลือต่ำกว่าจำนวนที่กำลังถูกจับ (ตัดได้สูงสุด {$allow})");
            }
            if ($after < 0) {
                throw new Exception("ตัดสต๊อคไม่ได้: ของจริงไม่พอ (มี {$before}, ต้องการตัด {$quantity})");
            }

            // ⬇️ ปรับตรงนี้: ใส่ updated_at เฉพาะถ้ามีคอลัมน์
            $update = ['quantity' => $after];
            if (Schema::hasColumn('product_color_size', 'updated_at')) {
                $update['updated_at'] = now();
            }

            DB::table('product_color_size')
                ->where('id', $variantId)
                ->update($update);

            DB::table('stock_transactions')->insert([
                'product_color_size_id' => $variantId,
                'order_id'              => null,
                'type'                  => 'out',
                'quantity'              => -$quantity,
                'quantity_before'       => $before,
                'quantity_after'        => $after,
                'reason'                => $reason,
                'reference_number'      => $referenceNumber,
                'user_id'               => Auth::id(),
                'user_name'             => Auth::user()->name ?? null,
                'created_at'            => now(),
            ]);
        });
    }
    public function adjustStock(
        int $variantId,
        int $delta,
        string $reason = 'ปรับสต๊อค',
        ?string $referenceNumber = null
    ): void {
        if ($delta === 0) {
            return;
        }
        if ($delta > 0) {
            $this->increaseStock($variantId, $delta, $reason, $referenceNumber);
        } else {
            $this->decreaseStock($variantId, -$delta, $reason, $referenceNumber);
        }
    }
      public function increaseStock(int $variantId, int $quantity, string $reason = 'รับสินค้าเข้า', ?string $referenceNumber = null): void
    {
        if ($quantity <= 0) return;

        DB::transaction(function () use ($variantId, $quantity, $reason, $referenceNumber) {
            $before = DB::table('product_color_size')
                ->where('id', $variantId)
                ->lockForUpdate()
                ->value('quantity');

            if ($before === null) throw new Exception("ไม่พบ variant id={$variantId}");

            $before = (int)$before;
            $after  = $before + $quantity;

            // ⬇️ ปรับตรงนี้: ใส่ updated_at เฉพาะถ้ามีคอลัมน์
            $update = ['quantity' => $after];
            if (Schema::hasColumn('product_color_size', 'updated_at')) {
                $update['updated_at'] = now();
            }

            DB::table('product_color_size')
                ->where('id', $variantId)
                ->update($update);

            DB::table('stock_transactions')->insert([
                'product_color_size_id' => $variantId,
                'order_id'              => null,
                'type'                  => 'in',
                'quantity'              => +$quantity,
                'quantity_before'       => $before,
                'quantity_after'        => $after,
                'reason'                => $reason,
                'reference_number'      => $referenceNumber,
                'user_id'               => Auth::id(),
                'user_name'             => Auth::user()->name ?? null,
                'created_at'            => now(),
            ]);
        });
    }
    /**
     * (LOW LEVEL) ปล่อย hold ทั้งหมดของ "ออเดอร์นี้ + variant นี้" (เปลี่ยนเป็น released)
     * @return int ปริมาณที่ปล่อยได้จริง
     */
    public function releaseAllForOrderVariant(int $variantId, int $orderId, string $orderNumber, string $reason = 'แก้ไขออเดอร์'): int
    {
        return DB::transaction(function () use ($variantId, $orderId, $orderNumber, $reason) {
            // ล็อค variant และ hold ของออเดอร์นี้
            $rows = DB::table('stock_holds')
                ->where('product_color_size_id', $variantId)
                ->where('order_id', $orderId)
                ->where('status', 'active')
                ->lockForUpdate()
                ->get();

            if ($rows->isEmpty()) return 0;

            // นับจำนวนที่จะปล่อย
            $releaseQty = (int) $rows->sum('quantity');

            // ทำเป็น released ทั้งหมด (ตามที่คุณต้องการ: ปล่อยของเดิมทั้งหมด)
            DB::table('stock_holds')
                ->where('product_color_size_id', $variantId)
                ->where('order_id', $orderId)
                ->where('status', 'active')
                ->update(['status' => 'released', 'updated_at' => now()]);

            // บันทึก log (type=release) โดยนับ before/after เป็น available ของระบบ
            $v = DB::table('v_current_stock')->where('id', $variantId)->lockForUpdate()->first();
            $availableBefore = (int)$v->available_stock;
            $availableAfter  = $availableBefore + $releaseQty;

            DB::table('stock_transactions')->insert([
                'product_color_size_id' => $variantId,
                'order_id'              => $orderId,
                'type'                  => 'release',
                'quantity'              => +$releaseQty,
                'quantity_before'       => $availableBefore,
                'quantity_after'        => $availableAfter,
                'reason'                => "{$reason} (ออเดอร์ {$orderNumber})",
                'user_id'               => Auth::id(),
                'user_name'             => Auth::user()->name ?? null,
                'reference_number'      => $orderNumber,
                'created_at'            => now(),
            ]);

            return $releaseQty;
        });
    }

    /**
     * (LOW LEVEL) จอง hold ใหม่ (insert แถวเดียว) ตามจำนวนที่ต้องการ
     */
    public function reserveNewForOrderVariant(int $variantId, int $orderId, int $quantity, string $orderNumber, string $reason = 'แก้ไขออเดอร์'): void
    {
        if ($quantity <= 0) return;

        DB::transaction(function () use ($variantId, $orderId, $quantity, $orderNumber, $reason) {
            // ตรวจโควต้าจาก view + hold ของออเดอร์นี้
            $sum = $this->getVariantSummary($variantId, $orderId);
            if ($quantity > $sum['max_total_for_order']) {
                throw new \Exception("ตั้งจำนวนเกินโควต้าที่อนุญาต (สูงสุด {$sum['max_total_for_order']})");
            }

            // insert hold แถวใหม่ (status=active)
            DB::table('stock_holds')->insert([
                'product_color_size_id' => $variantId,
                'order_id'              => $orderId,
                'quantity'              => $quantity,
                'status'                => 'active',
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            // log reserve
            $v = DB::table('v_current_stock')->where('id', $variantId)->lockForUpdate()->first();
            $availableBefore = (int)$v->available_stock;
            $availableAfter  = $availableBefore - $quantity;

            DB::table('stock_transactions')->insert([
                'product_color_size_id' => $variantId,
                'order_id'              => $orderId,
                'type'                  => 'reserve',
                'quantity'              => -$quantity,
                'quantity_before'       => $availableBefore,
                'quantity_after'        => $availableAfter,
                'reason'                => "{$reason} (ออเดอร์ {$orderNumber})",
                'user_id'               => Auth::id(),
                'user_name'             => Auth::user()->name ?? null,
                'reference_number'      => $orderNumber,
                'created_at'            => now(),
            ]);
        });
    }

    /**
     * SET แบบที่คุณกำหนด: ปล่อยของเดิมทั้งหมด แล้วจองใหม่ตามจำนวน desiredQty
     * (บันทึก 2 ทรานแซกชัน: release + reserve)
     */
     public function setHoldByReleaseThenReserve(int $variantId, int $orderId, int $desiredQty, string $orderNumber = ''): void
    {
        DB::transaction(function () use ($variantId, $orderId, $desiredQty) {
            // ปล่อยของเดิมทั้งหมดของออเดอร์นี้ก่อน
            DB::table('stock_holds')
                ->where('product_color_size_id', $variantId)
                ->where('order_id', $orderId)
                ->where('status', 'active')
                ->update([
                    'status' => 'released',
                    'updated_at' => now(),
                ]);

            if ($desiredQty <= 0) {
                return;
            }

            // แล้วจองใหม่ตาม desiredQty
            $this->reserveStock($variantId, $desiredQty, $orderId);
        });
    }

    /**
     * ยกเลิกออเดอร์: ปล่อย hold ของออเดอร์นี้ทุก variant
     */
   public function cancelOrderReleaseAll(int $orderId, string $orderNumber = ''): void
    {
        DB::transaction(function () use ($orderId) {
            DB::table('stock_holds')
                ->where('order_id', $orderId)
                ->where('status', 'active')
                ->update([
                    'status' => 'released',
                    'updated_at' => now(),
                ]);
        });
    }

    /**
     * จัดส่ง (ตัดสต๊อกจริง): mark hold เป็น consumed และลด current_stock
     */
    public function shipConsumeAll(int $orderId, string $orderNumber = ''): void
    {
        DB::transaction(function () use ($orderId) {

            $holds = DB::table('stock_holds')
                ->where('order_id', $orderId)
                ->where('status', 'active')
                ->lockForUpdate()
                ->get();

            foreach ($holds as $h) {
                $pcs = DB::table('product_color_size')->where('id', $h->product_color_size_id)->lockForUpdate()->first();
                if (!$pcs) continue;

                $newQty = (int)$pcs->quantity - (int)$h->quantity;
                if ($newQty < 0) {
                    throw new \Exception("ตัดสต๊อคติดลบ (variant {$h->product_color_size_id})");
                }

                DB::table('product_color_size')->where('id', $h->product_color_size_id)->update([
                    'quantity'   => $newQty,
                    'updated_at' => now(),
                ]);

                DB::table('stock_holds')->where('id', $h->id)->update([
                    'status'     => 'consumed',
                    'updated_at' => now(),
                ]);
            }
        });
    }
}
