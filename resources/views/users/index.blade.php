@extends('layouts.app')

@section('title', 'จัดการผู้ใช้งาน')

@section('content')
<style>
    /* Custom Styles for Users Page - Black & Gold Theme */
    .users-header {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(30, 30, 30, 0.9));
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 215, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .users-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.05) 0%, transparent 70%);
        animation: headerGlow 8s infinite ease-in-out;
    }

    @keyframes headerGlow {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-10%, -10%) scale(1.1); }
    }

    .users-header h2 {
        color: var(--gold);
        text-shadow: 0 0 15px rgba(255, 215, 0, 0.5), 0 0 30px rgba(255, 215, 0, 0.3);
        font-weight: 600;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .btn-add-user {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        border: none;
        color: #000;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }

    .btn-add-user:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.5);
        color: #000;
    }

    /* Alert Styling - Brighter */
    .alert {
        border: 1px solid;
        border-radius: 10px;
        padding: 1rem 1.5rem;
        background: rgba(30, 30, 30, 0.8);
        backdrop-filter: blur(10px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        animation: slideDown 0.5s ease;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.2), rgba(30, 30, 30, 0.9));
        color: #7de5a4 !important;
        border-color: rgba(25, 135, 84, 0.5);
        box-shadow: 0 5px 15px rgba(25, 135, 84, 0.2);
    }

    .alert-danger {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.2), rgba(30, 30, 30, 0.9));
        color: #ff6b7d !important;
        border-color: rgba(220, 53, 69, 0.5);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.2);
    }

    /* Search Card - Brighter */
    .search-card {
        background: linear-gradient(135deg, rgba(30, 30, 30, 0.95), rgba(18, 18, 18, 0.95));
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 215, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .search-card .form-control {
        background: rgba(10, 10, 10, 0.8);
        border: 1px solid rgba(255, 215, 0, 0.3);
        color: var(--text-primary);
        padding: 0.75rem 1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .search-card .form-control:focus {
        background: rgba(10, 10, 10, 0.9);
        border-color: var(--gold);
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.3);
        color: var(--text-primary);
    }

    .search-card .form-control::placeholder {
        color: rgba(204, 204, 204, 0.6);
    }

    .btn-search {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(255, 215, 0, 0.1));
        border: 1px solid var(--gold);
        color: var(--gold);
        font-weight: 600;
        padding: 0.75rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-search:hover {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        color: #000;
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
        transform: translateY(-2px);
    }

    /* Table Card - Brighter & Better */
    .table-card {
        background: linear-gradient(135deg, rgba(30, 30, 30, 0.95), rgba(18, 18, 18, 0.95));
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 25px rgba(255, 215, 0, 0.15);
        overflow: hidden;
    }

    .table-card .card-header {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.15), rgba(30, 30, 30, 0.8));
        border-bottom: 2px solid rgba(255, 215, 0, 0.4);
        padding: 1.5rem;
    }

    .table-card .card-header h5 {
        color: var(--gold);
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.4);
        font-weight: 600;
        margin: 0;
    }

    /* Table Styling - Much Brighter */
    .table-users {
        margin: 0;
        color: var(--text-primary);
    }

    .table-users thead th {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(30, 30, 30, 0.8));
        color: var(--gold);
        border: 1px solid rgba(255, 215, 0, 0.3);
        padding: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        text-shadow: 0 0 8px rgba(255, 215, 0, 0.3);
    }

    .table-users tbody td {
        background: rgba(18, 18, 18, 0.6);
        border: 1px solid rgba(255, 215, 0, 0.15);
        padding: 1rem;
        vertical-align: middle;
        color: #e8e8e8;
    }

    .table-users tbody tr {
        transition: all 0.3s ease;
    }

    .table-users tbody tr:hover {
        background: linear-gradient(90deg, rgba(255, 215, 0, 0.1), transparent);
        transform: translateX(5px);
        box-shadow: 0 2px 10px rgba(255, 215, 0, 0.2);
    }

    .table-users tbody tr:hover td {
        background: rgba(30, 30, 30, 0.8);
        border-color: rgba(255, 215, 0, 0.3);
    }

    /* Badge Styling - Brighter */
    .badge {
        padding: 0.5rem 0.8rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    .badge.bg-primary {
        background: linear-gradient(135deg, #4c9aff, #2684ff) !important;
        border: 1px solid rgba(76, 154, 255, 0.5);
        color: #fff !important;
    }

    .badge.bg-danger {
        background: linear-gradient(135deg, #ff5c8d, #ff3366) !important;
        border: 1px solid rgba(255, 51, 102, 0.5);
        color: #fff !important;
    }

    .badge.bg-warning {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark)) !important;
        border: 1px solid rgba(255, 215, 0, 0.5);
        color: #000 !important;
        text-shadow: none;
    }

    .badge.bg-info {
        background: linear-gradient(135deg, #5fedff, #36d8ff) !important;
        border: 1px solid rgba(95, 237, 255, 0.5);
        color: #000 !important;
    }

    /* Button Styling - Brighter */
    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
    }

    .btn-warning {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark)) !important;
        color: #000 !important;
    }

    .btn-warning:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
        color: #000 !important;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ff5c8d, #ff3366) !important;
        color: #fff !important;
    }

    .btn-danger:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(255, 51, 102, 0.5);
        color: #fff !important;
    }

    /* Empty State */
    .alert-info {
        background: linear-gradient(135deg, rgba(95, 237, 255, 0.15), rgba(30, 30, 30, 0.9));
        color: #7de9f7 !important;
        border: 1px solid rgba(95, 237, 255, 0.4);
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        font-size: 1.1rem;
        box-shadow: 0 5px 20px rgba(95, 237, 255, 0.2);
    }

    /* Pagination */
    .pagination {
        margin-top: 1.5rem;
        margin-bottom: 0;
    }

    .pagination .page-link {
        background: rgba(30, 30, 30, 0.8);
        border: 1px solid rgba(255, 215, 0, 0.3);
        color: var(--text-primary);
        margin: 0 0.25rem;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2), rgba(30, 30, 30, 0.9));
        border-color: var(--gold);
        color: var(--gold);
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(255, 215, 0, 0.3);
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        border-color: var(--gold);
        color: #000;
        box-shadow: 0 3px 10px rgba(255, 215, 0, 0.4);
    }

    /* Animations */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .users-header {
            padding: 1.5rem;
        }

        .btn-add-user {
            width: 100%;
            margin-top: 1rem;
        }

        .table-responsive {
            border-radius: 10px;
            overflow-x: auto;
        }
    }
