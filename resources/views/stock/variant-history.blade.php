@extends('layouts.app')

@section('content')
<style>
/* Variant History Page - Dark Gold Theme */
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
#variant-history-page {
    background-color: transparent;
    min-height: 100vh;
}

/* Header Section */
#variant-history-page h3 {
    color: var(--gold) !important;
    text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

#variant-history-page h3 i {
    color: var(--text-secondary);
}

#variant-history-page h5 {
    color: var(--text-primary) !important;
    font-weight: 500;
}

/* Badge styling */
#variant-history-page .badge.bg-secondary {
    background-color: rgba(108, 117, 125, 0.3) !important;
    color: var(--text-secondary) !important;
    border: 1px solid rgba(108, 117, 125, 0.5);
    padding: 0.35rem 0.75rem;
    font-weight: 500;
}

/* Button Group */
#variant-history-page .btn-group .btn-outline-primary {
    border: 2px solid rgba(212, 175, 55, 0.4) !important;
    color: var(--text-primary) !important;
    background-color: transparent !important;
    font-weight: 500;
    transition: none;
}

#variant-history-page .btn-group .btn-outline-primary.active {
    background: linear-gradient(135deg, var(--gold-dark), var(--gold)) !important;
    border-color: var(--gold) !important;
    color: #000 !important;
    box-shadow: 0 4px 10px rgba(255, 215, 0, 0.3);
}

/* Summary Cards */
#variant-history-page .card {
    background: linear-gradient(135deg, var(--dark-secondary), var(--dark-tertiary)) !important;
    border: 1px solid var(--border-gold) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4), 0 0 20px rgba(212, 175, 55, 0.1) !important;
    border-radius: 12px !important;
    transition: none;
}

#variant-history-page .card.bg-light {
    background: linear-gradient(135deg, #1a1a1a, #252525) !important;
    border: 2px solid var(--text-secondary) !important;
}

#variant-history-page .card.bg-success {
    background: linear-gradient(135deg, rgba(25, 135, 84, 0.3), rgba(25, 135, 84, 0.2)) !important;
    border: 2px solid rgba(25, 135, 84, 0.6) !important;
}

#variant-history-page .card-body small {
    color: var(--text-secondary) !important;
    font-size: 0.85rem;
}

#variant-history-page .card-body .text-white-50 {
    color: rgba(248, 248, 248, 0.7) !important;
}

#variant-history-page .card-body .fs-2 {
    color: var(--gold) !important;
    text-shadow: 0 0 8px rgba(255, 215, 0, 0.4);
}

#variant-history-page .card-body .text-warning {
    color: #ffc107 !important;
}

#variant-history-page .card.bg-success .fs-2 {
    color: #75b798 !important;
    text-shadow: 0 0 8px rgba(117, 183, 152, 0.4);
}

/* Table Container */
#variant-history-page .table-responsive {
    background-color: transparent;
}

/* Table */
#variant-history-page .table {
    color: var(--text-primary) !important;
    border-color: rgba(212, 175, 55, 0.2);
    margin-bottom: 0;
}

#variant-history-page .table thead th {
    color: var(--gold) !important;
    font-weight: 600;
    border-bottom: 2px solid var(--border-gold) !important;
    background-color: rgba(212, 175, 55, 0.1) !important;
    padding: 1rem 0.75rem;
    font-size: 0.9rem;
}

#variant-history-page .table tbody td {
    color: var(--text-primary) !important;
    border-color: rgba(212, 175, 55, 0.15);
    padding: 0.85rem 0.75rem;
    vertical-align: middle;
}

#variant-history-page .table tbody td small {
    color: var(--text-primary);
}

#variant-history-page .table-hover tbody tr {
    transition: none;
}

/* Badges in table */
#variant-history-page .badge.bg-success {
    background-color: rgba(25, 135, 84, 0.2) !important;
    color: #75b798 !important;
    border: 1px solid rgba(25, 135, 84, 0.4);
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

#variant-history-page .badge.bg-danger {
    background-color: rgba(220, 53, 69, 0.2) !important;
    color: #ff6b6b !important;
    border: 1px solid rgba(220, 53, 69, 0.4);
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

#variant-history-page .badge.bg-warning {
    background-color: rgba(255, 215, 0, 0.2) !important;
    color: var(--gold) !important;
    border: 1px solid rgba(255, 215, 0, 0.4);
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

