@extends('layouts.app')

@section('content')
<div class="container">
    <h1>สร้างออเดอร์ใหม่</h1>

    <form id="order-form" action="{{ route('orders.store') }}" method="POST"onsubmit="return prepareShippingAddressBeforeSubmit()">
        @csrf

        {{-- ================= ข้อมูลลูกค้า ================= --}}
{{-- ================= ข้อมูลลูกค้า (แก้ไขแล้ว) ================= --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold">ข้อมูลลูกค้า</span>
             
                <a href="{{ route('customers.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-users"></i> จัดการลูกค้า
                </a>
                
            </div>

            <div class="card-body">
                {{-- 1) ค้นหาลูกค้าเก่า --}}
                <div class="mb-3">
                    <label class="form-label">ค้นหาลูกค้า (ชื่อ / เบอร์โทร)</label>
                    <input type="text" id="customer-search" class="form-control"
                        placeholder="พิมพ์อย่างน้อย 2 ตัวอักษร เพื่อค้นหาลูกค้าเก่า...">
                    <div id="customer-search-results" class="list-group mt-1"></div>
                    <input type="hidden" name="customer_id" id="customer-id">
                </div>

                {{-- 2) ฟิลด์ข้อมูลลูกค้า --}}
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">ชื่อลูกค้า <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('customer.name') is-invalid @enderror" 
                            name="customer[name]" id="customer-name" value="{{ old('customer.name') }}" required>
                        @error('customer.name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">เบอร์โทร (ตัวเลข 10 หลัก)</label>
                        {{-- ✅ แก้ไข: ใส่ได้แค่ตัวเลข และห้ามเกิน 10 ตัว --}}
                        <input type="text" class="form-control @error('customer.phone') is-invalid @enderror" 
                            name="customer[phone]" id="customer-phone" value="{{ old('customer.phone') }}"
                            maxlength="10" 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('customer.phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">อีเมล</label>
                        <input type="email" class="form-control @error('customer.email') is-invalid @enderror" 
                            name="customer[email]" id="customer-email" value="{{ old('customer.email') }}">
                        @error('customer.email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">ช่องทางซื้อ <span class="text-danger">*</span></label>
                        <select class="form-select @error('customer.purchase_channel') is-invalid @enderror" 
                            name="customer[purchase_channel]" id="customer-channel" required>
                            <option value="">-- เลือกช่องทางซื้อ --</option>
                            <option value="facebook" {{ old('customer.purchase_channel') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                            <option value="line" {{ old('customer.purchase_channel') == 'line' ? 'selected' : '' }}>Line</option>
                            <option value="website" {{ old('customer.purchase_channel') == 'website' ? 'selected' : '' }}>เว็บไซต์</option>
                            <option value="shopee" {{ old('customer.purchase_channel') == 'shopee' ? 'selected' : '' }}>Shopee</option>
                            <option value="lazada" {{ old('customer.purchase_channel') == 'lazada' ? 'selected' : '' }}>Lazada</option>
                            <option value="offline" {{ old('customer.purchase_channel') == 'offline' ? 'selected' : '' }}>หน้าร้าน</option>
                        </select>
                        @error('customer.purchase_channel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">วิธีชำระเงิน <span class="text-danger">*</span></label>
                        <select class="form-select @error('customer.payment_method') is-invalid @enderror" 
                            name="customer[payment_method]" id="customer-payment" required>
                            <option value="">-- เลือกวิธีชำระเงิน --</option>
                            <option value="cash" {{ old('customer.payment_method') == 'cash' ? 'selected' : '' }}>เงินสด (Cash)</option>
                            <option value="bank_transfer" {{ old('customer.payment_method') == 'bank_transfer' ? 'selected' : '' }}>โอน/พร้อมเพย์</option>
                            <option value="cash_on_delivery" {{ old('customer.payment_method') == 'cash_on_delivery' ? 'selected' : '' }}>ชำระปลายทาง (COD)</option>
                            <option value="credit_card" {{ old('customer.payment_method') == 'credit_card' ? 'selected' : '' }}>บัตรเครดิต/เดบิต</option>
                            <option value="e_wallet" {{ old('customer.payment_method') == 'e_wallet' ? 'selected' : '' }}>วอลเล็ต</option>
                        </select>
                        @error('customer.payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                {{-- 3) เลือกที่อยู่จัดส่ง --}}
                <div class="mb-3">
                    <label class="form-label">เลือกที่อยู่จัดส่ง <span class="text-danger">*</span></label>
                    <select id="shipping_address_id" name="shipping_address_id" class="form-select">
                        <option value="">-- เลือกที่อยู่จัดส่ง --</option>
                        <option value="__new__">+ เพิ่มที่อยู่ใหม่</option>
                    </select>
                    <input type="hidden" name="existing_address_id" id="existing-address-id">
                </div>

                {{-- 4) ฟอร์มเพิ่มที่อยู่ใหม่ --}}
                <div class="mb-3 border rounded p-3 d-none" id="new-address-wrapper">
                    <h6 class="text-primary">เพิ่มที่อยู่ใหม่</h6>
                    <div class="row g-2">
                        <div class="col-md-12 mb-2">
                            <label class="form-label">ชื่อที่อยู่ (เช่น บ้าน, ที่ทำงาน)</label>
                            <input type="text" name="new_address[name]" id="new-address-label"
                                class="form-control" placeholder="ระบุชื่อเรียกสถานที่..." value="{{ old('new_address.name') }}">
                        </div>

                        {{-- ✅ ย้ายรหัสไปรษณีย์ขึ้นมาเพื่อให้กรอกก่อน --}}
                        <div class="col-md-4">
                            <label class="form-label">รหัสไปรษณีย์ <span class="text-danger">*</span></label>
                            <input type="text" name="new_address[postal_code]" id="new-address-postal"
                                class="form-control" placeholder="กรอกเลขไปรษณีย์..." value="{{ old('new_address.postal_code') }}" autocomplete="off">
                        </div>
                        <div class="col-md-8 d-flex align-items-center">
                            <small class="text-muted ms-2"><i class="fas fa-info-circle"></i> พิมพ์รหัสไปรษณีย์ ข้อมูลตำบล/อำเภอ/จังหวัด จะขึ้นอัตโนมัติ</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">ตำบล/แขวง <span class="text-danger">*</span></label>
                            <input type="text" name="new_address[subdistrict]" id="new-address-subdistrict"
                                class="form-control" value="{{ old('new_address.subdistrict') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">อำเภอ/เขต <span class="text-danger">*</span></label>
                            <input type="text" name="new_address[district]" id="new-address-district"
                                class="form-control" value="{{ old('new_address.district') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">จังหวัด <span class="text-danger">*</span></label>
                            <input type="text" name="new_address[province]" id="new-address-province"
                                class="form-control" value="{{ old('new_address.province') }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">ที่อยู่ (บ้านเลขที่ / หมู่ / ซอย / ถนน) <span class="text-danger">*</span></label>
                            <textarea name="new_address[address]" id="new-address-text"
                                class="form-control" rows="2" placeholder="บ้านเลขที่ หมู่ ซอย ถนน...">{{ old('new_address.address') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 5) สรุปที่อยู่ --}}
                <div class="mb-3">
                    <label class="form-label">ที่อยู่จัดส่ง (สรุป)</label>
                    <textarea class="form-control" id="customer-address-display"
                        name="customer[address]" rows="2" readonly>{{ old('customer.address') }}</textarea>
                    <div class="form-text">ช่องนี้จะถูกเติมอัตโนมัติจากที่อยู่ที่เลือก</div>
                </div>

                <input type="hidden" name="shipping_address" id="shipping-address">
            </div>
        </div>
    <div class="mb-3">

            <label>หมายเหตุ</label>

            <textarea name="notes" class="form-control" rows="2"></textarea>

        </div>
        <hr>

        <h5>ค้นหาสินค้า</h5>
        <input type="text" id="product-search" class="form-control" placeholder="ค้นหาชื่อสินค้า...">
        <div id="search-results" class="mt-2"></div>

        <h5 class="mt-4">สินค้าในออเดอร์</h5>
        <table class="table" id="order-items-table">
            <thead>
                <tr>
                    <th>สินค้า</th>
                    <th>สี-ไซส์</th>
                    <th>จำนวน</th>
                    <th>ราคาต่อหน่วย</th>
                    <th>รวม</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody id="order-items-body"></tbody>
        </table>

        <input type="hidden" name="items_json" id="items-json">

        <div class="mb-3">
            <label>ค่าจัดส่ง</label>
            <input type="number" name="shipping_fee" class="form-control" value="0" required>
        </div>
        <div class="mb-3">
            <label>ส่วนลด</label>
            <input type="number" name="discount" class="form-control" value="0">
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <button type="button" class="btn btn-success" onclick="submitOrder()">บันทึกออเดอร์</button>
    </form>
</div>

<!-- Modal เลือกสี-ไซส์ -->
<div class="modal fade" id="variantModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เลือกสี-ไซส์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <p><strong id="selected-product-name"></strong></p>
                <div class="mb-3">
                    <label>เลือกสี-ไซส์</label>
                    <select id="variant-select" class="form-control">
                        <option value="">-- เลือก --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>จำนวน</label>
                    <input type="number" id="variant-quantity" class="form-control" value="1" min="1" step="1" 
                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                        oninput="this.value = Math.floor(Math.abs(this.value))">
                    <small id="stock-hint" class="d-block mt-1 fw-bold"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary" onclick="confirmAddProduct()">เพิ่มสินค้า</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
{{-- ใส่ Script นี้ไว้ด้านล่างสุดของไฟล์ --}}
<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/JQL.min.js"></script>
<script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/typeahead.bundle.js"></script>
<link rel="stylesheet" href="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.css">
<script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.js"></script>

<script>
    $(document).ready(function() {
        // ✅ เปิดใช้งานระบบ Auto Complete ที่อยู่ไทย
        $.Thailand({
            $zipcode: $('#new-address-postal'),     // input ของรหัสไปรษณีย์
            $district: $('#new-address-subdistrict'), // input ของตำบล
            $amphoe: $('#new-address-district'),     // input ของอำเภอ
            $province: $('#new-address-province'),   // input ของจังหวัด
            
            // เมื่อเลือกข้อมูลเสร็จ ให้ทำอะไรต่อ?
            onDataFill: function(data){
                console.log('เลือกที่อยู่แล้ว:', data);
                // คุณอาจจะเพิ่ม logic ให้เคอร์เซอร์กระโดดไปช่อง "รายละเอียดที่อยู่" ต่อก็ได้
                $('#new-address-text').focus();
            }
        });
    });
</script>
<script>

document.addEventListener('DOMContentLoaded', function() {
    const customerSearch = document.getElementById('customer-search');
    const addressSelect = document.getElementById('shipping_address_id');
    let customerSearchTimer = null;

    // Customer Search
    if (customerSearch) {
        customerSearch.addEventListener('input', function(e) {
            clearTimeout(customerSearchTimer);
            const term = e.target.value.trim();

            if (term.length < 2) {
                document.getElementById('customer-search-results').innerHTML = '';
                return;
            }

            customerSearchTimer = setTimeout(() => searchCustomers(term), 300);
        });
    }

    // Address Select Change
    if (addressSelect) {
        addressSelect.addEventListener('change', onAddressSelectChange);
    }

    showNewAddressForm(false);
});

function searchCustomers(term) {
    const url = "{{ route('orders.customers.search') }}?q=" + encodeURIComponent(term);

    fetch(url)
        .then(r => r.json())
        .then(list => {
            const box = document.getElementById('customer-search-results');
            box.innerHTML = '';

            if (!Array.isArray(list) || !list.length) {
                box.innerHTML = '<div class="list-group-item small text-muted">ไม่พบลูกค้า</div>';
                return;
            }

            list.forEach(c => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action';
                btn.textContent = `${c.name} (${c.phone || '-'})`;
                btn.addEventListener('click', () => selectCustomerFromSearch(c));
                box.appendChild(btn);
            });
        })
        .catch(err => {
            console.error('searchCustomers error:', err);
            alert('เกิดข้อผิดพลาดในการค้นหาลูกค้า');
        });
}

function selectCustomerFromSearch(customer) {
    document.getElementById('customer-id').value = customer.id;
    document.getElementById('customer-search').value = customer.name;
    document.getElementById('customer-search-results').innerHTML = '';

    const nameInput = document.getElementById('customer-name');
    const phoneInput = document.getElementById('customer-phone');
    const emailInput = document.getElementById('customer-email');
    const channelInput = document.getElementById('customer-channel');
    const payInput = document.getElementById('customer-payment');

    if (nameInput) nameInput.value = customer.name || '';
    if (phoneInput) phoneInput.value = customer.phone || '';
    if (emailInput) emailInput.value = customer.email || '';
    if (channelInput && customer.purchase_channel) channelInput.value = customer.purchase_channel;
    if (payInput && customer.payment_method) payInput.value = customer.payment_method;

    loadCustomerAddresses(customer.id);
}

function loadCustomerAddresses(customerId) {
    const url = `/orders/customers/${customerId}/addresses`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById('shipping_address_id');
            const newBlock = document.getElementById('new-address-wrapper');
            const existingId = document.getElementById('existing-address-id');
            const addrDisplay = document.getElementById('customer-address-display');
            const shippingHidden = document.getElementById('shipping-address');

            if (!sel) return;

            sel.innerHTML = '';
            sel.append(new Option('-- เลือกที่อยู่จัดส่ง --', ''));

            const addresses = data.addresses || [];

            if (addresses.length > 0) {
                addresses.forEach(a => {
                    const text = a.label || a.full_address || `ที่อยู่ #${a.id}`;
                    const opt = new Option(text, a.id);
                    opt.dataset.fullAddress = a.full_address || '';
                    sel.append(opt);
                });

                sel.append(new Option('+ เพิ่มที่อยู่ใหม่', '__new__'));

                if (newBlock) newBlock.classList.add('d-none');
                if (existingId) existingId.value = '';
                if (addrDisplay) addrDisplay.value = '';
                if (shippingHidden) shippingHidden.value = '';
            } else {
                sel.append(new Option('+ เพิ่มที่อยู่ใหม่', '__new__'));
                sel.value = '__new__';

                if (newBlock) newBlock.classList.remove('d-none');
                if (existingId) existingId.value = '';
                if (addrDisplay) addrDisplay.value = '';
                if (shippingHidden) shippingHidden.value = '';
            }
        })
        .catch(err => {
            console.error('loadCustomerAddresses error:', err);
            alert('เกิดข้อผิดพลาดในการโหลดที่อยู่');
        });
}

function onAddressSelectChange(e) {
    const value = e.target.value;
    const existingId = document.getElementById('existing-address-id');
    const addrDisplay = document.getElementById('customer-address-display');
    const shippingHidden = document.getElementById('shipping-address');
    const newBlock = document.getElementById('new-address-wrapper');

    if (value && value !== '__new__') {
        if (existingId) existingId.value = value;
        const full = e.target.selectedOptions[0].dataset.fullAddress || '';
        if (addrDisplay) addrDisplay.value = full;
        if (shippingHidden) shippingHidden.value = full;
        if (newBlock) newBlock.classList.add('d-none');
    } else if (value === '__new__') {
        if (existingId) existingId.value = '';
        if (addrDisplay) addrDisplay.value = '';
        if (shippingHidden) shippingHidden.value = '';
        if (newBlock) newBlock.classList.remove('d-none');
    } else {
        if (existingId) existingId.value = '';
        if (addrDisplay) addrDisplay.value = '';
        if (shippingHidden) shippingHidden.value = '';
    }
}

function showNewAddressForm(show) {
    const box = document.getElementById('new-address-wrapper');
    if (!box) return;
    if (show) box.classList.remove('d-none');
    else box.classList.add('d-none');
}

function prepareShippingAddressBeforeSubmit() {
    const existingId = document.getElementById('existing-address-id');

    if (existingId && existingId.value) {
        return true;
    }

    const addr = document.getElementById('new-address-text')?.value || '';
    const subdist = document.getElementById('new-address-subdistrict')?.value || '';
    const dist = document.getElementById('new-address-district')?.value || '';
    const prov = document.getElementById('new-address-province')?.value || '';

    if (!addr || !subdist || !dist || !prov) {
        alert('⚠️ กรุณาเลือกที่อยู่จัดส่ง หรือกรอกที่อยู่ใหม่ให้ครบถ้วน');
        return false;
    }

    return true;
}

function submitOrder() {
    const form = document.getElementById('order-form');
    if (form) form.submit();
}
</script>


@endpush

@include('orders.partials.order-script')
@endsection