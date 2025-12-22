@extends('layouts.app')

@section('content')
<div class="container">
    <h1>สร้างออเดอร์ใหม่</h1>

    <form id="order-form" action="{{ route('orders.store') }}" method="POST"onsubmit="return prepareShippingAddressBeforeSubmit()">
        @csrf

       {{-- ================= ข้อมูลลูกค้า ================= --}}
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

            {{-- ✅ เก็บ id ลูกค้าที่เลือก (ถ้ามี) --}}
            <input type="hidden" name="customer_id" id="customer-id">
        </div>

        {{-- 2) ฟิลด์ข้อมูลลูกค้า --}}
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">ชื่อลูกค้า *</label>
                <input type="text" class="form-control" name="customer[name]" id="customer-name" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">เบอร์โทร</label>
                <input type="text" class="form-control" name="customer[phone]" id="customer-phone">
            </div>

            <div class="col-md-4">
                <label class="form-label">ช่องทางซื้อ *</label>
                <select class="form-select" name="customer[purchase_channel]" id="customer-channel" required>
                    <option value="">-- เลือกช่องทางซื้อ --</option>
                    <option value="facebook">Facebook</option>
                    <option value="line">Line</option>
                    <option value="website">เว็บไซต์</option>
                    <option value="shopee">Shopee</option>
                    <option value="lazada">Lazada</option>
                    <option value="offline">หน้าร้าน</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">วิธีชำระเงิน *</label>
                <select class="form-select" name="customer[payment_method]" id="customer-payment" required>
                    <option value="">-- เลือกวิธีชำระเงิน --</option>
                    <option value="bank_transfer">โอน/พร้อมเพย์</option>
                    <option value="cash_on_delivery">ชำระปลายทาง (COD)</option>
                    <option value="credit_card">บัตรเครดิต/เดบิต</option>
                    <option value="e_wallet">วอลเล็ต</option>
                </select>
            </div>
        </div>

        <hr>

        {{-- 3) ✅ เลือกที่อยู่จัดส่ง (Dropdown) --}}
        <div class="mb-3">
            <label class="form-label">เลือกที่อยู่จัดส่ง</label>
            <select id="shipping_address_id" name="shipping_address_id" class="form-select">
                <option value="">-- เลือกที่อยู่จัดส่ง --</option>
                <option value="__new__">+ เพิ่มที่อยู่ใหม่</option>
            </select>

            {{-- ✅ เก็บ id ที่อยู่เดิม ถ้าเลือก --}}
            <input type="hidden" name="existing_address_id" id="existing-address-id">
        </div>

        {{-- 4) ฟอร์ม "เพิ่มที่อยู่ใหม่" (ซ่อนไว้ตอนเริ่มต้น) --}}
        <div class="mb-3 border rounded p-3 d-none" id="new-address-wrapper">
            <h6 class="text-primary">เพิ่มที่อยู่ใหม่</h6>

            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">ชื่อที่อยู่</label>
                    <input type="text" name="new_address[name]" id="new-address-label"
                        class="form-control" placeholder="เช่น บ้าน, ที่ทำงาน">
                </div>
                <div class="col-md-9">
                    <label class="form-label">ที่อยู่ (รายละเอียด)</label>
                    <textarea name="new_address[address]" id="new-address-text"
                        class="form-control" rows="2"></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">ตำบล/แขวง</label>
                    <input type="text" name="new_address[district]" id="new-address-district"
                        class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">จังหวัด</label>
                    <input type="text" name="new_address[province]" id="new-address-province"
                        class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">รหัสไปรษณีย์</label>
                    <input type="text" name="new_address[postal_code]" id="new-address-postal"
                        class="form-control">
                </div>
            </div>
        </div>

        {{-- 5) ช่องสรุปที่อยู่ (อ่านอย่างเดียว) --}}
        <div class="mb-3">
            <label class="form-label">ที่อยู่จัดส่ง (สรุป)</label>
            <textarea class="form-control" id="customer-address-display"
                name="customer[address]" rows="2" readonly></textarea>
            <div class="form-text">
                ช่องนี้จะถูกเติมอัตโนมัติจากที่อยู่ที่เลือก / กรอกใหม่
            </div>
        </div>

        {{-- ✅ Hidden field ที่ OrderController ใช้จริง --}}
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
                    <input type="number" id="variant-quantity" class="form-control" value="1" min="1">
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
<script>
    let customerSearchTimer = null;

