@extends('layouts.app')

@section('content')
<div class="container">

    {{-- 🔙 ปุ่มกลับ --}}
    <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary mb-3">
        ← กลับไปหน้าแสดงข้อมูลสินค้า
    </a>

    <h2>แก้ไขรูปภาพสำหรับสินค้า: {{ $product->name }}</h2>

    {{-- แสดงข้อความแจ้งเตือนความสำเร็จ --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- แสดง Error ถ้ามี --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ✅ Form เพิ่มรูปภาพ --}}
    <form action="{{ route('products.images.store', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="mb-3">
            <label class="form-label">เลือกรูปภาพ</label>
            {{-- 
               ⚠️ แก้ไข: เปลี่ยน name="images[]" เป็น "image" และเอา multiple ออก 
               เพื่อให้ตรงกับ ProductController ที่รับค่า $request->file('image')
            --}}
            <input type="file" name="image" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-upload"></i> อัปโหลดรูปภาพใหม่
        </button>
    </form>

    <hr>

    <h4>รูปภาพปัจจุบัน</h4>
    <div class="row">
        @if($product->productImages->count() > 0)
            @foreach ($product->productImages as $image)
            <div class="col-md-3 text-center mb-4">
                <div class="card p-2 h-100">
                    <img src="{{ asset('storage/' . $image->image_url) }}" class="card-img-top" style="height: 200px; object-fit: contain;">
                    <div class="card-body">
                        @if ($image->is_main)
                            <span class="badge bg-success mb-2 w-100">รูปหลัก</span>
                        @else
                            {{-- ✅ แก้ไข Route เป็น products.setMain (ตามที่ประกาศใน web.php) --}}
                            <form action="{{ route('products.setMain', [$product->id, $image->id]) }}" method="POST" class="d-inline"> 
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm mb-2 w-100">
                                    ตั้งเป็นรูปหลัก
                                </button>
                            </form>
                        @endif

                        {{-- ✅ ปุ่มลบรูปภาพ --}}
                        <form action="{{ route('products.images.destroy', $image->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ต้องการลบรูปนี้ใช่หรือไม่?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm w-100">ลบ</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12 text-center text-muted mt-3">
                <p>ยังไม่มีรูปภาพสินค้า</p>
            </div>
        @endif
    </div>
</div>
@endsection