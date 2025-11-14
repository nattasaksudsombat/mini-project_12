@extends('layouts.app')
@include('layouts.navbarDB')
@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4"><i class="fas fa-chart-line me-2"></i>แดชบอร์ด</h2>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary  text-white h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-hand-holding-usd me-2"></i>รายรับทั้งหมด</h5>
                    <h2 class="display-6">

                                        @if($totalIncome)
                        <h3>{{ number_format($totalIncome, 2) }} บาท</h3>
                        @else
                        <h3> 0.00 บาท</h3>
                        @endif


                    </h2>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <small>ข้อมูลล่าสุด</small>
                    <a href="{{ route('incomes.index') }}" class="text-white">
                        <i class="fas fa-arrow-right"></i> ดูรายละเอียด
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-shopping-cart me-2"></i>รายจ่ายทั้งหมด</h5>
                    <h2 class="display-6"><h3>{{ number_format($totalExpense, 2) }} บาท</h3></h2>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <small>ข้อมูลล่าสุด</small>
                    <a href="{{ route('expenses.index') }}" class="text-white">
                        <i class="fas fa-arrow-right"></i> ดูรายละเอียด
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card {{ $balance >= 0 ? 'bg-success' : 'bg-warning' }} text-white h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-balance-scale me-2"></i>ยอดคงเหลือ</h5>
                    <h2 class="display-6"><h3>{{ number_format($balance, 2) }} บาท</h3> </h2>
                </div>
                <div class="card-footer">
                    <small>{{ $balance >= 0 ? 'คุณมีเงินเหลือเก็บ!' : 'คุณใช้จ่ายมากกว่ารายรับ!' }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filter Section -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>กรองข้อมูล</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard') }}" method="GET" class="row g-3">
                <!-- ช่วงเวลา -->
                <div class="col-md-3">
                    <label for="start_date" class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>

                <!-- Categories -->
                <div class="col-md-3">
                    <label for="income_category" class="form-label">ประเภทรายรับ</label>
                    <select class="form-select" id="income_category" name="income_category">
                        <option value="">ทั้งหมด</option>
                        @foreach($incomeCategories as $category)
                        <option value="{{ $category }}" {{ request('income_category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="expense_category" class="form-label">ประเภทรายจ่าย</label>
                    <select class="form-select" id="expense_category" name="expense_category">
                        <option value="">ทั้งหมด</option>
                        @foreach($expenseCategories as $category)
                        <option value="{{ $category }}" {{ request('expense_category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount Range -->
                <div class="col-md-3">
                    <label for="min_amount" class="form-label">จำนวนเงินต่ำสุด (บาท)</label>
                    <input type="number" class="form-control" id="min_amount" name="min_amount"
                        value="{{ request('min_amount') }}" min="0" step="0.01">
                </div>
                <div class="col-md-3">
                    <label for="max_amount" class="form-label">จำนวนเงินสูงสุด (บาท)</label>
                    <input type="number" class="form-control" id="max_amount" name="max_amount"
                        value="{{ request('max_amount') }}" min="0" step="0.01">
                </div>

                <!-- Buttons -->
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>ค้นหา
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i>รีเซ็ต
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Display filtered data if available -->
    @if($filteredIncomes !== null && $filteredExpenses !== null)
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header  text-white">
                    <h5 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i>รายรับตามเงื่อนไข</h5>
                </div>
                <div class="card-body">
                    @if(count($filteredIncomes) > 0)
                    <div class="table-responsive">
                        <table class="table ">
                            <thead class="table-light">
                                <tr>
                                    <th>วันที่</th>
                                    <th>รายการ</th>
                                    <th>ประเภท</th>
                                    <th class="text-end">จำนวนเงิน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($filteredIncomes as $income)
                                <tr>
                                    <td>{{ $income->date->format('d/m/Y') }}</td>
                                    <td>{{ $income->description }}</td>
                                    <td><span class="badge bg-info">{{ $income->category }}</span></td>
                                    <td class="text-end">{{ number_format($income->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="3" class="text-end">รวม:</th>
                                    <th class="text-end">{{ number_format($filteredIncomes->sum('amount'), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">ไม่พบข้อมูลรายรับตามเงื่อนไขที่กำหนด</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>รายจ่ายตามเงื่อนไข</h5>
                </div>
                <div class="card-body">
                    @if(count($filteredExpenses) > 0)
                    <div class="table-responsive">
                        <table class="table ">
                            <thead class="table-light">
                                <tr>
                                    <th>วันที่</th>
                                    <th>รายการ</th>
                                    <th>ประเภท</th>
                                    <th class="text-end">จำนวนเงิน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($filteredExpenses as $expense)
                                <tr>
                                    <td>{{ $expense->date->format('d/m/Y') }}</td>
                                    <td>{{ $expense->description }}</td>
                                    <td><span class="badge bg-secondary">{{ $expense->category }}</span></td>
                                    <td class="text-end">{{ number_format($expense->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-danger">
                                    <th colspan="3" class="text-end">รวม:</th>
                                    <th class="text-end">{{ number_format($filteredExpenses->sum('amount'), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">ไม่พบข้อมูลรายจ่ายตามเงื่อนไขที่กำหนด</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection