@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>เพิ่มรายจ่ายใหม่</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('expenses.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">รายการ</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}" required>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">จำนวนเงิน (บาท)</label>
                            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="date" class="form-label">วันที่</label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">ประเภท</label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">-- เลือกประเภท --</option>
                                <option value="อาหารและเครื่องดื่ม" {{ old('category') == 'อาหารและเครื่องดื่ม' ? 'selected' : '' }}>อาหารและเครื่องดื่ม</option>
                                <option value="การเดินทาง" {{ old('category') == 'การเดินทาง' ? 'selected' : '' }}>การเดินทาง</option>
                                <option value="ที่พักอาศัย" {{ old('category') == 'ที่พักอาศัย' ? 'selected' : '' }}>ที่พักอาศัย</option>
                                <option value="สาธารณูปโภค" {{ old('category') == 'สาธารณูปโภค' ? 'selected' : '' }}>สาธารณูปโภค</option>
                                <option value="การศึกษา" {{ old('category') == 'การศึกษา' ? 'selected' : '' }}>การศึกษา</option>
                                <option value="ความบันเทิง" {{ old('category') == 'ความบันเทิง' ? 'selected' : '' }}>ความบันเทิง</option>
                                <option value="สุขภาพ" {{ old('category') == 'สุขภาพ' ? 'selected' : '' }}>สุขภาพ</option>
                                <option value="เสื้อผ้า" {{ old('category') == 'เสื้อผ้า' ? 'selected' : '' }}>เสื้อผ้า</option>
                                <option value="การออม/การลงทุน" {{ old('category') == 'การออม/การลงทุน' ? 'selected' : '' }}>การออม/การลงทุน</option>
                                <option value="หนี้สิน" {{ old('category') == 'หนี้สิน' ? 'selected' : '' }}>หนี้สิน</option>
                                <option value="ของขวัญ/การบริจาค" {{ old('category') == 'ของขวัญ/การบริจาค' ? 'selected' : '' }}>ของขวัญ/การบริจาค</option>
                                <option value="อื่นๆ" {{ old('category') == 'อื่นๆ' ? 'selected' : '' }}>อื่นๆ</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>กลับ
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>บันทึก
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection