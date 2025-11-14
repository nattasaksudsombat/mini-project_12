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
                        $mainImage = $product->productImages->where('is_main', true)->first() ?? $product->productImages->first();
                    @endphp
                    @if ($mainImage)
                        <img src="{{ asset('storage/' . $mainImage->image_url) }}" alt="{{ $product->name }}" width="300" height="250">
                    @else
                        <p>ไม่มีรูปภาพสินค้า</p>
                    @endif
                    <a href="{{ route('products.images.edit', $product->id) }}" class="btn btn-image">แก้ไขรูปภาพ</a>
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
                <a href="{{ route('product.colorSize.create', ['product_id' => $product->id]) }}" class="btn btn-add">
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

        {{-- Modal: รายการออเดอร์ที่กำลังจับ --}}
        <div class="modal fade" id="holdsModal" tabindex="-1" aria-labelledby="holdsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-clipboard-list"></i>
                            ออเดอร์ที่กำลังจับอยู่: <span id="hold-title"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                    </div>
                    <div class="modal-body">
                        <div id="hold-empty" class="alert alert-info d-none">
                            ไม่มีออเดอร์ที่กำลังจับอยู่สำหรับรายการนี้
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped" id="holds-table">
                                <thead>
                                    <tr>
                                        <th>เลขออเดอร์</th>
                                        <th>ลูกค้า</th>
                                        <th>สถานะ</th>
                                        <th class="text-end">จำนวน</th>
                                        <th width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">รวมกำลังจับ</th>
                                        <th class="text-end" id="hold-total">0</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
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

    <script>
        (function() {
            const holdsData = JSON.parse(document.getElementById('holds-json').textContent || '{}');

            window.openHoldModal = function(variantId, colorName, sizeName) {
                const title = `${colorName || '-'} - ${sizeName || '-'}`;
                document.getElementById('hold-title').textContent = title;

                const list = holdsData[String(variantId)] || [];
                const tbody = document.querySelector('#holds-table tbody');
                const empty = document.getElementById('hold-empty');
                const sumEl = document.getElementById('hold-total');
                tbody.innerHTML = '';
                let sum = 0;

                if (!list.length) {
                    empty.classList.remove('d-none');
                    sumEl.textContent = '0';
                } else {
                    empty.classList.add('d-none');
                    list.forEach(row => {
                        sum += Number(row.quantity || 0);
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td><span class="badge bg-secondary">${escapeHtml(row.order_number ?? row.order_id ?? '-')}</span></td>
                            <td>${escapeHtml(row.customer_name || '-')}</td>
                            <td>${formatStatus(row.status)}</td>
                            <td class="text-end">${Number(row.quantity||0).toLocaleString('th-TH')}</td>
                            <td><a href="/orders/${row.order_id}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i> ดูออเดอร์</a></td>
                        `;
                        tbody.appendChild(tr);
                    });
                    sumEl.textContent = sum.toLocaleString('th-TH');
                }

                const modal = new bootstrap.Modal(document.getElementById('holdsModal'));
                modal.show();
            };

            function escapeHtml(x) {
                if (typeof x !== 'string') return '';
                return x.replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function formatStatus(s) {
                const map = {
                    pending: '<span class="badge text-bg-warning">รอดำเนินการ</span>',
                    processing: '<span class="badge text-bg-info">กำลังจัดการ</span>',
                    shipped: '<span class="badge text-bg-primary">จัดส่งแล้ว</span>',
                    delivered: '<span class="badge text-bg-success">ส่งสำเร็จ</span>',
                    cancelled: '<span class="badge text-bg-danger">ยกเลิก</span>'
                };
                return map[s] || `<span class="badge text-bg-secondary">${escapeHtml(String(s||'-'))}</span>`;
            }
        })();
    </script>
@endsection
