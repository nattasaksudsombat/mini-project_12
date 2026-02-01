@extends('layouts.app')
@include('layouts.navbarPD')

@section('content')
<style>
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
    
    .product-header h1 {
        color: var(--gold);
        font-weight: 600;
        font-size: 2rem;
        margin: 0;
        text-shadow: 0 0 20px rgba(255, 215, 0, 0.4),
                     0 0 30px rgba(255, 215, 0, 0.2);
        position: relative;
        z-index: 1;
    }
    
    .product-header h1 i {
        margin-right: 0.5rem;
        animation: bounce 2s infinite;
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    
    /* Search Alert */
    .search-alert {
        background: linear-gradient(90deg, rgba(95, 237, 255, 0.1), transparent);
        border-left: 4px solid var(--neon-blue);
        color: var(--neon-blue);
        padding: 1rem 1.5rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        box-shadow: 0 3px 10px rgba(95, 237, 255, 0.2);
    }
    
    .search-alert i {
        margin-right: 0.5rem;
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
        .product-header h1 {
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

<!-- Product Header -->
<div class="product-header container fade-in">
    <h3>
        <i class="fas fa-shopping-bag"></i>
        Product List
    </h3>
</div>

<!-- Search Alert -->
@if(request('search'))
    <div class="search-alert fade-in delay-1">
        <i class="fas fa-search"></i>
        ผลลัพธ์สำหรับ: <strong>{{ request('search') }}</strong>
    </div>
@endif

<!-- Warning Alert -->
@if($products->isEmpty())
    <div class="warning-alert fade-in delay-2">
        <i class="fas fa-exclamation-triangle"></i>
        ไม่พบสินค้าที่ตรงกับการค้นหา หรือยังไม่มีสินค้าเปิดใช้งาน
    </div>
@endif

<!-- Product Table -->
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

<!-- Pagination -->
<div class="pagination-container d-flex justify-content-center fade-in delay-4">
    {{ $products->links() }}
</div>

<script>
    // เพิ่มเอฟเฟกต์เมื่อโหลดหน้า
    document.addEventListener('DOMContentLoaded', function() {
        // เพิ่ม animation delay สำหรับแต่ละแถว
        const rows = document.querySelectorAll('.product-table tbody tr');
        rows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.animation = `fadeIn 0.5s ease-out ${0.5 + (index * 0.1)}s forwards`;
        });
    });
</script>
@endsection