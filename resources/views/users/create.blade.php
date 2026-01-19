@extends('layouts.app')

@section('title', 'เพิ่มผู้ใช้ใหม่')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">➕ เพิ่มผู้ใช้ใหม่</h2>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                    ← กลับ
                </a>
            </div>

            {{-- แสดง Error --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>❌ พบข้อผิดพลาด:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ฟอร์มเพิ่มผู้ใช้ --}}
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">กรอกข้อมูลผู้ใช้ใหม่</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        {{-- ชื่อผู้ใช้ --}}
                        <div class="mb-3">
                            <label class="form-label">ชื่อผู้ใช้ (Username) <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                                   value="{{ old('username') }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- อีเมล --}}
                        <div class="mb-3">
                            <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- รหัสผ่าน --}}
                        <div class="mb-3">
                            <label class="form-label">รหัสผ่าน <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร</small>
                        </div>

                        {{-- ยืนยันรหัสผ่าน --}}
                        <div class="mb-3">
                            <label class="form-label">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        {{-- บทบาท --}}
                        <div class="mb-3">
                            <label class="form-label">บทบาท (Role) <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="">-- เลือกบทบาท --</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>ผู้ดูแลระบบ (Admin)</option>
                                <option value="stock" {{ old('role') == 'stock' ? 'selected' : '' }}>คลังสินค้า (Stock)</option>
                                <option value="sales" {{ old('role') == 'sales' ? 'selected' : '' }}>ฝ่ายขาย (Sales)</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ปุ่มบันทึก --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                💾 บันทึก
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                ยกเลิก
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection