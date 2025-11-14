@extends('layouts.app')

@section('content')
<div class="container">
    <h2>เพิ่มสีและขนาดให้สินค้า: {{ $product->name }}</h2>

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

    <form action="{{ route('product.colorSize.store') }}" method="POST">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <div class="form-group">
            <label for="color_id">สี</label>
            <select name="color_id" id="color_id" class="form-control" required>
                <option value="">-- เลือกสี --</option>
                @foreach($colors as $color)
                    <option value="{{ $color->id }}">{{ $color->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="size_id">ขนาด</label>
            <select name="size_id" id="size_id" class="form-control" required>
                <option value="">-- เลือกขนาด --</option>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}">{{ $size->size_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="quantity">จำนวน</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="0" required>
        </div>

        <div class="form-group mt-4">
            <button type="submit" class="btn btn-success">บันทึก</button>
            <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary">กลับ</a>
        </div>
    </form>
</div>
@endsection
