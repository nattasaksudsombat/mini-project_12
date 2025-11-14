@extends('layouts.app')

@section('content')
<div class="container">

    <!-- แสดงข้อมูลที่ซ่อนสำหรับ JavaScript -->
    <script type="application/json" id="order-data">
        @json($order)
    </script>
    <script type="application/json" id="products-data">
        @json($products)
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
                    {{-- ป้ายสถานะแบบอ่านง่าย --}}
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

                {{-- ช่องทางซื้อ: ตัวเลือกภาษาไทย --}}
                <div class="col-md-4">
                    <label class="form-label">ช่องทางซื้อ *</label>
                    @php
                    // value => label (value ต้องตรง ENUM ใน DB)
                    $channelOptions = [
                    'facebook' => 'Facebook',
                    'line' => 'Line',
                    'website' => 'เว็บไซต์',
                    'shopee' => 'Shopee',
                    'lazada' => 'Lazada',
                    'offline' => 'หน้าร้าน',
                    ];
                    // รองรับค่าเดิมที่อาจเป็นทั้ง key หรือ label (จากข้อมูลเก่า)
                    $chRaw = old('customer.purchase_channel', $order->customer->purchase_channel ?? '');
                    $channelBackMap = array_flip($channelOptions); // label -> key
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

                {{-- วิธีชำระเงิน: ตัวเลือกภาษาไทย --}}
                <div class="col-md-3">
    <label class="form-label">วิธีชำระเงิน *</label>
    @php
        // value => label (value ต้องตรง ENUM ใน DB)
        $paymentOptions = [
            'bank_transfer'    => 'โอน/พร้อมเพย์',
            'cash_on_delivery' => 'ชำระปลายทาง (COD)',
            'credit_card'      => 'บัตรเครดิต/เดบิต',
            'e_wallet'         => 'วอลเล็ต',
        ];
        $pmRaw = old('customer.payment_method', $order->customer->payment_method ?? '');
        $paymentBackMap = array_flip($paymentOptions); // label -> key
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
                        <option value="pending" @selected($status==='pending' )>รอดำเนินการ</option>
                        <option value="processing" @selected($status==='processing' )>กำลังจัดการ</option>
                        <option value="shipped" @selected($status==='shipped' )>จัดส่งแล้ว</option>
                        <option value="delivered" @selected($status==='delivered' )>ส่งสำเร็จ</option>
                        <option value="cancelled" @selected($status==='cancelled' )>ยกเลิก</option>
                    </select>
                    <div id="shipLockHint" class="form-text text-danger d-none">
                        ยังไม่ชำระเงิน: ไม่สามารถเปลี่ยนเป็น “จัดส่งแล้ว/ส่งสำเร็จ”
                    </div>
                    @error('status') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">สถานะการชำระเงิน *</label>
                    <select class="form-select" name="payment_status" id="paymentSelect" required>
                        @php $pay = old('payment_status', $order->payment_status); @endphp
                        <option value="pending" @selected($pay==='pending' )>ยังไม่ชำระ</option>
                        <option value="paid" @selected($pay==='paid' )>ชำระแล้ว</option>
                    </select>
                    @error('payment_status') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                {{-- Tracking + หมายเหตุ --}}
                <div class="col-md-4">
                    <label class="form-label">เลขติดตามพัสดุ (Tracking Number)</label>
                    <input type="text"  disabled class="form-control" name="tracking_number"
                        value="{{ old('tracking_number', $order->tracking_number) }}"
                        placeholder="ระบุเลขพัสดุ (ถ้ามี)">
                </div>

                <div class="col-md-12">
                    <label class="form-label">หมายเหตุ</label>
                    <textarea class="form-control" name="notes" rows="2">{{ old('notes', $order->notes) }}</textarea>
                </div>

                {{-- เอา “ส่วนลด” ออกจาก UI แต่ส่งค่า 0.00 แบบซ่อน เพื่อไม่กระทบการคำนวณ/แบ็กเอนด์ --}}
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
                    <table class="table table-bordered" id="order-items-table">
                        <thead class="table-light">
                            <tr>
                                <th width="25%">สินค้า</th>
                                <th>รหัสสินค้า</th>
                                <th width="20%">สี-ไซส์</th>
                                <th width="15%">จำนวน</th>
                                <th width="15%">ราคาต่อหน่วย</th>
                                <th width="15%">รวม</th>
                                <th width="10%">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="order-items-body">
                            <!-- รายการสินค้าจะถูกเพิ่มด้วย JavaScript -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>รวมเป็นเงิน:</strong></td>
                                <td><strong id="subtotal-display">0.00</strong></td>
                                <td></td>
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
                    {{-- ค่าจัดส่ง: ดีฟอลต์ 0.00 --}}
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

                    {{-- ลบ “ส่วนลด” ออกจาก UI --}}
                    {{-- คงมี hidden discount (ด้านบน) เพื่อเซ็ตเป็น 0.00 --}}

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
                        <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(-1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" id="variant-quantity" class="form-control text-center" value="1" min="1">
                        <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(1)">
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
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
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

    .variant-info {
        background-color: #f8f9fa;
        padding: 8px;
        border-radius: 4px;
        margin-top: 4px;
    }

    .debug-info {
        background-color: #fff3cd;
        border: 1px solid #ffeaa7;
        padding: 10px;
        border-radius: 4px;
        font-size: 0.875rem;
        margin-bottom: 10px;
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
   Order Edit – Stock-Consistent (Modal ↔ Table) 100% Client-side
   เพิ่ม LiveStock Ledger: เพิ่ม/ลด/ลบ → “คงเหลือ” ขยับจริงทันที
   ========================================================= */

let selectedItems = [];     // รายการในออเดอร์ (ตาราง)
let products = [];          // จาก #products-data (ถ้ามี)
let currentProduct = null;  // ใช้กับโมดอล
const debugMode = false;

/* -------------------- Helpers -------------------- */
const N = (x) => Number(x || 0);
const norm = (s) => (s==null?'':String(s))
  .toLowerCase().replace(/[‐–—−-]/g,'-').replace(/[.,/\\|()[\]{}]/g,' ')
  .replace(/\s+/g,' ').trim();
const variantKey = (pid, cid, sid) => `${Number(pid)||0}:${String(cid??'')}:${String(sid??'')}`;
function escapeHtml(x){ if (typeof x!=='string') return ''; return x.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }

/* ---------------- Global registries ---------------- */
window.PCS_INDEX = window.PCS_INDEX || {};  // pid -> [{id,color_id,size_id,quantity,color_name,size_name}]
const VariantCache  = new Map();            // pid -> variants[] (cache API)

/* ---------- Live Stock Ledger: base + available ---------- */
const LiveStock = {
  _map: new Map(), // key -> { base, available }

  buildFromPCS(){
    this._map.clear();
    Object.keys(window.PCS_INDEX || {}).forEach(pid=>{
      (window.PCS_INDEX[pid]||[]).forEach(v=>{
        const key = variantKey(+pid, v.color_id ?? null, v.size_id ?? null);
        const base = N(v.quantity || 0);
        this._map.set(key, { base, available: base });
      });
    });
  },

  ensureKey(productId, colorId, sizeId, base=0){
    const key = variantKey(productId, colorId, sizeId);
    if (!this._map.has(key)) {
      const b = N(base||0);
      this._map.set(key, { base: b, available: b });
    }
    return key;
  },

  setBase(productId, colorId, sizeId, base){
    const key = this.ensureKey(productId, colorId, sizeId, base);
    const e = this._map.get(key);
    e.base = N(base||0);
    if (e.available > e.base) e.available = e.base;
    return key;
  },

  getBase(key){
    return this._map.has(key) ? this._map.get(key).base : 0;
  },
  getAvailable(key){
    return this._map.has(key) ? this._map.get(key).available : 0;
  },

  reserve(key, amount){          // หักออกจาก available
    if (!this._map.has(key)) return 0;
    const entry = this._map.get(key);
    const take = Math.max(0, Math.min(entry.available, N(amount||0)));
    entry.available -= take;
    return take;
  },

  unreserve(key, amount){        // คืนกลับเข้า available
    if (!this._map.has(key)) return 0;
    const entry = this._map.get(key);
    const put = Math.max(0, N(amount||0));
    entry.available += put;
    if (entry.available > entry.base) entry.available = entry.base;
    return put;
  }
};

/* ---------------- Parse initial JSON ---------------- */
function parseInitialJSON() {
  const pEl = document.getElementById('products-data');
  if (pEl && pEl.textContent.trim()) { try { products = JSON.parse(pEl.textContent); } catch{} }

  const oEl = document.getElementById('order-data');
  if (oEl && oEl.textContent.trim()) {
    try {
      const order = JSON.parse(oEl.textContent);
      if (order && Array.isArray(order.items)) {
        selectedItems = order.items.map(it => {
          const q = N(it.quantity || 1);
          const u = N(it.unit_price || it.price || 0);
          return {
            product_id   : it.product_id,
            product_name : it.product_name || it.name || (it.product && it.product.name) || 'สินค้าไม่ระบุ',
            unit_price   : u,
            quantity     : q,
            total_price  : u*q,

            color_id     : it.color_id ?? (it.color && it.color.id) ?? null,
            size_id      : it.size_id  ?? (it.size && it.size.id ) ?? null,
            color_size_id: it.product_color_size_id || it.color_size_id || it.variant_id || (it.product_color_size && it.product_color_size.id) || null,

            color_name   : it.color_name || (it.color && it.color.name) || '',
            size_name    : it.size_name  || (it.size && (it.size.size_name || it.size.name)) || '',
            variant_name : it.variant_name || '',
            is_existing_item: true,
            has_complete_ids: !!(
              (it.color_id || (it.color && it.color.id)) &&
              (it.size_id  || (it.size && it.size.id )) &&
              (it.product_color_size_id || it.color_size_id || it.variant_id || (it.product_color_size && it.product_color_size.id))
            ),
            // เก็บจำนวนเดิมตอนโหลด (สำคัญ)
            initial_qty: q,
            _reserved: 0,     // จำนวนที่แถวนี้จองไว้ในเลดเจอร์
            max_stock: 0
          };
        });
      }
    } catch {}
  }
}

/* ---------------- Hydrate stock from API ---------------- */
async function fetchVariants(pid){
  if (VariantCache.has(pid)) return VariantCache.get(pid);
  try {
    const res = await fetch(`/products/${pid}/variants`);
    const arr = res.ok ? await res.json() : [];
    VariantCache.set(pid, Array.isArray(arr)?arr:[]);
    return VariantCache.get(pid);
  } catch {
    VariantCache.set(pid, []);
    return [];
  }
}

async function hydratePCSFor(productIds){
  const unique = [...new Set(productIds.filter(Boolean))];
  for (const pid of unique){
    if (Array.isArray(window.PCS_INDEX[pid]) && window.PCS_INDEX[pid].length) continue;
    const arr = await fetchVariants(pid);
    window.PCS_INDEX[pid] = (arr||[]).map(v=>({
      id: v.id,
      color_id: v.color_id ?? v.color?.id ?? null,
      size_id : v.size_id  ?? v.size?.id  ?? null,
      quantity: N(v.quantity || v.stock || 0),
      color_name: v.color_name || v.color?.name || '',
      size_name : v.size_name  || v.size?.size_name || v.size?.name || ''
    }));
  }
  // สร้างเลดเจอร์จาก PCS_INDEX
  LiveStock.buildFromPCS();
}

/* ---------------- Resolve legacy item ids ---------------- */
function splitVariantName(vn){ if (!vn) return {c:'',s:''}; const a=String(vn).split(/[-–—]\s*/); return {c:(a[0]||'').trim(), s:(a[1]||'').trim()}; }
function findInPCSByNames(pid, cName, sName){
  const list=(window.PCS_INDEX&&window.PCS_INDEX[pid])?window.PCS_INDEX[pid]:[];
  const nc=norm(cName), ns=norm(sName);
  let hit = list.find(v => norm(v.color_name||v.color?.name)===nc &&
                            norm(v.size_name ||v.size?.size_name||v.size?.name)===ns);
  if (hit) return hit;
  const cand = list.filter(v =>
    (!nc || norm(v.color_name||v.color?.name)===nc) &&
    (!ns || norm(v.size_name ||v.size?.size_name||v.size?.name)===ns)
  );
  return cand.length===1 ? cand[0] : null;
}

async function resolveViaAPIByNames(pid, cName, sName){
  const list = await fetchVariants(pid);
  const nc=norm(cName), ns=norm(sName);
  let found = list.find(v =>
    norm(v.color_name || v.color?.name)===nc &&
    norm(v.size_name  || v.size?.size_name || v.size?.name)===ns
  );
  if (!found){
    const cand = list.filter(v =>
      (!nc || norm(v.color_name || v.color?.name)===nc) &&
      (!ns || norm(v.size_name  || v.size?.size_name || v.size?.name)===ns)
    );
    if (cand.length===1) found=cand[0];
  }
  if (!found) return null;
  return {
    colorId     : found.color_id ?? found.color?.id ?? null,
    sizeId      : found.size_id  ?? found.size?.id  ?? null,
    colorSizeId : found.id ?? null,
    colorName   : found.color_name || found.color?.name || cName,
    sizeName    : found.size_name  || found.size?.size_name || found.size?.name || sName,
    stock       : N(found.quantity || found.stock || 0)
  };
}

async function resolveItem(it){
  if (it.color_id && it.size_id && it.color_size_id) return true;
  const pid = Number(it.product_id||0);
  let cName=(it.color_name||'').trim(), sName=(it.size_name||'').trim();
  if ((!cName||!sName) && it.variant_name){ const {c,s}=splitVariantName(it.variant_name); cName ||= c; sName ||= s; }

  let hit = findInPCSByNames(pid, cName, sName);
  if (hit){
    it.color_id = hit.color_id; it.size_id = hit.size_id;
    it.color_name = hit.color_name || hit.color?.name || cName;
    it.size_name  = hit.size_name  || hit.size?.size_name || hit.size?.name || sName;
    const api = await resolveViaAPIByNames(pid, it.color_name, it.size_name);
    if (api){
      it.color_size_id = api.colorSizeId;
      LiveStock.setBase(pid, api.colorId, api.sizeId, api.stock);
    }
  }

  if (!it.color_id || !it.size_id || !it.color_size_id){
    const api = await resolveViaAPIByNames(pid, cName, sName);
    if (api){
      it.color_id      = it.color_id      ?? api.colorId;
      it.size_id       = it.size_id       ?? api.sizeId;
      it.color_size_id = it.color_size_id ?? api.colorSizeId;
      it.color_name    = it.color_name || api.colorName || cName;
      it.size_name     = it.size_name  || api.sizeName  || sName;
      LiveStock.setBase(pid, api.colorId, api.sizeId, api.stock);
    }
  }

  it.has_complete_ids = !!(it.color_id && it.size_id && it.color_size_id);
  return it.has_complete_ids;
}

/* ---------------- Seed reservations (สำคัญมาก) ---------------- */
function seedReservationsFromExisting(){
  // จองสต๊อกจากจำนวนที่มีอยู่ในออเดอร์ (เพื่อให้ available = base - sumReserved)
  selectedItems.forEach(it=>{
    if (it.color_id!=null && it.size_id!=null){
      const key  = LiveStock.ensureKey(it.product_id, it.color_id, it.size_id);
      const took = LiveStock.reserve(key, N(it.quantity||0));
      it._reserved = took; // จำว่าแถวนี้จองเท่าไร
    }
  });
}

/* ---------------- Stock math (ledger-based) ----------------
   base       = LiveStock.base
   free       = LiveStock.available          → “คงเหลือ” (ไม่รวมจำนวนของแถวนี้ เพราะแถวนี้ถูกจองไว้แล้ว)
   maxForRow  = it._reserved + free          → เพิ่มได้สูงสุดสำหรับแถวนี้
---------------------------------------------------------------- */
function computeCapsForIndex(index){
  const it = selectedItems[index];
  if (!it || it.color_id==null || it.size_id==null) return {base:0, free:0, max:0};

  const key  = LiveStock.ensureKey(it.product_id, it.color_id, it.size_id);
  const base = LiveStock.getBase(key);
  const free = Math.max(0, LiveStock.getAvailable(key));
  const max  = N(it._reserved || 0) + free;

  return { base, free, max };
}

/* ---------------- Quantity setter (จอง/คืนตามส่วนต่าง) ---------------- */
function setRowQuantity(index, newQty, silent=false){
  const it = selectedItems[index]; if (!it) return;
  if (it.color_id==null || it.size_id==null) return; // ต้องมี variant ก่อน

  const key  = LiveStock.ensureKey(it.product_id, it.color_id, it.size_id);
  const caps = computeCapsForIndex(index);

  let qty = Math.max(1, Math.min(N(newQty)||1, caps.max));

  const prev = N(it._reserved||0);
  const diff = qty - prev;

  if (diff > 0) LiveStock.reserve(key, diff);
  else if (diff < 0) LiveStock.unreserve(key, -diff);

  it.quantity   = qty;
  it._reserved  = qty;
  it.total_price = N(it.unit_price||0) * qty;

  if (!silent) renderOrderItems();
}

/* ---------------- Initialize ---------------- */
document.addEventListener('DOMContentLoaded', () => {
  initializeSystem().catch(e=>{
    console.error(e);
    showAlert('เกิดข้อผิดพลาดในการเริ่มต้นระบบ', 'danger');
  });
});

async function initializeSystem(){
  showLoadingIndicator();
  parseInitialJSON();

  // ดึง variants ของสินค้าที่อยู่ในออเดอร์ เพื่อสร้าง PCS_INDEX + เลดเจอร์
  const pids = (selectedItems||[]).map(it=>it.product_id).filter(Boolean);
  await hydratePCSFor(pids);

  // เติม IDs ที่ขาด
  for (let i=0;i<selectedItems.length;i++){ await resolveItem(selectedItems[i]); }

  // จองสต๊อกตามรายการเดิม
  seedReservationsFromExisting();

  renderOrderItems();
  calculateTotals();
  hideLoadingIndicator();
  setupEventListeners();
}

/* ---------------- Search & Modal ---------------- */
let searchTimer;
function setupEventListeners(){
  const s = document.getElementById('product-search');
  if (s) s.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer=setTimeout(()=>searchProducts(s.value),300);
  });

  const shippingFee = document.getElementById('shipping-fee');
  if (shippingFee) shippingFee.addEventListener('input', calculateTotals);

  const variantSelect = document.getElementById('variant-select');
  if (variantSelect) variantSelect.addEventListener('change', () => { updateStockInfo(); updateVariantPrice(); });

  const modalQty = document.getElementById('variant-quantity');
  if (modalQty){
    modalQty.addEventListener('input', enforceModalQuantityLimit);
    modalQty.addEventListener('change', enforceModalQuantityLimit);
  }
}

function searchProducts(q){
  const resultsDiv = document.getElementById('search-results');
  if (!resultsDiv) return;
  if (!q || q.length<2){ resultsDiv.innerHTML=''; return; }

  fetch(`/products/search?q=${encodeURIComponent(q)}`)
    .then(r => r.ok ? r.json() : Promise.reject(r.status))
    .then(list=>{
      if (!Array.isArray(list) || !list.length){
        resultsDiv.innerHTML = '<div class="alert alert-info">ไม่พบสินค้าที่ค้นหา</div>'; return;
      }
      let html = '';
      list.forEach(p=>{
        const price = N(p.price||0).toLocaleString('th-TH',{minimumFractionDigits:2});
        const name = escapeHtml(p.name||'ไม่ระบุชื่อ');
        const sku  = escapeHtml(p.id_stock || p.sku || 'ไม่ระบุ');
        html += `
          <div class="border p-2 d-flex justify-content-between align-items-center mb-2">
            <div><strong>${name}</strong><br><small>รหัส: ${sku} | ราคา: ${price} บาท</small></div>
            <button type="button" class="btn btn-sm btn-success"
              onclick="showVariantModal(${p.id}, '${name.replace(/'/g,"\\'")}', ${N(p.price||0)})">เลือก</button>
          </div>`;
      });
      resultsDiv.innerHTML = html;
    })
    .catch(()=> resultsDiv.innerHTML = '<div class="alert alert-warning">ค้นหาไม่สำเร็จ</div>');
}

