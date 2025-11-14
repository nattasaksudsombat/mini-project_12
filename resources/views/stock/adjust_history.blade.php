@extends('layouts.app')

@section('content')
<div class="container">
  <h3 class="mb-3">
    ประวัติการปรับสต๊อค (เฉพาะเข้า/ออก) —
    {{ $variant->product_name }}
    {{ $variant->color_name ? 'สี: '.$variant->color_name : '' }}
    {{ $variant->size_name ? ' ขนาด: '.$variant->size_name : '' }}
  </h3>

  <div class="table-responsive">
    <table class="table table-sm table-striped align-middle">
      <thead>
        <tr>
          <th style="width:180px">วันที่/เวลา</th>
          <th style="width:90px">ประเภท</th>
          <th class="text-end" style="width:110px">ก่อน</th>
          <th class="text-end" style="width:110px">เปลี่ยน</th>
          <th class="text-end" style="width:110px">หลัง</th>
          <th>เหตุผล</th>
          <th style="width:140px">ผู้ทำรายการ</th>
        </tr>
      </thead>
      <tbody>
        @forelse($history as $h)
          <tr>
            <td>{{ $h->created_at }}</td>
            <td>{{ $h->type }}</td>
            <td class="text-end">{{ number_format($h->before) }}</td>
            <td class="text-end">{{ $h->delta_str }}</td>
            <td class="text-end">{{ number_format($h->after) }}</td>
            <td>{{ $h->reason }}</td>
            <td>{{ $h->user_name }}</td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-muted">— ไม่มีข้อมูล —</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
