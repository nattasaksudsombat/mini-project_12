@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลผู้ใช้')

@section('content')
<style>
    /* Custom Styles for Edit User Page - Black & Gold Theme */
    .edit-header {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(30, 30, 30, 0.9));
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 215, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .edit-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.05) 0%, transparent 70%);
        animation: headerGlow 8s infinite ease-in-out;
    }

    @keyframes headerGlow {

        0%,
        100% {
            transform: translate(0, 0) scale(1);
        }

        50% {
            transform: translate(-10%, -10%) scale(1.1);
        }
    }

    .edit-header h2 {
        color: var(--gold);
        text-shadow: 0 0 15px rgba(255, 215, 0, 0.5);
        font-weight: 600;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .btn-back {
        background: rgba(30, 30, 30, 0.8);
        border: 1px solid rgba(255, 215, 0, 0.3);
        color: var(--gold);
        padding: 0.6rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }

    .btn-back:hover {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(30, 30, 30, 0.9));
        border-color: var(--gold);
        color: var(--gold);
        transform: translateX(-5px);
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
    }

    /* Alert Styling */
    .alert-success {
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.2), rgba(30, 30, 30, 0.9));
        color: #7de5a4 !important;
        border: 1px solid rgba(25, 135, 84, 0.5);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: 0 5px 15px rgba(25, 135, 84, 0.2);
        animation: slideDown 0.5s ease;
    }

    .alert-success strong {
        color: #7de5a4 !important;
    }

    .alert-danger {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.2), rgba(30, 30, 30, 0.9));
        color: #ff6b7d !important;
        border: 1px solid rgba(220, 53, 69, 0.5);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.2);
        animation: slideDown 0.5s ease;
    }

    .alert-danger strong,
    .alert-danger ul,
    .alert-danger li {
        color: #ff6b7d !important;
    }

    /* Form Card */
    .form-card {
        background: linear-gradient(135deg, rgba(30, 30, 30, 0.95), rgba(18, 18, 18, 0.95));
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 25px rgba(255, 215, 0, 0.15);
        overflow: hidden;
    }

    .form-card .card-header {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(30, 30, 30, 0.8));
        border-bottom: 2px solid rgba(255, 215, 0, 0.4);
        padding: 1.5rem;
    }

    .form-card .card-header h5 {
        color: var(--gold);
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.4);
        font-weight: 600;
        margin: 0;
    }

    .form-card .card-body {
        padding: 2rem;
    }

    /* Form Labels */
    .form-label {
        color: var(--gold) !important;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.95rem;
        text-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
    }

    .form-label .text-danger {
        color: #ff6b7d !important;
    }

    /* Form Inputs */
    .form-control,
    .form-select {
        background: rgba(10, 10, 10, 0.8) !important;
        border: 1px solid rgba(255, 215, 0, 0.3) !important;
        color: #e8e8e8 !important;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .form-control:focus,
    .form-select:focus {
        background: rgba(10, 10, 10, 0.9) !important;
        border-color: var(--gold) !important;
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.3) !important;
        color: #e8e8e8 !important;
    }

    .form-control::placeholder {
        color: rgba(204, 204, 204, 0.5) !important;
    }

    .form-control.is-invalid {
        border-color: rgba(220, 53, 69, 0.5) !important;
        background: rgba(10, 10, 10, 0.8) !important;
        color: #e8e8e8 !important;
    }

    .form-control.is-invalid:focus {
        box-shadow: 0 0 15px rgba(220, 53, 69, 0.3) !important;
    }

    /* Readonly Input */
    .form-control[readonly],
    .form-control.bg-light {
        background: rgba(30, 30, 30, 0.6) !important;
        border-color: rgba(255, 215, 0, 0.2) !important;
        color: #aaa !important;
        cursor: not-allowed;
    }

    /* Select Dropdown */
    .form-select option {
        background: #1e1e1e;
        color: #e8e8e8;
        padding: 0.5rem;
    }

    /* Small Text */
    .text-muted,
    small.text-muted {
        color: #aaa !important;
        font-size: 0.85rem;
    }

    /* Invalid Feedback */
    .invalid-feedback {
        color: #ff6b7d !important;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }

    /* HR Divider */
    hr {
        border-color: rgba(255, 215, 0, 0.2);
        opacity: 1;
        margin: 1.5rem 0;
    }

    /* Section Headers */
    h6.text-muted {
        color: var(--gold) !important;
        font-weight: 600;
        text-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
        margin-bottom: 1rem;
    }

    /* Buttons */
    .btn-cancel {
        background: rgba(30, 30, 30, 0.8);
        border: 1px solid rgba(255, 215, 0, 0.3);
        color: var(--text-primary);
        padding: 0.6rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: rgba(40, 40, 40, 0.9);
        border-color: var(--gold);
        color: var(--gold);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .btn-save {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        border: none;
        color: #000 !important;
        padding: 0.6rem 2rem;
        border-radius: 10px;
        font-weight: 700;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
    }

    .btn-save:hover {
        background: linear-gradient(135deg, var(--gold-dark), var(--gold));
        color: #000 !important;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.5);
    }

    .btn-save i {
        margin-right: 0.5rem;
    }

    /* Animations */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Input Group */
    .input-group {
        position: relative;
    }

    .input-group i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gold);
        z-index: 10;
        opacity: 0.7;
    }

    .input-group .form-control {
        padding-left: 2.5rem;
    }

    /* Password Strength Indicator */
    .password-strength {
        margin-top: 0.5rem;
        padding: 0.5rem;
        border-radius: 8px;
        background: rgba(30, 30, 30, 0.6);
        border: 1px solid rgba(255, 215, 0, 0.2);
        font-size: 0.85rem;
        color: #aaa;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .edit-header {
            padding: 1.5rem;
        }

        .form-card .card-body {
            padding: 1.5rem;
        }

        .btn-save,
        .btn-cancel {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            {{-- Header --}}
            <div class="edit-header" data-aos="fade-down" data-aos-duration="800">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h2 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>
                        {{ auth()->user()->role === 'admin' ? 'แก้ไขผู้ใช้' : 'แก้ไขข้อมูลส่วนตัว' }}:
                        <span style="color: var(--neon-gold);">{{ $user->username }}</span>
                    </h2>

                    <a href="{{ auth()->user()->role === 'admin' ? route('users.index') : route('dashboard') }}"
                        class="btn btn-back">
                        <i class="fas fa-arrow-left me-2"></i>กลับ
                    </a>
                </div>
            </div>

            {{-- ✅ แจ้งเตือนบันทึกสำเร็จ --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" data-aos="fade-down" data-aos-duration="600">
                <strong><i class="fas fa-check-circle me-2"></i>สำเร็จ!</strong> {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            {{-- แสดง Error รวมด้านบน --}}
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" data-aos="fade-down" data-aos-duration="600">
                <strong><i class="fas fa-exclamation-triangle me-2"></i>พบข้อผิดพลาด:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- ฟอร์มแก้ไข --}}
            <div class="card form-card" data-aos="fade-up" data-aos-duration="1000">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2"></i>ข้อมูลผู้ใช้งาน
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user) }}" id="editUserForm">
                        @csrf
                        @method('PUT')

                        {{-- ชื่อผู้ใช้ --}}
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-user me-2"></i>ชื่อผู้ใช้ (Username) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <i class="fas fa-user"></i>
                                <input type="text"
                                    name="username"
                                    class="form-control @error('username') is-invalid @enderror"
                                    value="{{ old('username', $user->username) }}"
                                    required
                                    placeholder="กรอกชื่อผู้ใช้">
                                @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- อีเมล --}}
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-envelope me-2"></i>อีเมล <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <i class="fas fa-envelope"></i>
                                <input type="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}"
                                    required
                                    placeholder="example@email.com">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-key me-2"></i>เปลี่ยนรหัสผ่าน (ถ้าไม่เปลี่ยนให้เว้นว่าง)
                        </h6>

                        {{-- รหัสผ่าน --}}
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-lock me-2"></i>รหัสผ่านใหม่
                                </label>
                                <div class="input-group">
                                    <i class="fas fa-lock"></i>
                                    <input type="password"
                                        name="password"
                                        id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="••••••••">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>ต้องมีอย่างน้อย 8 ตัวอักษร
                                </small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-lock me-2"></i>ยืนยันรหัสผ่านใหม่
                                </label>
                                <div class="input-group">
                                    <i class="fas fa-lock"></i>
                                    <input type="password"
                                        name="password_confirmation"
                                        id="password_confirmation"
                                        class="form-control"
                                        placeholder="••••••••">
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- บทบาท (Role) - Logic แยกตามสิทธิ์ --}}
                        @if(auth()->user()->role === 'admin')
                        {{-- Admin: เห็น Dropdown เลือกเปลี่ยนได้ --}}
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-shield-alt me-2"></i>บทบาท (Role) <span class="text-danger">*</span>
                            </label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                    👑 ผู้ดูแลระบบ (Admin)
                                </option>
                                <option value="stock" {{ old('role', $user->role) == 'stock' ? 'selected' : '' }}>
                                    📦 คลังสินค้า (Stock)
                                </option>
                                <option value="sales" {{ old('role', $user->role) == 'sales' ? 'selected' : '' }}>
                                    📊 ฝ่ายขาย (Sales)
                                </option>
                            </select>
                            @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @else
                        {{-- User ทั่วไป: เห็นแค่ข้อความ (เปลี่ยนไม่ได้) --}}
                        {{-- 1. เตรียมตัวแปรชื่อตำแหน่ง (ใส่ไว้ด้านบน input หรือบนสุดของไฟล์ก็ได้) --}}
                        @php
                        $roleName = match($user->role) {
                        'admin' => '👑 ผู้ดูแลระบบ (Admin)',
                        'stock' => '📦 คลังสินค้า (Stock)',
                        'sales' => '📊 ฝ่ายขาย (Sales)',
                        default => ucfirst($user->role)
                        };
                        @endphp

                        {{-- 2. ส่วนแสดงผล Input --}}
                        <div class="mb-3">
                            <label class="form-label">บทบาท (Role)</label>

                            <input type="text"
                                class="form-control bg-light"
                                value="{{ $roleName }}"
                                readonly> {{-- ✅ ปิด tag ให้เรียบร้อยที่นี่ --}}

                            <small class="text-muted">
                                คุณไม่สามารถเปลี่ยนตำแหน่งของตัวเองได้ ติดต่อผู้ดูแลระบบหากต้องการแก้ไข
                            </small>
                        </div>
                        @endif

                        {{-- ปุ่ม Action --}}
                        <div class="d-grid gap-2 mt-4 d-md-flex justify-content-md-end">
                            <a href="{{ auth()->user()->role === 'admin' ? route('users.index') : route('dashboard') }}"
                                class="btn btn-cancel">
                                <i class="fas fa-times me-2"></i>ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-save">
                                <i class="fas fa-save"></i>บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- AOS Animation & Scripts --}}
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"> </script>
<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });

    // Password match validation
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;

        if (password && password !== passwordConfirm) {
            e.preventDefault();
            alert('⚠️ รหัสผ่านไม่ตรงกัน กรุณาตรวจสอบอีกครั้ง');
            document.getElementById('password_confirmation').focus();
        }
    });

    // Auto dismiss alert after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Add focus animation to inputs
    document.querySelectorAll('.form-control, .form-select').forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transform = 'scale(1.01)';
        });
        input.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    });
</script>
@endpush
@endsection