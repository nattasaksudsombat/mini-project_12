@extends('layouts.app')

@section('content')
<div class="container">
    <h1>สร้างออเดอร์ใหม่</h1>

    <form id="order-form" action="{{ route('orders.store') }}" method="POST" onsubmit="return prepareShippingAddressBeforeSubmit()">
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
                        <label class="form-label">เบอร์โทร</label>
                        <input type="text" class="form-control @error('customer.phone') is-invalid @enderror" 
                            name="customer[phone]" id="customer-phone" value="{{ old('customer.phone') }}">
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
                        <div class="col-md-3">
                            <label class="form-label">ชื่อที่อยู่</label>
                            <input type="text" name="new_address[name]" id="new-address-label"
                                class="form-control" placeholder="เช่น บ้าน, ที่ทำงาน" value="{{ old('new_address.name') }}">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">ที่อยู่ (บ้านเลขที่ / หมู่ / ซอย / ถนน) <span class="text-danger">*</span></label>
                            <textarea name="new_address[address]" id="new-address-text"
                                class="form-control" rows="2">{{ old('new_address.address') }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">ตำบล/แขวง <span class="text-danger">*</span></label>
                            <input type="text" name="new_address[subdistrict]" id="new-address-subdistrict"
                                class="form-control" value="{{ old('new_address.subdistrict') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">อำเภอ/เขต <span class="text-danger">*</span></label>
                            <input type="text" name="new_address[district]" id="new-address-district"
                                class="form-control" value="{{ old('new_address.district') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">จังหวัด <span class="text-danger">*</span></label>
                            <input type="text" name="new_address[province]" id="new-address-province"
                                class="form-control" value="{{ old('new_address.province') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">รหัสไปรษณีย์ <span class="text-danger">*</span></label>
                            <input type="text" name="new_address[postal_code]" id="new-address-postal"
                                class="form-control" value="{{ old('new_address.postal_code') }}">
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

        {{-- ================= ค้นหาสินค้า ================= --}}
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
    // ============================================
    // ส่วนที่ 1: จัดการรายการสินค้า (สำคัญมากต้องอยู่บนสุด)
    // ============================================
    let selectedItems = [];
    let currentProduct = null;

    // แสดง modal เลือกสี-ไซส์ โดยโหลดข้อมูล variant จาก API
    function showVariantModal(id, name, price) {
        currentProduct = { id, name, price };
        document.getElementById('selected-product-name').textContent = name;
        document.getElementById('variant-quantity').value = 1; // Reset จำนวนเป็น 1

        fetch(`/products/${id}/variants`)
            .then(res => res.json())
            .then(data => {
                console.log('Variant data:', data); // 🔍 debug ข้อมูลที่โหลดมา

                const select = document.getElementById('variant-select');
                select.innerHTML = '<option value="">-- เลือก --</option>';

                if(Array.isArray(data)) {
                    data.forEach(v => {
                        // ปรับการอ่านค่า color/size ให้รองรับหลายรูปแบบ
                        const colorName = v.color_name || (v.color ? v.color.name : '') || '';
                        const sizeName = v.size_name || (v.size ? v.size.name : '') || (v.size ? v.size.size_name : '') || '';
                        const displayName = v.display_name || `${colorName} - ${sizeName}`;

                        select.innerHTML += `<option 
                            value="${v.id}" 
                            data-stock="${v.quantity}" 
                            data-color-id="${v.color_id}" 
                            data-size-id="${v.size_id}"
                            data-color-name="${colorName}"
                            data-size-name="${sizeName}">
                            ${displayName} (เหลือ: ${v.quantity})
                        </option>`;
                    });
                }

                new bootstrap.Modal(document.getElementById('variantModal')).show();
            })
            .catch(err => {
                console.error(err);
                alert('เกิดข้อผิดพลาดในการโหลดข้อมูลสินค้า');
            });
    }

    // ยืนยันการเลือกสินค้าและเพิ่มเข้า order
    function confirmAddProduct() {
        const select = document.getElementById('variant-select');
        const quantity = parseInt(document.getElementById('variant-quantity').value);
        const selectedOption = select.options[select.selectedIndex];

        if (!select.value || quantity < 1) {
            alert('กรุณาเลือกสินค้าและจำนวนให้ถูกต้อง');
            return;
        }

        const stock = parseInt(selectedOption.getAttribute('data-stock'));
        if (quantity > stock) {
            alert(`สินค้าคงเหลือไม่พอ (เหลือ ${stock})`);
            return;
        }

        // เพิ่มลง array
        selectedItems.push({
            product_id: currentProduct.id,
            variant_id: select.value,
            name: currentProduct.name,
            color_name: selectedOption.getAttribute('data-color-name'),
            size_name: selectedOption.getAttribute('data-size-name'),
            price: currentProduct.price,
            quantity: quantity,
            total: currentProduct.price * quantity
        });

        renderOrderItems();
        bootstrap.Modal.getInstance(document.getElementById('variantModal')).hide();
        document.getElementById('search-results').innerHTML = ''; // ปิดกล่องค้นหา
        document.getElementById('product-search').value = ''; // ล้างช่องค้นหา
    }

    // วาดตารางรายการสินค้า
    function renderOrderItems() {
        const tbody = document.getElementById('order-items-body');
        tbody.innerHTML = '';
        selectedItems.forEach((item, index) => {
            tbody.innerHTML += `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.color_name} - ${item.size_name}</td>
                    <td>${item.quantity}</td>
                    <td>${item.price}</td>
                    <td>${item.total}</td>
                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${index})">ลบ</button></td>
                </tr>`;
        });
        document.getElementById('items-json').value = JSON.stringify(selectedItems);
    }

    // ลบสินค้าออก
    function removeItem(index) {
        selectedItems.splice(index, 1);
        renderOrderItems();
    }

    // กดส่งออเดอร์
    function submitOrder() {
        renderOrderItems();
        if (selectedItems.length === 0) {
            alert('กรุณาเพิ่มสินค้าในออเดอร์ก่อน');
            return;
        }
        
        // เรียก function validate ที่อยู่ด้านล่าง
        if(!prepareShippingAddressBeforeSubmit()) {
            return;
        }

        document.getElementById('order-form').submit();
    }

    // ============================================
    // ส่วนที่ 2: Event Listeners และการจัดการลูกค้า
    // ============================================
    let customerSearchTimer = null;

    document.addEventListener('DOMContentLoaded', function () {
        const customerSearchInput = document.getElementById('customer-search');
        const productSearchInput = document.getElementById('product-search');
        const addressSelect = document.getElementById('shipping_address_id');

        // ---- 🔍 ค้นหาลูกค้า ----
        if (customerSearchInput) {
            customerSearchInput.addEventListener('input', function () {
                const term = this.value.trim();
                clearTimeout(customerSearchTimer);

                if (term.length < 2) {
                    document.getElementById('customer-search-results').innerHTML = '';
                    return;
                }

                customerSearchTimer = setTimeout(() => searchCustomers(term), 300);
            });
        }

        // ---- 🔍 ค้นหาสินค้า (ใช้ keyup ตามที่เคยขอมา) ----
        if (productSearchInput) {
            productSearchInput.addEventListener('keyup', function() {
                let q = this.value.trim();
                if (q.length < 2) return document.getElementById('search-results').innerHTML = '';

                // ใช้ route /products/search ที่มีอยู่แล้ว
                fetch(`{{ route('products.search') }}?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {
                        // รองรับทั้งแบบ array ตรงๆ และแบบ { products: [...] }
                        let products = data.products || data; 
                        let html = '';
                        
                        if(products.length === 0) {
                            html = '<div class="alert alert-secondary mt-2">ไม่พบสินค้า</div>';
                        } else {
                            products.forEach(p => {
                                html += `
                                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>${p.name}</strong><br>
                                            <small class="text-muted">รหัส: ${p.id_stock || '-'} | ราคา: ${p.price} บาท</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-success" onclick="showVariantModal(${p.id}, '${p.name}', ${p.price})">เลือก</button>
                                    </div>`;
                            });
                            html = `<div class="list-group mt-1">${html}</div>`;
                        }
                        document.getElementById('search-results').innerHTML = html;
                    })
                    .catch(err => console.error(err));
            });
        }

        // ---- 📍 เปลี่ยนที่อยู่จัดส่ง ----
        if (addressSelect) {
            addressSelect.addEventListener('change', onAddressSelectChange);
        }

        showNewAddressForm(false);
    });

    /**
     * ค้นหาลูกค้า
     */
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
            });
    }

    /**
     * เลือกลูกค้า
     */
    function selectCustomerFromSearch(customer) {
        document.getElementById('customer-id').value = customer.id;
        document.getElementById('customer-search').value = customer.name;
        document.getElementById('customer-search-results').innerHTML = '';

        const nameInput    = document.getElementById('customer-name');
        const phoneInput   = document.getElementById('customer-phone');
        const channelInput = document.getElementById('customer-channel');
        const payInput     = document.getElementById('customer-payment');

        if (nameInput)  nameInput.value  = customer.name || '';
        if (phoneInput) phoneInput.value = customer.phone || '';
        if (channelInput && customer.purchase_channel) channelInput.value = customer.purchase_channel;
        if (payInput && customer.payment_method)       payInput.value     = customer.payment_method;

        loadCustomerAddresses(customer.id);
    }

    /**
     * โหลดที่อยู่
     */
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

                    if (newBlock)       newBlock.classList.add('d-none');
                    if (existingId)     existingId.value = '';
                    if (addrDisplay)    addrDisplay.value = '';
                    if (shippingHidden) shippingHidden.value = '';
                } else {
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
            });
    }

    /**
     * เปลี่ยน Dropdown ที่อยู่
     */
    function onAddressSelectChange(e) {
        const value          = e.target.value;
        const existingId     = document.getElementById('existing-address-id');
        const addrDisplay    = document.getElementById('customer-address-display');
        const shippingHidden = document.getElementById('shipping-address');
        const newBlock       = document.getElementById('new-address-wrapper');

        if (value && value !== '__new__') {
            if (existingId) existingId.value = value;
            const full = e.target.selectedOptions[0].dataset.fullAddress || '';
            if (addrDisplay)    addrDisplay.value    = full;
            if (shippingHidden) shippingHidden.value = full;
            if (newBlock)       newBlock.classList.add('d-none');

        } else if (value === '__new__') {
            if (existingId)     existingId.value = '';
            if (addrDisplay)    addrDisplay.value = '';
            if (shippingHidden) shippingHidden.value = '';
            if (newBlock)       newBlock.classList.remove('d-none');

        } else {
            if (existingId)     existingId.value = '';
            if (addrDisplay)    addrDisplay.value = '';
            if (shippingHidden) shippingHidden.value = '';
        }
    }

    function showNewAddressForm(show) {
        const box = document.getElementById('new-address-wrapper');
        if (!box) return;
        if (show) box.classList.remove('d-none');
        else      box.classList.add('d-none');
    }

    /**
     * ตรวจสอบที่อยู่ก่อนส่งฟอร์ม
     */
    function prepareShippingAddressBeforeSubmit() {
        const existingId     = document.getElementById('existing-address-id');
        const shippingHidden = document.getElementById('shipping-address');
        const addrDisplay    = document.getElementById('customer-address-display');

        if (existingId && existingId.value) {
            return true;
        }

        const addr = document.getElementById('new-address-text')?.value || '';
        const subdist = document.getElementById('new-address-subdistrict')?.value || '';
        const dist = document.getElementById('new-address-district')?.value || '';
        const prov = document.getElementById('new-address-province')?.value || '';
        const zip  = document.getElementById('new-address-postal')?.value || '';

        // เช็คว่ากรอกครบไหม (ตัวอย่างเช็คแค่ที่อยู่กับจังหวัด)
        if(!addr || !prov) {
            alert('กรุณากรอกที่อยู่ให้ครบถ้วน หรือเลือกที่อยู่จัดส่ง');
            return false;
        }

        const parts = [];
        if (addr) parts.push(addr);
        if (subdist) parts.push('ต.' + subdist);
        if (dist)    parts.push('อ.' + dist);
        if (prov)    parts.push('จ.' + prov);
        if (zip)     parts.push(zip);

        const full = parts.join(' ');
        if (shippingHidden) shippingHidden.value = full;
        if (addrDisplay)    addrDisplay.value    = full;

        return true;
    }
</script>
@endpush
@endsection