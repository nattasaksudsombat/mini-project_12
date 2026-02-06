@extends('layouts.app')

@section('content')
<style>
/* Stock Adjust Page - Dark Gold Theme */
/* ธีมดำทองสำหรับหน้าปรับสต๊อค - ไม่มี hover */

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

/* Container หลัก */
#stock-adjust-container {
    background-color: transparent;
    min-height: 100vh;
    padding: 0;
}

/* Header หลัก */
#stock-adjust-container h4 {
    color: var(--gold) !important;
    text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
    font-weight: 600;
    margin-bottom: 2rem;
    letter-spacing: 0.5px;
}

/* Alert boxes */
#stock-adjust-container .alert-success {
    background-color: rgba(25, 135, 84, 0.15) !important;
    color: #75b798 !important;
    border: 1px solid rgba(25, 135, 84, 0.3);
    border-radius: 8px;
}

#stock-adjust-container .alert-danger {
    background-color: rgba(220, 53, 69, 0.15) !important;
    color: #ff6b6b !important;
    border: 1px solid rgba(220, 53, 69, 0.3);
    border-radius: 8px;
}

/* Card สรุปข้อมูล */
#stock-adjust-container .card {
    background: linear-gradient(135deg, var(--dark-secondary), var(--dark-tertiary)) !important;
    border: 1px solid var(--border-gold) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4), 0 0 20px rgba(212, 175, 55, 0.1) !important;
    border-radius: 12px !important;
}

#stock-adjust-container .card-header {
    background: linear-gradient(90deg, rgba(212, 175, 55, 0.2), rgba(212, 175, 55, 0.05)) !important;
    border-bottom: 2px solid var(--border-gold) !important;
    color: var(--gold) !important;
    font-weight: 600 !important;
    padding: 1rem 1.25rem;
    font-size: 1.05rem;
}

#stock-adjust-container .card-body {
    color: var(--text-primary);
}

/* Summary Card (TotalStock, Reserved, Available) */
#stock-adjust-container .card.p-3 {
    background: linear-gradient(135deg, #1a1a1a, #252525) !important;
    border: 2px solid var(--gold) !important;
    box-shadow: 0 0 25px rgba(255, 215, 0, 0.2) !important;
}

#stock-adjust-container .card.p-3 small {
    color: var(--text-secondary);
    font-weight: 500;
    font-size: 0.85rem;
}

#stock-adjust-container .card.p-3 span.fs-5 {
    color: var(--gold) !important;
    font-weight: 700;
    text-shadow: 0 0 8px rgba(255, 215, 0, 0.4);
}

/* Form elements */
#stock-adjust-container .form-label {
    color: var(--text-primary) !important;
    font-weight: 500;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

#stock-adjust-container .form-control {
    background-color: var(--dark-secondary) !important;
    border: 1px solid rgba(212, 175, 55, 0.3) !important;
    color: var(--text-primary) !important;
    border-radius: 6px;
    padding: 0.65rem 0.75rem;
    transition: none;
}

#stock-adjust-container .form-control:focus {
    background-color: var(--dark-tertiary) !important;
    border-color: var(--gold) !important;
    box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25) !important;
    color: var(--text-primary) !important;
}

#stock-adjust-container .form-control::placeholder {
    color: rgba(204, 204, 204, 0.5);
}

/* Radio buttons */
#stock-adjust-container .form-check-input {
    background-color: var(--dark-secondary);
    border: 2px solid rgba(212, 175, 55, 0.4);
    transition: none;
}

#stock-adjust-container .form-check-input:checked {
    background-color: var(--gold) !important;
    border-color: var(--gold) !important;
}

#stock-adjust-container .form-check-label {
    color: var(--text-primary);
    font-weight: 400;
}

/* Buttons - ไม่มี hover effect */
#stock-adjust-container .btn-primary {
    background: linear-gradient(135deg, var(--gold-dark), var(--gold)) !important;
    border: none !important;
    color: #000 !important;
    font-weight: 600;
    padding: 0.6rem 1.5rem;
    border-radius: 6px;
    box-shadow: 0 4px 10px rgba(255, 215, 0, 0.3);
    transition: none;
}

#stock-adjust-container .btn-outline-secondary {
    border: 2px solid var(--text-secondary) !important;
    color: var(--text-secondary) !important;
    background-color: transparent !important;
    font-weight: 500;
    padding: 0.6rem 1.5rem;
    border-radius: 6px;
    transition: none;
}

/* Preview section */
#stock-adjust-container #afterPreview {
    color: var(--gold) !important;
    font-weight: 600;
    font-size: 1rem;
    text-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
}

#stock-adjust-container .text-muted {
    color: var(--text-secondary) !important;
}

/* Table styling */
#stock-adjust-container .table {
    color: var(--text-primary) !important;
    border-color: rgba(212, 175, 55, 0.2);
}

#stock-adjust-container .table thead th {
    color: var(--gold) !important;
    font-weight: 600;
    border-bottom: 2px solid var(--border-gold) !important;
    background-color: rgba(212, 175, 55, 0.1);
    padding: 0.85rem 0.75rem;
    font-size: 0.9rem;
}

#stock-adjust-container .table tbody td {
    color: var(--text-primary) !important;
    border-color: rgba(212, 175, 55, 0.15);
    padding: 0.75rem;
    vertical-align: middle;
}

#stock-adjust-container .table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(255, 255, 255, 0.02);
}

/* Badges */
#stock-adjust-container .badge.text-bg-success {
    background-color: rgba(25, 135, 84, 0.2) !important;
    color: #75b798 !important;
    border: 1px solid rgba(25, 135, 84, 0.4);
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

#stock-adjust-container .badge.text-bg-danger {
    background-color: rgba(220, 53, 69, 0.2) !important;
    color: #ff6b6b !important;
    border: 1px solid rgba(220, 53, 69, 0.4);
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

#stock-adjust-container .badge.text-bg-warning {
    background-color: rgba(255, 215, 0, 0.2) !important;
    color: var(--gold) !important;
    border: 1px solid rgba(255, 215, 0, 0.4);
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

#stock-adjust-container .badge.text-bg-info {
    background-color: rgba(13, 202, 240, 0.2) !important;
    color: #5fedff !important;
    border: 1px solid rgba(13, 202, 240, 0.4);
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

#stock-adjust-container .badge.text-bg-secondary {
    background-color: rgba(108, 117, 125, 0.2) !important;
    color: var(--text-secondary) !important;
    border: 1px solid rgba(108, 117, 125, 0.4);
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

/* Text alignment */
#stock-adjust-container .text-end {
    text-align: right !important;
    font-variant-numeric: tabular-nums;
}

#stock-adjust-container .text-center {
    color: var(--text-secondary) !important;
}

/* Responsive */
@media (max-width: 768px) {
    #stock-adjust-container {
        padding: 0;
    }
    
    #stock-adjust-container .card {
        margin-bottom: 1rem;
    }
}
</style>

