@extends('layouts.app')

@section('content')
<body>    
<div class="container">
    <h2>แก้ไขสินค้า</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>เกิดข้อผิดพลาด!</strong>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @yield('scripts')
    </body>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="id_stock">รหัสสินค้า</label>
            <input type="text" name="id_stock" class="form-control" value="{{ old('id_stock', $product->id_stock) }}" required>
            @error('id_stock')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <!-- ชื่อสินค้า -->
        <div class="form-group">
            <label for="name">ชื่อสินค้า</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
        </div>

        <!-- ราคา -->
        <div class="form-group">
            <label for="price">ราคาขาย (บาท)</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
        </div>

        <!-- ต้นทุน -->
        <div class="form-group">
            <label for="cost">ต้นทุน (บาท)</label>
            <input type="number" step="0.01" name="cost" class="form-control" value="{{ old('cost', $product->cost) }}">
        </div>

        <!-- คำอธิบาย -->
        <div class="form-group">
            <label for="description">คำอธิบาย</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
        </div>
        <!-- แท็กสินค้า -->
        <div class="form-group">
            <label for="tags">แท็กสินค้า</label>
            <select name="tags[]" class="form-control" multiple>
                @foreach ($tags as $tag)
                <option value="{{ $tag->id }}"
                    {{ $product->tags->contains($tag->id) ? 'selected' : '' }}>
                    {{ $tag->tag_name }}
                </option>
                @endforeach
            </select>
            <small class="text-muted">กด Ctrl หรือ Command เพื่อเลือกหลายแท็ก</small>
        </div>

        <!-- หมวดหมู่ -->
        <div class="form-group">
            <label for="category_id">หมวดหมู่</label>
            <select name="category_id" class="form-control" required>
                <option value="">-- เลือกหมวดหมู่ --</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    {{ $product->category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->category_name }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- ปุ่มบันทึก -->
        <button type="submit" class="btn btn-success mt-3">บันทึกการแก้ไข</button>
        <a href="{{ route('products.show',$product->id) }}" class="btn btn-secondary mt-3">กลับ</a>
    </form>
</div>
@endsection