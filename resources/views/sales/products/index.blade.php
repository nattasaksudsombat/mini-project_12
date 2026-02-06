@extends('layouts.app')
@include('layouts.navbarSalesPD')

@section('content')
<style>
    /* Container */
    .products-container {
        padding: 2rem 1rem;
    }

    /* Search Panel */
    .search-panel {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    
    .search-panel h4 {
        color: white;
        font-weight: 700;
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
    }
    
    .search-panel h4 i {
        margin-right: 0.5rem;
    }
    
    .search-input-modern {
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid transparent;
        color: #1f2937;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .search-input-modern:focus {
        background: white;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.2);
        color: #1f2937;
    }
    
    .search-input-modern::placeholder {
        color: #9ca3af;
    }
    
    .search-select-modern {
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid transparent;
        color: #1f2937;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .search-select-modern:focus {
        background: white;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.2);
        color: #1f2937;
    }
    
    .search-select-modern option {
        background: white;
        color: #1f2937;
    }
    
    .search-label-modern {
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .btn-search-modern {
        background: white;
        border: none;
        color: #667eea;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    }
    
    .btn-search-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        color: #667eea;
    }
    
    .btn-reset-modern {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid white;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }
    
    .btn-reset-modern:hover {
        background: white;
        color: #667eea;
        transform: translateY(-2px);
    }

    /* Active Filters */
    .active-filters {
        background: rgba(102, 126, 234, 0.1);
        border: 2px solid #667eea;
        border-radius: 15px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .active-filters h5 {
        color: #667eea;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .filter-badge {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
        font-size: 0.85rem;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    /* Header Section */
    .products-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        margin-bottom: 2rem;
    }
    .products-header h1 {
        margin: 0;
        font-weight: 700;
        font-size: 2rem;
    }
    .products-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
        font-size: 1rem;
    }

    /* Alert Cards */
    .alert-modern {
        border: none;
        border-radius: 15px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        font-weight: 500;
    }
    .alert-warning-modern {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    .alert-modern i {
        margin-right: 0.5rem;
    }

    /* Table Card */
    .table-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        background: transparent;
    }

    /* Table Styles */
    .table-modern {
        margin: 0;
        background: transparent;
    }
    .table-modern thead {
        background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
    }
    .table-modern thead th {
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1.25rem 1rem;
        border: none;
        vertical-align: middle;
    }
    .table-modern tbody tr {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    .table-modern tbody tr:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: scale(1.01);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .table-modern tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border: none;
        color: #1f2937;
        font-weight: 500;
    }

    /* Product Image */
    .product-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 12px;
        border: 3px solid white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }
    .product-img:hover {
        transform: scale(1.15);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    }

    /* Product Name Link */
    .product-name {
        color: #667eea;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 1rem;
    }
    .product-name:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    /* Price Badge */
    .price-badge {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1rem;
        display: inline-block;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    }

    /* Stock Badge */
    .stock-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1rem;
        display: inline-block;
    }
    .stock-available {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: white;
        box-shadow: 0 4px 10px rgba(34, 197, 94, 0.3);
    }
    .stock-low {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
    }
    .stock-out {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
    }

    /* Pagination */
    .pagination-wrapper {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
    }
    .pagination {
        gap: 0.5rem;
    }
    .pagination .page-link {
        border-radius: 10px;
        border: none;
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        color: #667eea;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    .pagination .page-link:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    .pagination .active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: white;
    }
    .empty-state i {
        font-size: 5rem;
        margin-bottom: 1.5rem;
        opacity: 0.6;
    }
    .empty-state h3 {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .empty-state p {
        font-size: 1.1rem;
        opacity: 0.8;
    }
</style>

<div class="container products-container">
    {{-- Header --}}
    <div class="products-header">
        <h1><i class="fas fa-boxes"></i> รายการสินค้า (ฝ่ายขาย)</h1>
        <p>ค้นหาและดูรายละเอียดสินค้าทั้งหมด</p>
    </div>

    {{-- Advanced Search Panel --}}
    <div class="search-panel">
        <h4><i class="fas fa-search"></i> ค้นหาสินค้า</h4>
        <form action="{{ route('sales.products.index') }}" method="GET">
            <div class="row g-3">
                {{-- ชื่อสินค้า / รหัสสินค้า --}}
                <div class="col-md-4">
                    <label class="search-label-modern">ชื่อสินค้า / รหัสสินค้า</label>
                    <input type="text" name="search" class="form-control search-input-modern" 
                           placeholder="ค้นหาชื่อหรือรหัสสินค้า..." 
                           value="{{ request('search') }}">
                </div>

                {{-- หมวดหมู่ (ประเภท) --}}
                <div class="col-md-3">
                    <label class="search-label-modern">ประเภท / หมวดหมู่</label>
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

                {{-- สต็อก (จำนวน) --}}
                <div class="col-md-2">
                    <label class="search-label-modern">จำนวนขั้นต่ำ</label>
                    <input type="number" name="stock_min" class="form-control search-input-modern" 
                           placeholder="0" value="{{ request('stock_min') }}" min="0">
                </div>

                {{-- ปุ่มค้นหาและรีเซ็ต --}}
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-search-modern w-100">
                        <i class="fas fa-search"></i> ค้นหา
                    </button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('sales.products.index') }}" class="btn btn-reset-modern w-100">
                        <i class="fas fa-redo"></i> รีเซ็ต
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- แสดงตัวกรองที่ใช้งาน --}}
    @if(request()->hasAny(['search', 'category_id', 'color_id', 'size_id', 'tag_id', 'price_min', 'price_max', 'stock_min']))
    <div class="active-filters">
        <h5><i class="fas fa-filter"></i> ตัวกรองที่ใช้งาน:</h5>
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
                    <i class="fas fa-boxes"></i> จำนวน ≥ {{ request('stock_min') }}
                </span>
            @endif
        </div>
    </div>
    @endif

    {{-- Empty State Alert --}}
    @if($products->isEmpty())
        <div class="alert-modern alert-warning-modern">
            <i class="fas fa-exclamation-triangle"></i>
            ไม่พบสินค้าที่ตรงกับเงื่อนไขการค้นหา
        </div>
    @endif

    {{-- Products Table --}}
    @if(!$products->isEmpty())
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 10%;">รหัส</th>
                            <th class="text-center" style="width: 12%;">รูปภาพ</th>
                            <th style="width: 35%;">ชื่อสินค้า</th>
                            <th class="text-center" style="width: 18%;">ราคา</th>
                            <th class="text-center" style="width: 25%;">คงเหลือ</th>
                        </tr>
                    </thead>
                    <tbody>
    @foreach ($products as $product)
    {{-- ✅ 1. ใส่ Link ที่ตัว <tr> และเพิ่ม Cursor Pointer --}}
    <tr onclick="window.location='{{ route('sales.products.show', $product->id) }}'" 
        style="cursor: pointer;">
        
        <td class="text-center">
            <span style="color: #667eea; font-weight: 700; font-family: monospace; font-size: 1.1rem;">
                #{{ $product->id_stock }}
            </span>
        </td>
        <td class="text-center">
            @if ($product->productImages->count() > 0)
                <img src="{{ asset('storage/' . $product->productImages->first()->image_url) }}" 
                     alt="{{ $product->name }}" 
                     class="product-img">
            @else
                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 0.75rem; font-weight: 600;">
                    No Image
                </div>
            @endif
        </td>
        <td>
            {{-- ✅ 2. เอา <a> ออก เปลี่ยนเป็น <div> หรือ <span> แทน เพื่อไม่ให้ Link ซ้อนกัน --}}
            <div class="product-name">
                <i class="fas fa-box-open"></i> {{ $product->name }}
            </div>
        </td>
        <td class="text-center">
            <span class="price-badge">{{ number_format($product->price, 2) }} ฿</span>
        </td>
        <td class="text-center">
            @php
                $stock = $product->colorSizes->sum('quantity');
            @endphp
            <span class="stock-badge {{ $stock > 10 ? 'stock-available' : ($stock > 0 ? 'stock-low' : 'stock-out') }}">
                {{ number_format($stock) }} ชิ้น
            </span>
        </td>
    </tr>
    @endforeach
</tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="pagination-wrapper">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @else
        <div class="table-card" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>ไม่พบสินค้า</h3>
                <p>ยังไม่มีสินค้าในระบบหรือไม่มีสินค้าที่ตรงกับการค้นหา</p>
            </div>
        </div>
    @endif
</div>
@endsection