document.addEventListener('DOMContentLoaded', function () {
    const searchInput   = document.getElementById('customer-search');
    const addressSelect = document.getElementById('shipping_address_id');

    // ---- 🔍 ค้นหาลูกค้าแบบหน่วง 300ms ----
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.trim();
            clearTimeout(customerSearchTimer);

            if (term.length < 2) {
                document.getElementById('customer-search-results').innerHTML = '';
                return;
            }

            customerSearchTimer = setTimeout(() => searchCustomers(term), 300);
        });
    }

    // ---- 📍 เปลี่ยนที่อยู่จัดส่ง ----
    if (addressSelect) {
        addressSelect.addEventListener('change', onAddressSelectChange);
    }

    // ตอนเปิดหน้า → โหมดลูกค้าใหม่ (ซ่อนฟอร์มที่อยู่ใหม่)
    showNewAddressForm(false);
});

/**
 * ✅ เรียก API ค้นหาลูกค้า
 * GET /orders/customers/search?q=...
 */
function searchCustomers(term) {
    // ✅ ใช้ route name ที่ตรงกับ web.php
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

/**
 * ✅ เมื่อคลิกเลือกลูกค้าจากผลค้นหา
 */
function selectCustomerFromSearch(customer) {
    // 1) เติม customer_id
    document.getElementById('customer-id').value = customer.id;
    document.getElementById('customer-search').value = customer.name;
    document.getElementById('customer-search-results').innerHTML = '';

    // 2) เติมข้อมูลพื้นฐาน
    const nameInput    = document.getElementById('customer-name');
    const phoneInput   = document.getElementById('customer-phone');
    const channelInput = document.getElementById('customer-channel');
    const payInput     = document.getElementById('customer-payment');

    if (nameInput)  nameInput.value  = customer.name || '';
    if (phoneInput) phoneInput.value = customer.phone || '';
    if (channelInput && customer.purchase_channel) channelInput.value = customer.purchase_channel;
    if (payInput && customer.payment_method)       payInput.value     = customer.payment_method;

    // 3) โหลดที่อยู่ทั้งหมดของลูกค้า
    loadCustomerAddresses(customer.id);
}

/**
 * ✅ โหลดที่อยู่ของลูกค้า แล้วเติมลง <select id="shipping_address_id">
 * GET /orders/customers/{id}/addresses
 */
function loadCustomerAddresses(customerId) {
    // ✅ ใช้ URL ตรงตาม route
    const url = `/orders/customers/${customerId}/addresses`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            const sel            = document.getElementById('shipping_address_id');
            const newBlock       = document.getElementById('new-address-wrapper');
            const existingId     = document.getElementById('existing-address-id');
            const addrDisplay    = document.getElementById('customer-address-display');
            const shippingHidden = document.getElementById('shipping-address');

            if (!sel) return;

            // ล้าง option เก่า
            sel.innerHTML = '';
            sel.append(new Option('-- เลือกที่อยู่จัดส่ง --', ''));

            const addresses = data.addresses || [];

            if (addresses.length > 0) {
                // ✅ แสดงที่อยู่เก่าทั้งหมด
                addresses.forEach(a => {
                    const text = a.label || a.full_address || `ที่อยู่ #${a.id}`;
                    const opt = new Option(text, a.id);
                    opt.dataset.fullAddress = a.full_address || '';
                    sel.append(opt);
                });

                // ปุ่มเพิ่มที่อยู่ใหม่
                sel.append(new Option('+ เพิ่มที่อยู่ใหม่', '__new__'));

                // เริ่มต้น: ซ่อนฟอร์มเพิ่มใหม่
                if (newBlock)       newBlock.classList.add('d-none');
                if (existingId)     existingId.value = '';
                if (addrDisplay)    addrDisplay.value = '';
                if (shippingHidden) shippingHidden.value = '';
            } else {
                // ไม่มีที่อยู่ในระบบ → ใช้โหมดเพิ่มที่อยู่ใหม่
                sel.append(new Option('+ เพิ่มที่อยู่ใหม่', '__new__'));
                sel.value = '__new__';

                if (newBlock)       newBlock.classList.remove('d-none');
                if (existingId)     existingId.value = '';
                if (addrDisplay)    addrDisplay.value = '';
                if (shippingHidden) shippingHidden.value = '';
            }
        })
        .catch(err => {
            console.error('loadCustomerAddresses error:', err);
            alert('เกิดข้อผิดพลาดในการโหลดที่อยู่');
        });
}

