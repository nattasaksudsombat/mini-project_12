<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Exception;

class StockService
{
    private function vStockPk(): string
    {
        foreach (['variant_id', 'product_color_size_id', 'id'] as $col) {
            if (Schema::hasColumn('v_current_stock', $col)) {
                return $col;
            }
        }
        return 'id'; 
    }

    public function reserveStock(int $variantId, int $qty, int $orderId, string $orderNumber = ''): void
    {
        if ($qty <= 0) return;

        DB::transaction(function () use ($variantId, $qty, $orderId) {
            DB::table('product_color_size')->where('id', $variantId)->lockForUpdate()->first();

            $sum = $this->getVariantSummary($variantId, $orderId);
            if ($qty > $sum['available_stock']) {
                throw new \Exception("สต๊อคไม่พอ (คงเหลือ {$sum['available_stock']} ชิ้น)");
            }

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

    // decreaseStock: ตัดสต็อก (สินค้าออก)
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

            // ✅ แก้ไข: เอา updated_at ออก ถ้าไม่มี column นี้
            $updateData = ['quantity' => $after];
            if (Schema::hasColumn('product_color_size', 'updated_at')) {
                $updateData['updated_at'] = now();
            }
            
            DB::table('product_color_size')->where('id', $variantId)->update($updateData);

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
                'user_name'             => Auth::user()->username ?? null,
                'created_at'            => now(),
            ]);
        });
    }

    public function adjustStock(int $variantId, int $delta, string $reason = 'ปรับสต๊อค', ?string $referenceNumber = null): void {
        if ($delta === 0) return;
        if ($delta > 0) {
            $this->increaseStock($variantId, $delta, $reason, $referenceNumber);
        } else {
            $this->decreaseStock($variantId, -$delta, $reason, $referenceNumber);
        }
    }

    // increaseStock: เพิ่มสต็อก (สินค้าเข้า)
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

            // ✅ แก้ไข: เช็คก่อนว่ามี updated_at ไหม
            $updateData = ['quantity' => $after];
            if (Schema::hasColumn('product_color_size', 'updated_at')) {
                $updateData['updated_at'] = now();
            }

            DB::table('product_color_size')->where('id', $variantId)->update($updateData);

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
                'user_name'             => Auth::user()->username ?? null,
                'created_at'            => now(),
            ]);
        });
    }

    // releaseAllForOrderVariant: ปล่อยจอง (Release)
    public function releaseAllForOrderVariant(int $variantId, int $orderId, string $orderNumber, string $reason = 'แก้ไขออเดอร์'): int
    {
        return DB::transaction(function () use ($variantId, $orderId, $orderNumber, $reason) {
            $rows = DB::table('stock_holds')
                ->where('product_color_size_id', $variantId)
                ->where('order_id', $orderId)
                ->where('status', 'active')
                ->lockForUpdate()
                ->get();

            if ($rows->isEmpty()) return 0;

            $releaseQty = (int) $rows->sum('quantity');

            DB::table('stock_holds')
                ->where('product_color_size_id', $variantId)
                ->where('order_id', $orderId)
                ->where('status', 'active')
                ->update(['status' => 'released', 'updated_at' => now()]);

            $pk = $this->vStockPk();
            $v = DB::table('v_current_stock')->where($pk, $variantId)->lockForUpdate()->first();
            $availableBefore = (int)($v->available_stock ?? 0);
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
                'user_name'             => Auth::user()->username ?? null,
                'reference_number'      => $orderNumber,
                'created_at'            => now(),
            ]);

            return $releaseQty;
        });
    }

    // reserveNewForOrderVariant: จองเพิ่ม (Reserve)
    public function reserveNewForOrderVariant(int $variantId, int $orderId, int $quantity, string $orderNumber, string $reason = 'แก้ไขออเดอร์'): void
    {
        if ($quantity <= 0) return;

        DB::transaction(function () use ($variantId, $orderId, $quantity, $orderNumber, $reason) {
            $sum = $this->getVariantSummary($variantId, $orderId);
            if ($quantity > $sum['max_total_for_order']) {
                throw new \Exception("ตั้งจำนวนเกินโควต้าที่อนุญาต (สูงสุด {$sum['max_total_for_order']})");
            }

            DB::table('stock_holds')->insert([
                'product_color_size_id' => $variantId,
                'order_id'              => $orderId,
                'quantity'              => $quantity,
                'status'                => 'active',
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            $pk = $this->vStockPk();
            $v = DB::table('v_current_stock')->where($pk, $variantId)->lockForUpdate()->first();
            $availableBefore = (int)($v->available_stock ?? 0);
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
                'user_name'             => Auth::user()->username ?? null,
                'reference_number'      => $orderNumber,
                'created_at'            => now(),
            ]);
        });
    }

    public function setHoldByReleaseThenReserve(int $variantId, int $orderId, int $desiredQty, string $orderNumber = ''): void {
        DB::transaction(function () use ($variantId, $orderId, $desiredQty) {
            DB::table('stock_holds')->where('product_color_size_id', $variantId)->where('order_id', $orderId)->where('status', 'active')
                ->update(['status' => 'released', 'updated_at' => now()]);
            if ($desiredQty > 0) $this->reserveStock($variantId, $desiredQty, $orderId);
        });
    }

    public function cancelOrderReleaseAll(int $orderId, string $orderNumber = ''): void {
        DB::transaction(function () use ($orderId) {
            DB::table('stock_holds')->where('order_id', $orderId)->where('status', 'active')
                ->update(['status' => 'released', 'updated_at' => now()]);
        });
    }

    public function shipConsumeAll(int $orderId, string $orderNumber = ''): void {
        DB::transaction(function () use ($orderId) {
            $holds = DB::table('stock_holds')->where('order_id', $orderId)->where('status', 'active')->lockForUpdate()->get();
            foreach ($holds as $h) {
                $pcs = DB::table('product_color_size')->where('id', $h->product_color_size_id)->lockForUpdate()->first();
                if (!$pcs) continue;
                $newQty = (int)$pcs->quantity - (int)$h->quantity;
                if ($newQty < 0) throw new \Exception("ตัดสต๊อคติดลบ (variant {$h->product_color_size_id})");
                
                // ✅ แก้ไข: เช็ค updated_at ก่อนอัปเดต
                $updateData = ['quantity' => $newQty];
                if (Schema::hasColumn('product_color_size', 'updated_at')) {
                    $updateData['updated_at'] = now();
                }
                DB::table('product_color_size')->where('id', $h->product_color_size_id)->update($updateData);
                
                DB::table('stock_holds')->where('id', $h->id)->update(['status' => 'consumed', 'updated_at' => now()]);
            }
        });
    }
}