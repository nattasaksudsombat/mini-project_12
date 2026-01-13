<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - จัดการสต๊อกสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Sarabun', sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .card-header {
            background: #2a5298;
            color: white;
            padding: 20px;
            text-align: center;
            border: none;
        }
        .card-header h4 { margin: 0; font-weight: bold; }
        .card-body { padding: 30px; }
        .form-control:focus {
            box-shadow: none;
            border-color: #2a5298;
        }
        .btn-login {
            background: #2a5298;
            color: white;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            font-size: 16px;
            transition: 0.3s;
        }
        .btn-login:hover {
            background: #1e3c72;
            color: white;
        }
        .input-group-text { background: #f1f1f1; border-right: none; }
        .form-control { border-left: none; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="card-header">
        <i class="fas fa-boxes fa-3x mb-2"></i>
        <h4>Stock Management</h4>
        <small>เข้าสู่ระบบเพื่อใช้งาน</small>
    </div>
    <div class="card-body">
        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            
            @if ($errors->any())
                <div class="alert alert-danger text-center py-2" style="font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label text-muted">ชื่อผู้ใช้ (Username)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="กรอกชื่อผู้ใช้" required autofocus value="{{ old('username') }}">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted">รหัสผ่าน (Password)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="กรอกรหัสผ่าน" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login">
                เข้าสู่ระบบ <i class="fas fa-sign-in-alt ms-1"></i>
            </button>
        </form>
    </div>
    <div class="card-footer text-center py-3 bg-light text-muted" style="font-size: 12px;">
        &copy; {{ date('Y') }} ระบบจัดการสต๊อกสินค้า
    </div>
</div>

</body>
</html>