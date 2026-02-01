@extends('layouts.app')

@section('content')
<style>
    /* ตั้งค่าพื้นหลังและฟอนต์หลัก */
    body {
        background-color: #121212;
        font-family: 'Kanit', sans-serif;
        color: #e0e0e0;
    }

    /* จัดกึ่งกลางหน้าจอ */
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 85vh;
    }

    /* Animation ให้กรอบค่อยๆ ลอยขึ้นมาตอนโหลด */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Animation สั่นแจ้งเตือน (Shake) */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    /* การ์ด Login */
    .login-card {
        background: #1e1e1e;
        border: 2px solid #d4af37; /* ขอบสีทอง */
        border-radius: 15px;
        box-shadow: 0 0 25px rgba(212, 175, 55, 0.15);
        width: 100%;
        max-width: 550px; /* กรอบใหญ่ */
        padding: 3rem;
        animation: fadeInUp 0.8s ease-out;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .login-card:hover {
        transform: translateY(-5px); /* ลอยขึ้นเล็กน้อยเมื่อชี้ */
        border-color: #f1c40f;
        box-shadow: 0 0 35px rgba(212, 175, 55, 0.3);
    }

    /* ✅ Alert แจ้งเตือน Error แบบ Premium */
    .alert-premium-error {
        background-color: rgba(220, 53, 69, 0.15); /* พื้นหลังแดงจางๆ */
        border: 1px solid #ff4d4d; /* ขอบแดงสว่าง */
        color: #ff8080; /* ตัวหนังสือสีแดงพาสเทล */
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 2rem;
        text-align: center;
        box-shadow: 0 0 15px rgba(255, 77, 77, 0.2); /* เงาสีแดง */
        animation: shake 0.5s ease-in-out; /* สั่นดึงดูดความสนใจ */
        font-weight: 500;
    }

    /* ส่วนหัว */
    .login-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .login-header i {
        color: #d4af37;
        text-shadow: 0 0 10px rgba(212, 175, 55, 0.5);
    }

    .login-header h3 {
        color: #d4af37;
        font-weight: 700;
        margin-top: 15px;
        letter-spacing: 2px;
    }

    /* ข้อความคำอธิบาย (ปรับสีให้อ่านง่าย) */
    .text-desc {
        color: #cccccc !important; /* สีเทาสว่าง */
        font-weight: 300;
        font-size: 1rem;
    }

    /* ปรับแต่ง Input */
    .form-label {
        color: #d4af37;
        font-weight: 500;
        font-size: 1.1rem;
    }

    .input-group-text {
        background-color: #2c2c2c;
        border: 1px solid #444;
        border-right: none;
        color: #d4af37;
        width: 50px;
        justify-content: center;
    }

    .form-control {
        background-color: #2c2c2c;
        border: 1px solid #444;
        border-left: none;
        color: #fff !important;
        height: 55px; /* ช่องใหญ่ */
        font-size: 1.1rem;
        border-radius: 0 8px 8px 0;
    }

    .form-control:focus {
        background-color: #333;
        border-color: #d4af37;
        box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        color: #fff !important;
    }

    /* สี Placeholder ให้สว่างเห็นชัด */
    .form-control::placeholder {
        color: #999999 !important;
        opacity: 1;
    }

    /* ปุ่ม Login สีทอง */
    .btn-gold {
        background: linear-gradient(135deg, #d4af37, #f1c40f);
        border: none;
        color: #000;
        font-weight: 700;
        font-size: 1.2rem;
        padding: 12px;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .btn-gold:hover {
        background: linear-gradient(135deg, #f1c40f, #ffeb3b);
        transform: scale(1.02);
        box-shadow: 0 0 15px rgba(212, 175, 55, 0.6);
    }

    /* Checkbox */
    .form-check-input {
        background-color: #2c2c2c;
        border-color: #444;
        cursor: pointer;
    }
    .form-check-input:checked {
        background-color: #d4af37;
        border-color: #d4af37;
    }
</style>

<div class="container login-container">
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-gem fa-4x"></i>
            <h3>WELCOME BACK</h3>
            <p class="text-desc mt-2">กรุณาเข้าสู่ระบบเพื่อดำเนินการต่อ</p>
        </div>

        {{-- ✅ ส่วนแสดง Error แบบใหม่ --}}
        @if(session('error'))
            <div class="alert-premium-error">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- เพิ่มการแสดง Error จาก Validate (เช่น ลืมกรอกข้อมูล) --}}
        @if($errors->any())
            <div class="alert-premium-error">
                <ul class="mb-0 list-unstyled">
                    @foreach($errors->all() as $error)
                        <li><i class="fas fa-times-circle me-2"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            {{-- ชื่อผู้ใช้ --}}
            <div class="mb-4">
                <label for="username" class="form-label">ชื่อผู้ใช้ (Username)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" 
                           required autofocus placeholder="กรอกชื่อผู้ใช้ของคุณ...">
                </div>
            </div>

            {{-- รหัสผ่าน --}}
            <div class="mb-4">
                <label for="password" class="form-label">รหัสผ่าน (Password)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" 
                           required placeholder="••••••••">
                </div>
            </div>

            {{-- จดจำฉัน --}}
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label text-desc" for="remember" style="cursor: pointer;">
                        จดจำฉันไว้
                    </label>
                </div>
            </div>

            {{-- ปุ่ม Login --}}
            <div class="d-grid">
                <button type="submit" class="btn btn-gold shadow">
                    เข้าสู่ระบบ <i class="fas fa-sign-in-alt ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection