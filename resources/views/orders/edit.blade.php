@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>คำสั่งซื้อ #{{ $order->order_number ?? $order->id }}</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">← กลับ</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================= ข้อมูลลูกค้า ================= --}}
    <div class="card mb-3">
        <div class="card-header"><strong>ข้อมูลลูกค้า</strong></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>ชื่อลูกค้า:</strong> {{ $order->customer->name }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>เบอร์โทร:</strong> {{ $order->customer->phone ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>อีเมล:</strong> {{ $order->customer->email ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>ช่องทางการซื้อ:</strong> 
                        @php
                            $channelLabels = [
                                'facebook' => 'Facebook',
                                'line' => 'Line',
                                'website' => 'เว็บไซต์',
                                'shopee' => 'Shopee',
                                'lazada' => 'Lazada',
                                'offline' => 'หน้าร้าน',
                            ];
                        @endphp
                        {{ $channelLabels[$order->customer->purchase_channel] ?? ucfirst($order->customer->purchase_channel) }}
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>วิธีชำระเงิน:</strong> 
                        @php
                            $paymentLabels = [
                                'bank_transfer' => 'โอน/พร้อมเพย์',
                                'cash_on_delivery' => 'ชำระปลายทาง (COD)',
                                'credit_card' => 'บัตรเครดิต/เดบิต',
                                'e_wallet' => 'วอลเล็ต',
                            ];
                        @endphp
                        {{ $paymentLabels[$order->customer->payment_method] ?? ucfirst($order->customer->payment_method) }}
                    </p>
                </div>
                
                {{-- ✅ แก้ไขส่วนแสดงที่อยู่จัดส่ง ให้ดึงจาก customer_addresses ถ้ามี --}}
                <div class="col-md-12">
                    <p><strong>ที่อยู่จัดส่ง:</strong><br>
                        @if($order->customerAddress)
                            {{-- กรณีมีที่อยู่ในตาราง customer_addresses --}}
                            <strong>{{ $order->customerAddress->name }}</strong><br>
                            {{ $order->customerAddress->address }} 
                            {{ $order->customerAddress->subdistrict }} 
                            {{ $order->customerAddress->district }} 
                            {{ $order->customerAddress->province }} 
                            {{ $order->customerAddress->postal_code }}
                        @else
                            {{-- กรณีไม่มี (ใช้ที่อยู่ลูกค้าหลัก) --}}
                            {{ $order->customer->address }}
                        @endif
                    </p>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= ข้อมูลคำสั่งซื้อ ================= --}}
    <div class="card mb-3">
        <div class="m-4">
            <h5>Barcode Order ID</h5>
            <svg id="barcode"></svg>
        </div>
        <div class="card-header"><strong>ข้อมูลคำสั่งซื้อ</strong></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>สถานะคำสั่งซื้อ:</strong>
                        <span class="badge bg-{{ 
                            $order->status === 'cancelled' ? 'danger' : 
                            ($order->status === 'delivered' ? 'success' : 
                            ($order->status === 'shipped' ? 'primary' : 
                            ($order->status === 'processing' ? 'info' : 'warning'))) 
                        }}">
                            @switch($order->status)
                                @case('pending') รอดำเนินการ @break
                                @case('processing') กำลังจัดการ @break
                                @case('shipped') จัดส่งแล้ว @break
                                @case('delivered') ส่งสำเร็จ @break
                                @case('cancelled') ยกเลิก @break
                                @default {{ strtoupper($order->status) }}
                            @endswitch
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>สถานะการชำระเงิน:</strong>
                        <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'refunded' ? 'secondary' : 'warning') }}">
                            {{ $order->payment_status === 'paid' ? 'ชำระแล้ว' : ($order->payment_status === 'refunded' ? 'คืนเงินแล้ว' : 'ยังไม่ชำระ') }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Tracking Number:</strong>
                        @if ($order->tracking_number)
                        <span class="text-primary">{{ $order->tracking_number }}</span>
                        @else
                        <span class="text-muted">ยังไม่มี</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>วันที่สั่งซื้อ:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if($order->notes)
                <div class="col-md-12">
                    <p><strong>หมายเหตุ:</strong> {{ $order->notes }}</p>
                </div>
                @endif
            </div>

            @if($order->slip_image)
            <div class="mt-4">
                <h5>สลิปชำระเงิน</h5>
                <img src="{{ asset('storage/' . $order->slip_image) }}" class="img-fluid border rounded" style="max-width: 400px;">
            </div>
            @endif
        </div>
    </div>

    {{-- ================= รายการสินค้า ================= --}}
    <div class="card mb-3">
        <div class="card-header"><strong>รายการสินค้า</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>สินค้า</th>
                            <th>รหัสสินค้า</th>
                            <th>สี-ไซส์</th>
                            <th>จำนวน</th>
                            <th>ราคาต่อหน่วย</th>
                            <th>รวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->product->id_stock ?? '-' }}</td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $item->variant_name }}</span>
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>฿{{ number_format($item->unit_price, 2) }}</td>
                            <td>฿{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">ยอดรวมสินค้า:</th>
                            <th>฿{{ number_format($order->subtotal, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-end">ค่าจัดส่ง:</th>
                            <th>฿{{ number_format($order->shipping_fee, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-end">ส่วนลด:</th>
                            <th>฿{{ number_format($order->discount, 2) }}</th>
                        </tr>
                        <tr class="table-primary">
                            <th colspan="5" class="text-end h5">ยอดรวมทั้งหมด:</th>
                            <th class="h5 text-success">฿{{ number_format($order->total_price, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ================= ปุ่มจัดการ ================= --}}
    <div class="mt-4 d-flex gap-2 flex-wrap">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> พิมพ์ใบสั่งซื้อ
        </button>
        <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-info">
            <i class="fas fa-edit"></i> แก้ไขคำสั่งซื้อ
        </a>

        @if($order->payment_status === 'pending')
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
            <i class="fas fa-money-bill-wave"></i> ชำระเงิน / แนบสลิป
        </button>
        @endif

        @if($order->payment_status === 'paid' && $order->slip_image)
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#trackingModal">
            <i class="fas fa-truck"></i> เพิ่ม/แก้ไข Tracking Number
        </button>
        @endif

        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteOrderModal">
            <i class="fas fa-trash"></i> ลบออเดอร์
        </button>
    </div>
</div>

{{-- ================= Modal ชำระเงิน ================= --}}
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('orders.pay', $order->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">แนบสลิปชำระเงิน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="slip_image" class="form-label">อัปโหลดสลิป (JPG, PNG)</label>
                        <input type="file" class="form-control" name="slip_image" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success">บันทึก</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= Modal Tracking Number ================= --}}
<div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('orders.updateTracking', $order->id) }}">
    @csrf
    @method('PATCH') {{-- บรรทัดนี้สำคัญมาก --}}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="trackingModalLabel">เพิ่ม/แก้ไข Tracking Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tracking_number" class="form-label">Tracking Number</label>
                        <input type="text" class="form-control" name="tracking_number" id="tracking_number" 
                               value="{{ $order->tracking_number }}" placeholder="กรอกเลข Tracking">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success">บันทึก</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ================= Modal ยืนยันการลบออเดอร์ ================= --}}
<div class="modal fade" id="deleteOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ยืนยันการลบออเดอร์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>คำเตือน!</strong> การลบออเดอร์จะไม่สามารถย้อนกลับได้
                </div>
                <p>คุณแน่ใจหรือไม่ที่จะลบออเดอร์ <strong>#{{ $order->order_number }}</strong>?</p>
                <p class="text-muted">เมื่อลบแล้ว สินค้าทั้งหมดในออเดอร์จะถูกคืนสต็อกให้อัตโนมัติ</p>

                <h6>รายการสินค้าที่จะคืนสต็อก:</h6>
                <ul class="list-group list-group-flush">
                    @foreach($order->orderItems as $item)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $item->product_name }} ({{ $item->variant_name }})</span>
                        <span class="badge bg-info">+{{ $item->quantity }} ชิ้น</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">ยืนยันการลบ</button>
                </form>
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
<style>
    @media print {
        .btn, .card-header, nav, footer, .modal {
            display: none !important;
        }
        .container {
            max-width: 100% !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    // Render barcode จาก order number
    JsBarcode("#barcode", "{{ $order->order_number ?? $order->id }}", {
        format: "CODE128",
        width: 2,
        height: 60,
        displayValue: true
    });
</script>
<script>
// ============= Global Variables =============
let selectedItems = [];
let allProducts = [];
let currentProductForVariant = null;
let variantModal = null; // ย้ายมาประกาศและ init ใน DOMContentLoaded

// ============= Initialization =============
document.addEventListener('DOMContentLoaded', function() {
    // Init Modal
    const modalEl = document.getElementById('variantModal');
    if(modalEl) {
        variantModal = new bootstrap.Modal(modalEl);
    }

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
                // ✅ แก้ไข: เพิ่ม variant_id สำหรับ map กลับไปให้ Controller
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
        (p.name && p.name.toLowerCase().includes(term.toLowerCase())) ||
        (p.id_stock && p.id_stock.toLowerCase().includes(term.toLowerCase()))
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
    
    // ✅ แก้ไข: ดึงข้อมูล Color/Size จาก nested object (แก้ปัญหา undefined)
    // ใน Product::with(['colorSizes.color', 'colorSizes.size']) ตัวแปร colorSizes จะเป็น array
    const variants = product.color_sizes || product.colorSizes || []; // รองรับทั้ง snake_case และ camelCase

    if (Array.isArray(variants) && variants.length > 0) {
        variants.forEach(cs => {
            // ดึงชื่อสีและไซส์จาก relation
            const colorName = cs.color_name || (cs.color ? cs.color.name : '') || 'ไม่ระบุสี';
            const sizeName = cs.size_name || (cs.size ? (cs.size.size_name || cs.size.name) : '') || 'ไม่ระบุไซส์';
            const stock = cs.quantity || cs.stock || 0; // รองรับทั้ง quantity และ stock

            const opt = document.createElement('option');
            opt.value = cs.id; // นี่คือ variant_id (ProductColorSize ID)
            opt.textContent = `${colorName} - ${sizeName} (สต็อก: ${stock})`;
            
            // เก็บข้อมูลใส่ dataset
            opt.dataset.colorId = cs.color_id;
            opt.dataset.sizeId = cs.size_id;
            opt.dataset.stock = stock;
            opt.dataset.colorName = colorName;
            opt.dataset.sizeName = sizeName;
            
            variantSelect.appendChild(opt);
        });
    }
    
    document.getElementById('variant-quantity').value = 1;
    if(variantModal) variantModal.show();
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
    const variantId = parseInt(variantSelect.value); // ProductColorSize ID
    const colorId = parseInt(selected.dataset.colorId);
    const sizeId = parseInt(selected.dataset.sizeId);
    const stock = parseInt(selected.dataset.stock || 0);
    const qty = parseInt(qtyInput.value || 1);
    
    const colorName = selected.dataset.colorName || '-';
    const sizeName = selected.dataset.sizeName || '-';

    if (qty > stock) {
        alert(`สต็อกไม่พอ มีเพียง ${stock} ชิ้น`);
        return;
    }
    
    // เช็คว่ามีในรายการแล้วหรือยัง
    const existingIndex = selectedItems.findIndex(it => 
        it.product_id == currentProductForVariant.id && 
        it.color_size_id == variantId // เช็คจาก variant ID โดยตรงแม่นยำกว่า
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
            color_name: colorName,
            size_name: sizeName,
            variant_name: `${colorName} - ${sizeName}`,
            quantity: qty,
            unit_price: parseFloat(currentProductForVariant.price || 0),
            total_price: qty * parseFloat(currentProductForVariant.price || 0),
            color_size_id: variantId, // เก็บ ID นี้ไว้ส่งเป็น variant_id
            max_total_for_order: stock,
            is_existing_item: false
        });
    }
    
    renderOrderItems();
    calculateTotals();
    if(variantModal) variantModal.hide();
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
                    <div class="input-group input-group-sm" style="width: 120px;">
                        <button class="btn btn-outline-secondary" type="button" 
                                onclick="changeItemQuantity(${idx}, -1)">-</button>
                        <input type="number" class="form-control text-center" 
                               value="${it.quantity}" min="1" readonly>
                        <button class="btn btn-outline-secondary" type="button" 
                                onclick="changeItemQuantity(${idx}, 1)">+</button>
                    </div>
                </td>
                <td>฿${Number(it.unit_price).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="line-total">฿${Number(it.total_price).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
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
    
    // ถ้าเพิ่มสินค้า และเกินสต็อก (เฉพาะรายการใหม่ หรือ ถ้าระบบเช็คสต็อกแบบละเอียด)
    // ตรงนี้อาจต้องปรับ logic ถ้าเป็นสินค้าเก่าแล้วคืนสต็อก แต่เบื้องต้นเช็คคร่าวๆ
    if (!it.is_existing_item && newQty > maxTotal) {
        showAlert(`สต็อกไม่พอ มีเพียง ${maxTotal} ชิ้น`, 'warning');
        return;
    }
    
    it.quantity = newQty;
    it.total_price = it.unit_price * newQty;
    
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
    
    if (subEl) subEl.textContent = `฿${subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    if (totalEl) totalEl.value = `฿${total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
}

// ============= Form Actions =============
function resetForm() {
    if (confirm('รีเซ็ตข้อมูลกลับค่าเดิม?')) {
        location.reload();
    }
}

function submitOrder() {
    // Validate Items
    if (selectedItems.length === 0) {
        alert('กรุณาเพิ่มสินค้าอย่างน้อย 1 รายการ');
        return;
    }

    // Prepare JSON for Controller
    const payload = selectedItems.map(it => ({
        id: it.id,
        // ✅ ส่ง variant_id ไปให้ Controller ใช้หา ProductColorSize
        variant_id: it.color_size_id, 
        
        product_id: it.product_id,
        name: it.product_name, // Controller อาจจะเรียก name หรือ product_name
        product_name: it.product_name,
        
        quantity: it.quantity,
        price: it.unit_price,  // Controller อาจจะเรียก price หรือ unit_price
        unit_price: it.unit_price,
        
        color_id: it.color_id,
        size_id: it.size_id,
        color_name: it.color_name,
        size_name: it.size_name,
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
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endpush