{{-- resources/views/stock/history.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
/* Stock History Page - Dark Gold Theme */
:root {
    --dark-bg: #0a0a0a;
    --dark-secondary: #121212;
    --dark-tertiary: #1e1e1e;
    --gold: #FFD700;
    --gold-dark: #e6c300;
    --text-primary: #f8f8f8;
    --text-secondary: #cccccc;
    --border-gold: #d4af37;
}

/* Container */
#stock-history-page {
    background-color: transparent;
    min-height: 100vh;
}

/* Header */
#stock-history-page h2 {
    color: var(--gold) !important;
    text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
    font-weight: 600;
    margin-bottom: 1.5rem;
    letter-spacing: 0.5px;
}

#stock-history-page h2 i {
    color: var(--gold);
    margin-right: 0.5rem;
}

/* Card styling */
#stock-history-page .card {
    background: linear-gradient(135deg, var(--dark-secondary), var(--dark-tertiary)) !important;
    border: 1px solid var(--border-gold) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4), 0 0 20px rgba(212, 175, 55, 0.1) !important;
    border-radius: 12px !important;
}

#stock-history-page .card-body {
    color: var(--text-primary);
}

#stock-history-page .card-footer {
    background: linear-gradient(90deg, rgba(212, 175, 55, 0.05), transparent) !important;
    border-top: 1px solid var(--border-gold) !important;
    color: var(--text-primary);
}

/* Form elements */
#stock-history-page .form-label {
    color: var(--text-primary) !important;
    font-weight: 500;
    margin-bottom: 0.4rem;
    font-size: 0.9rem;
}

#stock-history-page .form-control,
#stock-history-page .form-select {
    background-color: var(--dark-secondary) !important;
    border: 1px solid rgba(212, 175, 55, 0.3) !important;
    color: var(--text-primary) !important;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    transition: none;
}

#stock-history-page .form-control:focus,
#stock-history-page .form-select:focus {
    background-color: var(--dark-tertiary) !important;
    border-color: var(--gold) !important;
    box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25) !important;
    color: var(--text-primary) !important;
}

#stock-history-page .form-control::placeholder {
    color: rgba(204, 204, 204, 0.5);
}

#stock-history-page .form-select option {
    background-color: var(--dark-secondary);
    color: var(--text-primary);
}

/* Button */
#stock-history-page .btn-primary {
    background: linear-gradient(135deg, var(--gold-dark), var(--gold)) !important;
    border: none !important;
    color: #000 !important;
    font-weight: 600;
    border-radius: 6px;
    box-shadow: 0 4px 10px rgba(255, 215, 0, 0.3);
    transition: none;
}

/* Table styling */
#stock-history-page .table {
    color: var(--text-primary) !important;
    border-color: rgba(212, 175, 55, 0.2);
    margin-bottom: 0;
}

#stock-history-page .table thead th {
    color: var(--gold) !important;
    font-weight: 600;
    border-bottom: 2px solid var(--border-gold) !important;
    background-color: rgba(212, 175, 55, 0.1) !important;
    padding: 0.85rem 0.75rem;
    font-size: 0.9rem;
    position: sticky;
    top: 0;
    z-index: 10;
}

#stock-history-page .table tbody td {
    color: var(--text-primary) !important;
    border-color: rgba(212, 175, 55, 0.15);
    padding: 0.75rem;
    vertical-align: middle;
}

#stock-history-page .table tbody td small {
    color: var(--text-primary);
}

#stock-history-page .table-hover tbody tr {
    transition: none;
}

/* Badges */
#stock-history-page .badge.bg-success {
    background-color: rgba(25, 135, 84, 0.2) !important;
    color: #75b798 !important;
    border: 1px solid rgba(25, 135, 84, 0.4);
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

#stock-history-page .badge.bg-danger {
    background-color: rgba(220, 53, 69, 0.2) !important;
    color: #ff6b6b !important;
    border: 1px solid rgba(220, 53, 69, 0.4);
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

#stock-history-page .badge.bg-warning {
    background-color: rgba(255, 215, 0, 0.2) !important;
    color: var(--gold) !important;
    border: 1px solid rgba(255, 215, 0, 0.4);
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

#stock-history-page .badge.bg-info {
    background-color: rgba(13, 202, 240, 0.2) !important;
    color: #5fedff !important;
    border: 1px solid rgba(13, 202, 240, 0.4);
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

#stock-history-page .badge.bg-secondary {
    background-color: rgba(108, 117, 125, 0.2) !important;
    color: var(--text-secondary) !important;
    border: 1px solid rgba(108, 117, 125, 0.4);
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

/* Text colors */
#stock-history-page .text-success {
    color: #75b798 !important;
}

#stock-history-page .text-danger {
    color: #ff6b6b !important;
}

#stock-history-page .text-muted {
    color: var(--text-secondary) !important;
}

#stock-history-page .text-end {
    text-align: right !important;
    font-variant-numeric: tabular-nums;
}

/* Pagination */
#stock-history-page .pagination {
    margin-bottom: 0;
}

#stock-history-page .page-link {
    background-color: var(--dark-secondary) !important;
    border: 1px solid rgba(212, 175, 55, 0.3) !important;
    color: var(--text-primary) !important;
    transition: none;
}

#stock-history-page .page-link:hover {
    background-color: var(--dark-tertiary) !important;
    border-color: var(--gold) !important;
    color: var(--gold) !important;
}

#stock-history-page .page-item.active .page-link {
    background-color: var(--gold) !important;
    border-color: var(--gold) !important;
    color: #000 !important;
}

#stock-history-page .page-item.disabled .page-link {
    background-color: rgba(18, 18, 18, 0.5) !important;
    border-color: rgba(212, 175, 55, 0.15) !important;
    color: rgba(204, 204, 204, 0.5) !important;
}

/* Responsive */
@media (max-width: 768px) {
    #stock-history-page {
        padding: 0;
    }
    
    #stock-history-page .table {
        font-size: 0.85rem;
    }
}
</style>

<div class="container-fluid" id="stock-history-page">
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
@endsection