@extends('layouts.app')

@section('content')
<div class="container">
    {{-- ส่วนหัว --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h3 class="mb-1">
                <i class="fas fa-history text-muted"></i> ประวัติสต็อก
            </h3>
            <h5 class="text-primary">
                {{ $variant->product_name }} 
                <span class="badge bg-secondary">{{ $variant->color_name ?? '-' }}</span>
                <span class="badge bg-secondary">{{ $variant->size_name ?? '-' }}</span>
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
                        <th style="width: 15%">วัน/เวลา</th>
                        <th style="width: 10%">ประเภท</th>
                        <th class="text-end" style="width: 8%">
                            <span data-bs-toggle="tooltip" title="จำนวนก่อนทำรายการ">ก่อน</span>
                        </th>
                        <th class="text-end" style="width: 8%">
                            <span data-bs-toggle="tooltip" title="จำนวนที่เปลี่ยน (+/-)">เปลี่ยน</span>
                        </th>
                        <th class="text-end" style="width: 8%">
                            <span data-bs-toggle="tooltip" title="จำนวนหลังทำรายการ">หลัง</span>
                        </th>
                        <th class="text-end" style="width: 8%">
                            <span class="badge bg-success" data-bs-toggle="tooltip" title="จำนวนพร้อมขาย (On-Hand - Reserved)">พร้อมขาย</span>
                        </th>
                        <th style="width: 20%">เหตุผล</th>
                        <th style="width: 10%">ผู้ทำรายการ</th>
                        <th style="width: 13%">อ้างอิง</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $row)
                    @php
                        // ✅ คำนวณ Available (พร้อมขาย) หลังทำรายการ
                        $availableAfter = $row->after;
                        
                        // ถ้าเป็นการจอง (reserve) ให้ลด Available ลง
                        if ($row->type === 'reserve') {
                            $availableAfter = $row->after - abs($row->delta);
                        }
                        // ถ้าเป็นการปล่อย (release) ให้เพิ่ม Available ขึ้น
                        elseif ($row->type === 'release') {
                            $availableAfter = $row->after + abs($row->delta);
                        }
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
                            <span class="badge bg-success">{{ number_format($availableAfter) }}</span>
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