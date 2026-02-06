@extends('layouts.app')

@section('content')
<style>
/* Stock Adjust History Page - Dark Gold Theme */
:root {
    --dark-bg: #0a0a0a;
    --dark-secondary: #121212;
    --dark-tertiary: #1e1e1e;
    --gold: #FFD700;
    --gold-dark: #e6c300;
    --text-primary: #f8f8f8;
    --text-secondary: #cccccc;
    --border-gold: #d4af37;
}

/* Container */
#stock-history-container {
    background-color: transparent;
    min-height: 100vh;
}

/* Header */
#stock-history-container h3 {
    color: var(--gold) !important;
    text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
    font-weight: 600;
    margin-bottom: 2rem;
    letter-spacing: 0.5px;
}

/* Table Container */
#stock-history-container .table-responsive {
    background: linear-gradient(135deg, var(--dark-secondary), var(--dark-tertiary));
    border: 1px solid var(--border-gold);
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4), 0 0 20px rgba(212, 175, 55, 0.1);
    padding: 1.5rem;
}

/* Table */
#stock-history-container .table {
    color: var(--text-primary) !important;
    border-color: rgba(212, 175, 55, 0.2);
    margin-bottom: 0;
}

#stock-history-container .table thead th {
    color: var(--gold) !important;
    font-weight: 600;
    border-bottom: 2px solid var(--border-gold) !important;
    background-color: rgba(212, 175, 55, 0.1);
    padding: 1rem 0.75rem;
    font-size: 0.95rem;
}

#stock-history-container .table tbody td {
    color: var(--text-primary) !important;
    border-color: rgba(212, 175, 55, 0.15);
    padding: 0.9rem 0.75rem;
    vertical-align: middle;
}

#stock-history-container .table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(255, 255, 255, 0.02);
}

/* Text alignment */
#stock-history-container .text-end {
    text-align: right !important;
    font-variant-numeric: tabular-nums;
    font-weight: 500;
}

#stock-history-container .text-center {
    color: var(--text-secondary) !important;
}

/* Empty state */
#stock-history-container .text-muted {
    color: var(--text-secondary) !important;
}

/* Responsive */
@media (max-width: 768px) {
    #stock-history-container .table-responsive {
        padding: 1rem;
    }
    
    #stock-history-container .table {
        font-size: 0.85rem;
    }
}
</style>

<div class="container" id="stock-history-container">
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