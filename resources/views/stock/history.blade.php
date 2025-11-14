{{-- resources/views/stock/history.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-history"></i> ประวัติการเปลี่ยนแปลงสต็อก</h2>

    {{-- ฟิลเตอร์ --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('stock.history') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">ประเภท</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">ทั้งหมด</option>
                        <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>เข้า</option>
                        <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>ออก</option>
                        <option value="adjust" {{ request('type') === 'adjust' ? 'selected' : '' }}>ปรับ</option>
                        <option value="reserve" {{ request('type') === 'reserve' ? 'selected' : '' }}>จอง</option>
                        <option value="release" {{ request('type') === 'release' ? 'selected' : '' }}>ปล่อย</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">สินค้า</label>
                    <select name="product_id" class="form-select form-select-sm">
                        <option value="">ทั้งหมด</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->id_stock }} - {{ $p->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">วันที่เริ่ม</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" 
                           value="{{ request('start_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" 
                           value="{{ request('end_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">ผู้ทำรายการ</label>
                    <input type="text" name="user_name" class="form-control form-control-sm" 
                           placeholder="ชื่อผู้ทำรายการ" value="{{ request('user_name') }}">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ตารางประวัติ --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th width="12%">วันที่/เวลา</th>
                            <th width="8%">ประเภท</th>
                            <th width="10%">รหัสสินค้า</th>
                            <th width="15%">ชื่อสินค้า</th>
                            <th width="10%">สี-ไซส์</th>
                            <th width="6%" class="text-end">ก่อน</th>
                            <th width="7%" class="text-end">เปลี่ยน</th>
                            <th width="6%" class="text-end">หลัง</th>
                            <th width="16%">เหตุผล</th>
                            <th width="8%">ผู้ทำรายการ</th>
                            <th width="8%">อ้างอิง</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                        <tr>
                            <td><small>{{ $t->created_at }}</small></td>
                            <td>
                                @switch($t->type)
                                    @case('in')
                                        <span class="badge bg-success">เข้า</span>
                                        @break
                                    @case('out')
                                        <span class="badge bg-danger">ออก</span>
                                        @break
                                    @case('adjust')
                                        <span class="badge bg-warning text-dark">ปรับ</span>
                                        @break
                                    @case('reserve')
                                        <span class="badge bg-info">จอง</span>
                                        @break
                                    @case('release')
                                        <span class="badge bg-secondary">ปล่อย</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $t->id_stock }}</td>
                            <td><small>{{ $t->product_name }}</small></td>
                            <td><small>{{ $t->variant_name }}</small></td>
                            <td class="text-end">{{ number_format($t->quantity_before) }}</td>
                            <td class="text-end">
                                <strong class="{{ $t->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $t->quantity > 0 ? '+' : '' }}{{ number_format($t->quantity) }}
                                </strong>
                            </td>
                            <td class="text-end">{{ number_format($t->quantity_after) }}</td>
                            <td><small>{{ $t->reason }}</small></td>
                            <td><small>{{ $t->user_name ?? '-' }}</small></td>
                            <td>
                                @if($t->reference_number)
                                    <small>{{ $t->reference_number }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                ไม่พบข้อมูลประวัติ
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            {{ $transactions->links() }}
        </div>
    </div>
</div>

<style>
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>
@endsection