#variant-history-page .badge.bg-info {
    background-color: rgba(13, 202, 240, 0.2) !important;
    color: #5fedff !important;
    border: 1px solid rgba(13, 202, 240, 0.4);
    padding: 0.4rem 0.8rem;
    font-weight: 500;
}

/* Text colors */
#variant-history-page .text-success {
    color: #75b798 !important;
}

#variant-history-page .text-danger {
    color: #ff6b6b !important;
}

#variant-history-page .text-info {
    color: #5fedff !important;
}

#variant-history-page .text-warning {
    color: var(--gold) !important;
}

#variant-history-page .text-muted {
    color: var(--text-secondary) !important;
}

#variant-history-page .text-end {
    text-align: right !important;
    font-variant-numeric: tabular-nums;
}

/* Links */
#variant-history-page a {
    color: var(--gold) !important;
    text-decoration: none;
}

#variant-history-page a:hover {
    color: var(--gold-dark) !important;
}

/* Empty state */
#variant-history-page .fa-box-open {
    color: var(--text-secondary) !important;
    opacity: 0.5;
}

/* Responsive */
@media (max-width: 768px) {
    #variant-history-page {
        padding: 0;
    }
    
    #variant-history-page .table {
        font-size: 0.85rem;
    }
    
    #variant-history-page .btn-group {
        display: flex;
        flex-direction: column;
    }
    
    #variant-history-page .btn-group .btn {
        border-radius: 6px !important;
        margin-bottom: 0.5rem;
    }
}
</style>

