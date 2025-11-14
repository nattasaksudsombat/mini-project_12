@extends('layouts.app')
@include('layouts.navbarPD')
@section('content')
<style>
    #results a:hover {
        background-color: #f8f9fa;
    }
</style>

<div class="container">
    <h2>ค้นหาสินค้า</h2>
    <input type="text" id="search" class="form-control" placeholder="พิมพ์ชื่อสินค้า...">
    <ul id="results" class="list-group mt-2"></ul>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let timer;
    $('#search').on('keyup', function() {
        clearTimeout(timer);
        let query = $(this).val();

        if (query.length < 2) {
            $('#results').empty();
            return;
        }

        timer = setTimeout(function() {
            $.ajax({
                url: '/products/search',
                data: {
                    q: query
                },
                success: function(data) {
                    $('#results').empty();
                    if (data.length === 0) {
                        $('#results').append('<li class="list-group-item">ไม่พบสินค้า</li>');
                        return;
                    }

                    data.forEach(function(product) {
                        $('#results').append(`
                            <li class="list-group-item d-flex align-items-center">
                                <img src="${product.image_url}" alt="${product.name}"
                                    style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                <a href="/products/${product.id}" class="text-decoration-none text-dark">
                                    <strong>${product.name}</strong> - ${Number(product.price).toLocaleString()} บาท
                                </a>
                            </li>
                        `);
                    });



                }
            });
        }, 150); // รอ 300ms ค่อยค้น  
    });
</script>
@endsection