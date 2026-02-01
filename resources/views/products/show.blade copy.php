@extends('layouts.app')
@include('layouts.navbarPD')

@section('content')
<style>
    .btn { padding: 6px 12px; font-size: 14px; text-decoration: none; border-radius: 4px; color: white; }
    .btn-edit { background-color: #007bff; }
    .btn-delete { background-color: #dc3545; }
    .btn-toggle { background-color: #6c757d; }
    .btn-image { background-color: #17a2b8; }
    .btn-add { background-color: #28a745; }
    .btn-bar { background-color: #0d6efd; }
</style>

<main class="container">
    <table>
        <thead>
            <tr>
                <th>รหัสสินค้า</th>
                <td colspan="4">{{ $product->id_stock }}</td>
                <td>
                    <div class="action-buttons">
                        <form action="{{ route('products.toggle', $product->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $product->is_active ? 'btn-warning' : 'btn-success' }}">
                                {{ $product->is_active ? 'ปิดการแสดง' : 'เปิดการแสดง' }}
                            </button>
                        </form>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('ต้องการลบใช่ไหม?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">ลบ</button>
                        </form>
                    </div>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>รูป</th>
                <td colspan="5">
    @php
        // ดึงรูปหลัก หรือรูปแรกถ้าไม่มีรูปหลัก
        $mainImage = $product->productImages->where('is_main', true)->first() ?? $product->productImages->first();
    @endphp

    @if ($mainImage)
        <img src="{{ asset('storage/' . $mainImage->image_url) }}" alt="{{ $product->name }}" width="300" height="250" style="object-fit: cover; border-radius: 8px;">
    @else
        <div class="text-muted p-4 border rounded bg-light text-center" style="width: 300px; height: 250px; display: flex; align-items: center; justify-content: center;">
            <p class="mb-0">ไม่มีรูปภาพสินค้า</p>
        </div>
    @endif

    <div class="mt-3">
        {{-- ✅ แก้ไขตรงนี้: เปลี่ยนจาก .edit เป็น .index --}}
        <a href="{{ route('product_images.index', $product->id) }}" class="btn btn-info text-white">
            <i class="fas fa-images"></i> แก้ไขรูปภาพ
        </a>
    </div>
</td>
            </tr>
            <tr>
                <th>ชื่อสินค้า</th>
                <td colspan="5">
                    {{ $product->name }}
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-edit">แก้ไขข้อความ</a>
                </td>
            </tr>
            <tr>
                <th>หมวดสินค้า</th>
                <td colspan="5">{{ $product->category->category_name ?: 'ไม่ระบุ' }}</td>
            </tr>
            <tr>
                <th>คำอธิบาย</th>
                <td colspan="5">{!! nl2br(e($product->description)) !!}</td>
            </tr>
            <tr>
                <th>แท็กสินค้า</th>
                <td>
                    @foreach ($product->tags as $tag)
                        <span class="badge">{{ $tag->tag_name }}</span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>ราคา</th>
                <td colspan="5">{{ number_format($product->price) }} ฿</td>
            </tr>
            <tr>
                <th>ต้นทุน</th>
                <td colspan="5">{{ number_format($product->cost) }} ฿</td>
            </tr>
            <tr>
                <th>จำนวนสินค้า</th>
                <td colspan="5">{{ number_format($product->colorSizes->sum('quantity')) }} ตัว</td>
            </tr>
        </tbody>
    </table>

    <div class="container">
        <h3>สินค้าตามสีและขนาด</h3>

        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle"></i>
                สถานะที่จะนับเป็น "กำลังจับสต๊อค": <strong>{{ implode(', ', $openStatuses) }}</strong>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('product.colorSize.create', ['product' => $product->id]) }}" class="btn btn-add">
                    <i class="fas fa-plus"></i> เพิ่มสี/ขนาดใหม่
                </a>
                <button type="button" class="btn btn-bar" data-bs-toggle="modal" data-bs-target="#barcodeModal">
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
                                    $variantId    = (int)($v->variant_id ?? $v->id);
                                    $sizeLabel    = $v->size_name ?: 'ไม่ระบุไซส์';

                                    $currentStock = isset($v->current_stock) ? (int)$v->current_stock
                                                   : (int)($v->quantity ?? 0);

                                    $reserved     = isset($v->reserved_stock) ? (int)$v->reserved_stock
                                                   : (int)($reservedByVariantId[$variantId] ?? 0);

                                    // Golden Rule: available = current - reserved (fallback ป้องกันติดลบ)
                                    $available    = isset($v->available_stock) ? (int)$v->available_stock
                                                   : max(0, $currentStock - $reserved);
                                @endphp
                                <tr>
                                    <td>{{ $sizeLabel }}</td>
                                    <td class="text-end">{{ number_format($currentStock) }}</td>
                                    <td class="text-end">
                                        <span class="{{ $reserved > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                                            {{ number_format($reserved) }}
                                        </span>
                                    </td>
                                    <td class="text-end">{{ number_format($available) }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            {{-- ปรับสต็อค (ใช้ StockService) --}}
                                            <a href="{{ route('stock.adjust.form', $variantId) }}" class="btn btn-sm btn-warning" title="ปรับสต็อค">
                                                <i class="fas fa-edit"></i> ปรับสต็อค
                                            </a>

                                            {{-- ดูประวัติ --}}
                                            <a href="{{ route('stock.variant.history', $variantId) }}" class="btn btn-sm btn-info" title="ประวัติ">
                                                <i class="fas fa-history"></i> ประวัติ
                                            </a>

                                            {{-- ดูออเดอร์ที่กำลังจับ (Modal) --}}
                                            <button type="button"
                                                    class="btn btn-sm btn-toggle"
                                                    onclick="openHoldModal({{ $variantId }}, '{{ e($colorName ?: '-') }}', '{{ e($sizeLabel) }}')">
                                                กำลังจับ?
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @if($rows->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center text-muted">ไม่มีข้อมูลสี/ไซส์</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach

 <div class="modal fade" id="holdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <div class="modal-content">
            <div class="modal-header">
                {{-- ID: holdModalTitle ต้องมีอยู่ตรงนี้ --}}
                <h5 class="modal-title" id="holdModalTitle">ตรวจสอบออเดอร์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        {{-- ID: holdTableBody ต้องมีอยู่ตรงนี้ (JS จะยัดข้อมูลลงตรงนี้) --}}
                        <tbody id="holdTableBody">
                            <tr><td colspan="6" class="text-center">กำลังโหลด...</td></tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="4" class="text-end">รวมกำลังจับทั้งหมด:</td>
                                {{-- ID: holdTotalSum ต้องมีอยู่ตรงนี้ --}}
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

        {{-- Modal: พิมพ์บาร์โค้ด --}}
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
                            <div class="mb-2">
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
                            <div class="mb-2">
                                <input type="number" name="quantity" class="form-control" min="1" value="1" required placeholder="จำนวนที่ต้องการพิมพ์">
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
    </div>

    {{-- holds data for modal --}}
    <script type="application/json" id="holds-json">
        @json($holdsRows, JSON_UNESCAPED_UNICODE)
    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ฟังก์ชันเปิด Modal และดึงข้อมูล (เหมือนหน้า Sales)
    // ฟังก์ชันนี้ต้องหน้าตาประมาณนี้ครับ
    async function openHoldModal(variantId, color, size) {
        // 1. ตั้งชื่อหัวข้อ Modal
        document.getElementById('holdModalTitle').textContent = `ออเดอร์ที่กำลังจับอยู่: {{ $product->name }} (${color} / ${size})`;
        
        const tbody = document.getElementById('holdTableBody');
        const sumEl = document.getElementById('holdTotalSum');
        
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">กำลังโหลด...</td></tr>';
        sumEl.textContent = '-';

        // เปิด Modal
        new bootstrap.Modal(document.getElementById('holdModal')).show();

        try {
            // 2. เรียก API ที่เราเพิ่งสร้างในขั้นตอนที่ 1-2
            const res = await fetch(`/products/api/check-stock?variant_id=${variantId}`);
            const data = await res.json();

            tbody.innerHTML = ''; // เคลียร์ของเก่า

            if (!data.holds || data.holds.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">ไม่มีออเดอร์ที่จับสินค้านี้อยู่</td></tr>';
                sumEl.textContent = '0';
            } else {
                let sum = 0;
                // 3. วนลูปแสดงข้อมูล
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
