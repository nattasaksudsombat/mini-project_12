@extends('layouts.app')

@section('content')
    <h1>Product Images</h1>
    <ul>
        @foreach($productImages as $image)
            <li><img src="{{ asset('storage/' . $product->productImages->first()->image_url) }}" alt="{{ $product->name }}" width="300" height="250">
            </li>
        @endforeach
    </ul>
@endsection
