@extends('layouts.app')
@include('layouts.navbarPD')

@section('content')
<style>
    /* Search Panel */
    .search-panel {
        background: linear-gradient(145deg, var(--dark-secondary), var(--dark-tertiary));
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 215, 0, 0.2);
    }
    
    .search-panel h4 {
        color: var(--gold);
        font-weight: 600;
        margin-bottom: 1.5rem;
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
    }
    
    .search-panel h4 i {
        margin-right: 0.5rem;
    }
    
    .search-input-modern {
        background: rgba(30, 30, 30, 0.6);
        border: 1px solid rgba(255, 215, 0, 0.3);
        color: var(--text-primary);
        padding: 0.75rem 1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .search-input-modern:focus {
        background: rgba(30, 30, 30, 0.8);
        border-color: var(--gold);
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.2);
        color: var(--text-primary);
    }
    
    .search-input-modern::placeholder {
        color: var(--text-secondary);
        opacity: 0.6;
    }
    
    .search-select-modern {
        background: rgba(30, 30, 30, 0.6);
        border: 1px solid rgba(255, 215, 0, 0.3);
        color: var(--text-primary);
        padding: 0.75rem 1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .search-select-modern:focus {
        background: rgba(30, 30, 30, 0.8);
        border-color: var(--gold);
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.2);
        color: var(--text-primary);
    }
    
    .search-select-modern option {
        background: var(--dark-bg);
        color: var(--text-primary);
    }
    
    .search-label-modern {
        color: var(--neon-blue);
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .btn-search-modern {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        border: none;
        color: var(--dark-bg);
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(255, 215, 0, 0.3);
    }
    
    .btn-search-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.5);
        color: var(--dark-bg);
    }
    
    .btn-reset-modern {
        background: rgba(95, 237, 255, 0.2);
        border: 1px solid rgba(95, 237, 255, 0.4);
        color: var(--neon-blue);
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-reset-modern:hover {
        background: rgba(95, 237, 255, 0.3);
        border-color: var(--neon-blue);
        color: var(--neon-blue);
        transform: translateY(-2px);
    }

    /* Product List Header */
    .product-header {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), transparent);
        border-left: 5px solid var(--gold);
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .product-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.1), transparent);
        animation: pulse 3s infinite;
    }
    
    .product-header h3 {
        color: var(--gold);
        font-weight: 600;
        font-size: 2rem;
        margin: 0;
        text-shadow: 0 0 20px rgba(255, 215, 0, 0.4),
                     0 0 30px rgba(255, 215, 0, 0.2);
        position: relative;
        z-index: 1;
    }
    
    .product-header h3 i {
        margin-right: 0.5rem;
        animation: bounce 2s infinite;
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    
    /* Active Filters Display */
    .active-filters {
        background: rgba(95, 237, 255, 0.1);
        border: 1px solid rgba(95, 237, 255, 0.3);
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .active-filters h5 {
        color: var(--neon-blue);
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.75rem;
    }
    
    .filter-badge {
        display: inline-block;
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(255, 215, 0, 0.1));
        border: 1px solid rgba(255, 215, 0, 0.4);
        color: var(--gold);
        padding: 0.4rem 1rem;
        border-radius: 20px;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    /* Warning Alert */
    .warning-alert {
        background: linear-gradient(90deg, rgba(255, 215, 0, 0.1), transparent);
        border-left: 4px solid var(--gold);
        color: var(--gold);
        padding: 1rem 1.5rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        box-shadow: 0 3px 10px rgba(255, 215, 0, 0.2);
    }
    
    .warning-alert i {
        margin-right: 0.5rem;
    }
    
    /* Table Container */
    .table-container {
        background: linear-gradient(145deg, var(--dark-secondary), var(--dark-tertiary));
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255, 215, 0, 0.2);
        overflow: hidden;
    }
    
    /* Table Styling */
    .product-table {
        background: transparent;
        color: var(--text-primary);
        margin-bottom: 0;
    }
    
    .product-table thead {
        background: linear-gradient(90deg, rgba(255, 215, 0, 0.15), rgba(255, 215, 0, 0.05));
        border-bottom: 2px solid var(--gold);
    }
    
    .product-table thead th {
        color: var(--gold);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
        padding: 1rem;
        border: none;
        text-align: center;
        position: relative;
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
    }
    
    .product-table thead th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: var(--gold);
        transition: width 0.3s ease;
    }
    
    .product-table thead th:hover::after {
        width: 80%;
    }
    
    .product-table tbody tr {
        background: rgba(30, 30, 30, 0.5);
        border-bottom: 1px solid rgba(255, 215, 0, 0.1);
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .product-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(255, 215, 0, 0.1), rgba(255, 215, 0, 0.05));
        transform: translateX(5px);
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.2);
        border-left: 3px solid var(--gold);
    }
    
    .product-table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border: none;
        color: var(--text-secondary);
        text-align: center;
    }
    
    .product-table tbody tr:hover td {
        color: var(--text-primary);
    }
    
    /* Product Image */
    .product-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid rgba(255, 215, 0, 0.3);
        transition: all 0.3s ease;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
    }
    
    .product-table tbody tr:hover .product-img {
        transform: scale(1.1) rotate(2deg);
        border-color: var(--gold);
        box-shadow: 0 5px 20px rgba(255, 215, 0, 0.4);
    }
    
    /* Product Name Link */
    .product-name {
        color: var(--neon-blue);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-block;
        position: relative;
    }
    
    .product-name::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--neon-blue);
        transition: width 0.3s ease;
    }
    
    .product-name:hover {
        color: var(--gold);
        text-shadow: 0 0 10px rgba(95, 237, 255, 0.5);
    }
    
    .product-name:hover::after {
        width: 100%;
        background: var(--gold);
    }
    
    /* Price Badge */
    .price-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(255, 215, 0, 0.1));
        border: 1px solid rgba(255, 215, 0, 0.4);
        border-radius: 20px;
        color: var(--gold);
        font-weight: 600;
        font-size: 1rem;
        box-shadow: 0 3px 10px rgba(255, 215, 0, 0.2);
        transition: all 0.3s ease;
    }
    
    .product-table tbody tr:hover .price-badge {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
    }
    
    /* Stock Badge */
    .stock-badge {
        display: inline-block;
        padding: 0.4rem 0.9rem;
        background: linear-gradient(135deg, rgba(95, 237, 255, 0.2), rgba(95, 237, 255, 0.1));
        border: 1px solid rgba(95, 237, 255, 0.4);
        border-radius: 20px;
        color: var(--neon-blue);
        font-weight: 600;
        box-shadow: 0 3px 10px rgba(95, 237, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .product-table tbody tr:hover .stock-badge {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(95, 237, 255, 0.4);
    }
    
    /* Pagination Styling */
    .pagination-container {
        margin-top: 2rem;
        padding: 1.5rem;
        background: linear-gradient(145deg, var(--dark-secondary), var(--dark-tertiary));
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 215, 0, 0.2);
    }
    
    .pagination {
        margin-bottom: 0;
        gap: 0.5rem;
    }
    
    .pagination .page-item .page-link {
        background: rgba(30, 30, 30, 0.6);
        border: 1px solid rgba(255, 215, 0, 0.3);
        color: var(--text-secondary);
        padding: 0.6rem 1rem;
        border-radius: 6px;
        transition: all 0.3s ease;
        font-weight: 500;
        margin: 0;
    }
    
    .pagination .page-item .page-link:hover {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(255, 215, 0, 0.1));
        border-color: var(--gold);
        color: var(--gold);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        border-color: var(--gold);
        color: var(--dark-bg);
        font-weight: 700;
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
        transform: scale(1.1);
    }
    
    .pagination .page-item.disabled .page-link {
        background: rgba(30, 30, 30, 0.3);
        border-color: rgba(255, 215, 0, 0.1);
        color: rgba(204, 204, 204, 0.3);
        cursor: not-allowed;
    }
    
    /* No Image Placeholder */
    .no-image {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(255, 215, 0, 0.05));
        border: 2px dashed rgba(255, 215, 0, 0.3);
        border-radius: 8px;
        color: var(--text-secondary);
        font-size: 0.75rem;
        margin: 0 auto;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .product-header h3 {
            font-size: 1.5rem;
        }
        
        .product-table {
            font-size: 0.85rem;
        }
        
        .product-img {
            width: 50px;
            height: 50px;
        }
    }
