@extends('layouts.app')
@include('layouts.navbarPD')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>🖼️ จัดการรูปภาพ: {{ $product->name }}</h3>
        <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> กลับ
        </a>
    </div>

    {{-- ✅ แสดง Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ✅ ส่วนอัปโหลดรูปภาพ (รองรับหลายไฟล์) --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-cloud-upload-alt"></i> อัปโหลดรูปภาพใหม่</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('product_images.store', $product->id) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">เลือกรูปภาพ (สามารถเลือกหลายไฟล์พร้อมกันได้)</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*" id="imageInput" required>
                    <small class="text-muted">รองรับไฟล์: JPG, JPEG, PNG, GIF, WEBP (สามารถเลือกหลายไฟล์พร้อมกัน)</small>
                </div>
                
                {{-- Preview รูปที่เลือก --}}
                <div id="imagePreview" class="mb-3 d-none">
                    <label class="form-label fw-bold">รูปภาพที่เลือก:</label>
                    <div id="previewContainer" class="row g-2"></div>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-upload"></i> อัปโหลดทั้งหมด
                </button>
            </form>
        </div>
    </div>

    {{-- ✅ แสดงรายการรูปภาพที่มีอยู่ --}}
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-images"></i> รูปภาพทั้งหมด ({{ $product->productImages->count() }} รูป)</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @forelse($product->productImages as $image)
                    <div class="col-md-3">
                        <div class="card h-100 {{ $image->is_main ? 'border-primary border-3' : 'border' }}">
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $image->image_url) }}" 
                                     class="card-img-top" 
                                     style="height: 200px; object-fit: cover;" 
                                     alt="Product Image"
                                     loading="lazy">
                                
                                @if($image->is_main)
                                    <span class="position-absolute top-0 start-0 badge bg-primary m-2">
                                        <i class="fas fa-star"></i> รูปหลัก
                                    </span>
                                @endif
                            </div>
                            
                            <div class="card-footer bg-white d-flex justify-content-between p-2 gap-1">
                                {{-- ปุ่มตั้งเป็นรูปหลัก --}}
                                @if(!$image->is_main)
                                    <form action="{{ route('product_images.setMain', ['product' => $product->id, 'image' => $image->id]) }}" 
                                          method="POST" 
                                          class="flex-fill">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-primary w-100" 
                                                title="ตั้งเป็นรูปหลัก">
                                            <i class="fas fa-star"></i> ตั้งเป็นหลัก
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-primary w-100 flex-fill" disabled>
                                        <i class="fas fa-star"></i> รูปหลักแล้ว
                                    </button>
                                @endif

                                {{-- ปุ่มลบ --}}
                                <form action="{{ route('product_images.destroy', ['product' => $product->id, 'image' => $image->id]) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('⚠️ ต้องการลบรูปนี้ใช่ไหม?')" 
                                      class="flex-fill">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                        <i class="fas fa-trash"></i> ลบ
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-image"></i> ยังไม่มีรูปภาพ กรุณาอัปโหลดรูปภาพ
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ✅ JavaScript สำหรับ Preview รูปภาพก่อนอัปโหลด --}}
<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
    const previewDiv = document.getElementById('imagePreview');
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';
    
    const files = e.target.files;
    
    if (files.length > 0) {
        previewDiv.classList.remove('d-none');
        
        Array.from(files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-2';
                    col.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" style="height: 100px; object-fit: cover;" alt="Preview">
                            <div class="card-body p-1 text-center">
                                <small class="text-muted">${file.name}</small>
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
                };
                
                reader.readAsDataURL(file);
            }
        });
    } else {
        previewDiv.classList.add('d-none');
    }
});

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>

<style>
.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.border-3 {
    border-width: 3px !important;
}
</style>
@endsection