</style>

<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="users-header" data-aos="fade-down" data-aos-duration="800">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h2 class="mb-0">
                @if(auth()->user()->role === 'admin')
                    <i class="fas fa-users-cog me-2"></i>จัดการผู้ใช้งาน
                @else
                    <i class="fas fa-user-circle me-2"></i>ข้อมูลส่วนตัว
                @endif
            </h2>
            
            {{-- ปุ่มเพิ่มผู้ใช้ (เฉพาะ Admin) --}}
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('users.create') }}" class="btn btn-add-user">
                    <i class="fas fa-user-plus me-2"></i>เพิ่มผู้ใช้ใหม่
                </a>
            @endif
        </div>
    </div>

    {{-- แจ้งเตือน --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" data-aos="fade-down" data-aos-duration="600">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" data-aos="fade-down" data-aos-duration="600">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ค้นหา (เฉพาะ Admin) --}}
    @if(auth()->user()->role === 'admin')
        <div class="search-card" data-aos="fade-up" data-aos-duration="800">
            <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                <div class="col-md-9 col-lg-10">
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-warning text-warning">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="🔍 ค้นหาชื่อผู้ใช้ หรืออีเมล..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 col-lg-2">
                    <button type="submit" class="btn btn-search w-100">
                        <i class="fas fa-search me-2"></i>ค้นหา
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- ตารางรายชื่อผู้ใช้ --}}
    <div class="table-card" data-aos="fade-up" data-aos-duration="1000">
        <div class="card-header">
            <h5 class="mb-0">
                @if(auth()->user()->role === 'admin')
                    <i class="fas fa-list-ul me-2"></i>รายชื่อผู้ใช้ทั้งหมด <span class="badge bg-warning ms-2">{{ $users->total() }} คน</span>
                @else
                    <i class="fas fa-id-card me-2"></i>ข้อมูลของคุณ
                @endif
            </h5>
        </div>
        <div class="card-body p-0">
            @if($users->isEmpty())
                <div class="alert alert-info m-4">
                    <i class="fas fa-info-circle me-2"></i>ไม่พบข้อมูลผู้ใช้
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-users mb-0">
                        <thead>
                            <tr>
                                <th width="80" class="text-center">
                                    <i class="fas fa-hashtag me-1"></i>ID
                                </th>
                                <th>
                                    <i class="fas fa-user me-1"></i>ชื่อผู้ใช้
                                </th>
                                <th>
                                    <i class="fas fa-envelope me-1"></i>อีเมล
                                </th>
                                <th width="150" class="text-center">
                                    <i class="fas fa-shield-alt me-1"></i>บทบาท
                                </th>
                                <th width="200" class="text-center">
                                    <i class="fas fa-cogs me-1"></i>จัดการ
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td class="text-center">
                                    <strong class="text-warning">{{ $user->id }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-warning text-dark me-2 d-flex align-items-center justify-content-center" 
                                             style="width: 35px; height: 35px; border-radius: 50%; font-weight: bold;">
                                            {{ strtoupper(substr($user->username, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $user->username }}</strong>
                                            @if($user->id === auth()->id())
                                                <span class="badge bg-primary ms-2">
                                                    <i class="fas fa-star me-1"></i>คุณ
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-at me-1 text-warning"></i>{{ $user->email }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $roleBadge = [
                                            'admin' => 'danger',
                                            'stock' => 'warning',
                                            'sales' => 'info',
                                        ][$user->role] ?? 'secondary';
                                        
                                        $roleName = [
                                            'admin' => 'ผู้ดูแลระบบ',
                                            'stock' => 'คลังสินค้า',
                                            'sales' => 'ฝ่ายขาย',
                                        ][$user->role] ?? $user->role;

                                        $roleIcon = [
                                            'admin' => 'fa-crown',
                                            'stock' => 'fa-boxes',
                                            'sales' => 'fa-chart-line',
                                        ][$user->role] ?? 'fa-user';
                                    @endphp
                                    <span class="badge bg-{{ $roleBadge }}">
                                        <i class="fas {{ $roleIcon }} me-1"></i>{{ $roleName }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{-- ✅ ปุ่มแก้ไข: Admin แก้ไขใครก็ได้, Sales/Stock แก้ไขแค่ตัวเอง --}}
                                    @if(auth()->user()->role === 'admin' || auth()->id() === $user->id)
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-action btn-sm">
                                            <i class="fas fa-edit me-1"></i>แก้ไข
                                        </a>
                                    @endif

                                    {{-- ✅ ปุ่มลบ: เฉพาะ Admin และไม่ใช่ตัวเอง --}}
                                    @if(auth()->user()->role === 'admin' && $user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('⚠️ คุณแน่ใจว่าต้องการลบผู้ใช้ {{ $user->username }} หรือไม่?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-action btn-sm">
                                                <i class="fas fa-trash-alt me-1"></i>ลบ
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if(auth()->user()->role === 'admin')
                    <div class="p-3">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- AOS Animation Library --}}
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });

    // Add hover effect to table rows
    document.querySelectorAll('.table-users tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Alert auto dismiss after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endpush
@endsection