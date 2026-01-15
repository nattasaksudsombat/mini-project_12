@extends('layouts.app')
@include('layouts.navbarDB')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-chart-pie me-2"></i>รายงานสรุปภาพรวม</h2>
        <div>
            <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print me-1"></i> พิมพ์รายงาน</button>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Daily Sales Chart -->
        <div class="col-md-8">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>ยอดขายรายวัน (30 วันล่าสุด)</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailySalesChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Top 5 Products -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-crown me-2 text-warning"></i>5 สินค้าขายดี</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>สินค้า</th>
                                    <th class="text-end">จำนวนขาย</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="ms-2">
                                                <h6 class="mb-0 text-truncate" style="max-width: 150px;">{{ $product->product_name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold">{{ number_format($product->total_qty) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-3 text-muted">ไม่มีข้อมูลสินค้าขายดี</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Monthly Sales Chart -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-success"></i>ยอดขายรายเดือน (ปีปัจจุบัน)</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlySalesChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="col-md-6">
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>สินค้าใกล้หมด (ต่ำกว่า 10 ชิ้น)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 290px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>สินค้า</th>
                                    <th>ตัวเลือก</th>
                                    <th class="text-center">คงเหลือ</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockItems as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $item->color->name ?? '-' }} / {{ $item->size->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger rounded-pill">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->product)
                                        <a href="{{ route('products.show', $item->product->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-search"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-success">
                                        <i class="fas fa-check-circle me-1"></i> สต็อกปกติทุกรายการ
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Daily Sales Chart
        const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: @json($dailyLabels),
                datasets: [{
                    label: 'ยอดขาย (บาท)',
                    data: @json($dailyValues),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Monthly Sales Chart
        const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: @json($monthlyLabels),
                datasets: [{
                    label: 'ยอดขาย (บาท)',
                    data: @json($monthlyValues),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection
