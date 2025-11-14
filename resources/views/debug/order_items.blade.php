@extends('layouts.app')

@section('content')
<div class="container">
  <h3 class="mb-3">Order Items (ล่าสุด 10 แถว)</h3>

  <div class="table-responsive">
    <table class="table table-sm table-striped align-middle">
      <thead>
        <tr>
          <th style="width:90px">ID</th>
          <th class="text-end" style="width:120px">Quantity</th>
          <th class="text-end" style="width:140px">Unit Price</th>
          <th class="text-end" style="width:140px">Total Price</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          @php
            // เผื่อ unit_price ว่าง คำนวณจาก total/qty แสดงเฉพาะบนจอ (ไม่แก้ใน DB)
            $unit = $r->unit_price ?? (($r->quantity ?? 0) ? ($r->total_price / $r->quantity) : 0);
          @endphp
          <tr>
            <td>{{ $r->id }}</td>
            <td class="text-end">{{ number_format((int)$r->quantity) }}</td>
            <td class="text-end">{{ number_format((float)$unit, 2) }}</td>
            <td class="text-end">{{ number_format((float)$r->total_price, 2) }}</td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center text-muted">— ไม่มีข้อมูล —</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mt-2">กลับ</a>
</div>
@endsection
