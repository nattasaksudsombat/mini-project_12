@extends('layouts.app')

@section('title', 'รายงาน & วิเคราะห์ - กราฟ')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📈 กราฟวิเคราะห์</h2>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            ← กลับ Dashboard
        </a>
    </div>

    {{-- เลือกช่วงวันย้อนหลัง --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.charts') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">แสดงข้อมูลย้อนหลัง</label>
                    <select name="days" class="form-select" onchange="this.form.submit()">
                        <option value="7" {{ $days == 7 ? 'selected' : '' }}>7 วัน</option>
                        <option value="15" {{ $days == 15 ? 'selected' : '' }}>15 วัน</option>
                        <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 วัน</option>
                        <option value="60" {{ $days == 60 ? 'selected' : '' }}>60 วัน</option>
                        <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 วัน</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- กราฟเส้นยอดขายรายวัน --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">📊 กราฟยอดขายรายวัน (ย้อนหลัง {{ $days }} วัน)</h5>
        </div>
        <div class="card-body">
            <canvas id="salesChart" style="max-height: 400px;"></canvas>
        </div>
    </div>

    <div class="row g-3">
        {{-- กราฟวงกลม: สัดส่วนยอดขายตามหมวดหมู่ --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">🥧 สัดส่วนยอดขายตามหมวดหมู่</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>

        {{-- กราฟแท่ง: สินค้าขายดี 5 อันดับแรก --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">🏆 สินค้าขายดี 5 อันดับแรก</h5>
                </div>
                <div class="card-body">
                    <canvas id="topProductsChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Load Chart.js จาก CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// ===================================================================
// 1. กราฟเส้นยอดขายรายวัน
// ===================================================================
const salesCtx = document.getElementById('salesChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: @json($salesLabels),
        datasets: [{
            label: 'ยอดขาย (บาท)',
            data: @json($salesData),
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true, position: 'top' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'ยอดขาย: ' + context.parsed.y.toLocaleString() + ' บาท';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString() + ' ฿';
                    }
                }
            }
        }
    }
});

// ===================================================================
// 2. กราฟวงกลมหมวดหมู่
// ===================================================================
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'pie',
    data: {
        labels: @json($categoryLabels),
        datasets: [{
            label: 'ยอดขาย (บาท)',
            data: @json($categoryData),
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'right' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.parsed.toLocaleString() + ' บาท';
                    }
                }
            }
        }
    }
});

// ===================================================================
// 3. กราฟแท่งสินค้าขายดี
// ===================================================================
const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
new Chart(topProductsCtx, {
    type: 'bar',
    data: {
        labels: @json($topProductLabels),
        datasets: [{
            label: 'จำนวนขาย (ชิ้น)',
            data: @json($topProductData),
            backgroundColor: 'rgba(255, 159, 64, 0.8)',
            borderColor: 'rgb(255, 159, 64)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString() + ' ชิ้น';
                    }
                }
            }
        }
    }
});
</script>
@endsection