{{-- resources/views/stock/report.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-boxes"></i> รายงานสต็อกสินค้า</h2>
        <div>
            <a href="{{ route('stock.export') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export CSV
            </a>
        </div>
    </div>

    {{-- ฟอร์มค้นหา --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('stock.report') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">ค้นหาสินค้า</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="ชื่อสินค้า หรือ รหัสสินค้า" value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">สถานะสต็อก</label>
                    <select name="stock_status" class="form-select">
                        <option value="">ทั้งหมด</option>
                        <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>
                            สินค้าหมด (0 ชิ้น)
                        </option>
                        <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>
                            สต็อกต่ำ (≤ 10 ชิ้น)
                        </option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> ค้นหา
                    </button>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('stock.report') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-redo"></i> รีเซ็ต
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ตารางแสดงผล --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">รหัสสินค้า</th>
                            <th width="25%">ชื่อสินค้า</th>
                            <th width="12%">สี</th>
                            <th width="10%">ไซส์</th>
                            <th width="10%" class="text-end">สต็อกปัจจุบัน</th>
                            <th width="10%" class="text-end">ถูกจอง</th>
                            <th width="10%" class="text-end">คงเหลือ</th>
                            <th width="13%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                        <tr>
                            <td>
                                <a href="{{ route('products.show', $stock->product_id) }}" 
                                   class="text-decoration-none">
                                    {{ $stock->id_stock }}
                                </a>
                            </td>
                            <td>{{ $stock->product_name }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $stock->color_name !== 'ไม่ระบุสี' ? '#ddd' : '#eee' }}; color: #333;">
                                    {{ $stock->color_name }}
                                </span>
                            </td>
                            <td>{{ $stock->size_name }}</td>
                            <td class="text-end">
                                <strong>{{ number_format($stock->current_stock) }}</strong>
                            </td>
                            <td class="text-end">
                                <span class="{{ $stock->reserved_stock > 0 ? 'text-danger' : 'text-muted' }}">
                                    {{ number_format($stock->reserved_stock) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($stock->available_stock <= 0)
                                    <span class="badge bg-danger">หมด</span>
                                @elseif($stock->available_stock <= 10)
                                    <span class="badge bg-warning text-dark">
                                        {{ number_format($stock->available_stock) }}
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        {{ number_format($stock->available_stock) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('stock.adjust.form', $stock->variant_id) }}" 
                                       class="btn btn-outline-primary" title="ปรับสต็อก">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('stock.variant.history', $stock->variant_id) }}" 
                                       class="btn btn-outline-info" title="ประวัติ">
                                        <i class="fas fa-history"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                ไม่พบข้อมูลสต็อก
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $stocks->links() }}
            </div>
        </div>
    </div>

    {{-- สรุปสถิติ --}}
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>สินค้าทั้งหมด</h6>
                    <h3>{{ number_format($stocks->total()) }}</h3>
                    <small>รายการ</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6>สินค้าหมด</h6>
                    <h3>
                        {{ number_format(DB::table('v_current_stock')->where('available_stock', '<=', 0)->count()) }}
                    </h3>
                    <small>รายการ</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6>สต็อกต่ำ</h6>
                    <h3>
                        {{ number_format(DB::table('v_current_stock')->where('available_stock', '>', 0)->where('available_stock', '<=', 10)->count()) }}
                    </h3>
                    <small>รายการ</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>มูลค่าสต็อกรวม</h6>
                    <h3>
                        @php
                        $totalValue = DB::table('v_current_stock as v')
                            ->join('products as p', 'v.product_id', '=', 'p.id')
                            ->selectRaw('SUM(v.current_stock * p.cost) as total')
                            ->value('total');
                        @endphp
                        {{ number_format($totalValue ?? 0) }}
                    </h3>
                    <small>บาท</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
</style>
@endsection