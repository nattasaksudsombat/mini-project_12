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
                <div class="col-md-12">
                    <p><strong>ที่อยู่จัดส่ง:</strong><br>{{ $order->customer->address }}</p>
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

{{-- ================= CSS สำหรับพิมพ์ ================= --}}
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
<!-- JsBarcode CDN -->
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
@endpush