</style>

<div class="container">
    <!-- Product Header -->
    <div class="product-header fade-in">
        <h3>
            <i class="fas fa-shopping-bag"></i>
            Product List
        </h3>
    </div>

    <!-- Advanced Search Panel -->
    <div class="search-panel fade-in delay-1">
        <h4><i class="fas fa-filter"></i> ค้นหาและกรองสินค้า</h4>
        <form action="{{ route('products.index') }}" method="GET">
            <div class="row g-3">
                {{-- ชื่อสินค้า / รหัสสินค้า --}}
                <div class="col-md-4">
                    <label class="search-label-modern">ชื่อสินค้า / รหัสสินค้า</label>
                    <input type="text" name="search" class="form-control search-input-modern" 
                           placeholder="ค้นหาชื่อหรือรหัสสินค้า..." 
                           value="{{ request('search') }}">
                </div>

                {{-- หมวดหมู่ --}}
                <div class="col-md-3">
                    <label class="search-label-modern">หมวดหมู่</label>
                    <select name="category_id" class="form-select search-select-modern">
                        <option value="">-- ทั้งหมด --</option>
                        @foreach(\App\Models\Category::orderBy('category_name')->get() as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- สี --}}
                <div class="col-md-2">
                    <label class="search-label-modern">สี</label>
                    <select name="color_id" class="form-select search-select-modern">
                        <option value="">-- ทั้งหมด --</option>
                        @foreach(\App\Models\Color::orderBy('name')->get() as $color)
                            <option value="{{ $color->id }}" {{ request('color_id') == $color->id ? 'selected' : '' }}>
                                {{ $color->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ไซส์ --}}
                <div class="col-md-3">
                    <label class="search-label-modern">ไซส์</label>
                    <select name="size_id" class="form-select search-select-modern">
                        <option value="">-- ทั้งหมด --</option>
                        @foreach(\App\Models\Size::orderBy('size_name')->get() as $size)
                            <option value="{{ $size->id }}" {{ request('size_id') == $size->id ? 'selected' : '' }}>
                                {{ $size->size_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- แท็ก --}}
                <div class="col-md-4">
                    <label class="search-label-modern">แท็ก</label>
                    <select name="tag_id" class="form-select search-select-modern">
                        <option value="">-- ทั้งหมด --</option>
                        @foreach(\App\Models\Tag::orderBy('tag_name')->get() as $tag)
                            <option value="{{ $tag->id }}" {{ request('tag_id') == $tag->id ? 'selected' : '' }}>
                                {{ $tag->tag_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ช่วงราคา --}}
                <div class="col-md-2">
                    <label class="search-label-modern">ราคาต่ำสุด</label>
                    <input type="number" name="price_min" class="form-control search-input-modern" 
                           placeholder="0" value="{{ request('price_min') }}" min="0" step="0.01">
                </div>

                <div class="col-md-2">
                    <label class="search-label-modern">ราคาสูงสุด</label>
                    <input type="number" name="price_max" class="form-control search-input-modern" 
                           placeholder="∞" value="{{ request('price_max') }}" min="0" step="0.01">
                </div>

                {{-- สต็อก --}}
                <div class="col-md-2">
                    <label class="search-label-modern">สต็อกขั้นต่ำ</label>
                    <input type="number" name="stock_min" class="form-control search-input-modern" 
                           placeholder="0" value="{{ request('stock_min') }}" min="0">
                </div>

                {{-- ปุ่มค้นหาและรีเซ็ต --}}
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-search-modern flex-fill">
                        <i class="fas fa-search"></i> ค้นหา
                    </button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('products.index') }}" class="btn btn-reset-modern w-100">
                        <i class="fas fa-redo"></i> รีเซ็ต
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- แสดงตัวกรองที่ใช้งาน --}}
    @if(request()->hasAny(['search', 'category_id', 'color_id', 'size_id', 'tag_id', 'price_min', 'price_max', 'stock_min']))
    <div class="active-filters fade-in delay-2">
        <h5><i class="fas fa-tags"></i> ตัวกรองที่ใช้งาน:</h5>
        <div>
            @if(request('search'))
                <span class="filter-badge">
                    <i class="fas fa-search"></i> "{{ request('search') }}"
                </span>
            @endif
            @if(request('category_id'))
                <span class="filter-badge">
                    <i class="fas fa-folder"></i> {{ \App\Models\Category::find(request('category_id'))->category_name ?? 'N/A' }}
                </span>
            @endif
            @if(request('color_id'))
                <span class="filter-badge">
                    <i class="fas fa-palette"></i> {{ \App\Models\Color::find(request('color_id'))->name ?? 'N/A' }}
                </span>
            @endif
            @if(request('size_id'))
                <span class="filter-badge">
                    <i class="fas fa-ruler"></i> {{ \App\Models\Size::find(request('size_id'))->size_name ?? 'N/A' }}
                </span>
            @endif
            @if(request('tag_id'))
                <span class="filter-badge">
                    <i class="fas fa-tag"></i> {{ \App\Models\Tag::find(request('tag_id'))->tag_name ?? 'N/A' }}
                </span>
            @endif
            @if(request('price_min') || request('price_max'))
                <span class="filter-badge">
                    <i class="fas fa-dollar-sign"></i> 
                    {{ request('price_min') ?: '0' }} - {{ request('price_max') ?: '∞' }} บาท
                </span>
            @endif
            @if(request('stock_min'))
                <span class="filter-badge">
                    <i class="fas fa-boxes"></i> สต็อก ≥ {{ request('stock_min') }}
                </span>
            @endif
        </div>
    </div>
    @endif

    {{-- Warning Alert --}}
    @if($products->isEmpty())
        <div class="warning-alert fade-in delay-3">
            <i class="fas fa-exclamation-triangle"></i>
            ไม่พบสินค้าที่ตรงกับเงื่อนไขการค้นหา
        </div>
    @endif

    {{-- Product Table --}}
    <div class="table-container fade-in delay-3">
        <div class="table-responsive">
            <table class="product-table table table-hover">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag me-2"></i>ID</th>
                        <th><i class="fas fa-image me-2"></i>Image</th>
                        <th><i class="fas fa-tag me-2"></i>Name</th>
                        <th><i class="fas fa-money-bill-wave me-2"></i>Price</th>
                        <th><i class="fas fa-boxes me-2"></i>Remaining Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                    <tr onclick="window.location='{{ route('products.show', $product->id) }}'">
                        <td>
                            <span style="color: var(--gold); font-weight: 600;">{{ $product->id_stock }}</span>
                        </td>
                        <td>
                            @if ($product->productImages->count() > 0)
                                <img src="{{ asset('storage/' . $product->productImages->first()->image_url) }}" 
                                     alt="{{ $product->name }}" 
                                     class="product-img">
                            @else
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('products.show', $product->id) }}" class="product-name">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td>
                            <span class="price-badge">{{ number_format($product->price, 2) }} ฿</span>
                        </td>
                        <td>
                            <span class="stock-badge">{{ $product->colorSizes->sum('quantity') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="pagination-container d-flex justify-content-center fade-in delay-4">
        {{ $products->appends(request()->query())->links() }}
    </div>
</div>

<script>
    // เพิ่มเอฟเฟกต์เมื่อโหลดหน้า
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.product-table tbody tr');
        rows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.animation = `fadeIn 0.5s ease-out ${0.5 + (index * 0.1)}s forwards`;
        });
    });
</script>
@endsection