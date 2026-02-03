@extends('layouts.app')

@section('title', 'เพิ่มผู้ใช้ใหม่')

@section('content')
<style>
    /* Custom Styles for Create User Page - Black & Gold Theme */
    .create-header {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(30, 30, 30, 0.9));
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 215, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .create-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.05) 0%, transparent 70%);
        animation: headerPulse 10s infinite ease-in-out;
    }

    @keyframes headerPulse {
        0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.5; }
        50% { transform: translate(10%, 10%) scale(1.1); opacity: 0.8; }
    }

    .create-header h2 {
        color: var(--gold);
        text-shadow: 0 0 15px rgba(255, 215, 0, 0.5), 0 0 30px rgba(255, 215, 0, 0.3);
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
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.25), rgba(30, 30, 30, 0.8));
        border-bottom: 2px solid rgba(25, 135, 84, 0.5);
        padding: 1.5rem;
    }

    .form-card .card-header h5 {
        color: #7de5a4;
        text-shadow: 0 0 10px rgba(25, 135, 84, 0.5);
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

    .input-group .form-control,
    .input-group .form-select {
        padding-left: 2.5rem;
    }

    /* Buttons */
    .btn-submit {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        color: #fff !important;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 700;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        width: 100%;
        font-size: 1rem;
    }

    .btn-submit:hover {
        background: linear-gradient(135deg, #20c997, #28a745);
        color: #fff !important;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.5);
    }

    .btn-submit i {
        margin-right: 0.5rem;
    }

    .btn-cancel {
        background: rgba(30, 30, 30, 0.8);
        border: 1px solid rgba(255, 215, 0, 0.3);
        color: var(--text-primary) !important;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-cancel:hover {
        background: rgba(40, 40, 40, 0.9);
        border-color: var(--gold);
        color: var(--gold) !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    /* Password Strength Meter */
    .password-strength {
        margin-top: 0.5rem;
        height: 5px;
        background: rgba(30, 30, 30, 0.6);
        border-radius: 10px;
        overflow: hidden;
        display: none;
    }

    .password-strength-bar {
        height: 100%;
        transition: all 0.3s ease;
        border-radius: 10px;
    }

    .password-strength.weak .password-strength-bar {
        width: 33%;
        background: linear-gradient(90deg, #dc3545, #ff6b7d);
    }

    .password-strength.medium .password-strength-bar {
        width: 66%;
        background: linear-gradient(90deg, var(--gold), var(--neon-gold));
    }

    .password-strength.strong .password-strength-bar {
        width: 100%;
        background: linear-gradient(90deg, #28a745, #20c997);
    }

    .password-help {
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: #aaa;
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

    /* Responsive */
    @media (max-width: 768px) {
        .create-header {
            padding: 1.5rem;
        }

        .form-card .card-body {
            padding: 1.5rem;
        }
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            {{-- Header --}}
            <div class="create-header" data-aos="fade-down" data-aos-duration="800">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h2 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>เพิ่มผู้ใช้ใหม่
                    </h2>
                    <a href="{{ route('users.index') }}" class="btn btn-back">
                        <i class="fas fa-arrow-left me-2"></i>กลับ
                    </a>
                </div>
            </div>

            {{-- แสดง Error --}}
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

            {{-- ฟอร์มเพิ่มผู้ใช้ --}}
            <div class="card form-card" data-aos="fade-up" data-aos-duration="1000">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>กรอกข้อมูลผู้ใช้ใหม่
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.store') }}" id="createUserForm">
                        @csrf

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
                                       value="{{ old('username') }}" 
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
                                       value="{{ old('email') }}" 
                                       required
                                       placeholder="example@email.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- รหัสผ่าน --}}
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-lock me-2"></i>รหัสผ่าน <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       class="form-control @error('password') is-invalid @enderror" 
                                       required
                                       placeholder="••••••••">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="password-strength" id="passwordStrength">
                                <div class="password-strength-bar"></div>
                            </div>
                            <small class="text-muted password-help">
                                <i class="fas fa-info-circle me-1"></i>รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร (แนะนำ 8 ตัวขึ้นไป)
                            </small>
                        </div>

                        {{-- ยืนยันรหัสผ่าน --}}
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-lock me-2"></i>ยืนยันรหัสผ่าน <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation"
                                       class="form-control" 
                                       required
                                       placeholder="••••••••">
                            </div>
                        </div>

                        {{-- บทบาท --}}
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-shield-alt me-2"></i>บทบาท (Role) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <i class="fas fa-shield-alt"></i>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">-- เลือกบทบาท --</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                        👑 ผู้ดูแลระบบ (Admin)
                                    </option>
                                    <option value="stock" {{ old('role') == 'stock' ? 'selected' : '' }}>
                                        📦 คลังสินค้า (Stock)
                                    </option>
                                    <option value="sales" {{ old('role') == 'sales' ? 'selected' : '' }}>
                                        📊 ฝ่ายขาย (Sales)
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- ปุ่มบันทึก --}}
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save"></i>บันทึก
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-cancel">
                                <i class="fas fa-times me-2"></i>ยกเลิก
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- AOS Animation & Scripts --}}
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const strengthMeter = document.getElementById('passwordStrength');
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        if (password.length === 0) {
            strengthMeter.style.display = 'none';
            strengthMeter.className = 'password-strength';
        } else {
            strengthMeter.style.display = 'block';
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            strengthMeter.className = 'password-strength';
            if (strength <= 2) {
                strengthMeter.classList.add('weak');
            } else if (strength <= 3) {
                strengthMeter.classList.add('medium');
            } else {
                strengthMeter.classList.add('strong');
            }
        }
    });

    // Password match validation
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;
        
        if (password !== passwordConfirm) {
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