async function showVariantModal(id, name, price){
  currentProduct = { id, name, price: N(price||0) };
  const el = document.getElementById('selected-product-name');
  if (el) el.textContent = name;
  try {
    const res = await fetch(`/products/${id}/variants`);
    const data = res.ok ? await res.json() : [];
    populateVariantSelect(data, currentProduct.price);
    const modal = new bootstrap.Modal(document.getElementById('variantModal'));
    modal.show();
  } catch {
    showAlert('โหลดรายการสี-ไซส์ไม่สำเร็จ', 'danger');
  }
}

function populateVariantSelect(variants, defaultPrice){
  const select = document.getElementById('variant-select');
  const priceInput = document.getElementById('variant-price');
  if (!select) return;
  select.innerHTML = '<option value="">-- เลือกสี-ไซส์ --</option>';
  (variants||[]).forEach(v=>{
    const color = v.color_name || v.color?.name || 'สีมาตรฐาน';
    const size  = v.size_name  || v.size?.size_name || v.size?.name || 'ไซส์มาตรฐาน';
    const stock = N(v.quantity || v.stock || 0);
    const opt = document.createElement('option');
    opt.value = v.id;
    opt.textContent = `${color} - ${size} (คงเหลือ: ${stock})`;
    opt.dataset.stock = String(stock);
    opt.dataset.colorId = String(v.color_id ?? '');
    opt.dataset.sizeId  = String(v.size_id  ?? '');
    opt.dataset.colorName = color;
    opt.dataset.sizeName  = size;
    select.appendChild(opt);
  });
  if (priceInput) priceInput.value = Number(defaultPrice||0).toFixed(2);
}

function updateStockInfo(){
  const sel = document.getElementById('variant-select');
  const stockInfo = document.getElementById('stock-info');
  const qty = document.getElementById('variant-quantity');
  if (!sel || !qty) return;
  if (!sel.value){
    if (stockInfo) stockInfo.textContent = '-';
    qty.value='0'; qty.max=''; qty.disabled=true; qty.placeholder='เลือกสี-ไซส์ก่อน';
    return;
  }
  const st = N(sel.selectedOptions[0].dataset.stock || 0);
  if (stockInfo) stockInfo.textContent = String(st);
  qty.disabled = st<=0;
  qty.max = st>0 ? String(st) : '';
  qty.placeholder = `สต๊อก: ${st}`;
  if (!qty.value || N(qty.value)<1) qty.value = st>0 ? '1' : '0';
}

function enforceModalQuantityLimit(){
  const qty = document.getElementById('variant-quantity');
  if (!qty) return;
  const max = N(qty.max || 0);
  let val = N(qty.value || 0);
  if (!Number.isFinite(val) || val < 1) val = 1;
  if (max && val > max){ val = max; showAlert(`สต๊อกไม่พอ มีแค่ ${max} ชิ้น`,'warning'); }
  qty.value = String(val);
}

function updateVariantPrice(){
  const priceInput = document.getElementById('variant-price');
  if (priceInput && currentProduct) priceInput.value = Number(currentProduct.price||0).toFixed(2);
}

function changeQuantity(delta){
  const qty = document.getElementById('variant-quantity');
  if (!qty || qty.disabled) return;
  const max = N(qty.max || 0);
  let v = N(qty.value || 1) + N(delta);
  if (!Number.isFinite(v) || v < 1) v = 1;
  if (max && v > max){ v = max; showAlert(`สต๊อกไม่พอ มีแค่ ${max} ชิ้น`,'warning'); }
  qty.value = String(v);
}

function confirmAddProduct(){
  const sel = document.getElementById('variant-select');
  const qtyEl = document.getElementById('variant-quantity');
  const priceEl = document.getElementById('variant-price');
  if (!sel || !qtyEl || !priceEl){ alert('ไม่พบฟิลด์ในโมดอล'); return; }

  const variantId = N(sel.value||0);
  const qty = N(qtyEl.value||0);
  const price = N(priceEl.value||0);
  const opt = sel.selectedOptions[0];
  const stock = N(opt?.dataset.stock || 0);

  if (!variantId || qty < 1){ alert('กรุณาเลือกสี-ไซส์และจำนวน'); return; }

  const colorId = opt.dataset.colorId ? N(opt.dataset.colorId) : null;
  const sizeId  = opt.dataset.sizeId  ? N(opt.dataset.sizeId ) : null;
  const colorName = opt.dataset.colorName || 'สีมาตรฐาน';
  const sizeName  = opt.dataset.sizeName  || 'ไซส์มาตรฐาน';
  const variantName = `${colorName} - ${sizeName}`;

  // ถ้ามีแถวเดิมสี-ไซส์เดียวกันอยู่แล้ว → ไม่ให้ซ้ำ
  const dup = selectedItems.find(i => i.product_id===currentProduct.id && i.color_id===colorId && i.size_id===sizeId);
  if (dup){ alert('สินค้านี้ (สี-ไซส์เดียวกัน) ถูกเพิ่มแล้ว'); return; }

  // ตรวจ live availability จากเลดเจอร์ (ให้แม่นยำกับสิ่งที่อยู่ในตาราง)
  const key = LiveStock.ensureKey(currentProduct.id, colorId, sizeId, stock);
  const liveAvail = LiveStock.getAvailable(key);
  if (qty > liveAvail){
    alert(`สต๊อกไม่พอ มีแค่ ${liveAvail} ชิ้น`);
    return;
  }

  // จองในเลดเจอร์ก่อน
  LiveStock.reserve(key, qty);

  // บันทึกแถว
  const it = {
    product_id: currentProduct.id,
    product_name: currentProduct.name,
    unit_price: price,
    quantity: qty,
    total_price: price*qty,
    color_id: colorId,
    size_id: sizeId,
    color_size_id: variantId,
    color_name: colorName,
    size_name: sizeName,
    variant_name: variantName,
    is_existing_item: false,
    has_complete_ids: true,
    initial_qty: qty,
    _reserved: qty,         // จำว่าจองไว้เท่าไร
    max_stock: qty + LiveStock.getAvailable(key) // สำหรับแสดง max ตอน render รอบนี้
  };
  selectedItems.push(it);

  const modal = bootstrap.Modal.getInstance(document.getElementById('variantModal'));
  modal && modal.hide();
  renderOrderItems();
  calculateTotals();
  clearSearch();
  showAlert(`เพิ่มสินค้า "${currentProduct.name} - ${variantName}" สำเร็จ`, 'success');
}

