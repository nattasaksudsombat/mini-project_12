@extends('layouts.app')

@section('content')
<div class="container">
  {{-- Alerts --}}
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <strong>พบข้อผิดพลาด:</strong>
      <ul class="mb-0">
        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
      </ul>
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">
      แก้ไขออเดอร์: {{ $order->order_number ?? ('ORD'.str_pad($order->id,5,'0',STR_PAD_LEFT)) }}
    </h3>
    <div>
      <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info me-2">
        <i class="fas fa-eye"></i> ดูรายละเอียด
      </a>
      <a href="{{ route('orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> กลับ
      </a>
    </div>
  </div>

  {{-- ===== ข้อมูลลูกค้า & ออเดอร์ (แสดงครบ แต่ lock ฟิลด์ไม่ให้แก้จากฟอร์มนี้) ===== --}}
  <div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
      <span class="fw-bold">ข้อมูลลูกค้า</span>
      <div class="d-flex gap-2">
        <span class="badge rounded-pill text-bg-secondary">
          เลขที่ออเดอร์: {{ $order->order_number ?? ('ORD'.str_pad($order->id,5,'0',STR_PAD_LEFT)) }}
        </span>
        <span class="badge rounded-pill
          @switch($order->status)
            @case('pending') text-bg-warning @break
            @case('processing') text-bg-info @break
            @case('shipped') text-bg-primary @break
            @case('delivered') text-bg-success @break
            @case('cancelled') text-bg-danger @break
            @default text-bg-secondary
          @endswitch">
          {{ ucfirst($order->status) }}
        </span>
        <span class="badge rounded-pill {{ $order->payment_status==='paid' ? 'text-bg-success' : 'text-bg-secondary' }}">
          {{ $order->payment_status==='paid' ? 'ชำระแล้ว' : 'ยังไม่ชำระ' }}
        </span>
      </div>
    </div>
    <div class="card-body row g-3">
      <div class="col-md-4">
        <label class="form-label">ชื่อลูกค้า</label>
        <input type="text" class="form-control" value="{{ $order->customer->name ?? '-' }}" disabled>
      </div>
      <div class="col-md-4">
        <label class="form-label">เบอร์โทร</label>
        <input type="text" class="form-control" value="{{ $order->customer->phone ?? '-' }}" disabled>
      </div>
      <div class="col-md-4">
        <label class="form-label">ช่องทางซื้อ</label>
        @php
          $channelLabels = [
            'facebook'=>'Facebook','line'=>'Line','website'=>'เว็บไซต์',
            'shopee'=>'Shopee','lazada'=>'Lazada','offline'=>'หน้าร้าน'
          ];
          $chKey = strtolower($order->customer->purchase_channel ?? '');
          $chLabel = $channelLabels[$chKey] ?? $order->customer->purchase_channel ?? '-';
        @endphp
        <input type="text" class="form-control" value="{{ $chLabel }}" disabled>
      </div>

      <div class="col-md-6">
        <label class="form-label">ที่อยู่จัดส่ง</label>
        <textarea class="form-control" rows="2" disabled>{{ $order->customer->address ?? '-' }}</textarea>
        <div class="form-text">หมายเหตุ: ฟอร์มนี้แก้ “จำนวนสินค้า” เท่านั้น ส่วนข้อมูลลูกค้า/สถานะ มีปุ่มเฉพาะของมัน</div>
      </div>

      <div class="col-md-3">
        <label class="form-label">วิธีชำระเงิน</label>
        @php
          $paymentLabels = [
            'bank_transfer'=>'โอน/พร้อมเพย์',
            'cash_on_delivery'=>'ชำระปลายทาง (COD)',
            'credit_card'=>'บัตรเครดิต/เดบิต',
            'e_wallet'=>'วอลเล็ต'
          ];
          $pmKey = strtolower($order->customer->payment_method ?? '');
          $pmLabel = $paymentLabels[$pmKey] ?? $order->customer->payment_method ?? '-';
        @endphp
        <input type="text" class="form-control" value="{{ $pmLabel }}" disabled>
      </div>

      <div class="col-md-3">
        <label class="form-label">เลขติดตามพัสดุ</label>
        <input type="text" class="form-control" value="{{ $order->tracking_number ?? '-' }}" disabled>
      </div>
    </div>
  </div>

  {{-- ===== แก้จำนวนสินค้า (เข้ากับ OrderController::update) ===== --}}
  <form method="POST" action="{{ route('orders.update', $order->id) }}" id="order-edit-form">
    @csrf @method('PUT')

    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-box"></i> แก้จำนวนสินค้า</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead>
              <tr>
                <th>สินค้า</th>
                <th style="width:160px">สี - ขนาด</th>
                <th class="text-center" style="width:140px">จำนวน</th>
                <th class="text-end" style="width:140px">ราคา/ชิ้น</th>
                <th class="text-end" style="width:140px">รวม</th>
                <th style="width:260px">สถานะสต๊อค</th>
              </tr>
            </thead>
            <tbody>
              @php $sub = 0; @endphp
              @foreach($items as $it)
                @php
                  $line = $it->quantity * $it->price;
                  $sub += $line;
                @endphp
                <tr data-order-item-id="{{ $it->id }}">
                  <td>
                    <div class="fw-semibold">{{ $it->product_name }}</div>
                    <small class="text-muted">ID: {{ $it->product_id }}</small>
                  </td>
                  <td>
                    <span class="badge bg-secondary">{{ $it->variant_name }}</span>
                  </td>

                  <td class="text-center">
                    <input type="number"
                           class="form-control form-control-sm qty-input"
                           name="items[{{ $it->id }}][quantity]"
                           value="{{ (int)$it->quantity }}"
                           min="0"
                           data-max-total="{{ (int)$it->max_total_for_order }}"
                           data-unit-price="{{ number_format((float)$it->price, 2, '.', '') }}">
                    <div class="form-text">
                      ตั้งได้สูงสุด: {{ (int)$it->max_total_for_order }} ชิ้น
                    </div>
                  </td>

                  <td class="text-end">{{ number_format($it->price, 2) }}</td>
                  <td class="text-end line-total">{{ number_format($line, 2) }}</td>

                  <td>
                    <small class="text-muted d-block">
                      TotalStock: {{ (int)$it->current_stock }} | Reserved: {{ (int)$it->reserved_stock }}
                    </small>
                    <small class="text-muted d-block">
                      Available: {{ (int)$it->available_stock }}
                    </small>
                    <small class="text-muted d-block">
                      คงเหลือ (โควต้าแก้ไขได้): <span class="quota">{{ (int)$it->quota_for_edit }}</span> ชิ้น
                    </small>
                    <small class="text-muted d-block">
                      ตั้งรวมสูงสุด: {{ (int)$it->max_total_for_order }} ชิ้น
                    </small>
                  </td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th colspan="4" class="text-end">รวมเป็นเงิน (Subtotal):</th>
                <th class="text-end" id="subtotal">{{ number_format($subtotal ?? $sub, 2) }}</th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>

        {{-- พรีวิวยอดรวมสุทธิ (อ่านอย่างเดียว) --}}
        <div class="row mt-3">
          <div class="col-md-4 ms-auto">
            <div class="card">
              <div class="card-body">
                @php
                  $shipping = (float)($order->shipping_fee ?? 0);
                  $discount = (float)($order->discount_amount ?? 0);
                @endphp
                <div class="d-flex justify-content-between">
                  <span>ค่าจัดส่ง</span>
                  <span id="ship-fee">{{ number_format($shipping,2) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span>ส่วนลด</span>
                  <span id="discount-amount">{{ number_format($discount,2) }}</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fw-bold">
                  <span>ยอดรวมสุทธิ (พรีวิว)</span>
                  <span id="grand-preview">
                    {{ number_format(($subtotal ?? $sub) + $shipping - $discount, 2) }}
                  </span>
                </div>
                <div class="form-text mt-2">
                  * ตัวเลขนี้เป็นการคำนวณพรีวิว ฝั่งระบบจะคำนวณจริงอีกครั้งเมื่อบันทึก
                </div>
              </div>
            </div>
          </div>
        </div>

      </div> {{-- card-body --}}
    </div> {{-- card --}}

    <div class="d-flex justify-content-between mb-4">
      <button type="button" class="btn btn-outline-secondary" onclick="if(confirm('รีเซ็ตกลับค่าเดิม?')) location.reload()">
        <i class="fas fa-undo"></i> รีเซ็ต
      </button>
      <button class="btn btn-primary">
        <i class="fas fa-save"></i> บันทึกการแก้ไข
      </button>
    </div>
  </form>
</div>

{{-- เล็กน้อยเพื่อ UX --}}
<style>
.table thead th{ position:sticky; top:0; z-index:1; background:#f8f9fa; }
.qty-input::-webkit-outer-spin-button,
.qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>
@endsection

@push('scripts')
<script>
(function(){
  function fmt(n){ return new Intl.NumberFormat('th-TH',{minimumFractionDigits:2,maximumFractionDigits:2}).format(n); }

  function parseNumber(text){
    if (typeof text !== 'string') return Number(text||0);
    return Number(text.replace(/,/g,''));
  }

  function recalcRow(tr){
    const inp  = tr.querySelector('.qty-input');
    const qty  = Math.max(0, parseInt(inp.value||'0',10));
    const unit = parseFloat(inp.dataset.unitPrice||'0');
    tr.querySelector('.line-total').textContent = fmt(qty*unit);
  }

  function recalcSubtotalAndGrand(){
    let sum = 0;
    document.querySelectorAll('.line-total').forEach(td => {
      const n = parseNumber(String(td.textContent));
      sum += (isNaN(n)?0:n);
    });
    document.getElementById('subtotal').textContent = fmt(sum);

    const ship = parseNumber(document.getElementById('ship-fee').textContent);
    const disc = parseNumber(document.getElementById('discount-amount').textContent);
    document.getElementById('grand-preview').textContent = fmt(sum + ship - disc);
  }

  // Clamp ปริมาณตาม max_total_for_order ขณะพิมพ์
  document.querySelectorAll('.qty-input').forEach(el=>{
    el.addEventListener('input', e=>{
      const maxTotal = parseInt(e.target.dataset.maxTotal||'0',10);
      let v = parseInt(e.target.value||'0',10);
      if (isNaN(v) || v < 0) v = 0;

      if (maxTotal > 0 && v > maxTotal) {
        v = maxTotal;
        e.target.value = v;
      }

      const tr = e.target.closest('tr');
      recalcRow(tr);
      recalcSubtotalAndGrand();
    });

    // เรียกหนึ่งครั้งตอนโหลด
    recalcRow(el.closest('tr'));
  });

  recalcSubtotalAndGrand();
})();
</script>
@endpush
