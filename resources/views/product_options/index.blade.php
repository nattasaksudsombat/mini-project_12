@extends('layouts.app')

@section('content')
    <h1>Product Options</h1>
    <ul>
        @foreach($productOptions as $option)
            <li>{{ $option->option_name }}: {{ $option->option_value }}</li>
        @endforeach
    </ul>
@endsection
