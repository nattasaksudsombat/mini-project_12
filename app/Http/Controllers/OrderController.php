<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Size;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ProductColorSize;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class OrderController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /* =========================
       รายการออเดอร์
    ========================== */
    public function index(Request $request)
    {
        $q = Order::with('customer');

        if ($search = $request->search) {
            $q->where(function($qq) use ($search) {
                $qq->where('order_number', 'like', "%{$search}%")
                   ->orWhereHas('customer', fn($c)=>$c->where('name','like',"%{$search}%"));
            });
        }
        if ($request->status)         $q->where('status', $request->status);
        if ($request->payment_status) $q->where('payment_status', $request->payment_status);

        if ($request->start_date && $request->end_date) {
            $q->whereBetween('created_at', [
                $request->start_date.' 00:00:00',
                $request->end_date.' 23:59:59',
            ]);
        }

        $orders = $q->latest()->paginate(20);
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('is_active', 1)->get();
        return view('orders.create', compact('products'));
    }

    /* =========================
       Utilities
    ========================== */
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
        $colorId = $colorId ?: ($colorName ? Color::where('name', trim($colorName))->value('id') : null);
        $sizeId  = $sizeId  ?: ($sizeName ? Size::where('size_name', trim($sizeName))->value('id') : null);

        if ((!$colorId || !$sizeId) && $variantName) {
            $parts = array_map('trim', explode('-', $variantName));
            if (count($parts) >= 2) {
                $colorId = $colorId ?: Color::where('name', $parts[0])->value('id');
                $sizeId  = $sizeId  ?: Size::where('size_name', $parts[1])->value('id');
            }
        }

        $variant = null;
        $hasVariants = ProductColorSize::where('product_id', $productId)->exists();

        if ($hasVariants && $colorId && $sizeId) {
            $variant = ProductColorSize::where([
                'product_id' => $productId,
                'color_id'   => $colorId,
                'size_id'    => $sizeId,
            ])->first();
        }
        return [$colorId, $sizeId, $variant, $hasVariants];
    }

    /* =========================
       สร้างออเดอร์ + จองสต๊อก
    ========================== */
   /* =========================
       สร้างออเดอร์ + จองสต๊อก
    ========================== */
    public function store(Request $request)
    {
        $request->validate([
            'customer.name'             => 'required|string|max:255',
            'customer.address'          => 'required|string',
            'customer.purchase_channel' => 'required|string',
            'customer.payment_method'   => 'required|string',
            'items_json'                => 'required|json',
            'shipping_fee'              => 'required|numeric',
        ]);

        return DB::transaction(function () use ($request) {
            $items = json_decode($request->items_json, true) ?: [];
            if (empty($items)) {
                throw new \Exception('ต้องเพิ่มสินค้าอย่างน้อย 1 รายการ');
            }

            $subtotal    = collect($items)->sum(fn($i) => (float)$i['unit_price'] * (int)$i['quantity']);
            $discount    = (float)($request->discount ?? 0);
            $shippingFee = (float)$request->shipping_fee;
            $totalPrice  = $subtotal + $shippingFee - $discount;

            $customer = Customer::create([
                'name'             => $request->input('customer.name'),
                'address'          => $request->input('customer.address'),
                'purchase_channel' => $request->input('customer.purchase_channel'),
                'payment_method'   => $request->input('customer.payment_method'),
            ]);

            $nextId      = (int) (Order::max('id') ?? 0) + 1;
            // ✅ แก้ไข: เพิ่มการตรวจสอบการสร้าง Order Number ให้เหมือนใน Model
            $latestOrder = Order::latest('id')->first();
            $number = $latestOrder ? ((int) str_replace('ORD', '', $latestOrder->order_number)) + 1 : 1;
            $orderNumber = 'ORD' . str_pad($number, 4, '0', STR_PAD_LEFT);
            // $orderNumber = 'ORD' . str_pad($nextId, 5, '0', STR_PAD_LEFT); // (ใช้ Logic จาก Model ดีกว่า)


            $order = Order::create([
                'customer_id'       => $customer->id,
                'shipping_address'  => $customer->address,
                'subtotal'          => $subtotal,
                'shipping_fee'      => $shippingFee,
                'discount'          => $discount,
                'total_price'       => $totalPrice, // total_price ใน DB คือยอดรวม (อาจสับสนกับ total_amount)
                'total_amount'      => $totalPrice, // total_amount คือยอดสุทธิ (จากโค้ดเดิมของคุณ)
                'notes'             => $request->notes,
                'status'            => 'pending',
                'payment_status'    => 'pending',
                'order_number'      => $orderNumber,
                'stock_reserved_at' => now(),
            ]);

            foreach ($items as $item) {
                $productId   = (int)$item['product_id'];
                $productName = $item['product_name'] ?? ($item['name'] ?? '');
                $qty         = (int)$item['quantity'];
                $unitPrice   = (float)$item['unit_price'];

                [$colorId, $sizeId, $variant, $hasVariants] = $this->resolveVariant(
                    $productId,
                    $item['color_id']     ?? null,
                    $item['size_id']      ?? null,
                    $item['color_name']   ?? null,
                    $item['size_name']    ?? null,
                    $item['variant_name'] ?? null
                );

                if ($hasVariants) {
                    if (!$variant) throw new \Exception("กรุณาเลือกสี/ไซส์ให้ครบถ้วนสำหรับสินค้า: {$productName}");
                    
                    // ✅ ==================  จุดที่แก้ไข ================== ✅
                    // เปลี่ยนชื่อเมธอดจาก reserveStock -> reserveNewForOrderVariant
                    // และสลับลำดับตัวแปร $order->id และ $qty
                    $this->stockService->reserveNewForOrderVariant(
                        $variant->id,    // 1. variantId
                        $order->id,      // 2. orderId
                        $qty,            // 3. quantity
                        $orderNumber,    // 4. orderNumber
                        'สร้างออเดอร์ใหม่' // 5. reason
                    );
                    // ✅ =================================================== ✅
                }

                $colorName   = $colorId ? (Color::find($colorId)->name ?? null) : ($item['color_name'] ?? null);
                $sizeName    = $sizeId  ? (Size::find($sizeId)->size_name ?? null) : ($item['size_name'] ?? null);
                $variantName = $this->createVariantName($colorName, $sizeName);

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $productId,
                    'product_name' => $productName,
                    'color_id'     => $colorId,
                    'size_id'      => $sizeId,
                    'variant_name' => $variantName,
                    'quantity'     => $qty,
                    'unit_price'   => $unitPrice,
                    'total_price'  => $qty * $unitPrice,
                ]);
            }

            return redirect()->route('orders.index')->with('success', 'สร้างออเดอร์และจองสต๊อคเรียบร้อยแล้ว');
        });
    }
  public function reserveStock(
        int $productColorSizeId,
        int $quantity,
        int $orderId,
        string $orderNumber,
        ?string $expiresAt = null
    ): void {
        if ($quantity <= 0) {
            throw new \Exception('จำนวนที่จองต้องมากกว่า 0');
        }

        DB::transaction(function () use ($productColorSizeId, $quantity, $orderId, $orderNumber, $expiresAt) {

            // 1) ล็อกแถว variant
            $pcs = DB::table('product_color_size')
                ->where('id', $productColorSizeId)
                ->lockForUpdate()
                ->first();

            if (!$pcs) {
                throw new \Exception('ไม่พบสินค้า Variant ที่จะจองสต๊อก');
            }

            // 2) คำนวณสต๊อกที่ถูกจองอยู่ (active)
            $reservedActive = (int) DB::table('stock_holds')
                ->where('product_color_size_id', $productColorSizeId)
                ->where('status', 'active')
                ->sum('quantity');

            $currentStock = (int) $pcs->quantity;
            $available    = max(0, $currentStock - $reservedActive);

            if ($quantity > $available) {
                throw new \Exception(
                    "สต๊อกไม่พอ จองได้สูงสุด {$available} ชิ้น (คงคลัง {$currentStock}, กำลังถูกจับ {$reservedActive})"
                );
            }

            // 3) ถ้ามี hold เดิมของออเดอร์นี้อยู่แล้ว -> บวกเพิ่ม
            $existing = DB::table('stock_holds')
                ->where('product_color_size_id', $productColorSizeId)
                ->where('order_id', $orderId)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if ($existing) {
                DB::table('stock_holds')
                    ->where('id', $existing->id)
                    ->update([
                        'quantity'   => (int)$existing->quantity + $quantity,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('stock_holds')->insert([
                    'product_color_size_id' => $productColorSizeId,
                    'order_id'   => $orderId,
                    'quantity'   => $quantity,
                    'status'     => 'active',
                    'expires_at' => $expiresAt,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // ไม่หัก product_color_size.quantity ตอนนี้
            // จะไปหักจริงในเมธอด shipConsumeAll() ตามที่คุณใช้อยู่แล้ว
        });
    }

    /* ------------------------------------------------------
     * หมายเหตุ:
     * จาก Controller ของคุณ ยังเรียกใช้เมธอดเหล่านี้ด้วย:
     *   - getVariantSummary($variantId, $orderId)
     *   - setHoldByReleaseThenReserve($variantId, $orderId, $desiredQty, $orderNumber)
     *   - cancelOrderReleaseAll($orderId, $orderNumber)
     *   - shipConsumeAll($orderId, $orderNumber)
     * ต้องมีเมธอดเหล่านี้อยู่ในคลาสนี้แล้ว (ตามโปรเจ็กต์เดิม)
     * ถ้ายังไม่มี แจ้งมาได้ เดี๋ยวผมเติมให้ครบชุด
     * ---------------------------------------------------- */

    /* =========================
       แสดง / แก้ไข
    ========================== */
    public function show($id)
    {
        $order = Order::with(['customer','items.product','items.color','items.size'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    public function edit($id)
    {
        $order = DB::table('orders')->find($id);
        abort_unless($order, 404);

        $items = DB::table('order_items as oi')
            ->join('products as p', 'p.id', '=', 'oi.product_id')
            ->leftJoin('product_color_size as pcs', function ($j) {
                $j->on('pcs.product_id','=','oi.product_id')
                  ->on('pcs.color_id','=','oi.color_id')
                  ->on('pcs.size_id','=','oi.size_id');
            })
            ->leftJoin('colors as c', 'c.id','=','oi.color_id')
            ->leftJoin('sizes  as s', 's.id','=','oi.size_id')
            ->selectRaw("
                oi.id, oi.product_id, oi.color_id, oi.size_id,
                COALESCE(pcs.id,0) as variant_id,
                oi.quantity,
                COALESCE(oi.unit_price, oi.total_price/NULLIF(oi.quantity,0), 0) as price,
                p.name as product_name,
                COALESCE(c.name,'-') as color_name,
                COALESCE(s.size_name,'-') as size_name
            ")
            ->where('oi.order_id', $id)
            ->get();

        $svc = $this->stockService;

        $items = $items->map(function ($row) use ($svc, $id) {
            $variantId = (int)$row->variant_id;
            $sum = ['current_stock'=>0,'reserved_stock'=>0,'available_stock'=>0,'reserved_by_this'=>0,'max_total_for_order'=>0];
            if ($variantId > 0) $sum = $svc->getVariantSummary($variantId, (int)$id);

            $row->current_stock       = $sum['current_stock'];
            $row->reserved_stock      = $sum['reserved_stock'];
            $row->available_stock     = $sum['available_stock'];
            $row->reserved_by_this    = $sum['reserved_by_this'];
            $row->max_total_for_order = $sum['max_total_for_order'];
            $row->quota_for_edit      = $sum['available_stock'] + $sum['reserved_by_this'];
            $row->variant_name        = trim(($row->color_name ?: '') . ' - ' . ($row->size_name ?: ''), ' -');
            return $row;
        });

        $subtotal = $items->reduce(fn($c,$r)=> $c + $r->quantity*$r->price, 0);

        return view('orders.edit', [
            'order'    => $order,
            'items'    => $items,
            'subtotal' => $subtotal,
        ]);
    }

    /* =========================
       อัปเดตออเดอร์ (ปล่อยเดิม → จองใหม่)
    ========================== */
    public function update(Request $request, $id)
{
    // อ่าน order + lock กันแข่ง
    $order = Order::with('customer')->lockForUpdate()->findOrFail($id);

    // รับ payload จากฟอร์ม (items_json จากหน้าที่คุณรวมไว้)
    $payload = json_decode($request->input('items_json', '[]'), true);
    if (!is_array($payload)) {
        $payload = [];
    }

    // optional: อนุญาตแก้ข้อมูลลูกค้า/สถานะ/ค่าส่ง ตามฟอร์มหน้า edit
    $customerInput = (array) $request->input('customer', []);
    $status        = $request->input('status', $order->status);
    $paymentStatus = $request->input('payment_status', $order->payment_status);
    $shippingFee   = (float) ($request->input('shipping_fee', $order->shipping_fee ?? 0));
    $discount      = (float) ($request->input('discount', 0));

    // เตรียม service + เบอร์ออเดอร์
    /** @var \App\Services\StockService $svc */
    $svc          = app(StockService::class);
    $orderNumber  = $order->order_number ?? ('ORD' . str_pad($order->id, 5, '0', STR_PAD_LEFT));

    DB::beginTransaction();
    try {

        /* -------------------- อัปเดตข้อมูลลูกค้า/คำสั่งซื้อพื้นฐาน -------------------- */
        if ($order->customer_id && !empty($customerInput)) {
            $order->customer->update([
                'name'             => $customerInput['name'] ?? $order->customer->name,
                'phone'            => $customerInput['phone'] ?? $order->customer->phone,
                'address'          => $customerInput['address'] ?? $order->customer->address,
                'purchase_channel' => $this->normalizePurchaseChannel($customerInput['purchase_channel'] ?? $order->customer->purchase_channel),
                'payment_method'   => $this->normalizePaymentMethod($customerInput['payment_method'] ?? $order->customer->payment_method),
            ]);

            // sync ไปที่ order ด้วย
            $order->shipping_address = $order->customer->address;
        }

        $order->status         = $status;
        $order->payment_status = $paymentStatus;

        /* -------------------- ดึงรายการเดิมในออเดอร์ -------------------- */
        $existing = OrderItem::where('order_id', $order->id)->get();

        // map เดิมตามคีย์ product/color/size เพื่อเช็คว่ามี/ไม่มี
        $makeKey = fn($pid, $cid, $sid) => $pid . ':' . (int)($cid ?? 0) . ':' . (int)($sid ?? 0);

        $existingByKey = [];
        foreach ($existing as $e) {
            $existingByKey[$makeKey($e->product_id, $e->color_id, $e->size_id)] = $e;
        }

        $seenKeys   = [];
        $newSubtotal = 0.0;

        /* -------------------- วน payload ใหม่ (สร้าง/อัปเดต/จองสต๊อก) -------------------- */
        foreach ($payload as $row) {
            $productId   = (int) ($row['product_id'] ?? 0);
            $qty         = max(0, (int) ($row['quantity'] ?? 0));
            $unitPrice   = (float) ($row['unit_price'] ?? $row['price'] ?? 0);
            $productName = trim((string)($row['product_name'] ?? $row['name'] ?? ''));

            // resolve variant
            [$colorId, $sizeId, $variant, $hasVariants] = $this->resolveVariant(
                $productId,
                $row['color_id']     ?? null,
                $row['size_id']      ?? null,
                $row['color_name']   ?? null,
                $row['size_name']    ?? null,
                $row['variant_name'] ?? null
            );

            if ($hasVariants && !$variant) {
                throw new \Exception("กรุณาเลือกสี/ไซส์ให้ครบถ้วนสำหรับสินค้า: {$productName}");
            }

            $key = $makeKey($productId, $colorId, $sizeId);
            $seenKeys[$key] = true;

            // ถ้ามีอยู่แล้ว → อัปเดตจำนวน + re-hold ผ่าน service
            if (isset($existingByKey[$key])) {
                $item = $existingByKey[$key];

                if ($hasVariants) {
                    // โหมด “ปล่อยทั้งหมดเดิม แล้วจองใหม่ตาม desired”
                    $svc->setHoldByReleaseThenReserve($variant->id, (int)$order->id, $qty, $orderNumber);
                }

                $item->update([
                    'quantity'    => $qty,
                    'unit_price'  => $unitPrice,
                    'total_price' => $qty * $unitPrice,
                ]);
            }
            // ถ้าเป็นของใหม่ → สร้างแถวใหม่ + จอง hold
            else {
                $colorName   = $colorId ? (Color::find($colorId)->name ?? '') : ($row['color_name'] ?? '');
                $sizeName    = $sizeId  ? (Size::find($sizeId)->size_name ?? '') : ($row['size_name'] ?? '');
                $variantName = $this->createVariantName($colorName, $sizeName);

                if ($hasVariants) {
                    $svc->setHoldByReleaseThenReserve($variant->id, (int)$order->id, $qty, $orderNumber);
                }

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $productId,
                    'product_name' => $productName ?: (Product::find($productId)->name ?? 'ไม่ระบุ'),
                    'color_id'     => $colorId,
                    'size_id'      => $sizeId,
                    'variant_name' => $variantName,
                    'quantity'     => $qty,
                    'unit_price'   => $unitPrice,
                    'total_price'  => $qty * $unitPrice,
                ]);
            }

            $newSubtotal += ($qty * $unitPrice);
        }

        /* -------------------- ของที่ถูกลบออกจาก payload → ปล่อย hold + ลบแถว -------------------- */
        foreach ($existing as $e) {
            $key = $makeKey($e->product_id, $e->color_id, $e->size_id);
            if (!isset($seenKeys[$key])) {
                // หา variant เพื่อปล่อย hold
                if ($e->color_id && $e->size_id) {
                    $pcs = ProductColorSize::where('product_id', $e->product_id)
                        ->where('color_id', $e->color_id)
                        ->where('size_id', $e->size_id)
                        ->first();

                    if ($pcs) {
                        $svc->setHoldByReleaseThenReserve($pcs->id, (int)$order->id, 0, $orderNumber);
                    }
                }
                $e->delete();
            }
        }

        /* -------------------- อัปเดตยอดรวม/ค่าส่ง/ส่วนลด -------------------- */
        $grand = $newSubtotal + $shippingFee - $discount;

        $order->subtotal     = $newSubtotal;
        $order->shipping_fee = $shippingFee;
        $order->discount     = $discount;      // ถ้ามีคอลัมน์ชื่อ discount_amount ให้เปลี่ยนชื่อฟิลด์ตรงนี้
        $order->total_price  = $grand;
        $order->total_amount = $grand;
        $order->updated_at   = now();
        $order->save();

        DB::commit();

        return redirect()
            ->route('orders.edit', $order->id)
            ->with('success', 'บันทึกการแก้ไขเรียบร้อย (เพิ่ม/ลด/ลบสินค้า และจัดการสต๊อกครบถ้วน)');
    } catch (\Throwable $e) {
        DB::rollBack();
        report($e);
        return back()->with('error', $e->getMessage());
    }
}


// ===== Normalizers สำหรับ ENUM =====
private function normalizePurchaseChannel($value): string
{
    $value = trim((string)$value);

    // mapping ค่าที่มากับฟอร์ม -> ค่า enum ที่บันทึกใน DB
    $map = [
        'facebook' => 'facebook',
        'เฟซบุ๊ก'   => 'facebook',
        'fb'       => 'facebook',

        'line'     => 'line',
        'ไลน์'      => 'line',

        'website'  => 'website',
        'เว็บ'       => 'website',
        'เว็บไซต์'   => 'website',

        'shopee'   => 'shopee',
        'lazada'   => 'lazada',

        'offline'  => 'offline',
        'หน้าร้าน'   => 'offline',
    ];

    $lower = mb_strtolower($value, 'UTF-8');
    foreach ($map as $k => $v) {
        if (mb_strtolower($k, 'UTF-8') === $lower) return $v;
    }
    // ค่า fallback
    return 'facebook';
}

private function normalizePaymentMethod($value): string
{
    $value = trim((string)$value);

    $map = [
        'bank_transfer'    => 'bank_transfer',
        'โอน'               => 'bank_transfer',
        'พร้อมเพย์'         => 'bank_transfer',
        'โอน/พร้อมเพย์'      => 'bank_transfer',

        'cash_on_delivery' => 'cash_on_delivery',
        'ชำระปลายทาง'        => 'cash_on_delivery',
        'ชำระปลายทาง (cod)' => 'cash_on_delivery',
        'cod'               => 'cash_on_delivery',

        'credit_card'      => 'credit_card',
        'บัตรเครดิต'         => 'credit_card',
        'บัตรเครดิต/เดบิต'   => 'credit_card',
        'เครดิต'             => 'credit_card',

        'e_wallet'         => 'e_wallet',
        'วอลเล็ต'            => 'e_wallet',
        'wallet'            => 'e_wallet',
    ];

    $lower = mb_strtolower($value, 'UTF-8');
    foreach ($map as $k => $v) {
        if (mb_strtolower($k, 'UTF-8') === $lower) return $v;
    }
    return 'bank_transfer';
}
    /* =========================
       ยกเลิก/จัดส่ง (จุดเปลี่ยนสต๊อก)
    ========================== */
    public function cancel($id, StockService $svc)
    {
        return DB::transaction(function () use ($id, $svc) {
            $order = DB::table('orders')->where('id',$id)->lockForUpdate()->first();
            abort_unless($order, 404);

            $orderNumber = $order->order_number ?? ('ORD'.str_pad($id,5,'0',STR_PAD_LEFT));
            $svc->cancelOrderReleaseAll((int)$id, $orderNumber);

            $updates = ['status' => 'cancelled'];
            if (Schema::hasColumn('orders','updated_at')) $updates['updated_at'] = now();
            DB::table('orders')->where('id', $id)->update($updates);

            return back()->with('success', 'ยกเลิกออเดอร์และปล่อยสต๊อคเรียบร้อย');
        });
    }

    public function ship($id, StockService $svc)
    {
        return DB::transaction(function () use ($id, $svc) {
            $order = DB::table('orders')->where('id',$id)->lockForUpdate()->first();
            abort_unless($order, 404);

            // (ถ้าจะบังคับ paid ก่อนส่ง ให้เช็ก $order->payment_status === 'paid')
            $orderNumber = $order->order_number ?? ('ORD'.str_pad($id,5,'0',STR_PAD_LEFT));
            $svc->shipConsumeAll((int)$id, $orderNumber);

            $updates = ['status' => 'shipped'];
            if (Schema::hasColumn('orders','updated_at')) $updates['updated_at'] = now();
            DB::table('orders')->where('id', $id)->update($updates);

            return back()->with('success', 'ตัดสต๊อกและปรับสถานะจัดส่งเรียบร้อย');
        });
    }

    /* =========================
       เปลี่ยนสถานะรวดเร็ว (ไม่ยุ่งสต๊อก)
       * ถ้าจะยกเลิก/จัดส่ง ให้เรียก cancel()/ship()
    ========================== */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $order = DB::table('orders')->where('id',$id)->lockForUpdate()->first();
            abort_unless($order, 404);

            $to = $request->status;
            // กันผู้ใช้เปลี่ยนมาทางนี้สำหรับ shipped/cancelled (ซึ่งต้องปรับสต๊อก)
            if (in_array($to, ['shipped','cancelled'], true)) {
                return back()->withErrors(['error'=>'การเปลี่ยนเป็น "จัดส่งแล้ว" หรือ "ยกเลิก" ต้องทำผ่านปุ่มเฉพาะ เพื่อปรับสต๊อกถูกต้อง']);
            }

            $updates = ['status'=>$to];
            if (Schema::hasColumn('orders','updated_at')) $updates['updated_at'] = now();
            DB::table('orders')->where('id',$id)->update($updates);

            return back()->with('success','อัปเดตสถานะเรียบร้อย');
        });
    }

    /* =========================
       ลบออเดอร์ / ลบรายการ
       - ถ้า pending/processing: ปล่อย hold แล้วลบ
       - ถ้า shipped/delivered: ไม่อนุญาต (เพราะตัดของแล้ว)
    ========================== */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $order = Order::with('items')->lockForUpdate()->findOrFail($id);

            if (in_array($order->status, ['shipped','delivered'], true)) {
                return redirect()->route('orders.index')
                    ->withErrors(['error'=>'ลบไม่ได้: ออเดอร์ถูกตัดสต๊อกแล้ว (shipped/delivered)']);
            }

            // ปล่อย hold ทั้งออเดอร์
            $orderNumber = $order->order_number ?? ('ORD'.str_pad($order->id,5,'0',STR_PAD_LEFT));
            $this->stockService->cancelOrderReleaseAll((int)$order->id, $orderNumber);

            // ลบรายการ (ไม่แตะ on-hand)
            foreach ($order->items as $item) {
                $item->delete();
            }
            $order->delete();

            return redirect()->route('orders.index')->with('success','ลบคำสั่งซื้อและปล่อยสต๊อกเรียบร้อยแล้ว');
        });
    }

    public function destroyItem($id)
    {
        return DB::transaction(function () use ($id) {
            $item  = OrderItem::lockForUpdate()->findOrFail($id);
            $order = Order::lockForUpdate()->findOrFail($item->order_id);

            if (in_array($order->status, ['shipped','delivered'], true)) {
                return back()->withErrors(['error'=>'ลบรายการไม่ได้: ออเดอร์ถูกตัดสต๊อกแล้ว (shipped/delivered)']);
            }

            // หา variant id เพื่อปล่อย hold ของแถวนี้
            $variantId = (int) ProductColorSize::where('product_id',$item->product_id)
                ->where('color_id',$item->color_id)
                ->where('size_id',$item->size_id)
                ->value('id');

            if ($variantId) {
                $orderNumber = $order->order_number ?? ('ORD'.str_pad($order->id,5,'0',STR_PAD_LEFT));
                // set hold เป็น 0 สำหรับแถวนี้ = ปล่อย
                $this->stockService->setHoldByReleaseThenReserve($variantId, (int)$order->id, 0, $orderNumber);
            }

            $item->delete();

            // อัปเดตยอดรวมออเดอร์
            $items = DB::table('order_items')->where('order_id',$order->id)->get(['quantity','unit_price']);
            $subtotal = 0.0;
            foreach ($items as $r) { $subtotal += (float)$r->unit_price * (int)$r->quantity; }
            $shipping = (float)($order->shipping_fee ?? 0);
            $discount = (float)($order->discount_amount ?? 0);
            $grand = $subtotal + $shipping - $discount;

            $updates = ['subtotal'=>$subtotal, 'total_amount'=>$grand];
            if (Schema::hasColumn('orders','updated_at')) $updates['updated_at']=now();
            DB::table('orders')->where('id',$order->id)->update($updates);

            return back()->with('success','ลบสินค้าและปล่อยสต๊อกของรายการนั้นเรียบร้อย');
        });
    }

    /* =========================
       Live Search / Variants (AJAX)
    ========================== */
    public function searchProducts(Request $request)
{
    $query = trim((string)$request->get('q', ''));

    if (mb_strlen($query) < 2) {
        return response()->json([]); // พิมพ์น้อยกว่า 2 ตัวอักษรไม่ค้นหา
    }

    // ถ้าไม่มีคอลัมน์ is_active เอาออกได้
    $products = \App\Models\Product::query()
        ->when($request->has('only_active'), fn($q)=>$q->where('is_active', 1))
        ->where(function ($q2) use ($query) {
            $q2->where('name', 'LIKE', "%{$query}%")
               ->orWhere('id_stock', 'LIKE', "%{$query}%")
               ->orWhere('sku', 'LIKE', "%{$query}%");
        })
        ->orderBy('name')
        ->limit(10)
        ->get(['id','name','price','id_stock','sku']);

    // คืนข้อมูลที่ JS ใช้แน่ๆ
    $payload = $products->map(function($p){
        return [
            'id'       => (int) $p->id,
            'name'     => (string) ($p->name ?? '-'),
            'price'    => (float) ($p->price ?? 0),
            'id_stock' => (string) ($p->id_stock ?? $p->sku ?? ''),
            'sku'      => (string) ($p->sku ?? ''),
        ];
    });

    return response()->json($payload);
}

    public function getProductVariants(Request $request, $productId)
{
    $excludeOrderId = (int)$request->query('exclude_order_id', 0);

    // 1) base variants ของสินค้านี้
    $variants = DB::table('product_color_size as pcs')
        ->leftJoin('colors as c', 'c.id', '=', 'pcs.color_id')
        ->leftJoin('sizes  as s', 's.id', '=', 'pcs.size_id')
        ->where('pcs.product_id', $productId)
        ->selectRaw('
            pcs.id,
            pcs.color_id,
            pcs.size_id,
            pcs.quantity,
            c.name      as color_name,
            s.size_name as size_name
        ')
        ->get();

    if ($variants->isEmpty()) {
        // ส่งเป็นอาเรย์ว่าง (รูปแบบเดิม) เพื่อไม่กระทบ JS หน้าต่าง ๆ
        return response()->json([]);
    }

    $ids = $variants->pluck('id')->all();

    // 2) ยอดจอง (active) ของทุก variant โดยยกเว้นออเดอร์ที่ระบุ (ถ้ามี)
    $reserved = DB::table('stock_holds')
        ->whereIn('product_color_size_id', $ids)
        ->where('status', 'active')
        ->when($excludeOrderId > 0, function ($q) use ($excludeOrderId) {
            $q->where(function ($qq) use ($excludeOrderId) {
                $qq->whereNull('order_id')
                   ->orWhere('order_id', '<>', $excludeOrderId);
            });
        })
        ->selectRaw('product_color_size_id, SUM(quantity) as qty')
        ->groupBy('product_color_size_id')
        ->pluck('qty', 'product_color_size_id');

    // 3) map กลับ: คงรูปแบบเดิมที่ JS ใช้ (มี field quantity สำหรับ “คงเหลือ”)
    $out = $variants->map(function ($v) use ($reserved) {
        $base      = (int)$v->quantity;                          // สต็อกในตาราง pcs
        $resv      = (int)($reserved[$v->id] ?? 0);              // สต็อกที่ถูกจับไว้
        $available = max(0, $base - $resv);                      // คงเหลือจริง
        $display   = trim(($v->color_name ?? '') . ' - ' . ($v->size_name ?? ''), ' -');

        return [
            'id'            => (int)$v->id,                       // ✅ id ของ variant (pcs.id)
            'color_id'      => $v->color_id,
            'size_id'       => $v->size_id,
            'color_name'    => $v->color_name ?? '',
            'size_name'     => $v->size_name ?? '',
            'base_quantity' => $base,                             // ฐาน (ออปชัน)
            'available'     => $available,                        // คงเหลือจริง
            'quantity'      => $available,                        // ✅ alias เดิมที่ JS อ่านอยู่
            'display_name'  => $display . " (คงเหลือ: {$available})",
        ];
    });

    // ส่ง “อาเรย์” ตรง ๆ (ไม่ห่อ) → ไม่กระทบโค้ดเดิม
    return response()->json($out);
}

    /* =========================
       Tracking / Payment
    ========================== */
    public function updateTracking(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        if ($order->payment_status !== 'paid') {
            return back()->withErrors(['error'=>'ไม่สามารถใส่ Tracking ได้จนกว่าจะชำระเงิน']);
        }
        $order->tracking_number = $request->tracking_number;
        $order->status = 'shipped';
        $order->save();

        return redirect()->route('orders.show',$id)->with('success','อัปเดต Tracking Number สำเร็จ');
    }

    public function pay(Request $request, $id)
    {
        $request->validate([
            'slip_image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $order = Order::findOrFail($id);
        if ($request->hasFile('slip_image')) {
            $file = $request->file('slip_image');
            $filename = 'slip_'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('slips', $filename, 'public');
            $order->slip_image = $path;
        }
        $order->payment_status = 'paid';
        $order->save();

        return redirect()->route('orders.show',$id)->with('success','ชำระเงินเรียบร้อยแล้ว');
    }
}
