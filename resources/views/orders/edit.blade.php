@extends('layouts.app')

@section('content')
<div class="container">

    <!-- แสดงข้อมูลที่ซ่อนสำหรับ JavaScript -->
    <script type="application/json" id="order-data">
        @json($order)
    </script>
    <script type="application/json" id="products-data">
        @json($products ?? [])
    </script>
    <script type="application/json" id="items-data">
        @json($items ?? [])
    </script>

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

    <form method="POST" action="{{ route('orders.update', $order->id) }}" id="orderForm">
        @csrf
        @method('PUT')

        {{-- ==== ลูกค้า & ออเดอร์ ==== --}}
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

            <div class="card-body row g-3">
                {{-- ลูกค้า --}}
                <div class="col-md-4">
                    <label class="form-label">ชื่อลูกค้า *</label>
                    <input type="text" class="form-control" name="customer[name]"
                        value="{{ old('customer.name', $order->customer->name ?? '') }}" required>
                    @error('customer.name') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">เบอร์โทร</label>
                    <input type="text" class="form-control" name="customer[phone]"
                        value="{{ old('customer.phone', $order->customer->phone ?? '') }}">
                </div>

                {{-- ช่องทางซื้อ --}}
                <div class="col-md-4">
                    <label class="form-label">ช่องทางซื้อ *</label>
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
                    $channelBackMap = array_flip($channelOptions);
                    $chKey = $channelBackMap[$chRaw] ?? strtolower($chRaw);
                    @endphp
                    <select class="form-select" name="customer[purchase_channel]" required>
                        <option value="" disabled {{ $chRaw ? '' : 'selected' }}>-- เลือกช่องทางซื้อ --</option>
                        @foreach($channelOptions as $val => $label)
                        <option value="{{ $val }}" @selected($chKey===$val || $chRaw===$label)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('customer.purchase_channel') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">ที่อยู่จัดส่ง *</label>
                    <textarea class="form-control" name="customer[address]" rows="2" required>{{ old('customer.address', $order->customer->address ?? '') }}</textarea>
                    @error('customer.address') <div class="text-danger small">{{ $message }}</div> @enderror
                    <div class="form-text">หมายเหตุ: ระบบจะอัปเดตที่อยู่นี้ไปยัง <code>shipping_address</code> ของออเดอร์ด้วย</div>
                </div>

                {{-- วิธีชำระเงิน --}}
                <div class="col-md-3">
                    <label class="form-label">วิธีชำระเงิน *</label>
                    @php
                        $paymentOptions = [
                            'bank_transfer'    => 'โอน/พร้อมเพย์',
                            'cash_on_delivery' => 'ชำระปลายทาง (COD)',
                            'credit_card'      => 'บัตรเครดิต/เดบิต',
                            'e_wallet'         => 'วอลเล็ต',
                        ];
                        $pmRaw = old('customer.payment_method', $order->customer->payment_method ?? '');
                        $paymentBackMap = array_flip($paymentOptions);
                        $pmKey = $paymentBackMap[$pmRaw] ?? strtolower($pmRaw);
                    @endphp
                    <select class="form-select" name="customer[payment_method]" required>
                        <option value="" disabled {{ $pmRaw ? '' : 'selected' }}>-- เลือกวิธีชำระเงิน --</option>
                        @foreach($paymentOptions as $val => $label)
                            <option value="{{ $val }}" @selected($pmKey===$val || $pmRaw===$label)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('customer.payment_method') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                {{-- สถานะ & การชำระเงิน --}}
                <div class="col-md-3">
                    <label class="form-label">สถานะคำสั่งซื้อ *</label>
                    <select class="form-select" name="status" id="statusSelect" required>
                        @php $status = old('status', $order->status); @endphp
                        <option value="pending" @selected($status==='pending')>รอดำเนินการ</option>
                        <option value="processing" @selected($status==='processing')>กำลังจัดการ</option>
                        <option value="shipped" @selected($status==='shipped')>จัดส่งแล้ว</option>
                        <option value="delivered" @selected($status==='delivered')>ส่งสำเร็จ</option>
                        <option value="cancelled" @selected($status==='cancelled')>ยกเลิก</option>
                    </select>
                    @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">สถานะการชำระเงิน *</label>
                    <select class="form-select" name="payment_status" id="paymentSelect" required>
                        @php $pay = old('payment_status', $order->payment_status); @endphp
                        <option value="pending" @selected($pay==='pending')>ยังไม่ชำระ</option>
                        <option value="paid" @selected($pay==='paid')>ชำระแล้ว</option>
                    </select>
                    @error('payment_status') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                {{-- Tracking + หมายเหตุ --}}
                <div class="col-md-4">
                    <label class="form-label">เลขติดตามพัสดุ (Tracking Number)</label>
                    <input type="text" class="form-control" name="tracking_number"
                        value="{{ old('tracking_number', $order->tracking_number) }}"
                        placeholder="ระบุเลขพัสดุ (ถ้ามี)">
                </div>

                <div class="col-md-12">
                    <label class="form-label">หมายเหตุ</label>
                    <textarea class="form-control" name="notes" rows="2">{{ old('notes', $order->notes) }}</textarea>
                </div>

                <input type="hidden" id="discount" name="discount" value="0.00">
            </div>
        </div>

        <!-- การจัดการสินค้า -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box"></i> จัดการสินค้า</h5>
            </div>
            <div class="card-body">
                <!-- ค้นหาสินค้า -->
                <div class="mb-3">
                    <label class="form-label">ค้นหาสินค้าเพิ่มเติม</label>
                    <div class="input-group">
                        <input type="text" id="product-search" class="form-control" placeholder="ค้นหาชื่อสินค้า...">
                        <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="search-results" class="mt-2"></div>
                </div>

                <!-- รายการสินค้าในออเดอร์ -->
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle" id="order-items-table">
                        <thead class="table-light">
                            <tr>
                                <th width="20%">สินค้า</th>
                                <th width="12%">รหัสสินค้า</th>
                                <th width="12%">สี-ไซส์</th>
                                <th width="12%" class="text-center">จำนวน</th>
                                <th width="10%" class="text-end">ราคา/ชิ้น</th>
                                <th width="10%" class="text-end">รวม</th>
                                <th width="18%">สถานะสต๊อค</th>
                                <th width="6%">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="order-items-body">
                            <!-- รายการสินค้าจะถูกเพิ่มด้วย JavaScript -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end"><strong>รวมเป็นเงิน:</strong></td>
                                <td class="text-end"><strong id="subtotal-display">0.00</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <input type="hidden" name="items_json" id="items-json">
            </div>
        </div>

        <!-- ค่าใช้จ่ายเพิ่มเติม -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calculator"></i> ค่าใช้จ่ายเพิ่มเติม</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">ค่าจัดส่ง <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="shipping_fee" id="shipping-fee" class="form-control"
                                    value="{{ old('shipping_fee', number_format((float)($order->shipping_fee ?? 0), 2, '.', '')) }}"
                                    min="0" step="0.01" required>
                                <span class="input-group-text">บาท</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">ยอดรวมสุทธิ</label>
                            <div class="input-group">
                                <input type="text" id="total-amount" class="form-control" readonly>
                                <span class="input-group-text">บาท</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- แสดงข้อผิดพลาด -->
        @if ($errors->any())
        <div class="alert alert-danger">
            <h6><i class="fas fa-exclamation-triangle"></i> พบข้อผิดพลาด:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- ปุ่มบันทึก -->
        <div class="d-flex justify-content-between mb-4">
            <div>
                <button type="button" class="btn btn-warning" onclick="resetForm()">
                    <i class="fas fa-undo"></i> รีเซ็ต
                </button>
            </div>
            <div>
                <button type="button" class="btn btn-success" onclick="submitOrder()">
                    <i class="fas fa-save"></i> บันทึกการแก้ไข
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal เลือกสี-ไซส์ -->
<div class="modal fade" id="variantModal" tabindex="-1" aria-labelledby="variantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="variantModalLabel">
                    <i class="fas fa-palette"></i> เลือกสี-ไซส์
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong id="selected-product-name"></strong>
                </div>

                <div class="mb-3">
                    <label class="form-label">เลือกสี-ไซส์ <span class="text-danger">*</span></label>
                    <select id="variant-select" class="form-control" required>
                        <option value="">-- กรุณาเลือกสี-ไซส์ --</option>
                    </select>
                    <div class="form-text">คงเหลือ: <span id="stock-info">-</span> ชิ้น</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">จำนวน <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <button type="button" class="btn btn-outline-secondary" onclick="changeModalQuantity(-1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" id="variant-quantity" class="form-control text-center" value="1" min="1">
                        <button type="button" class="btn btn-outline-secondary" onclick="changeModalQuantity(1)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">ราคาต่อหน่วย</label>
                    <div class="input-group">
                        <input type="number" id="variant-price" class="form-control" step="0.01" min="0">
                        <span class="input-group-text">บาท</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> ยกเลิก
                </button>
                <button type="button" class="btn btn-primary" onclick="confirmAddProduct()">
                    <i class="fas fa-plus"></i> เพิ่มสินค้า
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .table th {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        font-weight: 600;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
        cursor: pointer;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }

    .input-group-sm .form-control {
        font-size: 0.875rem;
    }

    .card-header {
        background-color: #e9ecef;
        border-bottom: 1px solid #dee2e6;
    }

    .card-header h5 {
        color: #495057;
    }

    #search-results {
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        position: relative;
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .table-responsive {
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
        max-height: 600px;
        overflow-y: auto;
    }

    .btn .fa-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .form-control:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.025);
    }

    .modal-content {
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .input-group .btn.disabled,
    .input-group .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    .form-control:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
    }
    
    .text-success { color: #28a745 !important; }
    .text-warning { color: #ffc107 !important; }
    .text-danger { color: #dc3545 !important; }
    
    .input-group .btn:not(:disabled):hover {
        transform: scale(1.05);
        transition: transform 0.1s ease;
    }
    
    .input-group .btn:not(:disabled):active {
        transform: scale(0.95);
    }

    @media (max-width: 768px) {
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }

        .table-responsive {
            max-height: 400px;
        }

        .modal-dialog {
            margin: 0.5rem;
        }
    }
</style>

<script>
/* =========================================================
   Order Edit – Combined with Stock Management
   ========================================================= */

let selectedItems = [];
let currentProduct = null;
let products = [];
const orderId = {{ $order->id }};

/* -------------------- Helpers -------------------- */
const N = (x) => Number(x || 0);
const fmt = (n) => new Intl.NumberFormat('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(n);

function escapeHtml(x) {
    if (typeof x !== 'string') return '';
    return x.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

/* ---------------- Initialize ---------------- */
document.addEventListener('DOMContentLoaded', () => {
    initializeSystem();
});

function initializeSystem() {
    parseInitialJSON();
    renderOrderItems();
    calculateTotals();
    setupEventListeners();
}

function parseInitialJSON() {
    // Parse products
    const pEl = document.getElementById('products-data');
    if (pEl && pEl.textContent.trim()) {
        try { products = JSON.parse(pEl.textContent); } catch(e) { console.error(e); }
    }

    // Parse items (from backend with stock info)
    const iEl = document.getElementById('items-data');
    if (iEl && iEl.textContent.trim()) {
        try {
            const items = JSON.parse(iEl.textContent);
            selectedItems = items.map(it => ({
                id: it.id,
                product_id: it.product_id,
                product_name: it.product_name || it.name || 'สินค้าไม่ระบุ',
                unit_price: N(it.price || it.unit_price || 0),
                quantity: N(it.quantity || 1),
                total_price: N(it.price || 0) * N(it.quantity || 1),
                color_id: it.color_id,
                size_id: it.size_id,
                color_size_id: it.product_color_size_id || it.color_size_id,
                color_name: it.color_name || '',
                size_name: it.size_name || '',
                variant_name: it.variant_name || '',
                // Stock information from backend
                current_stock: N(it.current_stock || 0),
                reserved_stock: N(it.reserved_stock || 0),
                available_stock: N(it.available_stock || 0),
                quota_for_edit: N(it.quota_for_edit || 0),
                max_total_for_order: N(it.max_total_for_order || 0),
                is_existing_item: true
            }));
        } catch(e) { console.error(e); }
    }
}

/* ---------------- Event Listeners ---------------- */
function setupEventListeners() {
    const searchInput = document.getElementById('product-search');
    if (searchInput) {
        let searchTimer;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => searchProducts(e.target.value), 300);
        });
    }

    const shippingFee = document.getElementById('shipping-fee');
    if (shippingFee) {
        shippingFee.addEventListener('input', calculateTotals);
    }

    const variantSelect = document.getElementById('variant-select');
    if (variantSelect) {
        variantSelect.addEventListener('change', () => {
            updateStockInfo();
            updateVariantPrice();
        });
    }

    const modalQty = document.getElementById('variant-quantity');
    if (modalQty) {
        modalQty.addEventListener('input', enforceModalQuantityLimit);
        modalQty.addEventListener('change', enforceModalQuantityLimit);
    }
}

/* ---------------- Search Products ---------------- */
function searchProducts(query) {
    const resultsDiv = document.getElementById('search-results');
    if (!resultsDiv) return;
    
    if (!query || query.length < 2) {
        resultsDiv.innerHTML = '';
        return;
    }

    fetch(`/products/search?q=${encodeURIComponent(query)}`)
        .then(r => r.ok ? r.json() : Promise.reject(r.status))
        .then(list => {
            if (!Array.isArray(list) || !list.length) {
                resultsDiv.innerHTML = '<div class="alert alert-info">ไม่พบสินค้าที่ค้นหา</div>';
                return;
            }
            
            let html = '';
            list.forEach(p => {
                const price = N(p.price || 0).toLocaleString('th-TH', {minimumFractionDigits: 2});
                const name = escapeHtml(p.name || 'ไม่ระบุชื่อ');
                const sku = escapeHtml(p.id_stock || p.sku || 'ไม่ระบุ');
                html += `
                    <div class="border p-2 d-flex justify-content-between align-items-center mb-2">
                        <div><strong>${name}</strong><br><small>รหัส: ${sku} | ราคา: ${price} บาท</small></div>
                        <button type="button" class="btn btn-sm btn-success"
                            onclick="showVariantModal(${p.id}, '${name.replace(/'/g, "\\'")}', ${N(p.price || 0)})">เลือก</button>
                    </div>`;
            });
            resultsDiv.innerHTML = html;
        })
        .catch(() => {
            resultsDiv.innerHTML = '<div class="alert alert-warning">ค้นหาไม่สำเร็จ</div>';
        });
}

function clearSearch() {
    const searchInput = document.getElementById('product-search');
    const resultsDiv = document.getElementById('search-results');
    if (searchInput) searchInput.value = '';
    if (resultsDiv) resultsDiv.innerHTML = '';
}

/* ---------------- Variant Modal ---------------- */
async function showVariantModal(id, name, price) {
    currentProduct = { id, name, price: N(price || 0) };
    const el = document.getElementById('selected-product-name');
    if (el) el.textContent = name;
    
    try {
        const res = await fetch(`/products/${id}/variants`);
        const data = res.ok ? await res.json() : [];
        await populateVariantSelect(data, currentProduct.price);
        const modal = new bootstrap.Modal(document.getElementById('variantModal'));
        modal.show();
    } catch(e) {
        showAlert('โหลดรายการสี-ไซส์ไม่สำเร็จ', 'danger');
    }
}

async function populateVariantSelect(variants, defaultPrice) {
    const select = document.getElementById('variant-select');
    const priceInput = document.getElementById('variant-price');
    if (!select) return;
    
    select.innerHTML = '<option value="">-- เลือกสี-ไซส์ --</option>';
    
    // Fetch available stock for each variant (excluding current order)
    for (const v of variants) {
        const color = v.color_name || v.color?.name || 'สีมาตรฐาน';
        const size = v.size_name || v.size?.size_name || v.size?.name || 'ไซส์มาตรฐาน';
        
        // Get available stock for this variant
        const availableStock = await getAvailableStock(currentProduct.id, v.id);
        
        const opt = document.createElement('option');
        opt.value = v.id;
        opt.textContent = `${color} - ${size} (คงเหลือ: ${availableStock})`;
        opt.dataset.stock = String(availableStock);
        opt.dataset.colorId = String(v.color_id ?? '');
        opt.dataset.sizeId = String(v.size_id ?? '');
        opt.dataset.colorName = color;
        opt.dataset.sizeName = size;
        select.appendChild(opt);
    }
    
    if (priceInput) priceInput.value = Number(defaultPrice || 0).toFixed(2);
}

async function getAvailableStock(productId, variantId) {
    try {
        const res = await fetch(`/api/products/${productId}/variants/${variantId}/available?exclude_order_id=${orderId}`);
        if (!res.ok) return 0;
        const data = await res.json();
        return N(data.available || 0);
    } catch(e) {
        console.error('Error fetching available stock:', e);
        return 0;
    }
}

function updateStockInfo() {
    const sel = document.getElementById('variant-select');
    const stockInfo = document.getElementById('stock-info');
    const qty = document.getElementById('variant-quantity');
    if (!sel || !qty) return;
    
    if (!sel.value) {
        if (stockInfo) stockInfo.textContent = '-';
        qty.value = '0';
        qty.max = '';
        qty.disabled = true;
        qty.placeholder = 'เลือกสี-ไซส์ก่อน';
        return;
    }
    
    const st = N(sel.selectedOptions[0].dataset.stock || 0);
    if (stockInfo) stockInfo.textContent = String(st);
    qty.disabled = st <= 0;
    qty.max = st > 0 ? String(st) : '';
    qty.placeholder = `สต๊อก: ${st}`;
    if (!qty.value || N(qty.value) < 1) qty.value = st > 0 ? '1' : '0';
}

function enforceModalQuantityLimit() {
    const qty = document.getElementById('variant-quantity');
    if (!qty) return;
    const max = N(qty.max || 0);
    let val = N(qty.value || 0);
    if (!Number.isFinite(val) || val < 1) val = 1;
    if (max && val > max) {
        val = max;
        showAlert(`สต๊อกไม่พอ มีแค่ ${max} ชิ้น`, 'warning');
    }
    qty.value = String(val);
}

function updateVariantPrice() {
    const priceInput = document.getElementById('variant-price');
    if (priceInput && currentProduct) {
        priceInput.value = Number(currentProduct.price || 0).toFixed(2);
    }
}

function changeModalQuantity(delta) {
    const qty = document.getElementById('variant-quantity');
    if (!qty || qty.disabled) return;
    const max = N(qty.max || 0);
    let v = N(qty.value || 1) + N(delta);
    if (!Number.isFinite(v) || v < 1) v = 1;
    if (max && v > max) {
        v = max;
        showAlert(`สต๊อกไม่พอ มีแค่ ${max} ชิ้น`, 'warning');
    }
    qty.value = String(v);
}

function confirmAddProduct() {
    const sel = document.getElementById('variant-select');
    const qtyEl = document.getElementById('variant-quantity');
    const priceEl = document.getElementById('variant-price');
    if (!sel || !qtyEl || !priceEl) {
        alert('ไม่พบฟิลด์ในโมดอล');
        return;
    }

    const variantId = N(sel.value || 0);
    const qty = N(qtyEl.value || 0);
    const price = N(priceEl.value || 0);
    const opt = sel.selectedOptions[0];
    const stock = N(opt?.dataset.stock || 0);

    if (!variantId || qty < 1) {
        alert('กรุณาเลือกสี-ไซส์และจำนวน');
        return;
    }

    const colorId = opt.dataset.colorId ? N(opt.dataset.colorId) : null;
    const sizeId = opt.dataset.sizeId ? N(opt.dataset.sizeId) : null;
    const colorName = opt.dataset.colorName || 'สีมาตรฐาน';
    const sizeName = opt.dataset.sizeName || 'ไซส์มาตรฐาน';
    const variantName = `${colorName} - ${sizeName}`;

    // Check for duplicates
    const dup = selectedItems.find(i => 
        i.product_id === currentProduct.id && 
        i.color_id === colorId && 
        i.size_id === sizeId
    );
    if (dup) {
        alert('สินค้านี้ (สี-ไซส์เดียวกัน) ถูกเพิ่มแล้ว');
        return;
    }

    if (qty > stock) {
        alert(`สต๊อกไม่พอ มีแค่ ${stock} ชิ้น`);
        return;
    }

    // Add new item
    const newItem = {
        id: null, // New item, no ID yet
        product_id: currentProduct.id,
        product_name: currentProduct.name,
        unit_price: price,
        quantity: qty,
        total_price: price * qty,
        color_id: colorId,
        size_id: sizeId,
        color_size_id: variantId,
        color_name: colorName,
        size_name: sizeName,
        variant_name: variantName,
        current_stock: stock,
        reserved_stock: 0,
        available_stock: stock,
        quota_for_edit: stock,
        max_total_for_order: stock,
        is_existing_item: false
    };

    selectedItems.push(newItem);

    const modal = bootstrap.Modal.getInstance(document.getElementById('variantModal'));
    if (modal) modal.hide();
    
    renderOrderItems();
    calculateTotals();
    clearSearch();
    showAlert(`เพิ่มสินค้า "${currentProduct.name} - ${variantName}" สำเร็จ`, 'success');
}

/* ---------------- Render Order Items ---------------- */
function renderOrderItems() {
    const tbody = document.getElementById('order-items-body');
    if (!tbody) return;
    tbody.innerHTML = '';

    selectedItems.forEach((it, idx) => {
        const variantLabel = it.variant_name || 
            `${it.color_name || ''}${(it.color_name && it.size_name) ? ' - ' : ''}${it.size_name || ''}`;

        const hasVariant = it.color_id != null && it.size_id != null;
        const isDisabled = !hasVariant;
        const disabledAttr = isDisabled ? 'disabled' : '';
        const disabledClass = isDisabled ? 'disabled' : '';

        const tr = document.createElement('tr');
        tr.className = 'product-row';
        tr.dataset.index = idx;

        tr.innerHTML = `
            <td>
                <div class="fw-bold">${escapeHtml(it.product_name)}</div>
                <small class="text-muted">ID: ${it.product_id}</small>
            </td>

            <td>${it.product_id}</td>

            <td>
                <span class="badge bg-secondary">${escapeHtml(variantLabel)}</span>
                ${!hasVariant ? '<div class="text-danger small mt-1"><i class="fas fa-exclamation-triangle"></i> ข้อมูลไม่ครบ</div>' : ''}
            </td>

            <td>
                <div class="input-group input-group-sm">
                    <button type="button" 
                            class="btn btn-outline-secondary ${disabledClass}" 
                            onclick="changeItemQuantity(${idx}, -1)"
                            ${disabledAttr}>
                        <i class="fas fa-minus"></i>
                    </button>

                    <input type="number" 
                           class="form-control text-center qty-input"
                           name="items[${it.id || 'new_' + idx}][quantity]"
                           value="${it.quantity}"
                           min="1"
                           max="${it.max_total_for_order}"
                           data-max-total="${it.max_total_for_order}"
                           data-unit-price="${it.unit_price}"
                           oninput="enforceQtyLimit(this, ${idx})"
                           onchange="updateQuantity(${idx}, this.value)"
                           ${disabledAttr}>

                    <button type="button" 
                            class="btn btn-outline-secondary ${disabledClass}" 
                            onclick="changeItemQuantity(${idx}, 1)"
                            ${disabledAttr}>
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </td>

            <td class="text-end">${fmt(it.unit_price)}</td>
            <td class="text-end line-total">${fmt(it.total_price)}</td>

            <td>
                <small class="text-muted d-block">
                    TotalStock: ${it.current_stock} | Reserved: ${it.reserved_stock}
                </small>
                <small class="text-muted d-block">
                    Available: ${it.available_stock}
                </small>
                <small class="text-muted d-block">
                    คงเหลือ (โควต้าแก้ไขได้): <span class="quota">${it.quota_for_edit}</span> ชิ้น
                </small>
                <small class="text-muted d-block">
                    ตั้งรวมสูงสุด: ${it.max_total_for_order} ชิ้น
                </small>
            </td>

            <td>
                <button class="btn btn-danger btn-sm" 
                        onclick="removeItem(${idx})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    if (!selectedItems.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fas fa-box-open fa-2x mb-2"></i>
                    <div>ยังไม่มีสินค้าในออเดอร์</div>
                </td>
            </tr>`;
    }

    updateItemsJson();
}

function enforceQtyLimit(el, index) {
    if (index < 0 || index >= selectedItems.length) return;
    const it = selectedItems[index];
    
    if (!it.color_id || !it.size_id) {
        el.value = it.quantity || 1;
        return;
    }

    const maxTotal = N(el.dataset.maxTotal || it.max_total_for_order || 0);
    let val = N(el.value || 0);
    
    if (!Number.isFinite(val) || val < 1) {
        val = 1;
    }
    
    if (val > maxTotal) {
        val = maxTotal;
        showAlert(`สต็อกไม่พอ จำกัดที่ ${maxTotal} ชิ้น`, 'warning');
    }
    
    it.quantity = val;
    it.total_price = N(it.unit_price) * val;
    el.value = String(val);
    
    updateSingleRowDisplay(index);
}

function updateSingleRowDisplay(index) {
    const it = selectedItems[index];
    
    // Update total price in table
    const row = document.querySelector(`#order-items-body tr[data-index="${index}"]`);
    if (row) {
        const totalCell = row.querySelector('.line-total');
        if (totalCell) {
            totalCell.textContent = fmt(it.total_price);
        }
    }
    
    calculateTotals();
    updateItemsJson();
}

function changeItemQuantity(index, delta) {
    if (index < 0 || index >= selectedItems.length) return;
    const it = selectedItems[index];
    
    if (!it.color_id || !it.size_id) {
        showAlert('ไม่สามารถเปลี่ยนจำนวนได้ เนื่องจากข้อมูลสี-ไซส์ไม่ครบถ้วน', 'warning');
        return;
    }

    const currentQty = N(it.quantity);
    const newQty = currentQty + N(delta);
    const maxTotal = N(it.max_total_for_order);
    
    if (newQty < 1) {
        showAlert('จำนวนต้องไม่ต่ำกว่า 1', 'warning');
        return;
    }
    
    if (newQty > maxTotal) {
        showAlert(`สต็อกไม่พอ มีเพียง ${maxTotal} ชิ้น`, 'warning');
        return;
    }
    
    it.quantity = newQty;
    it.total_price = N(it.unit_price) * newQty;
    
    renderOrderItems();
    calculateTotals();
}

function updateQuantity(index, value) {
    if (index < 0 || index >= selectedItems.length) return;
    const it = selectedItems[index];
    
    if (!it.color_id || !it.size_id) {
        showAlert('ไม่สามารถเปลี่ยนจำนวนได้ เนื่องจากข้อมูลสี-ไซส์ไม่ครบถ้วน', 'warning');
        renderOrderItems();
        return;
    }
    
    let qty = N(value || 0);
    const maxTotal = N(it.max_total_for_order);
    
    if (qty < 1) {
        showAlert('จำนวนต้องไม่ต่ำกว่า 1', 'warning');
        qty = 1;
    }
    
    if (qty > maxTotal) {
        showAlert(`สต็อกไม่พอ มีเพียง ${maxTotal} ชิ้น`, 'warning');
        qty = maxTotal;
    }
    
    it.quantity = qty;
    it.total_price = N(it.unit_price) * qty;
    
    renderOrderItems();
    calculateTotals();
}

function removeItem(index) {
    const it = selectedItems[index];
    if (!confirm('คุณต้องการลบสินค้านี้ออกจากออเดอร์หรือไม่?')) return;

    selectedItems.splice(index, 1);
    renderOrderItems();
    calculateTotals();
    showAlert('ลบสินค้าเรียบร้อยแล้ว', 'success');
}

/* ---------------- Calculate Totals ---------------- */
function calculateTotals() {
    let subtotal = 0;
    
    document.querySelectorAll('.line-total').forEach(td => {
        const val = String(td.textContent).replace(/,/g, '');
        const num = Number(val);
        if (!isNaN(num)) subtotal += num;
    });

    const shippingEl = document.getElementById('shipping-fee');
    const shipping = shippingEl ? N(shippingEl.value) : 0;
    const discount = 0; // Hidden field, always 0
    const total = subtotal + shipping - discount;

    const subEl = document.getElementById('subtotal-display');
    const totalEl = document.getElementById('total-amount');
    if (subEl) subEl.textContent = fmt(subtotal);
    if (totalEl) totalEl.value = fmt(total);
}

function updateItemsJson() {
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
    
    const hidden = document.getElementById('items-json');
    if (hidden) hidden.value = JSON.stringify(payload);
}

/* ---------------- Form Actions ---------------- */
function resetForm() {
    if (confirm('รีเซ็ตข้อมูลกลับค่าเดิม?')) {
        location.reload();
    }
}

function submitOrder() {
    // Validate all items have complete IDs
    for (let i = 0; i < selectedItems.length; i++) {
        const it = selectedItems[i];
        if (!it.is_existing_item && (!it.color_id || !it.size_id || !it.color_size_id)) {
            alert(`สินค้าใหม่แถวที่ ${i + 1} ขาดข้อมูลสี-ไซส์`);
            return;
        }
    }
    
    updateItemsJson();
    
    const totalEl = document.getElementById('total-amount');
    const total = totalEl ? totalEl.value : '0';
    
    if (!confirm(`ต้องการบันทึกการแก้ไขหรือไม่?\nยอดรวม: ${total} บาท\nจำนวนแถว: ${selectedItems.length}`)) {
        return;
    }

    const btn = document.querySelector('button[onclick="submitOrder()"]');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังบันทึก...';
    }
    
    setTimeout(() => {
        const form = document.getElementById('orderForm');
        if (form) form.submit();
    }, 200);
}

/* ---------------- UI Helpers ---------------- */
function showAlert(message, type = 'info') {
    const div = document.createElement('div');
    div.className = `alert alert-${type} alert-dismissible fade show`;
    const icon = type === 'danger' ? 'exclamation-triangle' : 
                 (type === 'success' ? 'check-circle' : 'info-circle');
    div.innerHTML = `
        <i class="fas fa-${icon}"></i>
        ${escapeHtml(message)}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(div, container.firstChild);
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
    
    setTimeout(() => {
        if (div.parentNode) div.remove();
    }, 5000);
}
</script>

@endsection