@extends('layouts.app')
@include('layouts.navbarPD')

@section('content')
<div class="container">
    <h2>เพิ่มสินค้าใหม่</h2>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf

        <!-- รหัสสินค้า -->
        <div class="mb-3">
            <label>รหัสสินค้า <span class="text-danger">*</span></label>
            <input type="text" name="id_stock" class="form-control @error('id_stock') is-invalid @enderror" value="{{ old('id_stock') }}" required>
            @error('id_stock')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- ชื่อสินค้า -->
        <div class="mb-3">
            <label>ชื่อสินค้า</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <!-- ราคา -->
        <div class="mb-3">
            <label>ราคา (บาท)</label>
            <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>
        </div>

        <!-- ต้นทุน -->
        <div class="mb-3">
            <label>ต้นทุน (บาท)</label>
            <input type="number" name="cost" class="form-control" value="{{ old('cost') }}" required>
        </div>

        <!-- คำอธิบาย -->
        <div class="mb-3">
            <label>คำอธิบาย</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <!-- หมวดหมู่ -->
        <div class="mb-3">
            <label>หมวดสินค้า</label>
            <select name="category_id" class="form-control" required>
                <option value="">-- เลือกหมวดสินค้า --</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->category_name }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- รูปภาพ -->
        <div class="mb-3">
            <label>รูปภาพหลัก</label>
            <input type="file" name="image" class="form-control">
        </div>

        <!-- ปุ่ม -->
        <button type="submit" class="btn btn-success">เพิ่มสินค้า</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">ยกเลิก</a>
    </form>
    <!-- resources/views/products.blade.php -->
    @if(session('success'))
    <div>{{ session('success') }}</div>
    @endif

    <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">นำเข้าข้อมูล Excel</button>
    </form>

    <a href="{{ route('export.products') }}">
        <button>ส่งออกข้อมูล Excel</button>
    </a>

</div>
@endsection