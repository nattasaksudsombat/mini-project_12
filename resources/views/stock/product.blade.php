@extends('layouts.app')

@section('content')
<div class="container">
  <h3 class="mb-3">สต๊อคสินค้า: {{ $product->name }}</h3>

  @foreach($grouped as $color => $rows)
    <h5 class="mt-4">สี: {{ $color }}</h5>
    <div class="table-responsive">
      <table class="table table-sm table-striped align-middle">
        <thead>
          <tr>
            <th style="width:120px">ขนาด</th>
            <th class="text-end" style="width:140px">จำนวน (สต๊อค)</th>
            <th class="text-end" style="width:140px">กำลังถูกจับ</th>
            <th class="text-end" style="width:140px">คงเหลือ</th>
            <th style="width:160px">จัดการ</th>
          </tr>
        </thead>
        <tbody>
        @foreach($rows as $r)
          <tr>
            <td>{{ $r->size_name }}</td>
            <td class="text-end">{{ number_format($r->current_stock) }}</td>
            <td class="text-end">{{ number_format($r->reserved_stock) }}</td>
            <td class="text-end">{{ number_format($r->available_stock) }}</td>
            <td>
              <a class="btn btn-outline-primary btn-sm" href="{{ route('stock.adjust.history', $r->variant_id) }}">ประวัติปรับสต๊อค</a>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  @endforeach
</div>
@endsection
