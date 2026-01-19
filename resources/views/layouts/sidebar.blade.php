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
            @if(auth()->check() && auth()->user()->role === 'admin')

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" 
       href="{{ route('reports.index') }}">
        <i class="bi bi-graph-up"></i>
        <span>รายงาน & วิเคราะห์</span>
    </a>
</li>

@endif


<!-- ====================================== -->
<!-- ⚙️ เมนูตั้งค่า & ผู้ใช้ (Admin Only) -->
<!-- ====================================== -->
@if(auth()->check() && auth()->user()->role === 'admin')

{{-- หัวข้อกลุ่ม (Optional) --}}
<li class="nav-header text-muted px-3 mt-3" style="font-size: 0.75rem; text-transform: uppercase;">
    การจัดการระบบ
</li>

{{-- เมนูจัดการผู้ใช้ --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" 
       href="{{ route('users.index') }}">
        <i class="bi bi-people"></i>
        <span>จัดการผู้ใช้งาน</span>
    </a>
</li>

{{-- เมนูตั้งค่า --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" 
       href="{{ route('settings.index') }}">
        <i class="bi bi-gear"></i>
        <span>ตั้งค่าระบบ</span>
    </a>
</li>

@endif

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