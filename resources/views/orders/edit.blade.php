@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>แก้ไขออเดอร์ #{{ $order->order_number ?? $order->id }}</h1>
        <div>
            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info me-2">
                <i class="fas fa-eye"></i> ดูรายละเอียด
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ✅ แก้ ID form เป็น order-form ให้ตรงกับ script --}}
    <form method="POST" action="{{ route('orders.update', $order->id) }}" id="order-form">
        @csrf
        @method('PUT')

        {{-- ================= ข้อมูลลูกค้า ================= --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="fw-bold">ข้อมูลลูกค้า</span>
                <div class="d-flex gap-2">
                    <span class="badge rounded-pill text-bg-secondary">
                        เลขที่ออเดอร์: {{ $order->order_number }}
                    </span>
                    <span class="badge rounded-pill
                        @switch(old('status', $order->status))
                          @case('pending') text-bg-warning @break
                          @case('processing') text-bg-info @break
                          @case('shipped') text-bg-primary @break
                          @case('delivered') text-bg-success @break
                          @case('cancelled') text-bg-danger @break
                          @default text-bg-secondary
                        @endswitch
                    ">
                        {{ ucfirst(old('status', $order->status)) }}
                    </span>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    {{-- ชื่อลูกค้า --}}
                    <div class="col-md-4">
                        <label class="form-label">ชื่อลูกค้า <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer[name]" 
                               value="{{ old('customer.name', $order->customer->name ?? '') }}" required>
                    </div>

                    {{-- เบอร์โทร --}}
                    <div class="col-md-4">
                        <label class="form-label">เบอร์โทร</label>
                        <input type="text" class="form-control" name="customer[phone]" 
                               value="{{ old('customer.phone', $order->customer->phone ?? '') }}">
                    </div>

                    {{-- อีเมล --}}
                    <div class="col-md-4">
                        <label class="form-label">อีเมล</label>
                        <input type="email" class="form-control" name="customer[email]" 
                               value="{{ old('customer.email', $order->customer->email ?? '') }}">
                    </div>

                    {{-- ช่องทางซื้อ --}}
                    <div class="col-md-6">
                        <label class="form-label">ช่องทางซื้อ <span class="text-danger">*</span></label>
                        @php
                        $channelOptions = [
                            'facebook' => 'Facebook', 'line' => 'Line', 'website' => 'เว็บไซต์',
                            'shopee' => 'Shopee', 'lazada' => 'Lazada', 'offline' => 'หน้าร้าน',
                        ];
                        $chRaw = old('customer.purchase_channel', $order->customer->purchase_channel ?? '');
                        @endphp
                        <select class="form-select" name="customer[purchase_channel]" required>
                            <option value="">-- เลือกช่องทางซื้อ --</option>
                            @foreach($channelOptions as $val => $label)
                            <option value="{{ $val }}" {{ $chRaw == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- วิธีชำระเงิน --}}
                    <div class="col-md-6">
                        <label class="form-label">วิธีชำระเงิน <span class="text-danger">*</span></label>
                        @php
                        $paymentOptions = [
                            'cash' => 'เงินสด (Cash)', 'bank_transfer' => 'โอน/พร้อมเพย์',
                            'cash_on_delivery' => 'ชำระปลายทาง (COD)', 'credit_card' => 'บัตรเครดิต/เดบิต',
                            'e_wallet' => 'วอลเล็ต',
                        ];
                        $pmRaw = old('customer.payment_method', $order->customer->payment_method ?? '');
                        @endphp
                        <select class="form-select" name="customer[payment_method]" required>
                            <option value="">-- เลือกวิธีชำระเงิน --</option>
                            @foreach($paymentOptions as $val => $label)
                            <option value="{{ $val }}" {{ $pmRaw == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ที่อยู่จัดส่ง --}}
                    <div class="col-md-12 mb-3">
                        <div class="card mb-3 address-card shadow-sm border-0">
                            <div class="card-body position-relative">
                                <h6 class="card-title text-success fw-bold mb-3">
                                    <i class="fas fa-map-marker-alt"></i> ที่อยู่จัดส่ง (แก้ไขได้)
                                </h6>
                                @php
                                    // Logic ดึงที่อยู่เดิม (คงเดิมไว้ตามไฟล์เก่า)
                                    $addr = ['name'=>'', 'address'=>'', 'soi'=>'', 'road'=>'', 'subdistrict'=>'', 'district'=>'', 'province'=>'', 'postal_code'=>''];
                                    if (is_numeric($order->shipping_address)) {
                                        $dbAddr = \App\Models\CustomerAddress::find($order->shipping_address);
                                        if ($dbAddr) {
                                            $addr['name'] = $dbAddr->name; $addr['address'] = $dbAddr->address;
                                            $addr['soi'] = $dbAddr->soi; $addr['road'] = $dbAddr->road;
                                            $addr['subdistrict'] = $dbAddr->subdistrict; $addr['district'] = $dbAddr->district;
                                            $addr['province'] = $dbAddr->province; $addr['postal_code'] = $dbAddr->postal_code;
                                        }
                                    } elseif ($order->customer && $order->customer->addresses->count() > 0) {
                                        $dbAddr = $order->customer->addresses->first();
                                        $addr['name'] = $dbAddr->name; $addr['address'] = $dbAddr->address;
                                        $addr['soi'] = $dbAddr->soi; $addr['road'] = $dbAddr->road;
                                        $addr['subdistrict'] = $dbAddr->subdistrict; $addr['district'] = $dbAddr->district;
                                        $addr['province'] = $dbAddr->province; $addr['postal_code'] = $dbAddr->postal_code;
                                    } else {
                                        $addr['address'] = $order->shipping_address;
                                    }
                                @endphp
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">ชื่อสถานที่</label>
                                        <input type="text" class="form-control" name="ship_name" value="{{ $addr['name'] }}">
                                    </div>
                                    <div class="col-md-9">
                                        <label class="form-label small text-muted">ที่อยู่ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="ship_address" value="{{ $addr['address'] }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">ซอย</label>
                                        <input type="text" class="form-control" name="ship_soi" value="{{ $addr['soi'] }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">ถนน</label>
                                        <input type="text" class="form-control" name="ship_road" value="{{ $addr['road'] }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">ตำบล/แขวง</label>
                                        <input type="text" class="form-control" name="ship_subdistrict" value="{{ $addr['subdistrict'] }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">อำเภอ/เขต</label>
                                        <input type="text" class="form-control" name="ship_district" value="{{ $addr['district'] }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">จังหวัด</label>
                                        <input type="text" class="form-control" name="ship_province" value="{{ $addr['province'] }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small text-muted">รหัสไปรษณีย์</label>
                                        <input type="text" class="form-control" name="ship_postal_code" value="{{ $addr['postal_code'] }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12"><hr></div>

                    <div class="col-md-4">
                        <label class="form-label">สถานะคำสั่งซื้อ</label>
                        <select class="form-select" name="status" required>
                            @php $status = old('status', $order->status); @endphp
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                            <option value="processing" {{ $status === 'processing' ? 'selected' : '' }}>กำลังจัดการ</option>
                            <option value="shipped" {{ $status === 'shipped' ? 'selected' : '' }}>จัดส่งแล้ว</option>
                            <option value="delivered" {{ $status === 'delivered' ? 'selected' : '' }}>ส่งสำเร็จ</option>
                            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">สถานะการชำระเงิน</label>
                        <select class="form-select" name="payment_status" required>
                            @php $pay = old('payment_status', $order->payment_status); @endphp
                            <option value="pending" {{ $pay === 'pending' ? 'selected' : '' }}>ยังไม่ชำระ</option>
                            <option value="paid" {{ $pay === 'paid' ? 'selected' : '' }}>ชำระแล้ว</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tracking Number</label>
                        <input type="text" class="form-control" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">ค่าจัดส่ง</label>
                        <input type="number" name="shipping_fee" class="form-control" value="{{ old('shipping_fee', $order->shipping_fee) }}" step="0.01">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">ส่วนลด</label>
                        <input type="number" name="discount" class="form-control" value="{{ old('discount', $order->discount) }}" step="0.01">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea class="form-control" name="notes" rows="2">{{ old('notes', $order->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= จัดการสินค้า (แก้ไขใหม่ให้เหมือนหน้า Create) ================= --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box"></i> จัดการสินค้า</h5>
            </div>
            <div class="card-body">
                {{-- ค้นหาสินค้า --}}
                <div class="mb-3">
                    <label class="form-label">ค้นหาสินค้า</label>
                    <input type="text" id="product-search" class="form-control" placeholder="ค้นหาชื่อสินค้า...">
                    <div id="search-results" class="mt-2"></div>
                </div>

                {{-- ตารางสินค้า --}}
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>รหัสสินค้า</th>
                                <th>สี-ไซส์</th>
                                <th width="120">จำนวน</th>
                                <th>ราคาต่อหน่วย</th>
                                <th>รวม</th>
                                <th width="80">ลบ</th>
                            </tr>
                        </thead>
                        <tbody id="order-items-body"></tbody>
                    </table>
                </div>

                <input type="hidden" name="items_json" id="items-json">
            </div>
        </div>

       {{-- ปุ่มจัดการ --}} 
       <div class="d-flex gap-2 mb-4"> <button type="button" class="btn btn-success" onclick="submitOrder()"> <i class="fas fa-save"></i> บันทึกการแก้ไข </button> <button type="button" class="btn btn-warning" onclick="resetForm()"> <i class="fas fa-undo"></i> รีเซ็ต </button> <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary"> <i class="fas fa-times"></i> ยกเลิก </a> </div>
    </form>
</div>

<div class="modal fade" id="variantModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style=" background-color: black;">
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
                        onkeypress="return event.charCode >= 48 && event.charCode <= 57">
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

@endsection

@push('scripts')
<script>
    // ==========================================
    // Script จัดการสินค้า (แก้ไขสำหรับ Edit)
    // ==========================================
    
    // เก็บสินค้าที่ถูกเลือกปัจจุบัน
    let selectedItems = [];
    let currentProduct = null;

    // ✅ เก็บข้อมูลตั้งต้นจาก Server ไว้เพื่อใช้ตอนกดปุ่ม "รีเซ็ต"
    const originalSavedItems = @json($order->orderItems);

    // ฟังก์ชันแปลงข้อมูลจาก Server เป็น Format ของตารางเรา
    function parseItems(items) {
        return items.map(item => {
            return {
                product_id: item.product_id,
                stock_id: item.product ? item.product.id_stock : '-', 
                product_name: item.product_name,
                unit_price: parseFloat(item.unit_price),
                quantity: parseInt(item.quantity),
                total_price: parseFloat(item.total_price),
                
                // ข้อมูล Variant
                color_id: item.color_id,
                size_id: item.size_id,
                color_name: item.color_name,
                size_name: item.size_name,
                variant_name: item.variant_name || `${item.color_name} - ${item.size_name}`,
                max_stock: 9999 // สินค้าเดิมให้ Max ไว้เยอะๆ ก่อน
            };
        });
    }

    // ✅ โหลดข้อมูลครั้งแรกเมื่อเข้าหน้าเว็บ
    document.addEventListener('DOMContentLoaded', function() {
        // โหลดข้อมูลใส่ selectedItems
        selectedItems = parseItems(originalSavedItems);
        renderOrderItems();
        setupEventListeners(); // เรียกตัวดักจับ Event
    });

    // ✅ ฟังก์ชันปุ่มรีเซ็ต (Reset Form)
    function resetForm() {
        if (!confirm('คุณต้องการรีเซ็ตข้อมูลทั้งหมดกลับเป็นค่าเริ่มต้นหรือไม่? ข้อมูลที่แก้ไขจะหายไป')) {
            return;
        }

        // 1. รีเซ็ตฟอร์ม HTML (ข้อมูลลูกค้า)
        document.getElementById('order-form').reset();

        // 2. รีเซ็ตรายการสินค้า กลับไปเป็นค่าตั้งต้น (originalSavedItems)
        selectedItems = parseItems(originalSavedItems);
        renderOrderItems();
    }

    // 1. แสดง modal เลือกสี-ไซส์
    function showVariantModal(id, name, price, id_stock) {
        currentProduct = { id, name, price, id_stock };
        document.getElementById('selected-product-name').textContent = name;

        fetch(`/products/${id}/variants`)
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('variant-select');
                select.innerHTML = '<option value="">-- เลือก --</option>';

                data.forEach(v => {
                    const availableQty = v.available || 0;
                    if (availableQty > 0) {
                        select.innerHTML += `<option 
                            value="${v.id}" 
                            data-stock="${availableQty}" 
                            data-color-id="${v.color_id}" 
                            data-size-id="${v.size_id}"
                            data-color-name="${v.color_name || v.color?.name || ''}"
                            data-size-name="${v.size_name || v.size?.name || ''}">
                            ${v.display_name} (จองได้ ${availableQty} ชิ้น)
                        </option>`;
                    }
                });

                new bootstrap.Modal(document.getElementById('variantModal')).show();
            });
    }

    // 2. ยืนยันการเลือกสินค้า
    function confirmAddProduct() {
        const select = document.getElementById('variant-select');
        const quantityInput = document.getElementById('variant-quantity');
        const quantity = parseInt(quantityInput.value);
        const variantId = parseInt(select.value);
        
        if (!variantId || quantity < 1) return alert('กรุณาเลือกสี-ไซส์และจำนวน');

        const option = select.options[select.selectedIndex];
        const stock = parseInt(option.dataset.stock);

        if (quantity > stock) return alert(`สต็อกไม่พอ มีแค่ ${stock}`);

        const colorId = parseInt(option.dataset.colorId);
        const sizeId = parseInt(option.dataset.sizeId);
        const colorName = option.getAttribute('data-color-name');
        const sizeName = option.getAttribute('data-size-name');
        const variantName = `${colorName} - ${sizeName}`;

        // เช็คซ้ำ
        if (selectedItems.some(i => i.product_id === currentProduct.id && i.color_id === colorId && i.size_id === sizeId)) {
            return alert('สินค้านี้ (สี-ไซส์เดียวกัน) ถูกเพิ่มแล้ว');
        }

        selectedItems.push({
            product_id: currentProduct.id,
            product_name: currentProduct.name,
            stock_id: currentProduct.id_stock,
            unit_price: currentProduct.price,
            quantity: quantity,
            total_price: currentProduct.price * quantity,
            color_id: colorId,
            size_id: sizeId,
            color_name: colorName,
            size_name: sizeName,
            variant_name: variantName,
            max_stock: stock
        });

        // Reset Modal Inputs
        select.value = "";
        quantityInput.value = 1;
        document.getElementById('stock-hint').textContent = "";

        bootstrap.Modal.getInstance(document.getElementById('variantModal')).hide();
        renderOrderItems();
    }

    // 3. แสดงรายการสินค้าในตาราง
    function renderOrderItems() {
        const tbody = document.getElementById('order-items-body');
        tbody.innerHTML = '';

        selectedItems.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.stock_id || '-'}</td>
                <td>${item.variant_name}</td>
                <td>
                    <input type="number" value="${item.quantity}" min="1" max="${item.max_stock}" 
                        onchange="updateQuantity(${index}, this.value)" class="form-control form-control-sm text-center">
                </td>
                <td>${parseFloat(item.unit_price).toFixed(2)}</td>
                <td>${parseFloat(item.total_price).toFixed(2)}</td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${index})"><i class="fas fa-trash"></i></button></td>
            `;
            tbody.appendChild(row);
        });

        document.getElementById('items-json').value = JSON.stringify(selectedItems);
    }

    // 4. แก้ไขจำนวน
    function updateQuantity(index, qty) {
        qty = parseInt(qty);
        if (qty < 1) {
            alert('จำนวนต้องอย่างน้อย 1 ชิ้น');
            renderOrderItems();
            return;
        }
        selectedItems[index].quantity = qty;
        selectedItems[index].total_price = qty * selectedItems[index].unit_price;
        renderOrderItems();
    }

    // 5. ลบสินค้า
    function removeItem(index) {
        if(!confirm('ต้องการลบสินค้านี้ใช่หรือไม่?')) return;
        selectedItems.splice(index, 1);
        renderOrderItems();
    }

    // 6. กดบันทึก
    function submitOrder() {
        renderOrderItems();
        if (selectedItems.length === 0) {
            alert('กรุณาเพิ่มสินค้าอย่างน้อย 1 รายการ');
            return;
        }
        document.getElementById('order-form').submit();
    }

    // รวม Event Listeners ไว้ในฟังก์ชันเดียว
    function setupEventListeners() {
        // แสดงสต็อกใน Modal
        const variantSelect = document.getElementById('variant-select');
        if (variantSelect) {
            variantSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                if(option.value === "") {
                     document.getElementById('stock-hint').textContent = '';
                     return;
                }
                const stock = option.dataset.stock;
                const stockHint = document.getElementById('stock-hint');
                
                if (stock) {
                    stockHint.textContent = `จองได้ทั้งหมด ${stock} ชิ้น`;
                    stockHint.style.color = '#28a745';
                }
            });
        }

        // ค้นหาสินค้า (AJAX)
        const searchInput = document.getElementById('product-search');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                let q = this.value.trim();
                if (q.length < 2) return document.getElementById('search-results').innerHTML = '';

                fetch(`/products/search?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(products => {
                        let html = '';
                        products.forEach(p => {
                            html += `
                                <div class="border p-2 d-flex justify-content-between mb-2 align-items-center">
                                    <div>
                                        <strong>${p.name}</strong><br>
                                        <small class="text-muted">รหัส: ${p.id_stock} | ราคา: ${p.price} บาท</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-success" 
                                        onclick="showVariantModal(${p.id}, '${p.name}', ${p.price}, '${p.id_stock}')">
                                        เลือก
                                    </button>
                                </div>`;
                        });
                        
                        if (html === '') {
                            html = '<div class="text-center text-muted p-3">ไม่พบสินค้า</div>';
                        }
                        
                        document.getElementById('search-results').innerHTML = html;
                    });
            });
        }
    }
</script>
@endpush