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

    {{-- ✅ ส่วนอัปโหลดรูปภาพ --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">อัปโหลดรูปภาพใหม่</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('product_images.store', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">เลือกรูปภาพ (ได้หลายไฟล์)</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-upload"></i> อัปโหลด
                </button>
            </form>
        </div>
    </div>

    {{-- ✅ แสดงรายการรูปภาพที่มีอยู่ --}}
    <div class="row g-3">
        @forelse($product->productImages as $image)
            <div class="col-md-3">
                <div class="card h-100 {{ $image->is_main ? 'border-primary border-2' : '' }}">
                    <div class="position-relative">
                        <img src="{{ asset('storage/' . $image->image_url) }}" class="card-img-top" 
                             style="height: 200px; object-fit: cover;" alt="Product Image">
                        
                        @if($image->is_main)
                            <span class="position-absolute top-0 start-0 badge bg-primary m-2">รูปหลัก</span>
                        @endif
                    </div>
                    
                    {{-- ตัดส่วนเลือกสีออกแล้ว --}}
                    
                    <div class="card-footer bg-white d-flex justify-content-between p-2">
                        {{-- ปุ่มตั้งเป็นรูปหลัก --}}
                        @if(!$image->is_main)
                            <form action="{{ route('product_images.set_main', ['product' => $product->id, 'productImage' => $image->id]) }}" method="POST" class="w-50 me-1">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary w-100" title="ตั้งเป็นรูปหลัก">
                                    ★ หลัก
                                </button>
                            </form>
                        @else
                            <button class="btn btn-sm btn-primary w-50 me-1" disabled>
                                ★ หลักแล้ว
                            </button>
                        @endif

                        {{-- ปุ่มลบ --}}
                        <form action="{{ route('product_images.destroy', $image->id) }}" method="POST" onsubmit="return confirm('ต้องการลบรูปนี้ใช่ไหม?')" class="w-50 ms-1">
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
                <div class="alert alert-warning text-center">ยังไม่มีรูปภาพ</div>
            </div>
        @endforelse
    </div>
</div>
@endsection