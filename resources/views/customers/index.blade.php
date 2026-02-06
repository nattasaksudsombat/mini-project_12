@extends('layouts.app')

@section('content')
<style>
    /* Modern Gradient Backgrounds */
    .gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .gradient-success {
        background: linear-gradient(135deg, #5cb85c 0%, #27ae60 100%);
    }
    .gradient-info {
        background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
    }
    .gradient-warning {
        background: linear-gradient(135deg, #f8b500 0%, #fceabb 100%);
    }
    
    /* Enhanced Cards */
    .stat-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    .stat-card .card-body {
        padding: 1.5rem;
    }
    .stat-card h4 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .stat-card small {
        font-size: 0.9rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    /* Modern Table */
    .modern-table {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    .modern-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .modern-table thead th {
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border: none;
        padding: 1rem;
    }
    .modern-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #f0f0f0;
    }
    .modern-table tbody tr:hover {
        background-color: #f8f9ff;
        transform: scale(1.01);
        box-shadow: 0 3px 10px rgba(102, 126, 234, 0.1);
    }
    .modern-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    .form-label {
    color: #7fe678;}
    
    /* Search Card */
    .search-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        background: linear-gradient(135deg, #8183e4 0%, #b974cd 100%);
    }
    .search-card .card-body {
        padding: 2rem;
    }
    
    /* Modern Buttons */
    .btn-modern {
        border-radius: 50px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        border: none;
    }
    .btn-modern-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .btn-modern-primary:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    .btn-modern-success {
        background: linear-gradient(135deg, #5cb85c 0%, #27ae60 100%);
        color: white;
    }
    .btn-modern-success:hover {
        background: linear-gradient(135deg, #27ae60 0%, #5cb85c 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(92, 184, 92, 0.4);
    }
    
    /* Badges */
    .badge-modern {
        border-radius: 20px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        font-size: 0.8rem;
    }
    
    /* Channel Badges */
    .channel-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .channel-facebook { background: linear-gradient(135deg, #4267B2 0%, #3b5998 100%); color: white; }
    .channel-line { background: linear-gradient(135deg, #00B900 0%, #00c300 100%); color: white; }
    .channel-website { background: linear-gradient(135deg, #6c757d 0%, #545b62 100%); color: white; }
    .channel-shopee { background: linear-gradient(135deg, #EE4D2D 0%, #f05537 100%); color: white; }
    .channel-lazada { background: linear-gradient(135deg, #0F156D 0%, #1a1f7a 100%); color: white; }
    .channel-offline { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; }
    
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .page-header p {
        opacity: 0.9;
        font-size: 1.1rem;
    }
    
    /* Alert Styling */
    .alert-modern {
        border-radius: 15px;
        border: none;
        padding: 1.2rem 1.5rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    
    /* Empty State */
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }
    .empty-state i {
        font-size: 4rem;
        color: #cbd5e0;
        margin-bottom: 1.5rem;
    }
    .empty-state p {
        color: #718096;
        font-size: 1.1rem;
    }
    
    /* Form Controls */
    .form-control-modern, .form-select-modern {
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    .form-control-modern:focus, .form-select-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    /* Action Buttons */
    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    .action-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    }
</style>

<div class="container-fluid px-4 py-4">
    {{-- Page Header --}}
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="fas fa-users me-3"></i>จัดการลูกค้า</h1>
                <p class="mb-0">ระบบจัดการข้อมูลลูกค้าทั้งหมด</p>
            </div>
            <div class="d-flex gap-2">
                @if(auth()->user()->role !== 'stock')
                <a href="{{ route('customers.create') }}" class="btn btn-modern btn-modern-success">
                    <i class="fas fa-plus me-2"></i>เพิ่มลูกค้าใหม่
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-modern alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-modern alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Search Form --}}
    <div class="card search-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('customers.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-search me-2"></i>ค้นหา
                        </label>
                        <input type="text" name="search" class="form-control form-control-modern" 
                               placeholder="ชื่อ, เบอร์โทร, อีเมล, ที่อยู่" 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-store me-2"></i>ช่องทางการซื้อ
                        </label>
                        <select name="purchase_channel" class="form-select form-select-modern">
                            <option value="">-- ทั้งหมด --</option>
                            <option value="facebook" {{ request('purchase_channel') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                            <option value="line" {{ request('purchase_channel') == 'line' ? 'selected' : '' }}>Line</option>
                            <option value="website" {{ request('purchase_channel') == 'website' ? 'selected' : '' }}>เว็บไซต์</option>
                            <option value="shopee" {{ request('purchase_channel') == 'shopee' ? 'selected' : '' }}>Shopee</option>
                            <option value="lazada" {{ request('purchase_channel') == 'lazada' ? 'selected' : '' }}>Lazada</option>
                            <option value="offline" {{ request('purchase_channel') == 'offline' ? 'selected' : '' }}>หน้าร้าน</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-credit-card me-2"></i>วิธีชำระเงิน
                        </label>
                        <select name="payment_method" class="form-select form-select-modern">
                            <option value="">-- ทั้งหมด --</option>
                            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>โอน/พร้อมเพย์</option>
                            <option value="cash_on_delivery" {{ request('payment_method') == 'cash_on_delivery' ? 'selected' : '' }}>ชำระปลายทาง (COD)</option>
                            <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>บัตรเครดิต/เดบิต</option>
                            <option value="e_wallet" {{ request('payment_method') == 'e_wallet' ? 'selected' : '' }}>วอลเล็ต</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-modern btn-modern-primary">
                                <i class="fas fa-search"></i> ค้นหา
                            </button>
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary" title="ล้างค้นหา">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Filter Info --}}
    @if(request()->hasAny(['search', 'purchase_channel', 'payment_method']))
    <div class="alert alert-info alert-modern mb-4">
        <i class="fas fa-filter me-2"></i>
        <strong>กำลังกรองข้อมูล:</strong>
        @if(request('search'))
            ค้นหา "<strong>{{ request('search') }}</strong>"
        @endif
        @if(request('purchase_channel'))
            @php
                $channelLabels = [
                    'facebook' => 'Facebook',
                    'line' => 'Line',
                    'website' => 'เว็บไซต์',
                    'shopee' => 'Shopee',
                    'lazada' => 'Lazada',
                    'offline' => 'หน้าร้าน',
                ];
            @endphp
            | ช่องทาง: <strong>{{ $channelLabels[request('purchase_channel')] ?? request('purchase_channel') }}</strong>
        @endif
        @if(request('payment_method'))
            @php
                $paymentLabels = [
                    'bank_transfer' => 'โอน/พร้อมเพย์',
                    'cash_on_delivery' => 'COD',
                    'credit_card' => 'บัตรเครดิต',
                    'e_wallet' => 'วอลเล็ต',
                ];
            @endphp
            | การชำระ: <strong>{{ $paymentLabels[request('payment_method')] ?? request('payment_method') }}</strong>
        @endif
        <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary ms-3">
            <i class="fas fa-times me-1"></i>ล้างตัวกรอง
        </a>
    </div>
    @endif

    {{-- Customer Table --}}
    <div class="card modern-table">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ชื่อลูกค้า</th>
                            <th>ติดต่อ</th>
                            <th>ช่องทางการซื้อ</th>
                            <th>วิธีชำระเงิน</th>
                            <th>จำนวนออเดอร์</th>
                            <th>วันที่เพิ่ม</th>
                            @if(auth()->user()->role !== 'stock')
                            <th class="text-center" width="120">จัดการ</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td><strong class="text-primary">#{{ $customer->id }}</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $customer->name }}</strong>
                                        @if($customer->notes)
                                        <br><small class="text-muted">
                                            <i class="fas fa-sticky-note"></i> {{ Str::limit($customer->notes, 30) }}
                                        </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($customer->phone)
                                <a href="tel:{{ $customer->phone }}" class="text-decoration-none text-success d-block mb-1">
                                    <i class="fas fa-phone"></i> {{ $customer->phone }}
                                </a>
                                @endif
                                @if($customer->email)
                                <a href="mailto:{{ $customer->email }}" class="text-decoration-none text-primary d-block">
                                    <i class="fas fa-envelope"></i> {{ Str::limit($customer->email, 25) }}
                                </a>
                                @endif
                                @if(!$customer->phone && !$customer->email)
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $channelIcons = [
                                        'facebook' => 'fab fa-facebook',
                                        'line' => 'fab fa-line',
                                        'website' => 'fas fa-globe',
                                        'shopee' => 'fas fa-shopping-bag',
                                        'lazada' => 'fas fa-shopping-cart',
                                        'offline' => 'fas fa-store',
                                    ];
                                    $channelLabels = [
                                        'facebook' => 'Facebook',
                                        'line' => 'Line',
                                        'website' => 'เว็บไซต์',
                                        'shopee' => 'Shopee',
                                        'lazada' => 'Lazada',
                                        'offline' => 'หน้าร้าน',
                                    ];
                                @endphp
                                @if($customer->purchase_channel)
                                <span class="channel-badge channel-{{ $customer->purchase_channel }}">
                                    <i class="{{ $channelIcons[$customer->purchase_channel] ?? 'fas fa-question' }}"></i>
                                    {{ $channelLabels[$customer->purchase_channel] ?? ucfirst($customer->purchase_channel) }}
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $paymentLabels = [
                                        'bank_transfer' => 'โอน/พร้อมเพย์',
                                        'cash_on_delivery' => 'COD',
                                        'credit_card' => 'บัตรเครดิต',
                                        'e_wallet' => 'วอลเล็ต',
                                    ];
                                @endphp
                                @if($customer->payment_method)
                                    <span class="badge bg-secondary">
                                        {{ $paymentLabels[$customer->payment_method] ?? ucfirst($customer->payment_method) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-modern bg-primary">
                                    <i class="fas fa-shopping-cart me-1"></i>{{ $customer->orders_count ?? 0 }}
                                </span>
                            </td>
                            <td>
                                <div class="text-nowrap">
                                    <i class="fas fa-calendar me-1"></i>{{ $customer->created_at->format('d/m/Y') }}
                                    <br><small class="text-muted">{{ $customer->created_at->diffForHumans() }}</small>
                                </div>
                            </td>
                            @if(auth()->user()->role !== 'stock')
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('customers.edit', $customer->id) }}" 
                                       class="btn btn-warning btn-sm action-btn" 
                                       title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" 
                                          onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบลูกค้า {{ $customer->name }}?\n\n⚠️ หากลูกค้ามีออเดอร์อยู่ จะไม่สามารถลบได้')" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm action-btn" title="ลบ">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role !== 'stock' ? '8' : '7' }}" class="border-0">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <p class="mb-0">
                                        @if(request()->hasAny(['search', 'purchase_channel', 'payment_method']))
                                            ไม่พบข้อมูลลูกค้าที่ค้นหา
                                        @else
                                            ยังไม่มีข้อมูลลูกค้า
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['search', 'purchase_channel', 'payment_method']))
                                    <a href="{{ route('customers.index') }}" class="btn btn-modern btn-modern-primary mt-3">
                                        <i class="fas fa-redo me-2"></i>แสดงทั้งหมด
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($customers->hasPages())
            <div class="d-flex justify-content-between align-items-center p-4 border-top">
                <div class="text-muted">
                    <i class="fas fa-info-circle me-2"></i>แสดง {{ $customers->firstItem() }} - {{ $customers->lastItem() }} จาก {{ $customers->total() }} รายการ
                </div>
                <div>
                    {{ $customers->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Statistics Cards --}}
    @if($customers->total() > 0)
    <div class="row mt-4 g-4">
        <div class="col-md-3">
            <div class="card stat-card gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4>{{ $customers->total() }}</h4>
                            <small>ลูกค้าทั้งหมด</small>
                        </div>
                        <div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                $fbCount = \App\Models\Customer::where('purchase_channel', 'facebook')->count();
                            @endphp
                            <h4>{{ $fbCount }}</h4>
                            <small>จาก Facebook</small>
                        </div>
                        <div>
                            <i class="fab fa-facebook fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                $lineCount = \App\Models\Customer::where('purchase_channel', 'line')->count();
                            @endphp
                            <h4>{{ $lineCount }}</h4>
                            <small>จาก Line</small>
                        </div>
                        <div>
                            <i class="fab fa-line fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                $webCount = \App\Models\Customer::where('purchase_channel', 'website')->count();
                            @endphp
                            <h4>{{ $webCount }}</h4>
                            <small>จากเว็บไซต์</small>
                        </div>
                        <div>
                            <i class="fas fa-globe fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection