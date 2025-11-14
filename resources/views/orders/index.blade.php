@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>จัดการออเดอร์</h1>
        <a href="{{ route('orders.create') }}" class="btn btn-success">สร้างออเดอร์ใหม่</a>
    </div>

    <!-- ฟอร์มค้นหา -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('orders.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อลูกค้า หรือ เลขออเดอร์" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">-- สถานะทั้งหมด --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>จัดส่งแล้ว</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>ส่งสำเร็จ</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="payment_status" class="form-control">
                            <option value="">-- การชำระเงินทั้งหมด --</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>รอชำระ</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>ชำระแล้ว</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ตารางแสดงออเดอร์ -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>เลขออเดอร์</th>
                            <th>ลูกค้า</th>
                            <th>ยอดรวม</th>
                            <th>สถานะ</th>
                            <th>การชำระเงิน</th>
                            <th>วันที่สั่งซื้อ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->customer->name }}</td>
                            <td>{{ number_format($order->total_price, 2) }} บาท</td>
                            <td>
                                <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'completed' ? 'success' : 'info') }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                    {{ $order->payment_status }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info btn-sm">ดู</a>
                                <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning btn-sm">แก้ไข</a>
                                
                                {{-- Quick actions --}}
                                @if($order->payment_status === 'pending')
                                <button class="btn btn-success btn-sm btn-open-payment-modal" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#paymentModal" 
                                        data-id="{{ $order->id }}"
                                        data-order-number="{{ $order->order_number }}">
                                    <i class="fas fa-money-bill-wave"></i> ชำระเงิน
                                </button>
                                @endif
                                
                                @if($order->payment_status === 'paid')
                                <button class="btn btn-warning btn-sm btn-open-tracking-modal" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#trackingModal" 
                                        data-id="{{ $order->id }}"
                                        data-order-number="{{ $order->order_number }}"
                                        data-tracking="{{ $order->tracking_number ?? '' }}">
                                    <i class="fas fa-truck"></i> Tracking
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">ไม่พบข้อมูลออเดอร์</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    <!-- สถิติสรุป -->
    @if($orders->count() > 0)
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $orders->count() }}</h4>
                            <p class="mb-0">ออเดอร์ทั้งหมด</p>
                        </div>
                        <div>
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>฿{{ number_format($orders->sum('total_price'), 2) }}</h4>
                            <p class="mb-0">ยอดขายรวม</p>
                        </div>
                        <div>
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $orders->sum(function($order) { return $order->orderItems->sum('quantity'); }) }}</h4>
                            <p class="mb-0">สินค้าที่ขาย</p>
                        </div>
                        <div>
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>฿{{ number_format($orders->avg('total_price'), 2) }}</h4>
                            <p class="mb-0">ค่าเฉลี่ยต่อออเดอร์</p>
                        </div>
                        <div>
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- ✅ Modal ชำระเงิน - แก้ไขแล้ว -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="payment-form" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">แนบสลิปชำระเงิน</h5>
                    <span class="text-muted ms-2" id="payment-order-display"></span>
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

<!-- ✅ Modal สำหรับแก้ไข Tracking Number - แก้ไขแล้ว -->
<div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="tracking-form">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="trackingModalLabel">เพิ่ม/แก้ไข Tracking Number</h5>
                    <span class="text-muted ms-2" id="tracking-order-display"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tracking_number" class="form-label">Tracking Number</label>
                        <input type="text" class="form-control" name="tracking_number" id="tracking_number" placeholder="กรอกเลข Tracking">
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
@endsection

@push('scripts')
<script>
// ✅ จัดการ Modal ชำระเงิน
document.addEventListener('DOMContentLoaded', function() {
    // Payment Modal
    const paymentButtons = document.querySelectorAll('.btn-open-payment-modal');
    const paymentForm = document.getElementById('payment-form');
    const paymentOrderDisplay = document.getElementById('payment-order-display');
    
    paymentButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-id');
            const orderNumber = this.getAttribute('data-order-number');
            
            // อัปเดต action ของฟอร์ม
            paymentForm.action = `/orders/${orderId}/pay`;
            
            // แสดงเลขออเดอร์ใน Modal
            paymentOrderDisplay.textContent = `(${orderNumber})`;
            
            console.log('Payment Modal - Order ID:', orderId, 'Order Number:', orderNumber);
        });
    });
    
    // Tracking Modal
    const trackingButtons = document.querySelectorAll('.btn-open-tracking-modal');
    const trackingForm = document.getElementById('tracking-form');
    const trackingOrderDisplay = document.getElementById('tracking-order-display');
    const trackingNumberInput = document.getElementById('tracking_number');
    
    trackingButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-id');
            const orderNumber = this.getAttribute('data-order-number');
            const currentTracking = this.getAttribute('data-tracking');
            
            // อัปเดต action ของฟอร์ม
            trackingForm.action = `/orders/${orderId}/tracking`;
            
            // แสดงเลขออเดอร์ใน Modal
            trackingOrderDisplay.textContent = `(${orderNumber})`;
            
            // ใส่ tracking number เดิม (ถ้ามี)
            trackingNumberInput.value = currentTracking || '';
            
            console.log('Tracking Modal - Order ID:', orderId, 'Order Number:', orderNumber);
        });
    });
});
</script>
@endpush