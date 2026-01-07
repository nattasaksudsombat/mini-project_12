@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>แก้ไขลูกค้า: {{ $customer->name }}</h1>
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

    <form id="customer-form" method="POST" action="{{ route('customers.update', $customer->id) }}">
        @csrf
        @method('PUT')

        {{-- 1. ข้อมูลส่วนตัวลูกค้า --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-user"></i> ข้อมูลส่วนตัว
            </div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">ชื่อลูกค้า <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $customer->name) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">เบอร์โทร</label>
                    <input type="text" name="phone" class="form-control"
                        value="{{ old('phone', $customer->phone) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">อีเมล</label>
                    <input type="email" name="email" class="form-control"
                        value="{{ old('email', $customer->email) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">ช่องทางซื้อหลัก</label>
                    <select name="purchase_channel" class="form-select">
                        <option value="">-- เลือกช่องทาง --</option>
                        @php $ch = old('purchase_channel', $customer->purchase_channel); @endphp
                        @foreach(['facebook'=>'Facebook','line'=>'Line','website'=>'เว็บไซต์','shopee'=>'Shopee','lazada'=>'Lazada','offline'=>'หน้าร้าน'] as $val => $label)
                            <option value="{{ $val }}" @selected($ch === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">วิธีชำระเงินหลัก</label>
                    <select name="payment_method" class="form-select">
                        <option value="">-- เลือกวิธีชำระ --</option>
                        @php $pm = old('payment_method', $customer->payment_method); @endphp
                        @foreach(['bank_transfer'=>'โอน/พร้อมเพย์','cash_on_delivery'=>'เก็บเงินปลายทาง (COD)','credit_card'=>'บัตรเครดิต'] as $val => $label)
                            <option value="{{ $val }}" @selected($pm === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-12">
                    <label class="form-label">หมายเหตุ</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $customer->notes) }}</textarea>
                </div>
            </div>
        </div>

        {{-- 2. ที่อยู่ (จัดการแบบ List) --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <span><i class="fas fa-map-marker-alt"></i> รายการที่อยู่จัดส่ง</span>
                <button type="button" class="btn btn-sm btn-light text-success fw-bold" id="btn-add-address">
                    <i class="fas fa-plus-circle"></i> เพิ่มที่อยู่ใหม่
                </button>
            </div>
            <div class="card-body bg-light" id="addresses-wrapper">
                @php
                    $oldAddresses = old('addresses');
                    $addresses = $oldAddresses ?? $customer->addresses->map(function($a){
                        return [
                            'id'          => $a->id,
                            'name'        => $a->name,
                            'address'     => $a->address,
                            'soi'         => $a->soi,
                            'road'        => $a->road,
                            'subdistrict' => $a->subdistrict,
                            'district'    => $a->district,
                            'province'    => $a->province,
                            'postal_code' => $a->postal_code,
                        ];
                    })->toArray();
                @endphp

                @if(!empty($addresses))
                    @foreach($addresses as $i => $addr)
                        <div class="card mb-3 address-card shadow-sm border-0" data-index="{{ $i }}">
                            <div class="card-body position-relative">
                                <button type="button" class="btn-close position-absolute top-0 end-0 m-3 btn-remove-address" aria-label="Close"></button>
                                
                                @if(!empty($addr['id']))
                                    <input type="hidden" name="addresses[{{ $i }}][id]" value="{{ $addr['id'] }}">
                                @endif

                                <h6 class="card-title text-primary fw-bold mb-3">
                                    <i class="fas fa-home"></i> ข้อมูลที่อยู่ชุดที่ {{ $i + 1 }}
                                </h6>

                                <div class="row g-2">
                                    {{-- แถว 1: ชื่อเรียก + บ้านเลขที่ --}}
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">ชื่อสถานที่</label>
                                        <input type="text" class="form-control" name="addresses[{{ $i }}][name]" value="{{ $addr['name'] ?? '' }}" placeholder="เช่น บ้าน">
                                    </div>
                                    <div class="col-md-9">
                                        <label class="form-label small text-muted">บ้านเลขที่ / อาคาร / หมู่บ้าน <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="addresses[{{ $i }}][address]" value="{{ $addr['address'] ?? '' }}" required>
                                    </div>

                                    {{-- แถว 2: ซอย + ถนน --}}
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">ซอย</label>
                                        <input type="text" class="form-control" name="addresses[{{ $i }}][soi]" value="{{ $addr['soi'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">ถนน</label>
                                        <input type="text" class="form-control" name="addresses[{{ $i }}][road]" value="{{ $addr['road'] ?? '' }}">
                                    </div>

                                    {{-- แถว 3: ตำบล -> อำเภอ -> จังหวัด --}}
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">ตำบล/แขวง <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="addresses[{{ $i }}][subdistrict]" value="{{ $addr['subdistrict'] ?? '' }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">อำเภอ/เขต <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="addresses[{{ $i }}][district]" value="{{ $addr['district'] ?? '' }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">จังหวัด <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="addresses[{{ $i }}][province]" value="{{ $addr['province'] ?? '' }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">รหัสไปรษณีย์ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="addresses[{{ $i }}][postal_code]" value="{{ $addr['postal_code'] ?? '' }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-warning text-center">
                        ยังไม่มีข้อมูลที่อยู่เพิ่มเติม (กดปุ่ม "เพิ่มที่อยู่ใหม่" เพื่อสร้าง)
                    </div>
                @endif
            </div>
        </div>

        <input type="hidden" name="deleted_address_ids" id="deleted_address_ids">

        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-lg">ยกเลิก</a>
            <button type="submit" class="btn btn-success btn-lg px-5">
                <i class="fas fa-save"></i> บันทึกการแก้ไข
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function(){
    let addressIndex = (function(){
        const cards = document.querySelectorAll('#addresses-wrapper .address-card');
        let maxIdx = -1;
        cards.forEach(card => {
            const idx = parseInt(card.getAttribute('data-index') || '0', 10);
            if (!isNaN(idx) && idx > maxIdx) maxIdx = idx;
        });
        return maxIdx + 1;
    })();

    function escapeHtml(str){
        if (str === null || str === undefined) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    }

    function addAddressRow(data = {}) {
        const wrapper = document.getElementById('addresses-wrapper');
        const alertBox = wrapper.querySelector('.alert');
        if(alertBox) alertBox.remove();

        const idx = addressIndex++;

        const name        = data.name        || '';
        const address     = data.address     || '';
        const soi         = data.soi         || '';
        const road        = data.road        || '';
        const subdistrict = data.subdistrict || '';
        const district    = data.district    || '';
        const province    = data.province    || '';
        const postal_code = data.postal_code || '';

        const html = `
            <div class="card mb-3 address-card shadow-sm border-0" data-index="${idx}">
                <div class="card-body position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 btn-remove-address" aria-label="Close"></button>
                    
                    <h6 class="card-title text-success fw-bold mb-3">
                        <i class="fas fa-plus-circle"></i> ที่อยู่ใหม่ (รายการที่ ${idx + 1})
                    </h6>

                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label small text-muted">ชื่อสถานที่</label>
                            <input type="text" class="form-control bg-white" name="addresses[${idx}][name]" value="${escapeHtml(name)}" placeholder="เช่น บ้าน">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label small text-muted">บ้านเลขที่ / อาคาร / หมู่บ้าน <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-white" name="addresses[${idx}][address]" value="${escapeHtml(address)}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small text-muted">ซอย</label>
                            <input type="text" class="form-control bg-white" name="addresses[${idx}][soi]" value="${escapeHtml(soi)}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">ถนน</label>
                            <input type="text" class="form-control bg-white" name="addresses[${idx}][road]" value="${escapeHtml(road)}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-muted">ตำบล/แขวง <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-white" name="addresses[${idx}][subdistrict]" value="${escapeHtml(subdistrict)}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted">อำเภอ/เขต <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-white" name="addresses[${idx}][district]" value="${escapeHtml(district)}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted">จังหวัด <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-white" name="addresses[${idx}][province]" value="${escapeHtml(province)}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted">รหัสไปรษณีย์ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-white" name="addresses[${idx}][postal_code]" value="${escapeHtml(postal_code)}" required>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const div = document.createElement('div');
        div.innerHTML = html;
        wrapper.appendChild(div.firstElementChild);
    }

    document.getElementById('btn-add-address').addEventListener('click', function(){
        addAddressRow();
    });

    document.getElementById('addresses-wrapper').addEventListener('click', function(e){
        if (e.target.classList.contains('btn-remove-address')) {
            const card = e.target.closest('.address-card');
            if (!card) return;
            const idInput = card.querySelector('input[name$="[id]"]');
            if (idInput && idInput.value) {
                if(!confirm('ยืนยันที่จะลบที่อยู่นี้ออกจากฐานข้อมูล?')) return;
                const hidden = document.getElementById('deleted_address_ids');
                const current = hidden.value ? hidden.value.split(',') : [];
                current.push(idInput.value);
                hidden.value = current.join(',');
            }
            card.remove();
        }
    });
})();
</script>
@endpush