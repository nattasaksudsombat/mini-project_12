@extends('layouts.app')

@section('content')
    <h1>Product Tags</h1>
    <ul>
        @foreach($productTags as $tag)
            <li>{{ $tag->tag->tag_name }}</li>
        @endforeach
    </ul>
@endsection