<div class="container" id="variant-history-page">
    {{-- ส่วนหัว --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h3 class="mb-1">
                <i class="fas fa-history text-muted"></i> ประวัติสต็อก
            </h3>
            <h5>
                <span style="color: var(--gold); font-weight: 600; text-shadow: 0 0 8px rgba(255, 215, 0, 0.4);">{{ $variant->product_name }}</span>
                @if($variant->color_name)
                <span class="badge" style="background: linear-gradient(135deg, var(--gold-dark), var(--gold)) !important; color: #000 !important; font-weight: 600; padding: 0.4rem 0.9rem; margin-left: 0.5rem;">
                    สี: {{ $variant->color_name }}
                </span>
                @endif
                @if($variant->size_name)
                <span class="badge" style="background: linear-gradient(135deg, var(--gold-dark), var(--gold)) !important; color: #000 !important; font-weight: 600; padding: 0.4rem 0.9rem;">
                    ไซส์: {{ $variant->size_name }}
                </span>
                @endif
            </h5>
        </div>
        
        {{-- ปุ่ม Filter --}}
        <div class="col-md-4 text-end">
            <div class="btn-group" role="group">
                <a href="{{ route('stock.variant.history', ['variant' => $variantId, 'scope' => 'all']) }}" 
                   class="btn btn-outline-primary {{ $scope === 'all' ? 'active' : '' }}">
                   ทั้งหมด
                </a>
                <a href="{{ route('stock.variant.history', ['variant' => $variantId, 'scope' => 'physical']) }}" 
                   class="btn btn-outline-primary {{ $scope === 'physical' ? 'active' : '' }}">
                   เข้า/ออก
                </a>
                <a href="{{ route('stock.variant.history', ['variant' => $variantId, 'scope' => 'holds']) }}" 
                   class="btn btn-outline-primary {{ $scope === 'holds' ? 'active' : '' }}">
                   จอง/ปล่อย
                </a>
            </div>
        </div>
    </div>

    {{-- การ์ดสรุปยอด --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <small class="text-muted">จำนวนจริงในคลัง (On-Hand)</small>
                    <div class="fs-2 fw-bold text-dark">{{ number_format($summary->current) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <small class="text-muted">ติดจอง (Reserved)</small>
                    <div class="fs-2 fw-bold text-warning">{{ number_format($summary->reserved) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <small class="text-white-50">พร้อมขาย (Available)</small>
                    <div class="fs-2 fw-bold">{{ number_format($summary->available) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ตารางประวัติ --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 14%">วัน/เวลา</th>
                        <th style="width: 8%">ประเภท</th>
                        <th class="text-end" style="width: 8%">
                            <span data-bs-toggle="tooltip" title="จำนวนก่อนทำรายการ (On-Hand)">ก่อน</span>
                        </th>
                        <th class="text-end" style="width: 8%">
                            <span data-bs-toggle="tooltip" title="จำนวนที่เปลี่ยน (+/-)">เปลี่ยน</span>
                        </th>
                        <th class="text-end" style="width: 8%">
                            <span data-bs-toggle="tooltip" title="จำนวนหลังทำรายการ (On-Hand)">หลัง</span>
                        </th>
                        <th class="text-end" style="width: 9%">
                            <span class="badge bg-success" data-bs-toggle="tooltip" title="จำนวนพร้อมขาย = On-Hand - Reserved">พร้อมขาย</span>
                        </th>
                        <th style="width: 20%">เหตุผล</th>
                        <th style="width: 10%">ผู้ทำรายการ</th>
                        <th style="width: 15%">อ้างอิง</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // ✅ เริ่มต้นจาก Summary ปัจจุบัน
                        $currentOnHand = $summary->current;
                        $currentReserved = $summary->reserved;
                        $currentAvailable = $summary->available;
                    @endphp
                    
                    @forelse($history as $index => $row)
                    @php
                        // ✅ คำนวณ Available ณ เวลานั้น (ย้อนกลับ)
                        // เริ่มจากปัจจุบัน แล้วย้อนกลับตาม transaction
                        
                        if ($index === 0) {
                            // รายการแรก (ล่าสุด) = สถานะปัจจุบัน
                            $availableAtTime = $currentAvailable;
                        } else {
                            // คำนวณจากรายการก่อนหน้า
                            $prevRow = $history[$index - 1];
                            
                            // ย้อนกลับตาม type
                            if ($row->type === 'reserve') {
                                // ก่อนจอง Available มากกว่า (ย้อนกลับ = เพิ่ม)
                                $availableAtTime = $prevAvailable + abs($row->delta);
                            } elseif ($row->type === 'release') {
                                // ก่อนปล่อย Available น้อยกว่า (ย้อนกลับ = ลด)
                                $availableAtTime = $prevAvailable - abs($row->delta);
                            } elseif ($row->type === 'in') {
                                // ก่อนเข้า Available น้อยกว่า (ย้อนกลับ = ลด)
                                $availableAtTime = $prevAvailable - abs($row->delta);
                            } elseif ($row->type === 'out') {
                                // ก่อนออก Available มากกว่า (ย้อนกลับ = เพิ่ม)
                                $availableAtTime = $prevAvailable + abs($row->delta);
                            } else {
                                $availableAtTime = $prevAvailable;
                            }
                        }
                        
                        // เก็บไว้ใช้รายการถัดไป
                        $prevAvailable = $availableAtTime;
                    @endphp
                    <tr>
                        <td><small>{{ $row->created_at }}</small></td>
                        <td>
                            @php
                                $badges = [
                                    'in' => 'bg-success',
                                    'out' => 'bg-danger',
                                    'reserve' => 'bg-warning text-dark',
                                    'release' => 'bg-info text-dark'
                                ];
                                $bg = $badges[$row->type] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $bg }}">{{ $row->type_th }}</span>
                        </td>
                        <td class="text-end text-muted">{{ number_format($row->before) }}</td>
                        <td class="text-end fw-bold {{ $row->delta > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $row->delta_str }}
                        </td>
                        <td class="text-end fw-bold">{{ number_format($row->after) }}</td>
                        <td class="text-end">
                            <span class="badge bg-success">{{ number_format($availableAtTime) }}</span>
                        </td>
                        <td>
                            <small>
                                @if(str_contains($row->reason, 'แก้ไขออเดอร์'))
                                    @if(str_contains($row->reason, 'Release Old'))
                                        <span class="text-info">🔄 ปล่อยจอง (แก้ไขเดิม)</span>
                                    @elseif(str_contains($row->reason, 'Reserve New'))
                                        <span class="text-warning">🔄 จองใหม่ (หลังแก้ไข)</span>
                                    @else
                                        {{ $row->reason }}
                                    @endif
                                @else
                                    {{ $row->reason }}
                                @endif
                            </small>
                        </td>
                        <td><small>{{ $row->user_name }}</small></td>
                        <td>
                            @if($row->order_id)
                                <a href="{{ route('orders.show', $row->order_id) }}" class="text-decoration-none">
                                    <i class="fas fa-file-invoice"></i> {{ $row->ref }}
                                </a>
                            @else
                                <small class="text-muted">{{ $row->ref ?? '-' }}</small>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3"></i><br>
                            ยังไม่มีประวัติการเคลื่อนไหว
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
// เปิดใช้งาน tooltip
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
@endsection