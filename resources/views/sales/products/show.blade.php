@extends('layouts.app')
@include('layouts.navbarSalesPD')

@section('content')

<style>
    .btn { padding: 6px 12px; font-size: 14px; text-decoration: none; border-radius: 4px; color: white; border: none; cursor: pointer; }
    .btn-history { background-color: #fd7e14; color: white; }
    .btn-history:hover { background-color: #e36d0d; color: white; }
    .btn-hold { background-color: #6610f2; color: white; }
    .btn-hold:hover { background-color: #520dc2; color: white; }
    .btn-secondary { background-color: #6c757d; color: white; }
    .img-thumbnail { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
    .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; }
</style>

<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">📦 รายละเอียดสินค้า (มุมมองฝ่ายขาย)</h3>
        <a href="{{ route('sales.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> กลับ
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <tr>
                    <th style="width: 15%;">รหัสสินค้า</th>
                    <td class="text-primary fw-bold">{{ $product->id_stock }}</td>
                    <th style="width: 15%;">ชื่อสินค้า</th>
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
                            <span class="badge bg-success"><i class="fas fa-check-circle"></i> เปิดขายอยู่</span>
                        @else
                            <span class="badge bg-secondary"><i class="fas fa-times-circle"></i> ปิดการขาย</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

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
                        <th class="text-center" style="width: 250px;">ตรวจสอบ / ประวัติ</th>
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
                                <img src="{{ asset('storage/'.$variantImage->image_url) }}" class="img-thumbnail">
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
                                $heldQty = 0;
                                if (class_exists(\App\Models\stockHold::class)) {
                                    $heldQty = \App\Models\stockHold::where('product_color_size_id', $variant->id)
                                        ->where('status', 'active')
                                        ->sum('quantity');
                                } elseif (class_exists(\App\Models\StockHold::class)) {
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
                                <a href="{{ route('stock.variant.history', ['variant' => $variant->id, 'scope' => 'all']) }}" 
                                   class="btn btn-sm btn-history" 
                                   target="_blank" 
                                   title="ดูประวัติทั้งหมด">
                                    <i class="fas fa-history"></i> ประวัติ
                                </a>

                                @if($heldQty > 0)
                                    <button type="button" class="btn btn-sm btn-hold btn-show-holds" 
                                            data-variant-id="{{ $variant->id }}"
                                            data-color="{{ str_replace('"', '&quot;', $variant->color->name ?? '-') }}"
                                            data-size="{{ str_replace('"', '&quot;', $variant->size->size_name ?? '-') }}">
                                        <i class="fas fa-hand-holding"></i> กำลังจับ
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-secondary" disabled>
                                        ว่าง
                                    </button>
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
            </table>
        </div>
    </div>
</main>

<div class="modal fade" id="holdsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    🛒 ออเดอร์ที่กำลังจอง: <span id="modalVariantTitle"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>วันที่</th>
                                <th>เลขออเดอร์</th>
                                <th>ลูกค้า</th>
                                <th class="text-center">สถานะ</th>
                                <th class="text-center">จำนวน</th>
                                <th class="text-center">ดู</th>
                            </tr>
                        </thead>
                        <tbody id="holdsTableBody">
                            <tr><td colspan="6" class="text-center">กำลังโหลด...</td></tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="4" class="text-end">รวมกำลังจับทั้งหมด:</td>
                                <td class="text-center text-danger" id="totalHolds">-</td>
                                <td>ชิ้น</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Page loaded - Initializing holds modal...');
    
    // ฟังก์ชันเปิด Modal และดึงข้อมูล
    window.openHoldModal = async function(variantId, colorName, sizeName) {
        console.log('🔍 Opening modal for variant:', variantId);
        
        const modalElement = document.getElementById('holdsModal');
        const titleEl = document.getElementById('modalVariantTitle');
        const tbody = document.getElementById('holdsTableBody');
        const sumEl = document.getElementById('totalHolds');
        
        if (!modalElement || !tbody || !sumEl) {
            console.error('❌ Required elements not found!');
            return;
        }
        
        // ตั้งชื่อหัวข้อ Modal
        if (titleEl) {
            titleEl.textContent = colorName + ' / ' + sizeName;
        }
        
        // แสดง Loading
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3"><div class="spinner-border text-primary"></div> กำลังโหลดข้อมูล...</td></tr>';
        sumEl.textContent = '...';

        // เปิด Modal
        const modal = new bootstrap.Modal(modalElement);
        modal.show();

        try {
            const apiUrl = '/stock/api/holds/' + variantId;
            console.log('📡 Fetching from:', apiUrl);
            
            const res = await fetch(apiUrl);
            console.log('📥 Response status:', res.status);
            
            if (!res.ok) {
                const errorText = await res.text();
                console.error('❌ API Error:', errorText);
                throw new Error('HTTP ' + res.status);
            }
            
            const data = await res.json();
            console.log('✅ Received data:', data);
            
            tbody.innerHTML = '';
            let sum = 0;

            if (!Array.isArray(data) || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">ไม่พบข้อมูลออเดอร์ที่จองอยู่</td></tr>';
            } else {
                data.forEach(function(row) {
                    sum += parseInt(row.quantity) || 0;
                    
                    const date = new Date(row.created_at).toLocaleDateString('th-TH', {
                        year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute:'2-digit'
                    });

                    const tr = document.createElement('tr');
                    tr.innerHTML = 
                        '<td>' + date + '</td>' +
                        '<td class="fw-bold text-primary">' + escapeHtml(row.order_number) + '</td>' +
                        '<td>' + escapeHtml(row.customer_name) + '</td>' +
                        '<td class="text-center">' + formatStatus(row.status) + '</td>' +
                        '<td class="text-end fw-bold text-warning">' + row.quantity.toLocaleString() + '</td>' +
                        '<td class="text-center">' +
                            '<a href="/orders/' + row.order_id + '" class="btn btn-sm btn-outline-info" target="_blank">' +
                                '<i class="fas fa-eye"></i> ดู' +
                            '</a>' +
                        '</td>';
                    tbody.appendChild(tr);
                });
            }
            
            sumEl.textContent = sum.toLocaleString('th-TH');

        } catch (err) {
            console.error('💥 Error:', err);
            tbody.innerHTML = 
                '<tr><td colspan="6" class="text-center text-danger py-4">' +
                    '<i class="fas fa-exclamation-triangle fa-2x mb-2"></i>' +
                    '<p class="mb-0">เกิดข้อผิดพลาด: ' + err.message + '</p>' +
                '</td></tr>';
            sumEl.textContent = '0';
        }
    };

    function escapeHtml(text) {
        if (!text) return '-';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatStatus(status) {
        const map = {
            'pending': '<span class="badge bg-warning text-dark">รอดำเนินการ</span>',
            'processing': '<span class="badge bg-info text-dark">กำลังเตรียม</span>',
            'shipped': '<span class="badge bg-primary">จัดส่งแล้ว</span>',
            'delivered': '<span class="badge bg-success">สำเร็จ</span>',
            'cancelled': '<span class="badge bg-danger">ยกเลิก</span>',
            'pending_payment': '<span class="badge bg-warning">รอชำระเงิน</span>'
        };
        return map[status] || '<span class="badge bg-secondary">' + escapeHtml(status) + '</span>';
    }

    // ผูก Event Listeners
    const holdButtons = document.querySelectorAll('.btn-show-holds');
    console.log('📌 Found', holdButtons.length, 'hold buttons');
    
    holdButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const variantId = this.getAttribute('data-variant-id');
            const color = this.getAttribute('data-color');
            const size = this.getAttribute('data-size');
            openHoldModal(variantId, color, size);
        });
    });
    
    console.log('✅ Initialization complete!');
});
</script>
@endsection