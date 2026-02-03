@extends('layouts.app')
@include('layouts.navbarPD')

@section('content')
<style>
    /* Product ID Header - เด่นๆ กลางบนสุด */
    .product-id-header {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(255, 215, 0, 0.05));
        border: 2px solid var(--gold);
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        text-align: center;
        box-shadow: 0 10px 40px rgba(255, 215, 0, 0.3);
        position: relative;
        overflow: hidden;
    }

    .product-id-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.1), transparent);
        animation: rotate 10s linear infinite;
    }

    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .product-id-label {
        color: var(--text-secondary);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }

    .product-id-value {
        color: var(--gold);
        font-size: 3rem;
        font-weight: 700;
        text-shadow: 0 0 30px rgba(255, 215, 0, 0.6),
            0 0 50px rgba(255, 215, 0, 0.4),
            0 0 70px rgba(255, 215, 0, 0.2);
        letter-spacing: 3px;
        position: relative;
        z-index: 1;
        animation: glow-pulse 3s infinite;
    }

    @keyframes glow-pulse {

        0%,
        100% {
            text-shadow: 0 0 30px rgba(255, 215, 0, 0.6),
                0 0 50px rgba(255, 215, 0, 0.4),
                0 0 70px rgba(255, 215, 0, 0.2);
        }

        50% {
            text-shadow: 0 0 40px rgba(255, 215, 0, 0.8),
                0 0 60px rgba(255, 215, 0, 0.6),
                0 0 80px rgba(255, 215, 0, 0.4);
        }
    }

    /* Container with Action Buttons on Top Right */
    .product-info-container {
        position: relative;
        background: linear-gradient(145deg, var(--dark-secondary), var(--dark-tertiary));
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 215, 0, 0.2);
    }

    /* Action Buttons - มุมขวาบน */
    .action-buttons-top {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        display: flex;
        gap: 0.5rem;
        z-index: 10;
    }

    .btn-custom {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .btn-toggle-active {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(255, 215, 0, 0.1));
        color: var(--gold);
        border: 1px solid rgba(255, 215, 0, 0.3);
    }

    .btn-toggle-active:hover {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.3), rgba(255, 215, 0, 0.2));
        box-shadow: 0 3px 15px rgba(255, 215, 0, 0.3);
        color: var(--gold);
    }

    .btn-toggle-inactive {
        background: linear-gradient(135deg, rgba(75, 181, 67, 0.2), rgba(75, 181, 67, 0.1));
        color: #4bb543;
        border: 1px solid rgba(75, 181, 67, 0.3);
    }

    .btn-toggle-inactive:hover {
        background: linear-gradient(135deg, rgba(75, 181, 67, 0.3), rgba(75, 181, 67, 0.2));
        box-shadow: 0 3px 15px rgba(75, 181, 67, 0.3);
        color: #4bb543;
    }

    .btn-delete {
        background: linear-gradient(135deg, rgba(255, 54, 196, 0.2), rgba(255, 54, 196, 0.1));
        color: var(--neon-pink);
        border: 1px solid rgba(255, 54, 196, 0.3);
    }

    .btn-delete:hover:not(:disabled) {
        background: linear-gradient(135deg, rgba(255, 54, 196, 0.3), rgba(255, 54, 196, 0.2));
        box-shadow: 0 3px 15px rgba(255, 54, 196, 0.3);
        color: var(--neon-pink);
    }

    .btn-delete:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: rgba(50, 50, 50, 0.3);
        color: rgba(204, 204, 204, 0.5);
        border-color: rgba(100, 100, 100, 0.3);
    }

    /* Disabled Warning Button */
    .btn-warning:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background: rgba(255, 193, 7, 0.3) !important;
        color: rgba(255, 255, 255, 0.5) !important;
        border-color: rgba(255, 193, 7, 0.3) !important;
    }

    .btn-warning:disabled:hover {
        box-shadow: none !important;
        transform: none !important;
    }

    /* Button Toggle with Danger State */
    .btn-toggle.btn-danger {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.3), rgba(220, 53, 69, 0.2));
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.4);
        font-weight: 600;
        animation: pulse-danger 2s infinite;
    }

    @keyframes pulse-danger {

        0%,
        100% {
            box-shadow: 0 0 5px rgba(220, 53, 69, 0.4);
        }

        50% {
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.6);
        }
    }

    .btn-toggle.btn-danger:hover {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.4), rgba(220, 53, 69, 0.3));
        box-shadow: 0 3px 15px rgba(220, 53, 69, 0.5);
    }

    /* Product Info Table */
    .product-info-table {
        width: 100%;
        margin-top: 0;
        border-collapse: collapse;
        color: var(--text-primary);
    }

    .product-info-table th {
        background: rgba(255, 215, 0, 0.1);
        color: var(--gold);
        font-weight: 600;
        padding: 1rem;
        border: 1px solid rgba(255, 215, 0, 0.3);
        text-align: left;
        width: 200px;
        vertical-align: top;
    }

    .product-info-table td {
        padding: 1rem;
        border: 1px solid rgba(255, 215, 0, 0.2);
        color: var(--text-primary);
        vertical-align: top;
    }

    .product-info-table th i {
        margin-right: 0.5rem;
        width: 20px;
        text-align: center;
    }

    /* Product Image */
    .product-main-image {
        border-radius: 12px;
        border: 3px solid rgba(255, 215, 0, 0.3);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .no-image-placeholder {
        width: 300px;
        height: 250px;
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(255, 215, 0, 0.05));
        border: 2px dashed rgba(255, 215, 0, 0.3);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 1rem;
        color: var(--text-secondary);
    }

    .no-image-placeholder i {
        font-size: 3rem;
        color: var(--gold);
        opacity: 0.5;
    }

    /* Buttons in Table */
    .btn-edit-info {
        background: linear-gradient(135deg, rgba(95, 237, 255, 0.2), rgba(95, 237, 255, 0.1));
        color: var(--neon-blue);
        border: 1px solid rgba(95, 237, 255, 0.3);
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }

    .btn-edit-info:hover {
        background: linear-gradient(135deg, rgba(95, 237, 255, 0.3), rgba(95, 237, 255, 0.2));
        box-shadow: 0 3px 15px rgba(95, 237, 255, 0.3);
        color: var(--neon-blue);
    }

    .btn-edit-image {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(255, 215, 0, 0.1));
        color: var(--gold);
        border: 1px solid rgba(255, 215, 0, 0.3);
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-edit-image:hover {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.3), rgba(255, 215, 0, 0.2));
        box-shadow: 0 3px 15px rgba(255, 215, 0, 0.3);
        color: var(--gold);
    }

    /* Tags */
    .tag-badge {
        display: inline-block;
        padding: 0.4rem 0.9rem;
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.15), rgba(255, 215, 0, 0.05));
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 20px;
        color: var(--gold);
        font-size: 0.85rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    /* Price & Stock Badge */
    .price-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(255, 215, 0, 0.1));
        border: 1px solid rgba(255, 215, 0, 0.4);
        border-radius: 20px;
        color: var(--gold);
        font-weight: 600;
        font-size: 1rem;
        box-shadow: 0 3px 10px rgba(255, 215, 0, 0.2);
    }

    .stock-badge {
        display: inline-block;
        padding: 0.4rem 0.9rem;
        background: linear-gradient(135deg, rgba(95, 237, 255, 0.2), rgba(95, 237, 255, 0.1));
        border: 1px solid rgba(95, 237, 255, 0.4);
        border-radius: 20px;
        color: var(--neon-blue);
        font-weight: 600;
        box-shadow: 0 3px 10px rgba(95, 237, 255, 0.2);
    }

    /* Section Header */
    .section-header {
        background: linear-gradient(90deg, rgba(255, 215, 0, 0.15), transparent);
        border-left: 4px solid var(--gold);
        padding: 1rem 1.5rem;
        margin: 2rem 0 1.5rem 0;
        border-radius: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .section-header h3 {
        color: var(--gold);
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    /* Alert Info */
    .stock-info-alert {
        background: linear-gradient(90deg, rgba(95, 237, 255, 0.1), transparent);
        border-left: 4px solid var(--neon-blue);
        color: var(--neon-blue);
        padding: 1rem 1.5rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Warning for orders */
    .delete-warning {
        background: linear-gradient(90deg, rgba(255, 54, 196, 0.1), transparent);
        border-left: 4px solid var(--neon-pink);
        color: var(--neon-pink);
        padding: 1rem 1.5rem;
        border-radius: 6px;
        margin-top: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Modal Styling */
    .modal-content {
        background: var(--dark-secondary);
        border: 1px solid rgba(255, 215, 0, 0.3);
        color: var(--text-primary);
    }

    .modal-header {
        border-bottom: 1px solid rgba(255, 215, 0, 0.2);
        background: linear-gradient(90deg, rgba(255, 215, 0, 0.1), transparent);
    }

    .modal-title {
        color: var(--gold);
    }

    .modal-footer {
        border-top: 1px solid rgba(255, 215, 0, 0.2);
    }

    .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .product-id-value {
            font-size: 2rem;
        }

        .action-buttons-top {
            position: relative;
            top: auto;
            right: auto;
            flex-direction: column;
            margin-bottom: 1rem;
        }

        .product-info-table th,
        .product-info-table td {
            display: block;
            width: 100%;
        }
        .btn:disabled, .btn[disabled] {
        background-color: #6c757d !important; /* สีเทา */
        border-color: #6c757d !important;
        color: #fff !important;
        opacity: 0.65;
        cursor: not-allowed; /* เมาส์เป็นรูปห้าม */
    }
    }
</style>

<div class="container-fluid fade-in">
    <!-- Product ID Header - เด่นๆ กลางบนสุด -->
    <div class="product-id-header">
        <div class="product-id-label">
            <i class="fas fa-barcode"></i> รหัสสินค้า
        </div>
        <div class="product-id-value">{{ $product->id_stock }}</div>
    </div>

    <!-- Product Information with Action Buttons on Top Right -->
    <div class="product-info-container fade-in delay-1">
        <!-- Action Buttons - มุมขวาบน -->
        <div class="action-buttons-top">
            <!-- ปิด/เปิดการแสดง -->
            <form action="{{ route('products.toggle', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-custom {{ $product->is_active ? 'btn-toggle-active' : 'btn-toggle-inactive' }}">
                    <i class="fas {{ $product->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                    {{ $product->is_active ? 'ปิด' : 'เปิด' }}
                </button>
            </form>



<form action="{{ route('products.destroy', $product->id) }}" 
      method="POST" 
      style="display: inline-block;"
      onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?');">
    @csrf
    @method('DELETE')
    
   <button type="submit" 
        class="btn btn-sm btn-delete" 
        {{-- ✅ ใช้ isset เช็คก่อน เพื่อกัน Error Undefined variable --}}
        {{ isset($hasActiveOrders) && $hasActiveOrders ? 'disabled' : '' }}
        title="{{ isset($hasActiveOrders) && $hasActiveOrders ? 'มีออเดอร์ค้างอยู่' : 'ลบสินค้า' }}">
    <i class="fas fa-trash-alt"></i> ลบ
    
    @if(isset($hasActiveOrders) && $hasActiveOrders)
        <span class="badge bg-light text-dark ms-1">ติดจอง</span>
    @endif
</button>
</form>
        </div>

        <!-- Product Info Table -->
        <table class="product-info-table">
            <tbody>
                <!-- รูปภาพ -->
                <tr>
                    <th>
                        <i class="fas fa-image"></i>
                        รูปภาพสินค้า
                    </th>
                    <td>
                        @php
                        $mainImage = $product->productImages->where('is_main', true)->first() ?? $product->productImages->first();
                        @endphp

                        @if ($mainImage)
                        <img src="{{ asset('storage/' . $mainImage->image_url) }}"
                            alt="{{ $product->name }}"
                            width="300"
                            height="250"
                            class="product-main-image">
                        @else
                        <div class="no-image-placeholder">
                            <i class="fas fa-image"></i>
                            <p class="mb-0">ไม่มีรูปภาพสินค้า</p>
                        </div>
                        @endif

                        <div>
                            <a href="{{ route('product_images.index', $product->id) }}" class="btn btn-custom btn-edit-image">
                                <i class="fas fa-images"></i> แก้ไขรูปภาพ
                            </a>
                        </div>
                    </td>
                </tr>

                <!-- ชื่อสินค้า -->
                <tr>
                    <th>
                        <i class="fas fa-tag"></i>
                        ชื่อสินค้า
                    </th>
                    <td>
                        {{ $product->name }}
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-custom btn-edit-info ms-2">
                            <i class="fas fa-edit"></i> แก้ไข
                        </a>
                    </td>
                </tr>

                <!-- หมวดสินค้า -->
                <tr>
                    <th>
                        <i class="fas fa-folder"></i>
                        หมวดสินค้า
                    </th>
                    <td>
                        {{ $product->category->category_name ?: 'ไม่ระบุ' }}
                    </td>
                </tr>

                <!-- คำอธิบาย -->
                <tr>
                    <th>
                        <i class="fas fa-align-left"></i>
                        คำอธิบาย
                    </th>
                    <td>
                        {!! nl2br(e($product->description)) !!}
                    </td>
                </tr>

                <!-- แท็กสินค้า -->
                <tr>
                    <th>
                        <i class="fas fa-tags"></i>
                        แท็กสินค้า
                    </th>
                    <td>
                        @forelse ($product->tags as $tag)
                        <span class="tag-badge">{{ $tag->tag_name }}</span>
                        @empty
                        <span class="">ไม่มีแท็ก</span>
                        @endforelse
                    </td>
                </tr>

                <!-- ราคา -->
                <tr>
                    <th>
                        <i class="fas fa-money-bill-wave"></i>
                        ราคา
                    </th>
                    <td>
                        <span class="price-badge">{{ number_format($product->price, 2) }} ฿</span>
                    </td>
                </tr>

                <!-- ต้นทุน -->
                <tr>
                    <th>
                        <i class="fas fa-calculator"></i>
                        ต้นทุน
                    </th>
                    <td>
                        <span class="price-badge">{{ number_format($product->cost, 2) }} ฿</span>
                    </td>
                </tr>

                <!-- จำนวนสินค้า -->
                <tr>
                    <th>
                        <i class="fas fa-boxes"></i>
                        จำนวนสินค้าทั้งหมด
                    </th>
                    <td>
                        <span class="stock-badge">{{ number_format($product->colorSizes->sum('quantity')) }} ตัว</span>
                    </td>
                </tr>
            </tbody>
        </table>

        @if($hasActiveOrders)
        <div class="delete-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <span>ไม่สามารถลบสินค้าได้ เนื่องจากมีออเดอร์ที่กำลังดำเนินการอยู่ (pending, processing)</span>
        </div>
        @endif
    </div>

    <!-- Color & Size Section -->
    <div class="section-header fade-in delay-2">
        <h3><i class="fas fa-palette me-2"></i>สินค้าตามสีและขนาด</h3>
    </div>

    <div class="stock-info-alert fade-in delay-3">
        <div>
            <i class="fas fa-info-circle me-2"></i>
            สถานะที่จะนับเป็น "กำลังจับสต๊อค": <strong>{{ implode(', ', $openStatuses) }}</strong>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('product.colorSize.create', ['product' => $product->id]) }}" class="btn btn-custom btn-edit-image">
                <i class="fas fa-plus"></i> เพิ่มสี/ขนาดใหม่
            </a>
            <button type="button" class="btn btn-custom btn-edit-info" data-bs-toggle="modal" data-bs-target="#barcodeModal">
                <i class="fas fa-barcode"></i> พิมพ์บาร์โค้ด
            </button>
        </div>
    </div>

    @foreach($variantsByColor as $colorName => $rows)
    <div class="card mb-3">
        <div class="card-header">
            <strong>สี: {{ $colorName ?: 'ไม่ระบุสี' }}</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="15%">ขนาด</th>
                            <th width="12%" class="text-end">จำนวน (สต๊อค)</th>
                            <th width="12%" class="text-end">กำลังถูกจับ</th>
                            <th width="12%" class="text-end">คงเหลือ</th>
                            <th width="49%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $v)
                        @php
                        // รองรับได้ทั้ง 2 แหล่งข้อมูล:
                        // - มาจาก v_current_stock: มี current_stock, reserved_stock, available_stock, variant_id
                        // - มาจาก product_color_size เดิม: มี quantity และต้องอิง $reservedByVariantId
                        $variantId = (int)($v->variant_id ?? $v->id);
                        $sizeLabel = $v->size_name ?: 'ไม่ระบุไซส์';

                        $currentStock = isset($v->current_stock) ? (int)$v->current_stock
                        : (int)($v->quantity ?? 0);

                        $reserved = isset($v->reserved_stock) ? (int)$v->reserved_stock
                        : (int)($reservedByVariantId[$variantId] ?? 0);

                        // Golden Rule: available = current - reserved (fallback ป้องกันติดลบ)
                        $available = isset($v->available_stock) ? (int)$v->available_stock
                        : max(0, $currentStock - $reserved);
                        @endphp
                        <tr>
                            <td>{{ $sizeLabel }}</td>
                            <td class="text-end">{{ number_format($currentStock) }}</td>
                            <td class="text-end">
                                <span class="{{ $reserved > 0 ? 'text-danger fw-semibold' : 'text' }}">
                                    {{ number_format($reserved) }}
                                </span>
                            </td>
                            <td class="text-end">{{ number_format($available) }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">

                                    <a href="{{ route('stock.adjust.form', $variantId) }}" class="btn btn-sm btn-warning" title="ปรับสต็อค">
                                        <i class="fas fa-edit"></i> ปรับสต็อค
                                    </a>


                                    {{-- ดูประวัติ (ใช้งานได้เสมอ) --}}
                                    <a href="{{ route('stock.variant.history', $variantId) }}" class="btn btn-sm btn-info" title="ประวัติ">
                                        <i class="fas fa-history"></i> ประวัติ
                                    </a>

                                    {{-- ดูออเดอร์ที่กำลังจับ (Modal) --}}
                                    <button type="button"
                                        class="btn btn-sm btn-toggle {{ $reserved > 0 ? 'btn-danger' : '' }}"
                                        onclick="openHoldModal({{ $variantId }}, '{{ e($colorName ?: '-') }}', '{{ e($sizeLabel) }}')">
                                        {{ $reserved > 0 ? 'มีออเดอร์จอง!' : 'กำลังจับ?' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        @if($rows->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center ">ไม่มีข้อมูลสี/ไซส์</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach

</div>

<!-- Modal: ตรวจสอบออเดอร์ -->
<div class="modal fade" id="holdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="holdModalTitle">ตรวจสอบออเดอร์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>วันที่</th>
                                <th>เลขออเดอร์</th>
                                <th>ลูกค้า</th>
                                <th class="text-center">สถานะ</th>
                                <th class="text-center">จำนวน</th>
                                <th class="text-center">ดู</th>
                            </tr>
                        </thead>
                        <tbody id="holdTableBody">
                            <tr>
                                <td colspan="6" class="text-center">กำลังโหลด...</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">รวมกำลังจับทั้งหมด:</td>
                                <td class="text-center text-danger" id="holdTotalSum">-</td>
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

<!-- Modal: พิมพ์บาร์โค้ด -->
<div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('products.printBarcode') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="barcodeModalLabel">พิมพ์บาร์โค้ดสินค้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-3">
                        <label class="form-label">เลือกสี-ไซส์</label>
                        <select name="variant_id" class="form-select" required>
                            <option value="">-- เลือกสี-ไซส์ --</option>
                            @foreach($variantsByColor as $cName => $rows)
                            @foreach($rows as $v)
                            @php
                            $variantId = (int)($v->variant_id ?? $v->id);
                            $sizeLabel = $v->size_name ?: 'ไม่ระบุไซส์';
                            @endphp
                            <option value="{{ $variantId }}">
                                {{ ($cName ?: 'ไม่ระบุสี') }} - {{ $sizeLabel }}
                            </option>
                            @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">จำนวนที่ต้องการพิมพ์</label>
                        <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-barcode"></i> พิมพ์บาร์โค้ด
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ฟังก์ชันเปิด Modal และดึงข้อมูล
    async function openHoldModal(variantId, color, size) {
        document.getElementById('holdModalTitle').textContent = `ออเดอร์ที่กำลังจับอยู่: {{ $product->name }} (${color} / ${size})`;

        const tbody = document.getElementById('holdTableBody');
        const sumEl = document.getElementById('holdTotalSum');

        tbody.innerHTML = '<tr><td colspan="6" class="text-center">กำลังโหลด...</td></tr>';
        sumEl.textContent = '-';

        new bootstrap.Modal(document.getElementById('holdModal')).show();

        try {
            const res = await fetch(`/products/api/check-stock?variant_id=${variantId}`);
            const data = await res.json();

            tbody.innerHTML = '';

            if (!data.holds || data.holds.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center ">ไม่มีออเดอร์ที่จับสินค้านี้อยู่</td></tr>';
                sumEl.textContent = '0';
            } else {
                let sum = 0;
                data.holds.forEach(row => {
                    sum += parseInt(row.quantity);
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.created_at}</td>
                        <td><a href="/orders/${row.order_id}" target="_blank">${escapeHtml(row.order_number)}</a></td>
                        <td>${escapeHtml(row.customer_name)}</td>
                        <td class="text-center">${formatStatus(row.status)}</td>
                        <td class="text-center fw-bold text-danger">${parseInt(row.quantity).toLocaleString()}</td>
                        <td class="text-center">
                            <a href="/orders/${row.order_id}" class="btn btn-sm btn-outline-info" target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
                sumEl.textContent = sum.toLocaleString();
            }
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
            'pending': '<span class="badge bg-warning text-dark">รอดำเนินการ</span>',
            'processing': '<span class="badge bg-info text-dark">กำลังเตรียม</span>',
            'shipped': '<span class="badge bg-primary">จัดส่งแล้ว</span>',
            'delivered': '<span class="badge bg-success">สำเร็จ</span>',
            'cancelled': '<span class="badge bg-danger">ยกเลิก</span>'
        };
        return map[status] || `<span class="badge bg-secondary">${status}</span>`;
    }
</script>
@endsection