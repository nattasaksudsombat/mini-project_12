@extends('layouts.app')
@include('layouts.navbarPD')

@section('content')
<h1>Product List</h1>
@if(request('search'))
    <div class="alert alert-info">
        ผลลัพธ์สำหรับ: <strong>{{ request('search') }}</strong>
    </div>
@endif
@if($products->isEmpty())
    <div class="alert alert-warning">
        ไม่พบสินค้าที่ตรงกับการค้นหา หรือยังไม่มีสินค้าเปิดใช้งาน
    </div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Remaining Stock</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
        <tr>
            <td>{{ $product->id_stock }}</td>
            <td>
                @if ($product->productImages->count() > 0)
                    <img src="{{ asset('storage/' . $product->productImages->first()->image_url) }}" alt="{{ $product->name }}" width="50" height="50">
                @else
                    No image
                @endif
            </td>
            <td>
                <a href="{{ route('products.show', $product->id) }}">
                    {{ $product->name }}
                </a>
            </td>
            <td>{{ $product->price }} ฿</td>
            <td>{{ $product->colorSizes->sum('quantity') }} </td>
            
        </tr>
        @endforeach
    </tbody>
</table>
<div class="d-flex justify-content-center">
    {{ $products->links() }}
</div>
@endsection
