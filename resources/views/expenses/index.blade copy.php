@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-hand-holding-usd me-2"></i>รายรับทั้งหมด</h2>
        <a href="{{ route('expenses.create') }}" class="btn btn-success">
            <i class="fas fa-plus-circle me-1"></i>เพิ่มรายรับใหม่
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>ค้นหาและกรองข้อมูล</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('expenses.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="category" class="form-label">ประเภท</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">-- ทั้งหมด --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>ค้นหา
                    </button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i>รีเซ็ต
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>รายการรายรับ</h5>
        </div>
        <div class="card-body">
        @if($expenses && count($expenses) > 0) <!-- ตรวจสอบว่า $expenses ไม่เป็น null -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ลำดับ</th>
                    <th>วันที่</th>
                    <th>รายการ</th>
                    <th>ประเภท</th>
                    <th class="text-end">จำนวนเงิน (บาท)</th>
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $index => $expense) <!-- เปลี่ยนจาก $expense เป็น $expenses -->
                    <tr>
                        <td>{{ $expense->firstItem() + $index }}</td> <!-- เปลี่ยนจาก $expense เป็น $expenses -->
                        <td>{{ $expense->date->format('d/m/Y') }}</td>
                        <td>{{ $expense->description }}</td>
                        <td>
                            <span class="badge bg-info">{{ $expense->category }}</span>
                        </td>
                        <td class="text-end">{{ number_format($expense->amount, 2) }}</td>
                        <td class="text-center">
                            <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('คุณแน่ใจว่าต้องการลบรายการนี้?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-primary">
                    <th colspan="4" class="text-end">รวมทั้งหมด:</th>
                    <th class="text-end">{{ number_format($expenses->sum('amount'), 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $expenses->links() }}
    </div>
@else
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle me-2"></i>ไม่พบข้อมูลรายจ่าย
    </div>
@endif


        </div>
    </div>
</div>
@endsection
