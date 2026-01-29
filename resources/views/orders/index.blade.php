@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>จัดการออเดอร์</h1>
            <p class="text-muted mb-0">ระบบจัดการคำสั่งซื้อทั้งหมด</p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->role !== 'stock')
            <a href="{{ route('customers.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-users"></i> จัดการลูกค้า
            </a>
            {{-- ซ่อนปุ่มสร้างออเดอร์สำหรับยศ Stock --}}
            
            <a href="{{ route('orders.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> สร้างออเดอร์ใหม่
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ฟอร์มค้นหา --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('orders.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">ค้นหา</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="ชื่อลูกค้า หรือ เลขออเดอร์" 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="">-- ทั้งหมด --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>จัดส่งแล้ว</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>ส่งสำเร็จ</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">การชำระเงิน</label>
                        <select name="payment_status" class="form-select">
                            <option value="">-- ทั้งหมด --</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>รอชำระ</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>ชำระแล้ว</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">วันที่เริ่มต้น</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">วันที่สิ้นสุด</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> ค้นหา
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ตารางแสดงออเดอร์ --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>เลขออเดอร์</th>
                            <th>ลูกค้า</th>
                            <th>ยอดรวม</th>
                            <th>สถานะ</th>
                            <th>การชำระเงิน</th>
                            <th>เลขส่ง (Tracking)</th>
                            <th>สลิป</th>
                            <th>วันที่สั่งซื้อ</th>
                            <th width="300">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>
                                <strong>{{ $order->order_number }}</strong>
                            </td>
                            <td>
                                {{ $order->customer->name }}
                                @if($order->customer->phone)
                                <br><small class="text-muted">{{ $order->customer->phone }}</small>
                                @endif
                            </td>
                            <td>
                                <strong class="text-success">฿{{ number_format($order->total_price, 2) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $order->status == 'pending' ? 'warning' : 
                                    ($order->status == 'processing' ? 'info' : 
                                    ($order->status == 'shipped' ? 'primary' : 
                                    ($order->status == 'delivered' ? 'success' : 
                                    ($order->status == 'cancelled' ? 'danger' : 'secondary')))) 
                                }}">
                                    @switch($order->status)
                                        @case('pending') รอดำเนินการ @break
                                        @case('processing') กำลังจัดการ @break
                                        @case('shipped') จัดส่งแล้ว @break
                                        @case('delivered') ส่งสำเร็จ @break
                                        @case('cancelled') ยกเลิก @break
                                        @default {{ $order->status }}
                                    @endswitch
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                    {{ $order->payment_status == 'paid' ? 'ชำระแล้ว' : 'รอชำระ' }}
                                </span>
                            </td>
                            <td>
                                @if($order->tracking_number)
                                    <span class="text-primary fw-bold">{{ $order->tracking_number }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($order->slip_image)
                                    <a href="{{ asset('storage/' . $order->slip_image) }}" target="_blank" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-receipt"></i> ดูสลิป
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    {{-- ซ่อนปุ่มแก้ไขสำหรับยศ Stock --}}
                                    @if(auth()->user()->role !== 'stock')
                                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning" title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                </div>
                                
                                {{-- ซ่อนปุ่มชำระเงินและ Tracking สำหรับยศ Stock --}}
                                @if(auth()->user()->role !== 'stock')
                                    @if($order->payment_status === 'pending')
                                    <button class="btn btn-success btn-sm btn-open-payment-modal" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#paymentModal" 
                                            data-id="{{ $order->id }}"
                                            data-order-number="{{ $order->order_number }}"
                                            title="ชำระเงิน">
                                        <i class="fas fa-money-bill-wave"></i> ชำระเงิน
                                    </button>
                                    @endif
                                    
                                    @if($order->payment_status === 'paid')
                                    <button class="btn btn-primary btn-sm btn-open-tracking-modal" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#trackingModal" 
                                            data-id="{{ $order->id }}"
                                            data-order-number="{{ $order->order_number }}"
                                            data-tracking="{{ $order->tracking_number ?? '' }}"
                                            title="จัดการ Tracking">
                                        <i class="fas fa-truck"></i> Tracking
                                    </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">ไม่พบข้อมูลออเดอร์</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    {{-- สถิติสรุป --}}
    @if($orders->count() > 0)
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $orders->total() }}</h4>
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
                            @php
                                $totalItems = $orders->sum(function($order) {
                                    return $order->orderItems->sum('quantity');
                                });
                            @endphp
                            <h4>{{ $totalItems }}</h4>
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

{{-- ================= Modal ชำระเงิน (เฉพาะยศที่ไม่ใช่ Stock) ================= --}}
@if(auth()->user()->role !== 'stock')
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="payment-form" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">
                        แนบสลิปชำระเงิน
                        <span class="text-muted ms-2" id="payment-order-display"></span>
                    </h5>
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

{{-- ================= Modal Tracking Number (เฉพาะยศที่ไม่ใช่ Stock) ================= --}}
<div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="tracking-form">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="trackingModalLabel">
                        เพิ่ม/แก้ไข Tracking Number
                        <span class="text-muted ms-2" id="tracking-order-display"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tracking_number" class="form-label">Tracking Number</label>
                        <input type="text" class="form-control" name="tracking_number" id="tracking_number" 
                               placeholder="กรอกเลข Tracking">
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
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment Modal
    const paymentButtons = document.querySelectorAll('.btn-open-payment-modal');
    const paymentForm = document.getElementById('payment-form');
    const paymentOrderDisplay = document.getElementById('payment-order-display');
    
    if (paymentButtons.length > 0 && paymentForm) {
        paymentButtons.forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                const orderNumber = this.getAttribute('data-order-number');
                
                paymentForm.action = `/orders/${orderId}/pay`;
                paymentOrderDisplay.textContent = `(${orderNumber})`;
            });
        });
    }
    
    // Tracking Modal
    const trackingButtons = document.querySelectorAll('.btn-open-tracking-modal');
    const trackingForm = document.getElementById('tracking-form');
    const trackingOrderDisplay = document.getElementById('tracking-order-display');
    const trackingNumberInput = document.getElementById('tracking_number');
    
    if (trackingButtons.length > 0 && trackingForm) {
        trackingButtons.forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                const orderNumber = this.getAttribute('data-order-number');
                const currentTracking = this.getAttribute('data-tracking');
                
                trackingForm.action = `/orders/${orderId}/tracking`;
                trackingOrderDisplay.textContent = `(${orderNumber})`;
                trackingNumberInput.value = currentTracking || '';
            });
        });
    }
});
</script>
@endpush