@extends('layouts.app')
@include('layouts.navbarPD')

@section('content')
<style>
    /* Create Product Page - Black & Gold Theme */
    .create-header {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(30, 30, 30, 0.9));
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 215, 0, 0.1);
    }

    .create-header h2 {
        color: var(--gold);
        text-shadow: 0 0 15px rgba(255, 215, 0, 0.5);
        font-weight: 600;
        margin: 0;
    }

    /* Alert */
    .alert-danger {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.2), rgba(30, 30, 30, 0.9));
        color: #ff6b7d !important;
        border: 1px solid rgba(220, 53, 69, 0.5);
        border-radius: 12px;
    }

    .alert-danger ul,
    .alert-danger li {
        color: #ff6b7d !important;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.2), rgba(30, 30, 30, 0.9));
        color: #7de5a4 !important;
        border: 1px solid rgba(25, 135, 84, 0.5);
        border-radius: 12px;
    }

    /* Form Card */
    .form-card {
        background: linear-gradient(135deg, rgba(30, 30, 30, 0.95), rgba(18, 18, 18, 0.95));
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 25px rgba(255, 215, 0, 0.15);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .form-card h4 {
        color: var(--gold);
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.4);
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid rgba(255, 215, 0, 0.3);
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

    /* Form Controls */
    .form-control,
    .form-select {
        background-color: rgba(10, 10, 10, 0.8) !important;
        color: #e8e8e8 !important;
        border: 1px solid rgba(255, 215, 0, 0.3) !important;
        border-radius: 10px;
        padding: 0.75rem 1rem;
    }

    .form-control:focus,
    .form-select:focus {
        background-color: rgba(10, 10, 10, 0.9) !important;
        border-color: var(--gold) !important;
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.3) !important;
        color: #e8e8e8 !important;
    }

    .form-control::placeholder {
        color: rgba(204, 204, 204, 0.5) !important;
    }

    .form-select option {
        background: #1e1e1e;
        color: #e8e8e8;
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    /* File Input */
    .form-control[type="file"] {
        padding: 0.5rem 1rem;
    }

    .form-control[type="file"]::file-selector-button {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(255, 215, 0, 0.1));
        border: 1px solid var(--gold);
        color: var(--gold);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        margin-right: 1rem;
        font-weight: 600;
        cursor: pointer;
    }

    .form-control[type="file"]::file-selector-button:hover {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        color: #000;
    }

    /* Invalid Feedback */
    .invalid-feedback {
        color: #ff6b7d !important;
        font-size: 0.85rem;
    }

    .is-invalid {
        border-color: rgba(220, 53, 69, 0.5) !important;
    }

    /* Buttons */
    .btn-submit {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        color: #fff !important;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 700;
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }

    .btn-submit:hover {
        color: #fff !important;
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.5);
    }

    .btn-cancel {
        background: rgba(30, 30, 30, 0.8);
        border: 1px solid rgba(255, 215, 0, 0.3);
        color: var(--text-primary) !important;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
    }

    .btn-cancel:hover {
        background: rgba(40, 40, 40, 0.9);
        border-color: var(--gold);
        color: var(--gold) !important;
    }

    /* Excel Import/Export Section */
    .excel-card {
        background: linear-gradient(135deg, rgba(95, 237, 255, 0.1), rgba(30, 30, 30, 0.9));
        border: 1px solid rgba(95, 237, 255, 0.3);
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 20px rgba(95, 237, 255, 0.1);
    }

    .excel-card h4 {
        color: #5fedff;
        text-shadow: 0 0 10px rgba(95, 237, 255, 0.4);
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .btn-excel-import {
        background: linear-gradient(135deg, #5fedff, #36d8ff);
        border: none;
        color: #000 !important;
        padding: 0.6rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        box-shadow: 0 5px 15px rgba(95, 237, 255, 0.3);
    }

    .btn-excel-import:hover {
        color: #000 !important;
        box-shadow: 0 8px 25px rgba(95, 237, 255, 0.5);
    }

    .btn-excel-export {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        border: none;
        color: #000 !important;
        padding: 0.6rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
    }

    .btn-excel-export:hover {
        color: #000 !important;
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.5);
    }
</style>

<div class="container py-4">
    <div class="create-header">
        <h2>
            <i class="fas fa-box-open me-2"></i>เพิ่มสินค้าใหม่
        </h2>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <strong><i class="fas fa-exclamation-triangle me-2"></i>พบข้อผิดพลาด:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-card">
            <h4><i class="fas fa-info-circle me-2"></i>ข้อมูลพื้นฐาน</h4>

            <div class="row">
                <!-- รหัสสินค้า -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-barcode me-2"></i>รหัสสินค้า <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="id_stock" 
                           class="form-control @error('id_stock') is-invalid @enderror" 
                           value="{{ old('id_stock') }}" 
                           required
                           placeholder="กรอกรหัสสินค้า">
                    @error('id_stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- ชื่อสินค้า -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-tag me-2"></i>ชื่อสินค้า <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           class="form-control" 
                           value="{{ old('name') }}" 
                           required
                           placeholder="กรอกชื่อสินค้า">
                </div>
            </div>

            <div class="row">
                <!-- ราคา -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-dollar-sign me-2"></i>ราคา (บาท) <span class="text-danger">*</span>
                    </label>
                    <input type="number" 
                           name="price" 
                           class="form-control" 
                           value="{{ old('price') }}" 
                           required
                           min="0"
                           step="0.01"
                           placeholder="0.00">
                </div>

                <!-- ต้นทุน -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-money-bill-wave me-2"></i>ต้นทุน (บาท) <span class="text-danger">*</span>
                    </label>
                    <input type="number" 
                           name="cost" 
                           class="form-control" 
                           value="{{ old('cost') }}" 
                           required
                           min="0"
                           step="0.01"
                           placeholder="0.00">
                </div>
            </div>

            <!-- หมวดหมู่ -->
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-th-large me-2"></i>หมวดสินค้า <span class="text-danger">*</span>
                </label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- เลือกหมวดสินค้า --</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- คำอธิบาย -->
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-align-left me-2"></i>คำอธิบาย
                </label>
                <textarea name="description" 
                          class="form-control"
                          placeholder="กรอกคำอธิบายสินค้า (ถ้ามี)">{{ old('description') }}</textarea>
            </div>

            <!-- รูปภาพ -->
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-image me-2"></i>รูปภาพหลัก
                </label>
                <input type="file" 
                       name="image" 
                       class="form-control"
                       accept="image/*">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>รองรับไฟล์: JPG, PNG, GIF (ขนาดไม่เกิน 2MB)
                </small>
            </div>
        </div>

        <!-- ปุ่มบันทึก -->
        <div class="d-flex gap-2 justify-content-end mb-4">
            <a href="{{ route('products.index') }}" class="btn btn-cancel">
                <i class="fas fa-times me-2"></i>ยกเลิก
            </a>
            <button type="submit" class="btn btn-submit">
                <i class="fas fa-save me-2"></i>เพิ่มสินค้า
            </button>
        </div>
    </form>

    <!-- Excel Import/Export Section -->
    <div class="excel-card">
        <h4><i class="fas fa-file-excel me-2"></i>นำเข้า/ส่งออกข้อมูล Excel</h4>
        
        <div class="row g-3">
            <!-- Import Excel -->
            <div class="col-md-6">
                <form action="{{ route('products.import') }}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      class="d-flex gap-2">
                    @csrf
                    <input type="file" 
                           name="file" 
                           class="form-control" 
                           required
                           accept=".xlsx,.xls">
                    <button type="submit" class="btn btn-excel-import text-nowrap">
                        <i class="fas fa-upload me-2"></i>นำเข้าข้อมูล
                    </button>
                </form>
            </div>

            <!-- Export Excel -->
            <div class="col-md-6">
                <a href="{{ route('export.products') }}" class="btn btn-excel-export w-100">
                    <i class="fas fa-download me-2"></i>ส่งออกข้อมูล Excel
                </a>
            </div>
        </div>

        <small class="text-muted d-block mt-2">
            <i class="fas fa-info-circle me-1"></i>
            ใช้สำหรับนำเข้าหรือส่งออกข้อมูลสินค้าจำนวนมากผ่านไฟล์ Excel
        </small>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto dismiss alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // File input preview name
    document.querySelector('input[type="file"][name="image"]')?.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
            console.log('Selected file:', fileName);
        }
    });
</script>
@endpush