@extends('layouts.app')

@section('content')
<div class="container">

 {{-- 🔙 ปุ่มกลับ --}}
    <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary mb-3">
        ← กลับไปหน้าแสดงข้อมูลสินค้า
    </a>
    <h2>แก้ไขรูปภาพสำหรับสินค้า: {{ $product->name }}</h2>

    <form action="{{ route('products.images.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') {{-- เพิ่มตรงนี้ --}}
        <input type="file" name="images[]" multiple>
        <button type="submit" class="btn btn-primary">อัปโหลด</button>
    </form>

    <hr>

    <h4>รูปภาพปัจจุบัน</h4>
    <div class="row">
        @foreach ($product->productImages as $image)
        <div style="margin-bottom: 15px;">
            <img src="{{ asset('storage/' . $image->image_url) }}" width="150">
            <p>
                @if ($image->is_main)
                <strong style="color: green;">[รูปหลัก]</strong>
                @else
            <form action="{{ route('products.images.setMain', [$product->id, $image->id]) }}" method="POST"> 
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-primary btn-sm"
                    {{ $image->is_main ? 'disabled' : '' }}>

                    {{ $image->is_main ? 'รูปหลัก' : 'ตั้งเป็นรูปหลัก' }}
                </button>
            </form>

            @endif

            <form action="{{ route('productImages.destroy', $image->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('ลบรูปนี้?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">ลบ</button>
            </form>
            </p>
        </div>
        @endforeach

    </div>
</div>
@endsection