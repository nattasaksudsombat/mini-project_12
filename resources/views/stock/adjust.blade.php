@extends('layouts.app')

@section('content')
<div class="container">
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
