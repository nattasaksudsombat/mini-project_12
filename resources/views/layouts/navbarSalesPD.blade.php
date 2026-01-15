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
                <li class="nav-item position-relative">
                    <form action="{{ route('sales.products.index') }}" method="GET" class="d-flex ms-auto me-3" role="search" autocomplete="off">
                        <input type="text" id="search" name="search" class="form-control"
                            placeholder="ค้นหาสินค้า (ชื่อหรือรหัส)..."
                            value="{{ request('search') }}">
                        <button class="btn btn-outline-light ms-2" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>

                    <ul id="results" class="list-group search-results shadow-lg"></ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-chart-line me-1"></i> แดชบอร์ด
                    </a>
                </li>

            </ul>

        </div>
    </div>
</nav>

@section('scripts')
<style>
    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        z-index: 1050;
        max-height: 400px;
        overflow-y: auto;
        display: none;
        margin-top: 5px;
        background: white;
        border-radius: 0 0 5px 5px;
    }

    .search-item-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        margin-right: 12px;
        border: 1px solid #eee;
    }

    .list-group-item-action:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let timer;

        $('#search').on('keyup', function() {
            clearTimeout(timer);
            let query = $(this).val().trim();
            let resultsBox = $('#results');

            if (query.length < 2) {
                resultsBox.empty().hide();
                return;
            }

            timer = setTimeout(function() {
                $.ajax({
                    url: "{{ route('products.search') }}",
                    method: 'GET',
                    data: {
                        q: query
                    },
                    success: function(data) {
                        resultsBox.empty();
                        // console.log("Search Data:", data); // ✅ เช็คข้อมูลที่ได้ใน Console (กด F12)

                        // ... ในส่วน success function(data) ...

                        if (data.length === 0) {
                            resultsBox.append('<li class="list-group-item text-center text-muted">ไม่พบสินค้า</li>');
                        } else {
                            data.forEach(function(product) {
                                let imgSrc = product.image_url;

                                // ✅ แก้ไขตรงนี้: สร้าง URL ของหน้า Sales เอง (ใช้ product.id)
                                let salesUrl = "/sales/products/" + product.id;

                                resultsBox.append(`
            <a href="${salesUrl}" class="list-group-item list-group-item-action d-flex align-items-center text-decoration-none">
                <img src="${imgSrc}" class="search-item-img" alt="${product.id_stock}">
                <div class="flex-grow-1" style="line-height: 1.3;">
                    <div class="fw-bold text-dark" style="font-size:0.95rem;">${product.id_stock}</div>
                    <small class="text-muted" style="font-size:0.85rem;">${product.name}</small>
                </div>
                <span class="fw-bold text-success ms-2">${product.price} ฿</span>
            </a>
        `);
                            });
                        }
                        resultsBox.show();
                    },
                    error: function(err) {
                        console.error('Search Error:', err);
                    }
                });
            }, 300);
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#search, #results').length) {
                $('#results').hide();
            }
        });
    });
</script>
@endsection