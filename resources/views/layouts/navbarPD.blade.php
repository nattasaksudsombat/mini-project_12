{{-- layouts/navbar.blade.php --}}
<style>
    /* ใช้ id หรือ class ที่เจาะจง modal ของคุณ */
    #addColorModal .modal-content {
        background-color: #2c2c2c !important;
        /* สีพื้นหลังเข้ม */
        color: #ffffff !important;
        /* ตัวหนังสือสีขาว */
    }

    #addColorModal .modal-header,
    #addColorModal .modal-footer {
        background-color: #1e1e1e !important;
        color: #ffffff !important;
    }

    #addColorModal .form-control {
        background-color: #444 !important;
        color: white !important;
        border: 1px solid #666;
    }

    #addColorModal label {
        color: #fff !important;
    }

    #addColorModal .btn {
        border: 1px solid #ccc;
        color: white;
    }

    #addColorModal .btn-close {
        filter: invert(1);
        /* ทำให้ปุ่มปิดมองเห็นได้ในพื้นหลังมืด */
    }

    .search-results {
        position: absolute;
        z-index: 9999;
        top: 60px;
        /* ปรับตามความสูง navbar */
        left: 40%;
        transform: translateX(-50%);
        width: 400px;
        background-color: white;
        border: 1px solid #ccc;
        max-height: 300px;
        overflow-y: auto;
    }

    .search-results li {
        cursor: pointer;
    }

    .search-results a {
        color: #000;
        text-decoration: none;
    }

    .search-results a:hover {
        background-color: #f8f9fa;
    }
    #results .list-group-item {
    padding: 8px 12px;
}

#results img {
    border-radius: 4px;
}

</style>

</style>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('products.index') }}">
            <i class="fas fa-boxes me-2"></i> ระบบจัดการสินค้า
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">



            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <form action="{{ route('products.index') }}" method="GET" class="d-flex ms-auto me-3" role="search">
                        <input type="text" id="search" class="form-control" placeholder="พิมพ์ชื่อสินค้า...">
                        <button class="btn btn-outline-light" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <ul id="results" class="list-group search-results"></ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-chart-line me-1"></i> แดชบอร์ด
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="{{ route('products.create') }}">
                        <i class="fas fa-list me-1"></i> เพิ่มสินค้า
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('categories.index') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                        <i class="fas fa-list me-1"></i> หมวดหมู่สินค้า
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('colors.index') ? 'active' : '' }}" href="{{ route('colors.index') }}">
                        <i class="fas fa-palette me-1"></i> สีสินค้า
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('tags.index') ? 'active' : '' }}" href="{{ route('tags.index') }}">
                        <i class="fas fa-tags me-1"></i> แท็กสินค้า
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('options.index') ? 'active' : '' }}" href="{{ route('sizes.index') }}">
                        <i class="fas fa-cogs me-1"></i> ออปชั่นสินค้า
                    </a>
                </li>
            </ul>

        </div>
    </div>
</nav>

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
                                <img src="${product.image_url}" alt="${product.id_stock}" 
                                    style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                <a href="/products/${product.id}" class="text-decoration-none text-dark">
                                    <strong>${product.id_stock}</strong> - ${Number(product.price).toLocaleString()} บาท
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