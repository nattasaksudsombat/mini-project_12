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

    <form method="POST" action="{{ route('orders.update', $order->id) }}" id="orderForm">
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
                    <span id="statusBadge" class="badge rounded-pill
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
                    <span id="paymentBadge" class="badge rounded-pill
                        {{ old('payment_status', $order->payment_status)==='paid' ? 'text-bg-success' : 'text-bg-secondary' }}">
                        {{ old('payment_status', $order->payment_status)==='paid' ? 'ชำระแล้ว' : 'ยังไม่ชำระ' }}
                    </span>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    {{-- ชื่อลูกค้า --}}
                    <div class="col-md-4">
                        <label class="form-label">ชื่อลูกค้า <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('customer.name') is-invalid @enderror" 
                            name="customer[name]" value="{{ old('customer.name', $order->customer->name ?? '') }}" required>
                        @error('customer.name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- เบอร์โทร --}}
                    <div class="col-md-4">
                        <label class="form-label">เบอร์โทร</label>
                        <input type="text" class="form-control @error('customer.phone') is-invalid @enderror" 
                            name="customer[phone]" value="{{ old('customer.phone', $order->customer->phone ?? '') }}">
                        @error('customer.phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- อีเมล --}}
                    <div class="col-md-4">
                        <label class="form-label">อีเมล</label>
                        <input type="email" class="form-control @error('customer.email') is-invalid @enderror" 
                            name="customer[email]" value="{{ old('customer.email', $order->customer->email ?? '') }}">
                        @error('customer.email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ช่องทางซื้อ --}}
                    <div class="col-md-6">
                        <label class="form-label">ช่องทางซื้อ <span class="text-danger">*</span></label>
                        @php
                        $channelOptions = [
                            'facebook' => 'Facebook',
                            'line' => 'Line',
                            'website' => 'เว็บไซต์',
                            'shopee' => 'Shopee',
                            'lazada' => 'Lazada',
                            'offline' => 'หน้าร้าน',
                        ];
                        $chRaw = old('customer.purchase_channel', $order->customer->purchase_channel ?? '');
                        @endphp
                        <select class="form-select @error('customer.purchase_channel') is-invalid @enderror" 
                            name="customer[purchase_channel]" required>
                            <option value="">-- เลือกช่องทางซื้อ --</option>
                            @foreach($channelOptions as $val => $label)
                            <option value="{{ $val }}" {{ $chRaw == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('customer.purchase_channel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- วิธีชำระเงิน --}}
                    <div class="col-md-6">
                        <label class="form-label">วิธีชำระเงิน <span class="text-danger">*</span></label>
                        @php
                        $paymentOptions = [
                            'bank_transfer'    => 'โอน/พร้อมเพย์',
                            'cash_on_delivery' => 'ชำระปลายทาง (COD)',
                            'credit_card'      => 'บัตรเครดิต/เดบิต',
                            'e_wallet'         => 'วอลเล็ต',
                        ];
                        $pmRaw = old('customer.payment_method', $order->customer->payment_method ?? '');
                        @endphp
                        <select class="form-select @error('customer.payment_method') is-invalid @enderror" 
                            name="customer[payment_method]" required>
                            <option value="">-- เลือกวิธีชำระเงิน --</option>
                            @foreach($paymentOptions as $val => $label)
                            <option value="{{ $val }}" {{ $pmRaw == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('customer.payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ที่อยู่จัดส่ง --}}
                    <div class="col-md-12">
                        <label class="form-label">ที่อยู่จัดส่ง <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('customer.address') is-invalid @enderror" 
                            name="customer[address]" rows="3" required>{{ old('customer.address', $order->customer->address ?? '') }}</textarea>
                        @error('customer.address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">ที่อยู่นี้จะถูกอัปเดตเป็นที่อยู่จัดส่งของออเดอร์</div>
                    </div>

                    <div class="col-12"><hr></div>

                    {{-- สถานะคำสั่งซื้อ --}}
                    <div class="col-md-4">
                        <label class="form-label">สถานะคำสั่งซื้อ <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                            name="status" id="statusSelect" required>
                            @php $status = old('status', $order->status); @endphp
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                            <option value="processing" {{ $status === 'processing' ? 'selected' : '' }}>กำลังจัดการ</option>
                            <option value="shipped" {{ $status === 'shipped' ? 'selected' : '' }}>จัดส่งแล้ว</option>
                            <option value="delivered" {{ $status === 'delivered' ? 'selected' : '' }}>ส่งสำเร็จ</option>
                            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- สถานะการชำระเงิน --}}
                    <div class="col-md-4">
                        <label class="form-label">สถานะการชำระเงิน <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_status') is-invalid @enderror" 
                            name="payment_status" id="paymentSelect" required>
                            @php $pay = old('payment_status', $order->payment_status); @endphp
                            <option value="pending" {{ $pay === 'pending' ? 'selected' : '' }}>ยังไม่ชำระ</option>
                            <option value="paid" {{ $pay === 'paid' ? 'selected' : '' }}>ชำระแล้ว</option>
                        </select>
                        @error('payment_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tracking Number --}}
                    <div class="col-md-4">
                        <label class="form-label">เลขติดตามพัสดุ (Tracking Number)</label>
                        <input type="text" class="form-control" name="tracking_number"
                            value="{{ old('tracking_number', $order->tracking_number) }}"
                            placeholder="ระบุเลขพัสดุ (ถ้ามี)">
                    </div>

                    {{-- ค่าจัดส่ง --}}
                    <div class="col-md-4">
                        <label class="form-label">ค่าจัดส่ง</label>
                        <input type="number" name="shipping_fee" id="shipping-fee" class="form-control" 
                            value="{{ old('shipping_fee', $order->shipping_fee) }}" step="0.01" required 
                            onchange="calculateTotals()">
                    </div>

                    {{-- ส่วนลด --}}
                    <div class="col-md-4">
                        <label class="form-label">ส่วนลด</label>
                        <input type="number" name="discount" id="discount" class="form-control" 
                            value="{{ old('discount', $order->discount) }}" step="0.01" 
                            onchange="calculateTotals()">
                    </div>

                    {{-- ยอดรวมทั้งหมด (แสดงอย่างเดียว) --}}
                    <div class="col-md-4">
                        <label class="form-label">ยอดรวมทั้งหมด</label>
                        <input type="text" id="total-amount" class="form-control" readonly>
                    </div>

                    {{-- หมายเหตุ --}}
                    <div class="col-md-12">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea class="form-control" name="notes" rows="2">{{ old('notes', $order->notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= จัดการสินค้า ================= --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box"></i> จัดการสินค้า</h5>
            </div>
            <div class="card-body">
                {{-- ค้นหาสินค้า --}}
                <div class="mb-3">
                    <label class="form-label">ค้นหาสินค้าเพื่อเพิ่ม</label>
                    <input type="text" id="product-search" class="form-control" placeholder="ค้นหาชื่อสินค้า...">
                    <div id="search-results" class="mt-2"></div>
                </div>

                {{-- ตารางสินค้าในออเดอร์ --}}
                <div class="table-responsive">
                    <table class="table table-bordered" id="order-items-table">
                        <thead class="table-light">
                            <tr>
                                <th>สินค้า</th>
                                <th>สี-ไซส์</th>
                                <th>จำนวน</th>
                                <th>ราคาต่อหน่วย</th>
                                <th>รวม</th>
                                <th width="100">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="order-items-body"></tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end"><strong>ยอดรวมสินค้า:</strong></td>
                                <td colspan="2" id="subtotal-display">฿0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <input type="hidden" name="items_json" id="items-json">
            </div>
        </div>

        {{-- ปุ่มจัดการ --}}
        <div class="d-flex gap-2 mb-4">
            <button type="button" class="btn btn-success" onclick="submitOrder()">
                <i class="fas fa-save"></i> บันทึกการแก้ไข
            </button>
            <button type="button" class="btn btn-warning" onclick="resetForm()">
                <i class="fas fa-undo"></i> รีเซ็ต
            </button>
            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> ยกเลิก
            </a>
        </div>
    </form>
</div>

{{-- Modal เลือกสี-ไซส์ --}}
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="addItemToOrder()">เพิ่มสินค้า</button>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Data --}}
<script type="application/json" id="order-data">
    @json($order)
</script>
<script type="application/json" id="products-data">
    @json($products ?? [])
</script>
<script type="application/json" id="items-data">
    @json($items ?? [])
</script>
@endsection

@push('scripts')
<script>
// ============= Global Variables =============
let selectedItems = [];
let allProducts = [];
let currentProductForVariant = null;
const variantModal = new bootstrap.Modal(document.getElementById('variantModal'));

// ============= Initialization =============
document.addEventListener('DOMContentLoaded', function() {
    try {
        const orderData = JSON.parse(document.getElementById('order-data')?.textContent || '{}');
        const productsData = JSON.parse(document.getElementById('products-data')?.textContent || '[]');
        const itemsData = JSON.parse(document.getElementById('items-data')?.textContent || '[]');
        
        allProducts = productsData;
        
        // โหลดสินค้าในออเดอร์
        if (Array.isArray(itemsData) && itemsData.length > 0) {
            selectedItems = itemsData.map(it => ({
                id: it.id || null,
                product_id: it.product_id,
                product_name: it.product_name || it.name,
                color_id: it.color_id,
                size_id: it.size_id,
                color_name: it.color_name || '',
                size_name: it.size_name || '',
                variant_name: it.variant_name || `${it.color_name} - ${it.size_name}`,
                quantity: parseInt(it.quantity) || 1,
                unit_price: parseFloat(it.unit_price || it.price || 0),
                total_price: parseFloat(it.total_price || (it.quantity * it.unit_price)) || 0,
                color_size_id: it.color_size_id || it.product_color_size_id,
                max_total_for_order: it.max_total_for_order || 9999,
                is_existing_item: true
            }));
        }
        
        renderOrderItems();
        calculateTotals();
        
        // ค้นหาสินค้า
        const searchInput = document.getElementById('product-search');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                searchProducts(e.target.value);
            });
        }
        
        // Badge Updates
        document.getElementById('statusSelect')?.addEventListener('change', updateStatusBadge);
        document.getElementById('paymentSelect')?.addEventListener('change', updatePaymentBadge);
        
    } catch (err) {
        console.error('Init error:', err);
        showAlert('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + err.message, 'danger');
    }
});

// ============= Product Search =============
function searchProducts(term) {
    const resultsBox = document.getElementById('search-results');
    if (!resultsBox) return;
    
    if (!term || term.length < 2) {
        resultsBox.innerHTML = '';
        return;
    }
    
    const filtered = allProducts.filter(p => 
        p.name?.toLowerCase().includes(term.toLowerCase()) ||
        p.id_stock?.toLowerCase().includes(term.toLowerCase())
    );
    
    if (filtered.length === 0) {
        resultsBox.innerHTML = '<div class="alert alert-info">ไม่พบสินค้า</div>';
        return;
    }
    
    let html = '<div class="list-group">';
    filtered.forEach(p => {
        html += `
            <button type="button" class="list-group-item list-group-item-action" 
                    onclick="selectProductForOrder(${p.id})">
                <strong>${escapeHtml(p.name)}</strong> 
                <span class="badge bg-secondary">${escapeHtml(p.id_stock || '')}</span>
                <br><small class="text-muted">ราคา: ฿${Number(p.price || 0).toFixed(2)}</small>
            </button>
        `;
    });
    html += '</div>';
    resultsBox.innerHTML = html;
}

function selectProductForOrder(productId) {
    const product = allProducts.find(p => p.id == productId);
    if (!product) return;
    
    currentProductForVariant = product;
    document.getElementById('selected-product-name').textContent = product.name;
    
    const variantSelect = document.getElementById('variant-select');
    variantSelect.innerHTML = '<option value="">-- เลือก --</option>';
    
    if (Array.isArray(product.color_sizes) && product.color_sizes.length > 0) {
        product.color_sizes.forEach(cs => {
            const opt = document.createElement('option');
            opt.value = cs.id;
            opt.textContent = `${cs.color_name} - ${cs.size_name} (สต็อก: ${cs.stock})`;
            opt.dataset.colorId = cs.color_id;
            opt.dataset.sizeId = cs.size_id;
            opt.dataset.stock = cs.stock;
            opt.dataset.colorName = cs.color_name;
            opt.dataset.sizeName = cs.size_name;
            variantSelect.appendChild(opt);
        });
    }
    
    document.getElementById('variant-quantity').value = 1;
    variantModal.show();
    document.getElementById('search-results').innerHTML = '';
}

function addItemToOrder() {
    const variantSelect = document.getElementById('variant-select');
    const qtyInput = document.getElementById('variant-quantity');
    
    if (!variantSelect.value) {
        alert('กรุณาเลือกสี-ไซส์');
        return;
    }
    
    const selected = variantSelect.selectedOptions[0];
    const colorId = parseInt(selected.dataset.colorId);
    const sizeId = parseInt(selected.dataset.sizeId);
    const stock = parseInt(selected.dataset.stock || 0);
    const qty = parseInt(qtyInput.value || 1);
    
    if (qty > stock) {
        alert(`สต็อกไม่พอ มีเพียง ${stock} ชิ้น`);
        return;
    }
    
    const existingIndex = selectedItems.findIndex(it => 
        it.product_id == currentProductForVariant.id && 
        it.color_id == colorId && 
        it.size_id == sizeId
    );
    
    if (existingIndex >= 0) {
        selectedItems[existingIndex].quantity += qty;
        selectedItems[existingIndex].total_price = selectedItems[existingIndex].quantity * selectedItems[existingIndex].unit_price;
    } else {
        selectedItems.push({
            id: null,
            product_id: currentProductForVariant.id,
            product_name: currentProductForVariant.name,
            color_id: colorId,
            size_id: sizeId,
            color_name: selected.dataset.colorName,
            size_name: selected.dataset.sizeName,
            variant_name: `${selected.dataset.colorName} - ${selected.dataset.sizeName}`,
            quantity: qty,
            unit_price: parseFloat(currentProductForVariant.price || 0),
            total_price: qty * parseFloat(currentProductForVariant.price || 0),
            color_size_id: parseInt(variantSelect.value),
            max_total_for_order: stock,
            is_existing_item: false
        });
    }
    
    renderOrderItems();
    calculateTotals();
    variantModal.hide();
    showAlert('เพิ่มสินค้าสำเร็จ', 'success');
}

// ============= Render & Calculate =============
function renderOrderItems() {
    const tbody = document.getElementById('order-items-body');
    if (!tbody) return;
    
    if (selectedItems.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">ยังไม่มีสินค้า</td></tr>';
        return;
    }
    
    let html = '';
    selectedItems.forEach((it, idx) => {
        html += `
            <tr data-index="${idx}">
                <td>${escapeHtml(it.product_name)}</td>
                <td><span class="badge bg-info">${escapeHtml(it.variant_name)}</span></td>
                <td>
                    <div class="input-group input-group-sm" style="width: 150px;">
                        <button class="btn btn-outline-secondary" type="button" 
                                onclick="changeItemQuantity(${idx}, -1)">-</button>
                        <input type="number" class="form-control text-center" 
                               value="${it.quantity}" min="1"
                               onchange="updateQuantity(${idx}, this.value)">
                        <button class="btn btn-outline-secondary" type="button" 
                                onclick="changeItemQuantity(${idx}, 1)">+</button>
                    </div>
                </td>
                <td>฿${Number(it.unit_price).toFixed(2)}</td>
                <td class="line-total">฿${Number(it.total_price).toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" 
                            onclick="removeItem(${idx})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function changeItemQuantity(index, delta) {
    if (index < 0 || index >= selectedItems.length) return;
    const it = selectedItems[index];
    
    const newQty = it.quantity + delta;
    const maxTotal = it.max_total_for_order || 9999;
    
    if (newQty < 1) {
        showAlert('จำนวนต้องไม่ต่ำกว่า 1', 'warning');
        return;
    }
    
    if (newQty > maxTotal) {
        showAlert(`สต็อกไม่พอ มีเพียง ${maxTotal} ชิ้น`, 'warning');
        return;
    }
    
    it.quantity = newQty;
    it.total_price = it.unit_price * newQty;
    
    renderOrderItems();
    calculateTotals();
}

function updateQuantity(index, value) {
    if (index < 0 || index >= selectedItems.length) return;
    const it = selectedItems[index];
    
    let qty = parseInt(value || 0);
    const maxTotal = it.max_total_for_order || 9999;
    
    if (qty < 1) {
        showAlert('จำนวนต้องไม่ต่ำกว่า 1', 'warning');
        qty = 1;
    }
    
    if (qty > maxTotal) {
        showAlert(`สต็อกไม่พอ มีเพียง ${maxTotal} ชิ้น`, 'warning');
        qty = maxTotal;
    }
    
    it.quantity = qty;
    it.total_price = it.unit_price * qty;
    
    renderOrderItems();
    calculateTotals();
}

function removeItem(index) {
    if (!confirm('คุณต้องการลบสินค้านี้หรือไม่?')) return;
    selectedItems.splice(index, 1);
    renderOrderItems();
    calculateTotals();
    showAlert('ลบสินค้าเรียบร้อยแล้ว', 'success');
}

function calculateTotals() {
    let subtotal = 0;
    selectedItems.forEach(it => {
        subtotal += it.total_price;
    });
    
    const shippingEl = document.getElementById('shipping-fee');
    const discountEl = document.getElementById('discount');
    const shipping = parseFloat(shippingEl?.value || 0);
    const discount = parseFloat(discountEl?.value || 0);
    const total = subtotal + shipping - discount;
    
    const subEl = document.getElementById('subtotal-display');
    const totalEl = document.getElementById('total-amount');
    
    if (subEl) subEl.textContent = `฿${subtotal.toFixed(2)}`;
    if (totalEl) totalEl.value = `฿${total.toFixed(2)}`;
}

// ============= Form Actions =============
function resetForm() {
    if (confirm('รีเซ็ตข้อมูลกลับค่าเดิม?')) {
        location.reload();
    }
}

function submitOrder() {
    // Validate
    for (let i = 0; i < selectedItems.length; i++) {
        const it = selectedItems[i];
        if (!it.is_existing_item && (!it.color_id || !it.size_id || !it.color_size_id)) {
            alert(`สินค้าใหม่แถวที่ ${i + 1} ขาดข้อมูลสี-ไซส์`);
            return;
        }
    }
    
    // Update JSON
    const payload = selectedItems.map(it => ({
        id: it.id,
        product_id: it.product_id,
        name: it.product_name,
        product_name: it.product_name,
        quantity: it.quantity,
        unit_price: it.unit_price,
        price: it.unit_price,
        product_color_size_id: it.color_size_id,
        color_size_id: it.color_size_id,
        color_id: it.color_id,
        size_id: it.size_id,
        color_name: it.color_name,
        size_name: it.size_name,
        variant_name: it.variant_name,
        is_existing_item: !!it.is_existing_item
    }));
    
    document.getElementById('items-json').value = JSON.stringify(payload);
    
    const totalEl = document.getElementById('total-amount');
    const total = totalEl ? totalEl.value : '฿0.00';
    
    if (!confirm(`ต้องการบันทึกการแก้ไขหรือไม่?\nยอดรวม: ${total}\nจำนวนสินค้า: ${selectedItems.length} รายการ`)) {
        return;
    }
    
    const btn = document.querySelector('button[onclick="submitOrder()"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังบันทึก...';
    }
    
    setTimeout(() => {
        document.getElementById('orderForm').submit();
    }, 200);
}

// ============= UI Updates =============
function updateStatusBadge() {
    const select = document.getElementById('statusSelect');
    const badge = document.getElementById('statusBadge');
    const value = select.value;
    
    badge.className = 'badge rounded-pill';
    
    if (value === 'pending') badge.classList.add('text-bg-warning');
    else if (value === 'processing') badge.classList.add('text-bg-info');
    else if (value === 'shipped') badge.classList.add('text-bg-primary');
    else if (value === 'delivered') badge.classList.add('text-bg-success');
    else if (value === 'cancelled') badge.classList.add('text-bg-danger');
    else badge.classList.add('text-bg-secondary');
    
    badge.textContent = select.selectedOptions[0].text;
}

function updatePaymentBadge() {
    const select = document.getElementById('paymentSelect');
    const badge = document.getElementById('paymentBadge');
    const value = select.value;
    
    badge.className = 'badge rounded-pill';
    
    if (value === 'paid') {
        badge.classList.add('text-bg-success');
        badge.textContent = 'ชำระแล้ว';
    } else {
        badge.classList.add('text-bg-secondary');
        badge.textContent = 'ยังไม่ชำระ';
    }
}

function showAlert(message, type = 'info') {
    const div = document.createElement('div');
    div.className = `alert alert-${type} alert-dismissible fade show`;
    const icon = type === 'danger' ? 'exclamation-triangle' : 
                 (type === 'success' ? 'check-circle' : 'info-circle');
    div.innerHTML = `
        <i class="fas fa-${icon}"></i> ${escapeHtml(message)}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(div, container.firstChild);
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
    
    setTimeout(() => { if (div.parentNode) div.remove(); }, 5000);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endpush
@endsection