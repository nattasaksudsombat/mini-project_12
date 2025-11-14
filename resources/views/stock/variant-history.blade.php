@extends('layouts.app')

@php
    // ===== Fallback กันพังเมื่อ Controller ไม่ส่งตัวแปรมา =====

    // 1) หา variantId จากตัวแปร/พารามิเตอร์ route
    $variantId = (int) (
        ($variant->id ?? 0)
        ?: (request()->route('variantId') ?? 0)
        ?: (request()->route('variant')   ?? 0)
        ?: (request()->route('id')        ?? 0)
    );

    // 2) ถ้าไม่มี $variant ให้ดึงจาก DB
    if (!isset($variant) || empty($variant->product_name)) {
        $variant = \DB::table('product_color_size as pcs')
            ->join('products as p','p.id','=','pcs.product_id')
            ->leftJoin('colors as c','c.id','=','pcs.color_id')
            ->leftJoin('sizes  as s','s.id','=','pcs.size_id')
            ->selectRaw('pcs.id, pcs.product_id, p.name as product_name, c.name as color_name, s.size_name')
            ->where('pcs.id', $variantId)
            ->first();
    }

    // 3) ถ้าไม่มี $summary ให้คำนวณจาก v_current_stock (Golden Rule)
    if (!isset($summary)) {
        $v = \DB::table('v_current_stock')->where('id',$variantId)->first();
        $summary = (object)[
            'current'   => (int)($v->current_stock  ?? 0),
            'reserved'  => (int)($v->reserved_stock ?? 0),
            'available' => (int)($v->available_stock?? 0),
        ];
    }

    // 4) scope ของตารางประวัติ
    $scope = $scope ?? request()->query('scope','all');

    // 5) ถ้าไม่มี $history ให้โหลดจาก stock_transactions + map label ไทย
    if (!isset($history)) {
        $q = \DB::table('stock_transactions')
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
            return (object)[
                'created_at' => $r->created_at,
                'type'       => $r->type,
                'type_th'    => $mapTH[$r->type] ?? $r->type,
                'before'     => (int)$r->quantity_before,
                'delta'      => (int)$r->quantity,
                'delta_str'  => ($r->quantity >= 0 ? '+' : '').(int)$r->quantity,
                'after'      => (int)$r->quantity_after,
                'reason'     => $r->reason,
                'user_name'  => $r->user_name ?? '-',
                'order_id'   => $r->order_id,
                'ref'        => $r->reference_number,
            ];
        });
    }

    // 6) ถ้าไม่มี $holds ให้โหลดออเดอร์ที่กำลังจับจาก stock_holds.status='active'
    if (!isset($holds)) {
        if (\Schema::hasTable('stock_holds')) {
            $openStatuses = ['pending','processing'];
            $orderNoExpr = \Schema::hasColumn('orders','order_number') ? 'o.order_number'
                : (\Schema::hasColumn('orders','code')     ? 'o.code'
                : (\Schema::hasColumn('orders','order_no') ? 'o.order_no' : 'o.id'));

            $holds = \DB::table('stock_holds as sh')
                ->leftJoin('orders as o','o.id','=','sh.order_id')
                ->where('sh.product_color_size_id',$variantId)
                ->where('sh.status','active')
                ->when(\Schema::hasTable('orders'), function($qq) use ($openStatuses) {
                    $qq->whereIn('o.status',$openStatuses);
                })
                ->orderByDesc('sh.updated_at')
                ->get([
                    'sh.order_id','sh.quantity','o.status',
                    \DB::raw("$orderNoExpr as order_number"),
                ]);
        } else {
            $holds = collect();
        }
    }
@endphp

