@extends('layouts.app')

@section('content')
<div class="container">
    <h2>แก้ไขข้อมูล สี-ขนาด สำหรับสินค้า: {{ $colorSize->product->name }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>เกิดข้อผิดพลาด!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('product.colorSize.update', $colorSize->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="color_id">สี</label>
            <select name="color_id" id="color_id" class="form-control" required>
                @foreach($colors as $color)
                    <option value="{{ $color->id }}" {{ $color->id == $colorSize->color_id ? 'selected' : '' }}>
                        {{ $color->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="size_id">ขนาด</label>
            <select name="size_id" id="size_id" class="form-control" required>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}" {{ $size->id == $colorSize->size_id ? 'selected' : '' }}>
                        {{ $size->size_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="quantity">จำนวน</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="0" value="{{ $colorSize->quantity }}" required>
        </div>

        <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary">อัปเดต</button>
            <a href="{{ route('products.show', $colorSize->product_id) }}" class="btn btn-secondary">กลับ</a>
        </div>
    </form>
</div>
@endsection
