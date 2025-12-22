{{-- resources/views/customers/edit.blade.php --}}
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

        {{-- ข้อมูลหลักของลูกค้า --}}
        <div class="card mb-4">
            <div class="card-header">
                <strong>ข้อมูลลูกค้า</strong>
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
                        $ch = old('purchase_channel', $customer->purchase_channel);
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
                        $pm = old('payment_method', $customer->payment_method);
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
                @php
                    // ถ้ามี old('addresses') (มาจาก validate fail) ให้ใช้ old แทน
                    $oldAddresses = old('addresses');
                    $addresses    = $oldAddresses ?? $customer->addresses->map(function($a){
                        return [
                            'id'          => $a->id,
                            'name'        => $a->name,
                            'address'     => $a->address,
                            'district'    => $a->district,
                            'province'    => $a->province,
                            'postal_code' => $a->postal_code,
                        ];
                    })->toArray();
                @endphp

                @if(!empty($addresses))
                    @foreach($addresses as $i => $addr)
                        <div class="border rounded p-3 mb-3 address-card" data-index="{{ $i }}">
                            {{-- ซ่อน id สำหรับ address เดิม --}}
                            @if(!empty($addr['id']))
                                <input type="hidden" name="addresses[{{ $i }}][id]" value="{{ $addr['id'] }}">
                            @endif

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>ที่อยู่ #{{ $i + 1 }}</strong>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-address">
                                    <i class="fas fa-trash-alt"></i> ลบที่อยู่นี้
                                </button>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">ชื่อสถานที่ (เช่น บ้าน, ที่ทำงาน)</label>
                                    <input type="text" class="form-control"
                                        name="addresses[{{ $i }}][name]"
                                        value="{{ $addr['name'] ?? '' }}">
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label">รายละเอียดที่อยู่</label>
                                    <textarea class="form-control" rows="2"
                                        name="addresses[{{ $i }}][address]">{{ $addr['address'] ?? '' }}</textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">ตำบล / แขวง</label>
                                    <input type="text" class="form-control"
                                        name="addresses[{{ $i }}][district]"
                                        value="{{ $addr['district'] ?? '' }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">อำเภอ / เขต</label>
                                    <input type="text" class="form-control"
                                        name="addresses[{{ $i }}][province]"
                                        value="{{ $addr['province'] ?? '' }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">รหัสไปรษณีย์</label>
                                    <input type="text" class="form-control"
                                        name="addresses[{{ $i }}][postal_code]"
                                        value="{{ $addr['postal_code'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- ถ้าไม่มีเลย ให้มี 1 แถวเปล่า (กรณีลูกค้าเก่าที่ไม่เคยเก็บที่อยู่แยก) --}}
                    <div class="border rounded p-3 mb-3 address-card" data-index="0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>ที่อยู่ #1</strong>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-address">
                                <i class="fas fa-trash-alt"></i> ลบที่อยู่นี้
                            </button>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">ชื่อสถานที่ (เช่น บ้าน, ที่ทำงาน)</label>
                                <input type="text" class="form-control"
                                    name="addresses[0][name]" value="">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">รายละเอียดที่อยู่</label>
                                <textarea class="form-control" rows="2"
                                    name="addresses[0][address]"></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">ตำบล / แขวง</label>
                                <input type="text" class="form-control"
                                    name="addresses[0][district]" value="">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">อำเภอ / เขต</label>
                                <input type="text" class="form-control"
                                    name="addresses[0][province]" value="">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">รหัสไปรษณีย์</label>
                                <input type="text" class="form-control"
                                    name="addresses[0][postal_code]" value="">
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- เก็บ id ที่ถูกลบไว้ เผื่อ Controller ใช้ลบใน DB (หากคุณรองรับ) --}}
        <input type="hidden" name="deleted_address_ids" id="deleted_address_ids">

        <div class="d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> บันทึกการแก้ไข
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
    // เริ่ม index ตามจำนวนที่อยู่ที่มีอยู่แล้ว
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
        return String(str)
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;')
            .replace(/'/g,'&#039;');
    }

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

    // ปุ่มเพิ่มที่อยู่ใหม่
    document.getElementById('btn-add-address').addEventListener('click', function(){
        addAddressRow();
    });

    // ปุ่มลบที่อยู่ (ใช้ event delegation)
    document.getElementById('addresses-wrapper').addEventListener('click', function(e){
        if (e.target.closest('.btn-remove-address')) {
            const card = e.target.closest('.address-card');
            if (!card) return;

            // ถ้าใน card นี้มี input id แปลว่าเป็นที่อยู่เดิมใน DB
            const idInput = card.querySelector('input[name$="[id]"]');
            if (idInput && idInput.value) {
                // เก็บ id ไว้ใน hidden deleted_address_ids
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
