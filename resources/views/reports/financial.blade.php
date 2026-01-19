@extends('layouts.app')

@section('title', 'รายงาน & วิเคราะห์ - การเงิน')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">💰 รายงานรายรับ-รายจ่าย</h2>
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary me-2">
                ← กลับ Dashboard
            </a>
            <a href="{{ route('reports.export.financial') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
               class="btn btn-success">
                📥 Export Excel
            </a>
        </div>
    </div>

    {{-- ฟอร์มเลือกช่วงวันที่ --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.financial') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">🔍 ค้นหา</button>
                </div>
            </form>
        </div>
    </div>

    {{-- สรุปยอดรวม --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="text-muted">รวมรายรับ</h6>
                    <h3 class="text-success">{{ number_format($totalIncome, 2) }}</h3>
                    <small class="text-muted">บาท</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h6 class="text-muted">รวมรายจ่าย</h6>
                    <h3 class="text-danger">{{ number_format($totalExpense, 2) }}</h3>
                    <small class="text-muted">บาท</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-{{ $balance >= 0 ? 'primary' : 'warning' }}">
                <div class="card-body text-center">
                    <h6 class="text-muted">คงเหลือ</h6>
                    <h3 class="text-{{ $balance >= 0 ? 'primary' : 'warning' }}">
                        {{ number_format($balance, 2) }}
                    </h3>
                    <small class="text-muted">บาท</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ตารางรายการ --}}
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">📋 รายการรายรับ-รายจ่าย ({{ $startDate }} ถึง {{ $endDate }})</h5>
        </div>
        <div class="card-body">
            @if($transactions->isEmpty())
                <div class="alert alert-info text-center">
                    ไม่มีข้อมูลในช่วงวันที่ที่เลือก
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="100">วันที่</th>
                                <th>รายการ</th>
                                <th width="150" class="text-end">รายรับ (บาท)</th>
                                <th width="150" class="text-end">รายจ่าย (บาท)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $trans)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($trans['date'])->format('d/m/Y') }}</td>
                                <td>{{ $trans['type'] }}</td>
                                <td class="text-end text-success">
                                    @if($trans['income'] > 0)
                                        <strong>{{ number_format($trans['income'], 2) }}</strong>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end text-danger">
                                    @if($trans['expense'] > 0)
                                        <strong>{{ number_format($trans['expense'], 2) }}</strong>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr>
                                <td colspan="2" class="text-end"><strong>รวมทั้งหมด</strong></td>
                                <td class="text-end text-success"><strong>{{ number_format($totalIncome, 2) }}</strong></td>
                                <td class="text-end text-danger"><strong>{{ number_format($totalExpense, 2) }}</strong></td>
                            </tr>
                            <tr class="table-{{ $balance >= 0 ? 'success' : 'warning' }}">
                                <td colspan="2" class="text-end"><strong>คงเหลือ</strong></td>
                                <td colspan="2" class="text-end">
                                    <strong>{{ number_format($balance, 2) }}</strong> บาท
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection