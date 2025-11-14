@extends('layouts.app')

@section('content')
<style>
/* ===== Black and Gold Theme for Expense Tracker ===== */

/* Main colors and variables */
:root {
  --gold: #d4af37;
  --gold-light: #e9d498;
  --gold-dark: #996515;
  --black: #121212;
  --dark-gray: #1e1e1e;
  --light-gray: #2a2a2a;
  --text-light: #f5f5f5;
  --text-gold: #d4af37;
  --danger: #ff4500;
}

/* Body and global styling */
body {
  background-color: var(--black);
  color: var(--text-light);
  font-family: 'Kanit', 'Prompt', sans-serif;
}

/* Card styling */
.card.card-dark-red {
  background-color: var(--dark-gray);
  border: 1px solid var(--gold);
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
  transition: transform 0.3s, box-shadow 0.3s;
}

.card.card-dark-red:hover {
  box-shadow: 0 6px 16px rgba(212, 175, 55, 0.3);
  transform: translateY(-3px);
}

.card-header {
  background-color: var(--black);
  border-bottom: 2px solid var(--gold);
  padding: 15px 20px;
}

.card-header h5 {
  color: var(--gold);
  font-weight: 600;
}

/* Buttons styling */
.btn-add-new, .btn-filter {
  background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
  color: var(--black);
  font-weight: 600;
  border: none;
  box-shadow: 0 4px 8px rgba(153, 101, 21, 0.3);
  transition: all 0.3s;
}

.btn-add-new:hover, .btn-filter:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(212, 175, 55, 0.4);
  background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold) 100%);
  color: var(--black);
}

.btn-secondary {
  background-color: var(--light-gray);
  border: 1px solid var(--gold-light);
  color: var(--text-light);
}

.btn-warning {
  background-color: var(--gold-dark);
  border: none;
  color: var(--text-light);
}

.btn-danger {
  background-color: var(--danger);
  border: none;
}

/* Form controls */
.form-control, .form-select {
  background-color: var(--light-gray);
  border: 1px solid var(--gold-light);
  color: var(--text-light);
  transition: all 0.3s;
}

.form-control:focus, .form-select:focus {
  background-color: var(--light-gray);
  border-color: var(--gold);
  box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
  color: var(--text-light);
}

label.form-label {
  color: var(--gold-light);
  font-weight: 500;
}

/* Table styling */
.table.table-dark-red {
  color: var(--text-light);
}

.table.table-dark-red thead th {
  background-color: var(--black);
  color: var(--gold);
  border-bottom: 2px solid var(--gold);
  font-weight: 600;
}

.table.table-dark-red tbody tr {
  border-bottom: 1px solid rgba(212, 175, 55, 0.2);
  transition: all 0.2s;
}

.table.table-dark-red tbody tr:hover {
  background-color: rgba(212, 175, 55, 0.1);
}

/* Category badges */
.badge-category {
  background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
  color: var(--black);
  font-weight: 500;
  padding: 5px 10px;
  border-radius: 20px;
  display: inline-block;
}

/* Icons styling */
i.fas {
  color: var(--gold);
}

/* Badge styling */
.badge.bg-danger {
  background: linear-gradient(135deg, var(--danger), #b30000) !important;
  font-size: 0.9rem;
  padding: 6px 12px;
}

/* Pagination */
.pagination .page-item.active .page-link {
  background-color: var(--gold);
  border-color: var(--gold-dark);
  color: var(--black);
}

.pagination .page-link {
  background-color: var(--dark-gray);
  border-color: var(--gold-light);
  color: var(--gold);
}

.pagination .page-link:hover {
  background-color: var(--gold-dark);
  color: var(--text-light);
}

/* Alert styling */
.alert-warning {
  background-color: rgba(212, 175, 55, 0.2);
  border: 1px solid var(--gold-dark);
  color: var(--gold-light);
}

/* Animation for buttons */
@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.7);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(212, 175, 55, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(212, 175, 55, 0);
  }
}

.btn-add-new {
  animation: pulse 2s infinite;
}

/* Title styling */
h2 {
  color: var(--gold);
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
  font-weight: 700;
  position: relative;
  padding-bottom: 10px;
}

h2:after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 80px;
  height: 3px;
  background: linear-gradient(90deg, var(--gold), transparent);
}

/* Custom scroll bar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: var(--dark-gray);
}

::-webkit-scrollbar-thumb {
  background: var(--gold-dark);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: var(--gold);
}

/* Transitions and hover effects */
.btn, a, .card, .badge-category {
  transition: all 0.3s ease;
}

/* Add Thai font support */
@import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap');

/* Container styling */
.container-fluid.py-4 {
  padding-top: 2rem !important;
  padding-bottom: 2rem !important;
}

/* Button hover animations */
.btn-sm:hover {
  transform: scale(1.05);
}

/* Additional gold accents */
.btn-warning:hover {
  background-color: var(--gold);
  color: var(--black);
}

/* Custom container background */
.container-fluid {
  background-image: linear-gradient(to bottom right, rgba(0,0,0,0.9), rgba(30,30,30,0.9)), 
                    url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="none"/><path d="M0 50 L50 0 L100 50 L50 100 Z" fill="%23d4af37" opacity="0.03"/></svg>');
  background-attachment: fixed;
}
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-hand-holding-usd me-2" style="color: #ffd700;"></i>รายรับทั้งหมด</h2>
        <a href="{{ route('incomes.create') }}" class="btn btn-add-new">
            <i class="fas fa-plus-circle me-1"></i>เพิ่มรายรับใหม่
        </a>
    </div>

    <div class="card card-dark-gold mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>ค้นหาและกรองข้อมูล</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('incomes.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
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
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-filter w-100">
                        <i class="fas fa-search me-1"></i>ค้นหา
                    </button>
                </div>
                <div class="col-12 text-end mt-2">
                    <a href="{{ route('incomes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i>รีเซ็ต
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-dark-gold">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>รายการรายรับ</h5>
                <span class="badge bg-warning text-dark">{{ $incomes->total() }} รายการ</span>
            </div>
        </div>
        <div class="card-body">
            @if($incomes->count() > 0)
            <div class="table-responsive">
                <table class="table  table-dark-gold">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>วันที่</th>
                            <th>รายการ</th>
                            <th>ประเภท</th>
                            <th class="text-end">จำนวนเงิน (บาท)</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incomes as $index => $income)
                        <tr>
                            <td class="text-center">{{ $incomes->firstItem() + $index }}</td>
                            <td>{{ $income->date->format('d/m/Y') }}</td>
                            <td>{{ $income->description }}</td>
                            <td><span class="badge-category">{{ $income->category }}</span></td>
                            <td class="text-end">{{ number_format($income->amount, 2) }}</td>
                            <td class="text-center">
                                <a href="{{ route('incomes.edit', $income->id) }}" class="btn btn-sm btn-warning me-1">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </a>
                                <form action="{{ route('incomes.destroy', $income->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('คุณแน่ใจว่าต้องการลบรายการนี้?')">
                                        <i class="fas fa-trash"></i> ลบ
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $incomes->links() }}
            </div>
            @else
            <div class="alert alert-warning text-center">
                <i class="fas fa-info-circle me-2"></i>ไม่พบข้อมูลรายรับตามเงื่อนไขที่กำหนด
            </div>
            @endif
        </div>
    </div>
    
    
</div>

@endsection