{{-- resources/views/customers/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>เพิ่มลูกค้าใหม่</h1>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> กลับ
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>พบข้อผิดพลาด:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="customer-form" method="POST" action="{{ route('customers.store') }}">
        @csrf

        {{-- ข้อมูลหลักของลูกค้า --}}
        <div class="card mb-4">
            <div class="card-header">
                <strong>ข้อมูลลูกค้า</strong>
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">ชื่อลูกค้า <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">เบอร์โทร</label>
                    <input type="text" name="phone" class="form-control"
                        value="{{ old('phone') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">ช่องทางซื้อหลัก</label>
                    @php
                        $channelOptions = [
                            'facebook' => 'Facebook',
                            'line'     => 'Line',
                            'website'  => 'เว็บไซต์',
                            'shopee'   => 'Shopee',
                            'lazada'   => 'Lazada',
                            'offline'  => 'หน้าร้าน',
                        ];
                        $ch = old('purchase_channel');
                    @endphp
                    <select name="purchase_channel" class="form-select">
                        <option value="">-- เลือกช่องทางซื้อ --</option>
                        @foreach($channelOptions as $val => $label)
                            <option value="{{ $val }}" @selected($ch === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">วิธีชำระเงินหลัก</label>
                    @php
                        $paymentOptions = [
                            'bank_transfer'    => 'โอน/พร้อมเพย์',
                            'cash_on_delivery' => 'ชำระปลายทาง (COD)',
                            'credit_card'      => 'บัตรเครดิต/เดบิต',
                            'e_wallet'         => 'วอลเล็ต',
                        ];
                        $pm = old('payment_method');
                    @endphp
                    <select name="payment_method" class="form-select">
                        <option value="">-- เลือกวิธีชำระเงิน --</option>
                        @foreach($paymentOptions as $val => $label)
                            <option value="{{ $val }}" @selected($pm === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ที่อยู่หลายรายการ --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>ที่อยู่ของลูกค้า</strong>
                <button type="button" class="btn btn-sm btn-success" id="btn-add-address">
                    <i class="fas fa-plus"></i> เพิ่มที่อยู่ใหม่
                </button>
            </div>
            <div class="card-body" id="addresses-wrapper">
                {{-- เริ่มต้น 1 แถวเปล่า --}}
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> บันทึกลูกค้า
            </button>
            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                ยกเลิก
            </a>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
(function(){
    // index สำหรับ addresses[x]
    let addressIndex = 0;

    // ฟังก์ชันเพิ่ม card ที่อยู่ใหม่
    function addAddressRow(data = {}) {
        const wrapper = document.getElementById('addresses-wrapper');
        const idx = addressIndex++;

        const name        = data.name        || '';
        const address     = data.address     || '';
        const district    = data.district    || '';
        const province    = data.province    || '';
        const postal_code = data.postal_code || '';

        const html = `
            <div class="border rounded p-3 mb-3 address-card" data-index="${idx}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>ที่อยู่ #${idx + 1}</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-address">
                        <i class="fas fa-trash-alt"></i> ลบที่อยู่นี้
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">ชื่อสถานที่ (เช่น บ้าน, ที่ทำงาน)</label>
                        <input type="text" class="form-control"
                            name="addresses[${idx}][name]"
                            value="${escapeHtml(name)}">
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">รายละเอียดที่อยู่</label>
                        <textarea class="form-control" rows="2"
                            name="addresses[${idx}][address]">${escapeHtml(address)}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">ตำบล / แขวง</label>
                        <input type="text" class="form-control"
                            name="addresses[${idx}][district]"
                            value="${escapeHtml(district)}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">อำเภอ / เขต</label>
                        <input type="text" class="form-control"
                            name="addresses[${idx}][province]"
                            value="${escapeHtml(province)}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">รหัสไปรษณีย์</label>
                        <input type="text" class="form-control"
                            name="addresses[${idx}][postal_code]"
                            value="${escapeHtml(postal_code)}">
                    </div>
                </div>
            </div>
        `;

        const div = document.createElement('div');
        div.innerHTML = html;
        wrapper.appendChild(div.firstElementChild);
    }

    // escape HTML ป้องกัน XSS เวลาใส่ค่าเก่า
    function escapeHtml(str){
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;')
            .replace(/'/g,'&#039;');
    }

    // event: กดปุ่มเพิ่มที่อยู่
    document.getElementById('btn-add-address').addEventListener('click', function(){
        addAddressRow();
    });

    // event delegation: กดปุ่มลบที่อยู่
    document.getElementById('addresses-wrapper').addEventListener('click', function(e){
        if (e.target.closest('.btn-remove-address')) {
            const card = e.target.closest('.address-card');
            if (card) card.remove();
        }
    });

    // ตอนเปิดหน้า create ให้มีอย่างน้อย 1 แถว
    if (document.getElementById('addresses-wrapper').children.length === 0) {
        addAddressRow();
    }
})();
</script>
@endpush
