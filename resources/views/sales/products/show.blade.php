@extends('layouts.app')
@include('layouts.navbarSalesPD')

@section('content')

<style>
    /* Container */
    .product-detail-container {
        padding: 2rem 1rem;
    }

    /* Header Section */
    .detail-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .detail-header h3 {
        margin: 0;
        font-weight: 700;
        font-size: 1.75rem;
    }
    .btn-back {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 2px solid white;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }
    .btn-back:hover {
        background: white;
        color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
    }

    /* Info Card - ไม่มีพื้นหลังสีขาว */
    .info-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
    }
    .info-card .card-body {
        padding: 0;
    }

    /* Info Table */
    .info-table {
        margin: 0;
        background: transparent;
    }
    .info-table tr {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .info-table tr:last-child {
        border-bottom: none;
    }
    .info-table th {
        background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        color: white;
        font-weight: 600;
        padding: 1.25rem 1.5rem;
        width: 15%;
        border: none;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-table td {
        padding: 1.25rem 1.5rem;
        color: #1f2937;
        font-weight: 500;
        background: rgba(255, 255, 255, 0.5);
        border: none;
    }

    /* Badges */
    .badge-modern {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .badge-active {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    }
    .badge-inactive {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
        box-shadow: 0 4px 10px rgba(107, 114, 128, 0.3);
    }

    /* Price Badge */
    .price-display {
        color: #10b981;
        font-weight: 700;
        font-size: 1.25rem;
    }

    /* Product ID */
    .product-id {
        color: #667eea;
        font-weight: 700;
        font-size: 1.1rem;
        font-family: monospace;
    }

    /* Images Section */
    .images-section {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    .images-section h5 {
        color: #1f2937;
        font-weight: 700;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
    }
    .product-image {
        height: 140px;
        width: 140px;
        object-fit: cover;
        border-radius: 15px;
        border: 3px solid white;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }
    .product-image:hover {
        transform: scale(1.1) rotate(2deg);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }

    /* Stock Table Card */
    .stock-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        background: transparent;
    }
    .stock-card .card-header {
        background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        color: white;
        padding: 1.5rem 2rem;
        border: none;
    }
    .stock-card .card-header h5 {
        margin: 0;
        font-weight: 700;
        font-size: 1.25rem;
    }

    /* Stock Table */
    .stock-table {
        margin: 0;
        background: transparent;
    }
    .stock-table thead {
        background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
    }
    .stock-table thead th {
        color: white;
        font-weight: 600;
        padding: 1.25rem 1rem;
        border: none;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stock-table tbody tr {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    .stock-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: scale(1.01);
    }
    .stock-table tbody td {
        padding: 1.25rem 1rem;
        border: none;
        color: #1f2937;
        font-weight: 500;
    }

    /* Thumbnail Image */
    .img-thumbnail-modern {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 12px;
        border: 3px solid white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    /* Size Badge */
    .size-badge {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        color: #1f2937;
        border: 2px solid #e5e7eb;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
    }

    /* Quantity Display */
    .qty-available {
        color: #10b981;
        font-weight: 700;
        font-size: 1.2rem;
    }
    .qty-out {
        color: #ef4444;
        font-weight: 700;
        font-size: 1.2rem;
    }
    .qty-hold {
        color: #f59e0b;
        font-weight: 700;
        font-size: 1.1rem;
    }

    /* Buttons */
    .btn-modern {
        border-radius: 50px;
        padding: 0.5rem 1.25rem;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        border: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-history {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    .btn-history:hover {
        background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        color: white;
    }
    .btn-hold {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
    }
    .btn-hold:hover {
        background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
        color: white;
    }
    .btn-secondary-modern {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #1f2937;
    }
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        opacity: 0.3;
        color: #9ca3af;
    }
    .empty-state p {
        font-size: 1.1rem;
        color: #6b7280;
        font-weight: 500;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 20px;
        border: none;
        overflow: hidden;
    }
    .modal-header {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        padding: 1.5rem 2rem;
    }
    .modal-title {
        font-weight: 700;
        font-size: 1.25rem;
    }
    .modal-body {
        padding: 2rem;
    }
    .modal-footer {
        border: none;
        padding: 1.5rem 2rem;
        background: #f9fafb;
    }
    .images-section h5 {
    color: #f3f3f3;
    }
    i.fas.fa-images {
    color: wheat;
}element.style {
    font-weight: 600;
    color: #ffffff;
}
</style>

<main class="container product-detail-container">
    {{-- Header --}}
    <div class="detail-header">
        <h3><i class="fas fa-box-open"></i> รายละเอียดสินค้า (มุมมองฝ่ายขาย)</h3>
        <a href="{{ route('sales.products.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> กลับ
        </a>
    </div>

    {{-- Product Info Card --}}
    <div class="card info-card" >
        <div class="card-body">
            <table class="table info-table">
                <tr>
                    <th>รหัสสินค้า</th>
                    <td class="product-id">#{{ $product->id_stock }}</td>
                    <th>ชื่อสินค้า</th>
                    <td>{{ $product->name }}</td>
                </tr>
                <tr>
                    <th>หมวดหมู่</th>
                    <td>{{ $product->category->category_name ?? '-' }}</td>
                    <th>ราคาขาย</th>
                    <td class="price-display">{{ number_format($product->price, 2) }} ฿</td>
                </tr>
                <tr>
                    <th>สถานะ</th>
                    <td colspan="3">
                        @if($product->is_active)
                            <span class="badge-modern badge-active">
                                <i class="fas fa-check-circle"></i> เปิดขายอยู่
                            </span>
                        @else
                            <span class="badge-modern badge-inactive">
                                <i class="fas fa-times-circle"></i> ปิดการขาย
                            </span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Product Images --}}
    @if($product->productImages->count() > 0)
    <div class="images-section">
        <h5><i class="fas fa-images"></i> รูปภาพสินค้า</h5>
        <div class="d-flex gap-3 flex-wrap">
            @foreach($product->productImages as $img)
                <img src="{{ asset('storage/'.$img->image_url) }}" class="product-image">
            @endforeach
        </div>
    </div>
    @endif

    {{-- Stock Table --}}
    <div class="card stock-card">
        <div class="card-header">
            <h5><i class="fas fa-chart-bar"></i> รายการสต็อก (สี / ไซส์)</h5>
        </div>
        <div class="table-responsive">
            <table class="table stock-table">
                <thead>
                    <tr>
                        
                        <th>สี</th>
                        <th>ไซส์</th>
                        <th class="text-center">คงเหลือ</th>
                        <th class="text-center">ติดจอง</th>
                        <th class="text-center" style="width: 280px;">ตรวจสอบ / ประวัติ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->colorSizes as $variant)
                    <tr>

                        <td style="font-weight: 600; color: #ffffff;">{{ $variant->color->name ?? '-' }}</td>
                        <td>
                            <span class="size-badge">{{ $variant->size->size_name ?? '-' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="{{ $variant->quantity > 0 ? 'qty-available' : 'qty-out' }}">
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
                                <span class="qty-hold">{{ number_format($heldQty) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('stock.variant.history', ['variant' => $variant->id, 'scope' => 'all']) }}" 
                                   class="btn-modern btn-history" 
                                   target="_blank" 
                                   title="ดูประวัติทั้งหมด">
                                    <i class="fas fa-history"></i> ประวัติ
                                </a>

                                @if($heldQty > 0)
                                    <button type="button" class="btn-modern btn-hold btn-show-holds" 
                                            data-variant-id="{{ $variant->id }}"
                                            data-color="{{ str_replace('"', '&quot;', $variant->color->name ?? '-') }}"
                                            data-size="{{ str_replace('"', '&quot;', $variant->size->size_name ?? '-') }}">
                                        <i class="fas fa-hand-holding"></i> กำลังจับ
                                    </button>
                                @else
                                    <button type="button" class="btn-modern btn-secondary-modern" disabled>
                                        ว่าง
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>ยังไม่มีรายการสต็อกสินค้า</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

{{-- Modal --}}
<div class="modal fade" id="holdsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg " 
">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-shopping-cart"></i> ออเดอร์ที่กำลังจอง: <span id="modalVariantTitle"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="
    background-color: #000000; ">
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