/**
 * ✅ เวลาเปลี่ยน <select id="shipping_address_id">
 */
function onAddressSelectChange(e) {
    const value          = e.target.value;
    const existingId     = document.getElementById('existing-address-id');
    const addrDisplay    = document.getElementById('customer-address-display');
    const shippingHidden = document.getElementById('shipping-address');
    const newBlock       = document.getElementById('new-address-wrapper');

    if (value && value !== '__new__') {
        // ใช้ที่อยู่เดิม
        if (existingId) existingId.value = value;
        const full = e.target.selectedOptions[0].dataset.fullAddress || '';
        if (addrDisplay)    addrDisplay.value    = full;
        if (shippingHidden) shippingHidden.value = full;
        if (newBlock)       newBlock.classList.add('d-none');

    } else if (value === '__new__') {
        // สลับไปกรอกที่อยู่ใหม่
        if (existingId)     existingId.value = '';
        if (addrDisplay)    addrDisplay.value = '';
        if (shippingHidden) shippingHidden.value = '';
        if (newBlock)       newBlock.classList.remove('d-none');

    } else {
        // ไม่เลือกอะไร
        if (existingId)     existingId.value = '';
        if (addrDisplay)    addrDisplay.value = '';
        if (shippingHidden) shippingHidden.value = '';
    }
}

/**
 * ✅ Show/hide ฟอร์มเพิ่มที่อยู่ใหม่
 */
function showNewAddressForm(show) {
    const box = document.getElementById('new-address-wrapper');
    if (!box) return;
    if (show) box.classList.remove('d-none');
    else      box.classList.add('d-none');
}

/**
 * ✅ ก่อน submit: ถ้าไม่ได้เลือกที่อยู่เดิม ให้ประกอบที่อยู่จากฟอร์มใหม่
 */
function prepareShippingAddressBeforeSubmit() {
    const existingId     = document.getElementById('existing-address-id');
    const shippingHidden = document.getElementById('shipping-address');
    const addrDisplay    = document.getElementById('customer-address-display');

    // เลือกที่อยู่เดิมแล้ว → ไม่ต้องประกอบใหม่
    if (existingId && existingId.value) {
        return true;
    }

    const name = document.getElementById('new-address-label')?.value || '';
    const addr = document.getElementById('new-address-text')?.value || '';
    const dist = document.getElementById('new-address-district')?.value || '';
    const prov = document.getElementById('new-address-province')?.value || '';
    const zip  = document.getElementById('new-address-postal')?.value || '';

    const parts = [];
    if (addr) parts.push(addr);
    if (dist) parts.push(dist);
    if (prov) parts.push(prov);
    if (zip)  parts.push(zip);

    const full = parts.join(' ');
    if (shippingHidden) shippingHidden.value = full;
    if (addrDisplay)    addrDisplay.value    = full;

    return true;
    }
</script>


@endpush

@include('orders.partials.order-script')
@endsection