/* ---------------- Table (render + guards) ---------------- */
function renderOrderItems() {
    const tbody = document.getElementById('order-items-body');
    if (!tbody) return;
    tbody.innerHTML = '';

    selectedItems.forEach((it, idx) => {
        const { free, max } = computeCapsForIndex(idx);
        it.max_stock = max;

        // ตรวจสอบและแก้ไขค่าที่ไม่ถูกต้อง
        if (!Number.isFinite(N(it.quantity)) || N(it.quantity) < 1) {
            it.quantity = (max > 0 ? 1 : 0);
            it._reserved = it.quantity;
        }
        it.total_price = N(it.unit_price) * N(it.quantity);

        const variantLabel = it.variant_name || 
            `${it.color_name || ''}${(it.color_name && it.size_name) ? ' - ' : ''}${it.size_name || ''}`;

        const tr = document.createElement('tr');
        tr.className = 'product-row';
        tr.dataset.productId = it.product_id;

        // สร้าง HTML สำหรับปุ่ม disabled state
        const hasVariant = it.color_id != null && it.size_id != null;
        const isDisabled = !hasVariant;
        const disabledAttr = isDisabled ? 'disabled' : '';
        const disabledClass = isDisabled ? 'disabled' : '';

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
                            ${disabledAttr}
                            title="${isDisabled ? 'ไม่สามารถแก้ไขได้เนื่องจากข้อมูลไม่ครบ' : 'ลดจำนวน'}">
                        <i class="fas fa-minus"></i>
                    </button>

                    <input type="number" 
                           class="form-control text-center"
                           value="${it.quantity}"
                           min="1"
                           max="${Number.isFinite(it.max_stock) ? it.max_stock : ''}"
                           step="1"
                           placeholder="${Number.isFinite(it.max_stock) ? `สต็อก: ${it.max_stock}` : 'สต็อก: 0'}"
                           oninput="enforceQtyLimit(this, ${idx})"
                           onchange="updateQuantity(${idx}, this.value)"
                           ${disabledAttr}
                           title="${isDisabled ? 'ไม่สามารถแก้ไขได้เนื่องจากข้อมูลไม่ครบ' : 'ใส่จำนวนที่ต้องการ'}">

                    <button type="button" 
                            class="btn btn-outline-secondary ${disabledClass}" 
                            onclick="changeItemQuantity(${idx}, 1)"
                            ${disabledAttr}
                            title="${isDisabled ? 'ไม่สามารถแก้ไขได้เนื่องจากข้อมูลไม่ครบ' : 'เพิ่มจำนวน'}">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="form-text mt-1">
                    ${hasVariant ? 
                        `คงเหลือ: <span class="text-${free > 5 ? 'success' : (free > 0 ? 'warning' : 'danger')}" id="stock-info-${idx}">${free}</span> ชิ้น` :
                        '<span class="text-danger">ไม่สามารถตรวจสอบสต็อกได้</span>'
                    }
                </div>
            </td>

            <td>
                <div class="input-group input-group-sm">
                    <input type="number" 
                           class="form-control"
                           value="${Number(it.unit_price).toFixed(2)}"
                           step="0.01" 
                           min="0"
                           onchange="updateItemPrice(${idx}, this.value)">
                    <span class="input-group-text">฿</span>
                </div>
            </td>

            <td class="fw-bold">${Number(it.total_price || 0).toFixed(2)}</td>

            <td>
                <button class="btn btn-danger btn-sm" 
                        onclick="removeItem(${idx})" 
                        title="ลบรายการ">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    if (!selectedItems.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-box-open fa-2x mb-2"></i>
                    <div>ยังไม่มีสินค้าในออเดอร์</div>
                </td>
            </tr>`;
    }

    updateItemsJson();
    calculateTotals();
}
function addQuantityControlStyles() {
    const styleId = 'quantity-control-styles';
    if (document.getElementById(styleId)) return;
    
    const style = document.createElement('style');
    style.id = styleId;
    style.textContent = `
        /* สไตล์สำหรับปุ่ม +/- */
        .input-group .btn.disabled,
        .input-group .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        /* สไตล์สำหรับ input ที่ disabled */
        .form-control:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        
        /* เน้นสีสต็อก */
        .text-success { color: #28a745 !important; }
        .text-warning { color: #ffc107 !important; }
        .text-danger { color: #dc3545 !important; }
        
        /* Animation สำหรับปุ่ม */
        .input-group .btn:not(:disabled):hover {
            transform: scale(1.05);
            transition: transform 0.1s ease;
        }
        
        .input-group .btn:not(:disabled):active {
            transform: scale(0.95);
        }
    `;
    document.head.appendChild(style);
}
document.addEventListener('DOMContentLoaded', function() {
    addQuantityControlStyles();
});
function enforceQtyLimit(el, index) {
    if (index < 0 || index >= selectedItems.length) return;
    const it = selectedItems[index];
    
    if (it.color_id == null || it.size_id == null) {
        el.value = it.quantity || 1;
        return;
    }

    const caps = computeCapsForIndex(index);
    let val = N(el.value || 0);
    
    // ตรวจสอบค่าที่ป้อน
    if (!Number.isFinite(val) || val < 1) {
        val = 1;
    }
    
    if (val > caps.max) {
        val = caps.max;
        showAlert(`สต็อกไม่พอ จำกัดที่ ${caps.max} ชิ้น`, 'warning');
    }
    
    // อัพเดทแบบ silent (ไม่ render ซ้ำทันที)
    setRowQuantity(index, val, true);
    
    // sync ค่ากลับไปที่ input
    el.value = String(selectedItems[index].quantity);
    
    // อัพเดทการแสดงผลเฉพาะข้อมูลที่เปลี่ยน
    updateSingleRowDisplay(index);
}
// ฟังก์ชันอัพเดทการแสดงผลเฉพาะแถวเดียว (เพื่อความเร็ว)
function updateSingleRowDisplay(index) {
    const it = selectedItems[index];  
    const caps = computeCapsForIndex(index);
    
    // อัพเดท total price
    it.total_price = N(it.unit_price) * N(it.quantity);
    
    // อัพเดทตัวเลขในตาราง
    const stockInfo = document.getElementById(`stock-info-${index}`);
    if (stockInfo) {
        stockInfo.textContent = caps.free;
    }
    
    // อัพเดท total price ในตาราง
    const row = document.querySelector(`#order-items-body tr:nth-child(${index + 1})`);
    if (row) {
        const totalCell = row.querySelector('td:nth-child(6)');
        if (totalCell) {
            totalCell.textContent = Number(it.total_price || 0).toFixed(2);
        }
    }
    
    // อัพเดทยอดรวมทั้งหมด
    calculateTotals();
    updateItemsJson();
}

function changeItemQuantity(index, delta) {
    if (index < 0 || index >= selectedItems.length) return;
    const it = selectedItems[index];
    
    // ตรวจสอบว่ามี variant IDs ครบหรือไม่
    if (it.color_id == null || it.size_id == null) {
        showAlert('ไม่สามารถเปลี่ยนจำนวนได้ เนื่องจากข้อมูลสี-ไซส์ไม่ครบถ้วน', 'warning');
        return;
    }

    const key = LiveStock.ensureKey(it.product_id, it.color_id, it.size_id);
    const caps = computeCapsForIndex(index);
    
    const currentQty = N(it.quantity);
    const newQty = currentQty + N(delta);
    
    // ตรวจสอบขีดจำกัด
    if (newQty < 1) {
        showAlert('จำนวนต้องไม่ต่ำกว่า 1', 'warning');
        return;
    }
    
    if (newQty > caps.max) {
        showAlert(`สต็อกไม่พอ มีเพียง ${caps.max} ชิ้น (คงเหลือ: ${caps.free}, จองไว้: ${it._reserved})`, 'warning');
        return;
    }
    
    // อัพเดทจำนวนผ่าน setRowQuantity (จะจัดการ ledger ให้อัตโนมัติ)
    setRowQuantity(index, newQty);
}

function updateQuantity(index, value) {
    if (index < 0 || index >= selectedItems.length) return;
    const it = selectedItems[index];
    
    if (it.color_id == null || it.size_id == null) {
        showAlert('ไม่สามารถเปลี่ยนจำนวนได้ เนื่องจากข้อมูลสี-ไซส์ไม่ครบถ้วน', 'warning');
        renderOrderItems();
        return;
    }
    
    const caps = computeCapsForIndex(index);
    let qty = N(value || 0);
    
    if (qty < 1) {
        showAlert('จำนวนต้องไม่ต่ำกว่า 1', 'warning');
        qty = 1;
    }
    
    if (qty > caps.max) {
        showAlert(`สต็อกไม่พอ มีเพียง ${caps.max} ชิ้น`, 'warning');
        qty = caps.max;
    }
    
    setRowQuantity(index, qty);
}

function updateItemPrice(index, value){
  const it = selectedItems[index]; if (!it) return;
  const p = N(value);
  if (!Number.isFinite(p) || p < 0){ showAlert('ราคาต้องไม่ติดลบ', 'warning'); renderOrderItems(); return; }
  it.unit_price = p;
  it.total_price = p*N(it.quantity);
  renderOrderItems();
}

function removeItem(index){
  const it = selectedItems[index];
  if (!confirm('คุณต้องการลบสินค้านี้ออกจากออเดอร์หรือไม่?')) return;

  // คืนสต๊อกในเลดเจอร์ให้ครบก่อนลบ
  if (it && it.color_id!=null && it.size_id!=null){
    const key = LiveStock.ensureKey(it.product_id, it.color_id, it.size_id);
    const prev = N(it._reserved||0);
    if (prev > 0) LiveStock.unreserve(key, prev);
  }

  selectedItems.splice(index, 1);
  renderOrderItems();
  calculateTotals();
  showAlert('ลบสินค้าเรียบร้อยแล้ว (คืนสต๊อกแล้ว)', 'success');
}

/* ---------------- Totals & Submit ---------------- */
function calculateTotals(){
  const sub = selectedItems.reduce((s,it)=> s + N(it.quantity)*N(it.unit_price), 0);
  const shipEl = document.getElementById('shipping-fee');
  const discountEl = document.getElementById('discount'); // hidden = 0
  const shipping = shipEl ? N(shipEl.value) : 0;
  const discount = discountEl ? N(discountEl.value) : 0;
  const total = sub + shipping - discount;

  const subEl = document.getElementById('subtotal-display');
  const totalEl = document.getElementById('total-amount');
  if (subEl) subEl.textContent = sub.toLocaleString('th-TH',{minimumFractionDigits:2});
  if (totalEl) totalEl.value = total.toLocaleString('th-TH',{minimumFractionDigits:2});
}

function updateItemsJson(){
  const payload = selectedItems.map(it=>({
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
    is_existing_item: !!it.is_existing_item,
    has_complete_ids: !!it.has_complete_ids
  }));
  const hidden = document.getElementById('items-json');
  if (hidden) hidden.value = JSON.stringify(payload);
}

function clearSearch(){ const s=document.getElementById('product-search'); const r=document.getElementById('search-results'); if (s) s.value=''; if (r) r.innerHTML=''; }
function resetForm(){ if (confirm('รีเซ็ตข้อมูลกลับค่าเดิม?')) location.reload(); }

async function submitOrder(){
  // ตรวจรายการใหม่ต้องมี ids ครบ
  for (let i=0;i<selectedItems.length;i++){
    const it = selectedItems[i];
    if (!it.is_existing_item && (!it.color_id || !it.size_id || !it.color_size_id)){
      alert(`สินค้าใหม่แถวที่ ${i+1} ขาดข้อมูลสี-ไซส์`); return;
    }
  }
  updateItemsJson();
  const totalEl = document.getElementById('total-amount');
  const total = totalEl ? totalEl.value : '0';
  if (!confirm(`ต้องการบันทึกการแก้ไขหรือไม่?\nยอดรวม: ${total} บาท\nจำนวนแถว: ${selectedItems.length}`)) return;

  const btn = document.querySelector('button[onclick="submitOrder()"]');
  showSubmitLoading(btn);
  setTimeout(()=> (document.getElementById('orderForm') || document.getElementById('order-form'))?.submit(), 200);
}

function showSubmitLoading(button){
  if (!button) return;
  button.disabled = true;
  button.dataset.originalText = button.innerHTML;
  button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังบันทึก...';
}
function hideSubmitLoading(button){
  if (button && button.dataset.originalText){
    button.disabled = false;
    button.innerHTML = button.dataset.originalText;
    delete button.dataset.originalText;
  }
}

/* ---------------- UI helpers ---------------- */
function showLoadingIndicator(){
  const d = document.createElement('div');
  d.id = 'loading-indicator';
  d.className = 'alert alert-info text-center';
  d.innerHTML = `<i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...`;
  document.querySelector('.container')?.insertBefore(d, document.querySelector('.container').firstChild);
}
function hideLoadingIndicator(){ document.getElementById('loading-indicator')?.remove(); }
function showAlert(message, type='info'){
  const div = document.createElement('div');
  div.className = `alert alert-${type} alert-dismissible fade show`;
  div.innerHTML = `
    <i class="fas fa-${type==='danger'?'exclamation-triangle':(type==='success'?'check-circle':'info-circle')}"></i>
    ${escapeHtml(message)}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
  const c = document.querySelector('.container');
  if (c){ c.insertBefore(div, c.firstChild); window.scrollTo({top:0, behavior:'smooth'}); }
  setTimeout(()=> div.parentNode && div.remove(), 5000);
}
async function loadAvailabilityForEdit(productId, orderId) {
  const [vRes, rRes] = await Promise.all([
    fetch(`/api/products/${productId}/variants`),
    fetch(`/api/products/${productId}/reserved?exclude_order_id=${orderId}`)
  ]);
  const variants = await vRes.json();
  const reserved = await rRes.json(); // {variant_id: qty}

  // แสดงผล
  const cont = document.getElementById('variantAvailability');
  cont.innerHTML = '';
  variants.forEach(v => {
    const r = Number(reserved[String(v.id)] || 0);
    const availableForThisOrder = Math.max(0, Number(v.quantity) - r);
    const row = document.createElement('div');
    row.innerHTML = `
      <div>
        <b>${v.color_name}</b> / ${v.size_name} :
        สต๊อก <b>${v.quantity}</b> |
        กำลังถูกจับ(ยกเว้นออเดอร์นี้) <b>${r}</b> |
        <span>คงเหลือสำหรับออเดอร์นี้</span> <b>${availableForThisOrder}</b> ชิ้น
      </div>`;
    cont.appendChild(row);
  });
}
</script>

@endsection