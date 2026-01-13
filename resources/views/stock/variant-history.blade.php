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
        
        {{-- ปุ่ม Filter (จุดที่เคย Error แก้ให้แล้ว) --}}
        <div class="col-md-4 text-end">
            <div class="btn-group" role="group">
                {{-- ✅ แก้ไข: ใช้ 'variant' => $variantId ให้ตรงกับ Route --}}
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
            
            <a href="{{ route('stock.adjust.form', $variantId) }}" class="btn btn-warning ms-2">
                <i class="fas fa-edit"></i> ปรับสต็อก
            </a>
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
                        <th class="text-end" style="width: 10%">ก่อน</th>
                        <th class="text-end" style="width: 10%">เปลี่ยน</th>
                        <th class="text-end" style="width: 10%">หลัง</th>
                        <th style="width: 20%">เหตุผล</th>
                        <th style="width: 10%">ผู้ทำรายการ</th>
                        <th style="width: 15%">อ้างอิง</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $row)
                    <tr>
                        <td>{{ $row->created_at }}</td>
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
                        <td>{{ $row->reason }}</td>
                        <td>{{ $row->user_name }}</td>
                        <td>
                            @if($row->order_id)
                                <a href="{{ route('orders.show', $row->order_id) }}" class="text-decoration-none">
                                    <i class="fas fa-file-invoice"></i> {{ $row->ref }}
                                </a>
                            @else
                                {{ $row->ref ?? '-' }}
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
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
@endsection