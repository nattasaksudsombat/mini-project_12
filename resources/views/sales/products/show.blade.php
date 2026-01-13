@extends('layouts.app')
{{-- เลือก Navbar ตามที่คุณต้องการ (เช่น navbarPD หรือ navbarDB) --}}
@include('layouts.navbarPD')

@section('content')


<main class="container py-4">
    {{-- ส่วนหัว --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">📦 รายละเอียดสินค้า (มุมมองฝ่ายขาย)</h3>
        <a href="{{ route('sales.products.index') }}" class="btn" style="background-color: #6c757d;">
            <i class="fas fa-arrow-left"></i> กลับ
        </a>
    </div>

    {{-- การ์ดแสดงรายละเอียดสินค้า --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <tr>
                    <th>รหัสสินค้า</th>
                    <td class="text-primary fw-bold">{{ $product->id_stock }}</td>
                    <th>ชื่อสินค้า</th>
                    <td>{{ $product->name }}</td>
                </tr>
                <tr>
                    <th>หมวดหมู่</th>
                    <td>{{ $product->category->category_name ?? '-' }}</td>
                    <th>ราคาขาย</th>
                    <td class="fw-bold text-success">{{ number_format($product->price, 2) }} ฿</td>
                </tr>
                <tr>
                    <th>สถานะ</th>
                    <td colspan="3">
                        @if($product->is_active)
                        <span class="status-badge bg-success"><i class="fas fa-check-circle"></i> เปิดขายอยู่</span>
                        @else
                        <span class="status-badge bg-secondary"><i class="fas fa-times-circle"></i> ปิดการขาย</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ส่วนแสดงรูปภาพ --}}
    @if($product->productImages->count() > 0)
    <div class="mb-4 p-3 bg-light rounded border">
        <h5 class="mb-3">🖼️ รูปภาพสินค้า</h5>
        <div class="d-flex gap-2 flex-wrap">
            @foreach($product->productImages as $img)
            <img src="{{ asset('storage/'.$img->image_url) }}" class="rounded shadow-sm" style="height: 120px; width: 120px; object-fit: cover; border: 2px solid white;">
            @endforeach
        </div>
    </div>
    @endif

    {{-- ตารางสต็อก (เพิ่มปุ่ม History และ Hold ตามที่ขอ) --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">📊 รายการสต็อก (สี / ไซส์)</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">รูป</th>
                        <th>สี</th>
                        <th>ไซส์</th>
                        <th class="text-center">คงเหลือ</th>
                        <th class="text-center">ติดจอง</th>
                        <th class="text-center" style="width: 250px;">ตรวจสอบ / ประวัติ</th> {{-- ขยายช่องนี้ --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->colorSizes as $variant)
                    <tr>
                        <td class="text-center">
                            @php
                            $variantImage = $product->productImages->where('color_id', $variant->color_id)->first();
                            @endphp
                            @if($variantImage)
                            <img src="{{ asset('storage/'.$variantImage->image_url) }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $variant->color->name ?? '-' }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $variant->size->size_name ?? '-' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold {{ $variant->quantity > 0 ? 'text-success' : 'text-danger' }}" style="font-size: 1.1em;">
                                {{ number_format($variant->quantity) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                            // ✅ ตรวจสอบว่ามีตาราง stock_holds หรือไม่
                            $heldQty = 0;
                            if (Schema::hasTable('stock_holds')) {
                            $heldQty = \App\Models\StockHold::where('product_color_size_id', $variant->id)
                            ->where('status', 'active')
                            ->sum('quantity');
                            }
                            @endphp
                            @if($heldQty > 0)
                            <span class="text-warning fw-bold">{{ number_format($heldQty) }}</span>
                            @else
                            <span class="text-muted text-sm">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">

                                {{-- ✅ 1. ปุ่มดูประวัติ - ลบการใช้ $scope ออก --}}
                                {{-- ✅ ใช้ค่าคงที่ 'all' --}}
                                <a href="{{ route('stock.variant.history', ['variant' => $variant->id, 'scope' => 'all']) }}"> class="btn btn-sm btn-info"
                                    target="_blank"
                                    title="ดูประวัติทั้งหมด">
                                    <i class="fas fa-history"></i> ประวัติ
                                </a>

                                {{-- ✅ 2. ปุ่มดูรายการจอง (Hold) --}}
                                @if($heldQty > 0)
                                <button type="button" class="btn btn-sm btn-warning"
                                    onclick="openHoldModal({{ $variant->id }}, '{{ $variant->color->name }}', '{{ $variant->size->size_name }}')">
                                    <i class="fas fa-hand-holding"></i> กำลังจอง
                                </button>
                                @else
                                <button type="button" class="btn btn-sm btn-secondary" disabled>
                                    <i class="fas fa-check"></i> ว่าง
                                </button>
                                @endif

                                {{-- ✅ 3. ปุ่มแก้ไข (Admin/Stock) --}}
                                @if(auth()->user()->hasAnyRole(['admin', 'stock']))
                                <a href="{{ route('product.colorSize.edit', ['product' => $product->id, 'colorSize' => $variant->id]) }}"
                                    class="btn btn-sm btn-primary"
                                    title="แก้ไข">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif

                                {{-- ✅ 4. ปุ่มปรับสต็อก (Admin/Stock) --}}
                                @if(auth()->user()->hasAnyRole(['admin', 'stock']))
                                <a href="{{ route('stock.adjust.form', ['variant' => $variant->id]) }}"
                                    class="btn btn-sm btn-success"
                                    title="ปรับสต็อก">
                                    <i class="fas fa-boxes"></i>
                                </a>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>ยังไม่มีรายการสต็อกสินค้า</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>

                {{-- ✅ Modal สำหรับดูรายการจอง --}}
                <div class="modal fade" id="holdModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-hand-holding text-warning"></i>
                                    รายการจองสต็อก: <span id="modalVariantName"></span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div id="holdsList">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">กำลังโหลด...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    // ✅ ฟังก์ชันเปิด Modal ดูรายการจอง
                    function openHoldModal(variantId, colorName, sizeName) {
                        const modal = new bootstrap.Modal(document.getElementById('holdModal'));
                        document.getElementById('modalVariantName').textContent = `${colorName} - ${sizeName}`;

                        // เรียก API ดูรายการจอง
                        fetch(`/api/stock/holds/${variantId}`)
                            .then(response => response.json())
                            .then(data => {
                                let html = '';

                                if (data.holds && data.holds.length > 0) {
                                    html = '<div class="table-responsive"><table class="table table-sm">';
                                    html += '<thead><tr>';
                                    html += '<th>Order</th>';
                                    html += '<th class="text-end">จำนวน</th>';
                                    html += '<th>สถานะ</th>';
                                    html += '<th>วันที่</th>';
                                    html += '</tr></thead><tbody>';

                                    data.holds.forEach(hold => {
                                        html += '<tr>';
                                        html += `<td><a href="/orders/${hold.order_id}" target="_blank">${hold.order_number}</a></td>`;
                                        html += `<td class="text-end">${hold.quantity}</td>`;
                                        html += `<td><span class="badge bg-${hold.status === 'pending' ? 'warning' : 'info'}">${hold.status}</span></td>`;
                                        html += `<td>${new Date(hold.updated_at).toLocaleDateString('th-TH')}</td>`;
                                        html += '</tr>';
                                    });

                                    html += '</tbody></table></div>';
                                } else {
                                    html = '<div class="alert alert-info">ไม่มีรายการจอง</div>';
                                }

                                document.getElementById('holdsList').innerHTML = html;
                            })
                            .catch(error => {
                                document.getElementById('holdsList').innerHTML = '<div class="alert alert-danger">เกิดข้อผิดพลาด</div>';
                                console.error(error);
                            });

                        modal.show();
                    }
                </script>

                <style>
                    .btn-history {
                        background-color: #6c757d;
                        color: white;
                    }

                    .btn-history:hover {
                        background-color: #5a6268;
                        color: white;
                    }

                    .btn-toggle {
                        background-color: #ffc107;
                        color: #000;
                    }

                    .btn-toggle:hover {
                        background-color: #e0a800;
                        color: #000;
                    }
                </style>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                <script>
                    // ฟังก์ชันตามชื่อที่คุณขอเป๊ะๆ
                    async function openHoldModal(variantId, colorName, sizeName) {
                        // อัปเดตหัวข้อ Modal ให้รู้ว่าดูตัวไหนอยู่
                        document.getElementById('modalVariantTitle').textContent = `${colorName} / ${sizeName}`;

                        const tbody = document.getElementById('holdsTableBody');
                        const sumEl = document.getElementById('totalHolds');

                        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3"><div class="spinner-border text-primary"></div> กำลังโหลดข้อมูล...</td></tr>';

                        // เปิด Modal ก่อนเลย
                        const modal = new bootstrap.Modal(document.getElementById('holdsModal'));
                        modal.show();

                        try {
                            // เรียก API ไปดึงข้อมูล Holds (ใช้ Endpoint เดิม)
                            const res = await fetch(`/api/variants/${variantId}/holds`);

                            if (!res.ok) throw new Error('ไม่สามารถดึงข้อมูลได้');

                            const data = await res.json();

                            tbody.innerHTML = '';
                            let sum = 0;

                            if (data.length === 0) {
                                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">ไม่พบข้อมูลออเดอร์ที่จองอยู่</td></tr>';
                            } else {
                                data.forEach(row => {
                                    sum += row.quantity;
                                    const tr = document.createElement('tr');

                                    const date = new Date(row.created_at).toLocaleDateString('th-TH', {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    });

                                    tr.innerHTML = `
                        <td>${date}</td>
                        <td class="fw-bold text-primary">${row.order_number}</td>
                        <td>${escapeHtml(row.customer_name)}</td>
                        <td class="text-center fw-bold">${row.quantity}</td>
                        <td class="text-center">${formatStatus(row.status)}</td>
                        <td class="text-center">
                            <a href="/orders/${row.order_id}" class="btn btn-sm btn-outline-info" target="_blank">
                                <i class="fas fa-eye"></i> ดู
                            </a>
                        </td>
                    `;
                                    tbody.appendChild(tr);
                                });
                            }
                            sumEl.textContent = sum.toLocaleString('th-TH');

                        } catch (err) {
                            console.error(err);
                            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">เกิดข้อผิดพลาด: ${err.message}</td></tr>`;
                        }
                    }

                    function escapeHtml(text) {
                        if (!text) return '-';
                        return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
                    }

                    function formatStatus(status) {
                        const map = {
                            'pending': '<span class="badge text-bg-warning">รอดำเนินการ</span>',
                            'processing': '<span class="badge text-bg-info">กำลังเตรียม</span>',
                            'shipped': '<span class="badge text-bg-primary">จัดส่งแล้ว</span>',
                            'delivered': '<span class="badge text-bg-success">สำเร็จ</span>',
                            'cancelled': '<span class="badge text-bg-danger">ยกเลิก</span>'
                        };
                        return map[status] || `<span class="badge text-bg-secondary">${status}</span>`;
                    }
                </script>
                @endsection