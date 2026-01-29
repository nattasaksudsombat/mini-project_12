@extends('layouts.app')
@include('layouts.navbarDB')

@section('title', 'รายงาน & วิเคราะห์ - Dashboard')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📊 Dashboard สรุปภาพรวม</h2>
        <div>
            <a href="{{ route('reports.charts') }}" class="btn btn-outline-primary me-2">
                📈 กราฟวิเคราะห์
            </a>
            <a href="{{ route('reports.financial') }}" class="btn btn-outline-success">
                💰 รายงานการเงิน
            </a>
        </div>
    </div>

    {{-- ฟอร์มเลือกช่วงวันที่ --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">🔍 ค้นหา</button>
                </div>
            </form>
        </div>
    </div>

    {{-- การ์ดสรุปยอด (4 กล่อง) --}}
    <div class="row g-3 mb-4">
        {{-- ยอดขายวันนี้ --}}
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">💵 ยอดขายวันนี้</h6>
                    <h3 class="text-primary mb-0">{{ number_format($salesToday, 2) }}</h3>
                    <small class="text-muted">บาท</small>
                </div>
            </div>
        </div>

        {{-- ยอดขายเดือนนี้ --}}
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">📆 ยอดขายเดือนนี้</h6>
                    <h3 class="text-success mb-0">{{ number_format($salesThisMonth, 2) }}</h3>
                    <small class="text-muted">บาท</small>
                </div>
            </div>
        </div>

        {{-- ยอดขายปีนี้ --}}
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">📅 ยอดขายปีนี้</h6>
                    <h3 class="text-info mb-0">{{ number_format($salesThisYear, 2) }}</h3>
                    <small class="text-muted">บาท</small>
                </div>
            </div>
        </div>

        {{-- กำไรสุทธิ (ช่วงที่เลือก) --}}
        <div class="col-md-3">
            <div class="card border-{{ $netProfit >= 0 ? 'success' : 'danger' }}">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">💰 กำไรสุทธิ (ช่วงที่เลือก)</h6>
                    <h3 class="text-{{ $netProfit >= 0 ? 'success' : 'danger' }} mb-0">
                        {{ number_format($netProfit, 2) }}
                    </h3>
                    <small class="text-muted">บาท</small>
                </div>
            </div>
        </div>
    </div>

    {{-- รายละเอียดการคำนวณกำไร --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">📋 รายละเอียดการคำนวณกำไรสุทธิ ({{ $startDate }} ถึง {{ $endDate }})</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-sm">
                <tbody>
                    <tr>
                        <td class="text-end"><strong>รายรับจาก Order (ที่ชำระแล้ว)</strong></td>
                        <td class="text-end text-success"><strong>+{{ number_format($totalRevenue, 2) }}</strong> บาท</td>
                    </tr>
                    <tr>
                        <td class="text-end"><strong>ต้นทุนสินค้า</strong></td>
                        <td class="text-end text-danger"><strong>-{{ number_format($totalCost, 2) }}</strong> บาท</td>
                    </tr>
                    <tr>
                        <td class="text-end"><strong>รายจ่าย (Expenses)</strong></td>
                        <td class="text-end text-danger"><strong>-{{ number_format($totalExpense, 2) }}</strong> บาท</td>
                    </tr>
                    <tr>
                        <td class="text-end"><strong>รายรับอื่น (Incomes)</strong></td>
                        <td class="text-end text-success"><strong>+{{ number_format($totalIncome, 2) }}</strong> บาท</td>
                    </tr>
                    <tr class="table-{{ $netProfit >= 0 ? 'success' : 'danger' }}">
                        <td class="text-end"><strong>กำไรสุทธิ</strong></td>
                        <td class="text-end"><strong>{{ number_format($netProfit, 2) }}</strong> บาท</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- สถานะระบบ --}}
    <div class="row g-3">
        {{-- ออเดอร์รอจัดส่ง --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">📦 ออเดอร์รอจัดส่ง</h5>
                </div>
                <div class="card-body text-center">
                    <h1 class="display-4 text-warning">{{ $pendingOrders }}</h1>
                    <p class="mb-0">รายการ</p>
                   <a href="{{ route('products.index') }}" class="btn btn-danger btn-sm mt-2">
    ดูสินค้า
</a>
                </div>
            </div>
        </div>

        {{-- สินค้าสต็อกใกล้หมด --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">⚠️ สินค้าสต็อกใกล้หมด</h5>
                </div>
                <div class="card-body text-center">
                    <h1 class="display-4 text-danger">{{ $lowStockCount }}</h1>
                    <p class="mb-0">รายการ (สต็อก ≤ 10)</p>
                   <a href="{{ route('products.index') }}?stock_status=low" class="btn btn-danger btn-sm mt-2">
    ดูรายการ
</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection