{{-- layouts/sidebar.blade.php --}}
<div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebarMenu">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-chart-line me-2"></i> แดชบอร์ด
                </a>
            </li>
            <li class="nav-item">
                {{-- ✅ ตรวจสอบว่าเป็น "คนขาย" (sales) หรือไม่ --}}
                @if(Auth::user()->role === 'sales')
                {{-- 👉 ถ้าเป็นคนขาย ให้ไปหน้า Sales --}}
                <a class="nav-link {{ Request::is('sales/products*') ? 'active' : '' }}" href="{{ url('/sales/products') }}">
                    <i class="fas fa-tags me-2"></i> ดูราคาสินค้า
                </a>
                @else
                {{-- 👉 ถ้าเป็นแอดมิน (หรืออื่นๆ) ให้ไปหน้าจัดการสินค้าเหมือนเดิม --}}
                <a class="nav-link {{ Route::is('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">
                    <i class="fas fa-box me-2"></i> จัดการสินค้า
                </a>
                @endif
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('orders.index') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                    <i class="fas fa-shopping-cart me-2"></i> รายการคำสั่งซื้อ
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                    <i class="fas fa-chart-bar me-2"></i> รายงานและวิเคราะห์
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('settings.index') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                    <i class="fas fa-cogs me-2"></i> ตั้งค่าและผู้ใช้
                </a>
            </li>
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>