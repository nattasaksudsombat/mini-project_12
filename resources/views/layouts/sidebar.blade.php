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
                <a class="nav-link {{ Route::is('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">
                    <i class="fas fa-box me-2"></i> จัดการสินค้า
                </a>
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
                    <a class="nav-link text-danger" href="">
                        <i class="fas fa-sign-out-alt me-1"></i> ออกจากระบบ
                    </a>
                </li>
        </ul>
    </div>
</div>
