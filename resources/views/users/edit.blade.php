@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลผู้ใช้')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    {{-- เปลี่ยนหัวข้อตามสิทธิ์ --}}
                    ✏️ {{ auth()->user()->role === 'admin' ? 'แก้ไขผู้ใช้' : 'แก้ไขข้อมูลส่วนตัว' }}: {{ $user->username }}
                </h2>
                
                {{-- ปุ่มกลับ: ถ้าเป็น Admin กลับ Users List, ถ้าไม่ใช่ กลับ Dashboard --}}
                <a href="{{ auth()->user()->role === 'admin' ? route('users.index') : route('dashboard') }}" class="btn btn-outline-secondary">
                    ← กลับ
                </a>
            </div>

            {{-- ✅ แจ้งเตือนบันทึกสำเร็จ (เพิ่มส่วนนี้) --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <strong><i class="fas fa-check-circle"></i> สำเร็จ!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- แสดง Error รวมด้านบน (เผื่อมี) --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <strong>❌ พบข้อผิดพลาด:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ฟอร์มแก้ไข --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-user-edit"></i> ข้อมูลผู้ใช้งาน</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        {{-- ชื่อผู้ใช้ --}}
                        <div class="mb-3">
                            <label class="form-label">ชื่อผู้ใช้ (Username) <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                                   value="{{ old('username', $user->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- อีเมล --}}
                        <div class="mb-3">
                            <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>
                        <h6 class="text-muted mb-3">เปลี่ยนรหัสผ่าน (ถ้าไม่เปลี่ยนให้เว้นว่าง)</h6>

                        {{-- รหัสผ่าน --}}
                        <div class="row g-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">รหัสผ่านใหม่</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" style="font-size: 0.8rem;">ต้องมีอย่างน้อย 8 ตัวอักษร</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
                            </div>
                        </div>

                        <hr>

                        {{-- บทบาท (Role) - Logic แยกตามสิทธิ์ --}}
                        @if(auth()->user()->role === 'admin')
                            {{-- Admin: เห็น Dropdown เลือกเปลี่ยนได้ --}}
                            <div class="mb-3">
                                <label class="form-label">บทบาท (Role) <span class="text-danger">*</span></label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>ผู้ดูแลระบบ (Admin)</option>
                                    <option value="stock" {{ old('role', $user->role) == 'stock' ? 'selected' : '' }}>คลังสินค้า (Stock)</option>
                                    <option value="sales" {{ old('role', $user->role) == 'sales' ? 'selected' : '' }}>ฝ่ายขาย (Sales)</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            {{-- User ทั่วไป: เห็นแค่ข้อความ (เปลี่ยนไม่ได้) --}}
                            <div class="mb-3">
                                <label class="form-label">บทบาท (Role)</label>
                                <input type="text" class="form-control bg-light" 
                                       value="{{ ucfirst($user->role) }}" 
                                       readonly>
                                <small class="text-muted">คุณไม่สามารถเปลี่ยนตำแหน่งของตัวเองได้ ติดต่อผู้ดูแลระบบหากต้องการแก้ไข</small>
                            </div>
                        @endif

                        {{-- ปุ่ม Action --}}
                        <div class="d-grid gap-2 mt-4 d-md-flex justify-content-md-end">
                            <a href="{{ auth()->user()->role === 'admin' ? route('users.index') : route('dashboard') }}" class="btn btn-secondary me-md-2">
                                ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-warning text-dark px-4">
                                <i class="fas fa-save"></i> บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection