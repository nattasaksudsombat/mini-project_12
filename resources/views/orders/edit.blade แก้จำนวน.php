@extends('layouts.app')

@section('content')
<div class="container">
  <h3 class="mb-3">แก้ไขออเดอร์: {{ $order->order_number ?? ('ORD'.str_pad($order->id,5,'0',STR_PAD_LEFT)) }}</h3>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif

  <form method="POST" action="{{ route('orders.update', $order->id) }}" id="order-edit-form">
    @csrf @method('PUT')

    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th>สินค้า</th>
            <th style="width:160px">สี - ขนาด</th>
            <th class="text-center" style="width:140px">จำนวน</th>
            <th class="text-end" style="width:140px">ราคา/ชิ้น</th>
            <th class="text-end" style="width:140px">รวม</th>
            <th style="width:220px">สถานะสต๊อค</th>
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
            <td>{{ $it->product_name }}</td>
            <td>{{ $it->variant_name }}</td>

            <td class="text-center">
              <input type="number"
                     class="form-control form-control-sm qty-input"
                     name="items[{{ $it->id }}][quantity]"
                     value="{{ $it->quantity }}"
                     min="0"
                     data-max-total="{{ $it->max_total_for_order }}"
                     data-unit-price="{{ $it->price }}">
            </td>

            <td class="text-end">{{ number_format($it->price, 2) }}</td>
            <td class="text-end line-total">{{ number_format($line, 2) }}</td>

            <td>
              <small class="text-muted d-block">
                TotalStock: {{ $it->current_stock }} | Reserved: {{ $it->reserved_stock }}
              </small>
              <small class="text-muted d-block">
                Available: {{ $it->available_stock }}
              </small>
              <small class="text-muted d-block">
                คงเหลือ (โควต้าแก้ไขได้): <span class="quota">{{ $it->quota_for_edit }}</span> ชิ้น
              </small>
              <small class="text-muted d-block">
                ตั้งรวมสูงสุด: {{ $it->max_total_for_order }} ชิ้น
              </small>
            </td>
          </tr>
        @endforeach
        </tbody>

        <tfoot>
          <tr>
            <th colspan="4" class="text-end">รวมเป็นเงิน:</th>
            <th class="text-end" id="subtotal">{{ number_format($subtotal ?? $sub, 2) }}</th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="mt-3">
      <button class="btn btn-primary">บันทึกการแก้ไข</button>
      <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-secondary">กลับ</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(function(){
  function fmt(n){ return new Intl.NumberFormat('th-TH',{minimumFractionDigits:2,maximumFractionDigits:2}).format(n); }

  function recalcRow(tr){
    const inp  = tr.querySelector('.qty-input');
    const qty  = Math.max(0, parseInt(inp.value||'0',10));
    const unit = parseFloat(inp.dataset.unitPrice||'0');
    tr.querySelector('.line-total').textContent = fmt(qty*unit);
  }

  function recalcSubtotal(){
    let sum = 0;
    document.querySelectorAll('.line-total').forEach(td => {
      const n = Number(String(td.textContent).replace(/,/g,''));
      sum += (isNaN(n)?0:n);
    });
    document.getElementById('subtotal').textContent = fmt(sum);
  }

  document.querySelectorAll('.qty-input').forEach(el=>{
    el.addEventListener('input', e=>{
      const maxTotal = parseInt(e.target.dataset.maxTotal||'0',10);
      let v = parseInt(e.target.value||'0',10);
      if (v > maxTotal) { v = maxTotal; e.target.value = v; } // hard clamp
      if (isNaN(v) || v < 0)   { v = 0;        e.target.value = 0; }
      const tr = e.target.closest('tr'); recalcRow(tr); recalcSubtotal();
    });
    recalcRow(el.closest('tr'));
  });
  recalcSubtotal();
})();
</script>
@endpush