<div class="container" id="stock-adjust-container">
  <h4 class="mb-3">
    ปรับสต๊อค — {{ $variant->product_name }}
    {{ $variant->color_name ? 'สี: '.$variant->color_name : '' }}
    {{ $variant->size_name ? ' ขนาด: '.$variant->size_name : '' }}
  </h4>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif

  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card p-3">
        <div class="d-flex justify-content-between"><small>TotalStock</small><span class="fs-5" id="cur">{{ number_format($summary->current) }}</span></div>
        <div class="d-flex justify-content-between"><small>Reserved</small><span class="fs-5">{{ number_format($summary->reserved) }}</span></div>
        <div class="d-flex justify-content-between"><small>Available</small><span class="fs-5" id="avail">{{ number_format($summary->available) }}</span></div>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card">
        <div class="card-header">ฟอร์มปรับสต๊อค (manual)</div>
        <div class="card-body">
          <form method="POST" action="{{ route('stock.adjust.save',$variant->id) }}" id="adj-form">
            @csrf

            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label d-block">ประเภทการปรับ</label>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="action" id="act_in" value="in" checked>
                  <label for="act_in" class="form-check-label">เพิ่มเข้า</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="action" id="act_out" value="out">
                  <label for="act_out" class="form-check-label">ตัดออก</label>
                </div>
              </div>

              <div class="col-md-4">
                <label class="form-label">จำนวน</label>
                <input type="number" class="form-control" name="quantity" id="qty" min="1" value="1" required>
              </div>

              <div class="col-md-4">
                <label class="form-label">เลขอ้างอิง (ถ้ามี)</label>
                <input type="text" class="form-control" name="ref" placeholder="เช่น DOC-001">
              </div>

              <div class="col-12">
                <label class="form-label">เหตุผล</label>
                <input type="text" class="form-control" name="reason" placeholder="เช่น รับสินค้าเข้า / สินค้าเสียหาย / ตรวจนับ">
              </div>
            </div>

            <div class="mt-3 d-flex align-items-center gap-3">
              <button class="btn btn-primary">บันทึกการปรับ</button>
              <a href="{{ route('stock.variant.history',$variant->id) }}" class="btn btn-outline-secondary">ดูประวัติ</a>

              <div class="ms-auto">
                <small class="text-muted d-block">พรีวิวหลังปรับ</small>
                <span id="afterPreview" class="fw-semibold"></span>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header">รายการล่าสุด</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-sm mb-0 align-middle">
              <thead>
                <tr>
                  <th style="width:170px">วันที่/เวลา</th>
                  <th style="width:100px">ประเภท</th>
                  <th class="text-end">ก่อน</th>
                  <th class="text-end">เปลี่ยน</th>
                  <th class="text-end">หลัง</th>
                  <th>เหตุผล</th>
                  <th style="width:140px">ผู้ทำ</th>
                  <th style="width:120px">อ้างอิง</th>
                </tr>
              </thead>
              <tbody>
                @forelse($last10 as $r)
                  @php $delta = ($r->quantity >= 0 ? '+' : '').$r->quantity; @endphp
                  <tr>
                    <td>{{ $r->created_at }}</td>
                    <td>
                      @switch($r->type)
                        @case('in')      <span class="badge text-bg-success">เข้า</span> @break
                        @case('out')     <span class="badge text-bg-danger">ออก</span> @break
                        @case('reserve') <span class="badge text-bg-warning">จอง</span> @break
                        @case('release') <span class="badge text-bg-info">ปล่อย</span> @break
                        @default         <span class="badge text-bg-secondary">{{ $r->type }}</span>
                      @endswitch
                    </td>
                    <td class="text-end">{{ number_format($r->quantity_before) }}</td>
                    <td class="text-end">{{ $delta }}</td>
                    <td class="text-end">{{ number_format($r->quantity_after) }}</td>
                    <td>{{ $r->reason }}</td>
                    <td>{{ $r->user_name ?? '-' }}</td>
                    <td>{{ $r->reference_number ?? '-' }}</td>
                  </tr>
                @empty
                  <tr><td colspan="8" class="text-center text-muted">— ไม่มีข้อมูล —</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script>
(function(){
  const current  = {{ (int)$summary->current }};
  const reserved = {{ (int)$summary->reserved }};
  const available= {{ (int)$summary->available }};

  function fmt(n){ return new Intl.NumberFormat('th-TH').format(n); }

  function preview(){
    const act = document.querySelector('input[name="action"]:checked').value;
    const qty = Math.max(1, parseInt(document.getElementById('qty').value||'1',10));
    let afterOnHand = current;
    if (act === 'in')  afterOnHand = current + qty;
    if (act === 'out') afterOnHand = current - qty;

    // ป้องกันตัดจน on-hand < reserved (กฎใน Service ก็ตรวจอีกชั้น)
    const minAllowed = reserved;
    const warn = (act==='out' && afterOnHand < minAllowed) ? ' (เกินกว่าที่ตัดได้)' : '';

    document.getElementById('afterPreview').textContent =
      `On-hand หลังปรับ: ${fmt(afterOnHand)} | Reserved: ${fmt(reserved)} | Available: ${fmt(afterOnHand - reserved)}${warn}`;
  }

  document.querySelectorAll('input[name="action"]').forEach(el => el.addEventListener('change', preview));
  document.getElementById('qty').addEventListener('input', preview);
  preview();
})();
</script>
@endpush
@endsection