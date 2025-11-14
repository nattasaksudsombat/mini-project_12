{{-- layouts/navbar.blade.php --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-wallet me-2"></i> ระบบจัดการรายรับ-รายจ่าย
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-chart-line me-1"></i> แดชบอร์ด
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('incomes.index') ? 'active' : '' }}" href="{{ route('incomes.index') }}">
                        <i class="fas fa-hand-holding-usd me-1"></i> รายรับ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('expenses.index') ? 'active' : '' }}" href="{{ route('expenses.index') }}">
                        <i class="fas fa-shopping-cart me-1"></i> รายจ่าย
                    </a>
                </li>
               
            </ul>
        </div>
    </div>
</nav>