@section('content')
<div class="container">
  <h4 class="mb-3">
    ประวัติสต๊อค — {{ $variant->product_name ?? '-' }}
    {{ !empty($variant->color_name) ? 'สี: '.$variant->color_name : '' }}
    {{ !empty($variant->size_name)  ? ' ขนาด: '.$variant->size_name  : '' }}
  </h4>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif

  <div class="row g-3 align-items-center mb-3">
    <div class="col-auto">
      <div class="card p-2"><small>TotalStock</small><div class="fs-5 text-end">{{ number_format($summary->current) }}</div></div>
    </div>
    <div class="col-auto">
      <div class="card p-2"><small>Reserved</small><div class="fs-5 text-end">{{ number_format($summary->reserved) }}</div></div>
    </div>
    <div class="col-auto">
      <div class="card p-2"><small>Available</small><div class="fs-5 text-end">{{ number_format($summary->available) }}</div></div>
    </div>
    <div class="col ms-auto text-end">
      <a href="{{ route('stock.adjust.form',$variantId) }}" class="btn btn-warning">ปรับสต๊อค</a>
      <a href="{{ route('stock.variant.history',['variantId'=>$variantId,'scope'=>'all']) }}" class="btn btn-outline-secondary {{ $scope==='all'?'active':'' }}">ทั้งหมด</a>
      <a href="{{ route('stock.variant.history',['variantId'=>$variantId,'scope'=>'holds']) }}" class="btn btn-outline-secondary {{ $scope==='holds'?'active':'' }}">เฉพาะ จอง/ปล่อย</a>
      <a href="{{ route('stock.variant.history',['variantId'=>$variantId,'scope'=>'physical']) }}" class="btn btn-outline-secondary {{ $scope==='physical'?'active':'' }}">เฉพาะ เข้า/ออก</a>
    </div>
  </div>

  @if($scope!=='physical')
    <div class="card mb-3">
      <div class="card-header">ออเดอร์ที่กำลังจับ (active)</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm mb-0 align-middle">
            <thead><tr><th>ออเดอร์</th><th>สถานะ</th><th class="text-end">จำนวน</th><th></th></tr></thead>
            <tbody>
              @forelse($holds as $h)
                <tr>
                  <td><span class="badge bg-secondary">{{ $h->order_number }}</span></td>
                  <td>{{ $h->status ?? '-' }}</td>
                  <td class="text-end">{{ number_format($h->quantity) }}</td>
                  <td><a class="btn btn-sm btn-outline-info" href="{{ url('/orders/'.$h->order_id) }}">ดูออเดอร์</a></td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted">— ไม่มีออเดอร์ที่กำลังจับ —</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @endif

  <div class="card">
    <div class="card-header">ไทม์ไลน์การเปลี่ยนแปลง (ล่าสุด)</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-sm mb-0 align-middle">
          <thead>
            <tr>
              <th style="width:170px">วันที่/เวลา</th>
              <th style="width:110px">ประเภท</th>
              <th class="text-end" style="width:110px">ก่อน</th>
              <th class="text-end" style="width:110px">เปลี่ยน</th>
              <th class="text-end" style="width:110px">หลัง</th>
              <th>เหตุผล</th>
              <th style="width:140px">ผู้ทำ</th>
              <th style="width:120px">อ้างอิง</th>
              <th style="width:100px"></th>
            </tr>
          </thead>
          <tbody>
  @forelse($history as $h)
    @php
      // ป้องกัน property หาย: map ทุกค่าที่ต้องใช้
      $type    = $h->type ?? '';
      $typeTH  = $h->type_th ?? ($typeMap[$type] ?? $type);

      $before  = isset($h->before) ? (int)$h->before : (int)($h->quantity_before ?? 0);
      $delta   = isset($h->delta)  ? (int)$h->delta  : (int)($h->quantity ?? 0);
      $after   = isset($h->after)  ? (int)$h->after  : (int)($h->quantity_after ?? ($before + $delta));
      $deltaStr= isset($h->delta_str) ? $h->delta_str : (($delta >= 0 ? '+' : '').$delta);

      $reason  = $h->reason ?? '';
      $user    = $h->user_name ?? '-';
      $ref     = $h->ref ?? ($h->reference_number ?? '-');
      $orderId = $h->order_id ?? null;
    @endphp
    <tr>
      <td>{{ $h->created_at }}</td>
      <td>
        @switch($type)
          @case('reserve') <span class="badge text-bg-warning">{{ $typeTH }}</span> @break
          @case('release') <span class="badge text-bg-info">{{ $typeTH }}</span> @break
          @case('in')      <span class="badge text-bg-success">{{ $typeTH }}</span> @break
          @case('out')     <span class="badge text-bg-danger">{{ $typeTH }}</span> @break
          @default         <span class="badge text-bg-secondary">{{ $typeTH }}</span>
        @endswitch
      </td>
      <td class="text-end">{{ number_format($before) }}</td>
      <td class="text-end">{{ $deltaStr }}</td>
      <td class="text-end">{{ number_format($after) }}</td>
      <td>{{ $reason }}</td>
      <td>{{ $user }}</td>
      <td>{{ $ref }}</td>
      <td>
        @if($orderId)
          <a class="btn btn-sm btn-outline-primary" href="{{ url('/orders/'.$orderId) }}">ออเดอร์</a>
        @endif
      </td>
    </tr>
  @empty
    <tr><td colspan="9" class="text-center text-muted">— ไม่มีข้อมูล —</td></tr>
  @endforelse
</tbody>

        </table>
      </div>
    </div>
  </div>

</div>
@endsection
