{{-- layouts/sidebar.blade.php --}}
{{-- ✅ เพิ่ม style="max-width: 240px;" เพื่อจำกัดความกว้างให้เล็กลง --}}
<div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="sidebarMenu" style="max-width: 240px;">
    <div class="position-sticky pt-2"> {{-- ลด padding ด้านบนลง --}}

        {{-- ✅ ส่วนแสดงข้อมูลผู้ใช้งาน (ปรับให้เล็กลง) --}}
        <div class="text-center mb-3 pb-2 border-bottom">
            {{-- ลดขนาด Avatar จาก 50px เป็น 40px --}}
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" 
                 style="width: 40px; height: 40px; font-size: 18px;">
                {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
            </div>
            <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">{{ Auth::user()->username }}</h6>
            <span class="badge 
                @if(Auth::user()->role == 'admin') bg-danger 
                @elseif(Auth::user()->role == 'sales') bg-success 
                @else bg-warning text-dark @endif" 
                style="font-size: 0.7rem;">
                {{ ucfirst(Auth::user()->role) }}
            </span>
            <div class="mt-1">
                <a href="{{ route('users.edit', Auth::user()->id) }}" class="text-secondary" style="font-size: 0.75rem; text-decoration: none;">
                    <i class="fas fa-user-edit"></i> แก้ไขส่วนตัว
                </a>
            </div>
        </div>

        <ul class="nav flex-column" style="font-size: 0.9rem;"> {{-- ลดขนาดตัวอักษรเมนู --}}

            <li class="nav-item">
                <a class="nav-link py-2 {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                    href="{{ route('reports.index') }}">
                    <i class="fas fa-chart-line bi bi-graph-up me-2"></i>
                    <span>รายงาน</span>
                </a>
            </li>

            <li class="nav-item">
                {{-- ✅ ตรวจสอบว่าเป็น "คนขาย" (sales) หรือไม่ --}}
                @if(Auth::user()->role === 'sales')
                <a class="nav-link py-2 {{ Request::is('sales/products*') ? 'active' : '' }}" href="{{ url('/sales/products') }}">
                    <i class="fas fa-tags me-2"></i> ดูราคาสินค้า
                </a>
                @else
                <a class="nav-link py-2 {{ Route::is('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">
                    <i class="fas fa-box me-2"></i> จัดการสินค้า
                </a>
                @endif
            </li>
            
            <li class="nav-item">
                <a class="nav-link py-2 {{ Route::is('orders.index') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                    <i class="fas fa-shopping-cart me-2"></i> คำสั่งซื้อ
                </a>
            </li>

            @if(auth()->check() && auth()->user()->role === 'admin')

            {{-- หัวข้อกลุ่ม --}}
            <li class="nav-header text-muted px-3 mt-3 mb-1" style="font-size: 0.7rem; text-transform: uppercase;">
                จัดการระบบ
            </li>

            <li class="nav-item">
                <a class="nav-link py-2 {{ request()->routeIs('users.*') ? 'active' : '' }}"
                    href="{{ route('users.index') }}">
                    <i class="bi bi-people me-2"></i>
                    <span>ผู้ใช้งาน</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link py-2 {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                    href="{{ route('settings.index') }}">
                    <i class="bi bi-gear me-2"></i>
                    <span>ตั้งค่า</span>
                </a>
            </li>

            @endif

            <hr class="my-2">

            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link py-2 w-100 text-start border-0 bg-transparent text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        <span>ออกจากระบบ</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>