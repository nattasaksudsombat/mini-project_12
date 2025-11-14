@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>แก้ไขรายรับ</h5>
                </div>
                <div class="card-body">
                <form action="{{ route('incomes.update', $income) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">รายการ</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description', $income->description) }}" required>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">จำนวนเงิน (บาท)</label>
                            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $income->amount) }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="date" class="form-label">วันที่</label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $income->date->format('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">ประเภท</label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">-- เลือกประเภท --</option>
                                <option value="เงินเดือน" {{ old('category', $income->category) == 'เงินเดือน' ? 'selected' : '' }}>เงินเดือน</option>
                                <option value="โบนัส" {{ old('category', $income->category) == 'โบนัส' ? 'selected' : '' }}>โบนัส</option>
                                <option value="ค่าคอมมิชชั่น" {{ old('category', $income->category) == 'ค่าคอมมิชชั่น' ? 'selected' : '' }}>ค่าคอมมิชชั่น</option>
                                <option value="รายได้เสริม" {{ old('category', $income->category) == 'รายได้เสริม' ? 'selected' : '' }}>รายได้เสริม</option>
                                <option value="เงินกู้" {{ old('category', $income->category) == 'เงินกู้' ? 'selected' : '' }}>เงินกู้</option>
                                <option value="ของขวัญ" {{ old('category', $income->category) == 'ของขวัญ' ? 'selected' : '' }}>ของขวัญ</option>
                                <option value="อื่นๆ" {{ old('category', $income->category) == 'อื่นๆ' ? 'selected' : '' }}>อื่นๆ</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('incomes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>กลับ
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
