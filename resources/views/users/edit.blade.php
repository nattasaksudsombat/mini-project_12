@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลผู้ใช้')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    ✏️ {{ auth()->user()->role === 'admin' ? 'แก้ไขผู้ใช้' : 'แก้ไขข้อมูลส่วนตัว' }}: {{ $user->username }}
                </h2>
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

            {{-- ฟอร์มแก้ไข --}}
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">แก้ไขข้อมูลผู้ใช้</h5>
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

                        {{-- รหัสผ่าน (ไม่บังคับ) --}}
                        <div class="mb-3">
                            <label class="form-label">รหัสผ่านใหม่ <small class="text-muted">(เว้นว่างถ้าไม่ต้องการเปลี่ยน)</small></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร</small>
                        </div>

                        {{-- ยืนยันรหัสผ่าน --}}
                        <div class="mb-3">
                            <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        {{-- บทบาท (เฉพาะ Admin เท่านั้นที่เห็นและแก้ไขได้) --}}
                        @if(auth()->user()->role === 'admin')
                            <div class="mb-3">
                                <label class="form-label">บทบาท (Role) <span class="text-danger">*</span></label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                        ผู้ดูแลระบบ (Admin)
                                    </option>
                                    <option value="stock" {{ old('role', $user->role) == 'stock' ? 'selected' : '' }}>
                                        คลังสินค้า (Stock)
                                    </option>
                                    <option value="sales" {{ old('role', $user->role) == 'sales' ? 'selected' : '' }}>
                                        ฝ่ายขาย (Sales)
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            {{-- แสดงบทบาทปัจจุบัน (อ่านอย่างเดียว) --}}
                            <div class="mb-3">
                                <label class="form-label">บทบาท (Role)</label>
                                <input type="text" class="form-control" 
                                       value="{{ $user->role === 'admin' ? 'ผู้ดูแลระบบ' : ($user->role === 'stock' ? 'คลังสินค้า' : 'ฝ่ายขาย') }}" 
                                       readonly>
                                <small class="text-muted">คุณไม่สามารถเปลี่ยนบทบาทของตัวเองได้</small>
                            </div>
                        @endif

                        {{-- ปุ่มบันทึก --}}
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning text-dark">
                                💾 บันทึกการแก้ไข
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