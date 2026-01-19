@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>จัดการลูกค้า</h1>
            <p class="text-muted mb-0">ระบบจัดการข้อมูลลูกค้าทั้งหมด</p>
        </div>
        <div class="d-flex gap-2">
            {{-- ซ่อนปุ่มสร้างลูกค้าสำหรับยศ Stock --}}
            @if(auth()->user()->role !== 'stock')
            <a href="{{ route('customers.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> เพิ่มลูกค้าใหม่
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ฟอร์มค้นหา --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('customers.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">ค้นหา</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="ชื่อ, เบอร์โทร, อีเมล, ที่อยู่" 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ช่องทางการซื้อ</label>
                        <select name="purchase_channel" class="form-select">
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
                        <label class="form-label">วิธีชำระเงิน</label>
                        <select name="payment_method" class="form-select">
                            <option value="">-- ทั้งหมด --</option>
                            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>โอน/พร้อมเพย์</option>
                            <option value="cash_on_delivery" {{ request('payment_method') == 'cash_on_delivery' ? 'selected' : '' }}>ชำระปลายทาง (COD)</option>
                            <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>บัตรเครดิต/เดบิต</option>
                            <option value="e_wallet" {{ request('payment_method') == 'e_wallet' ? 'selected' : '' }}>วอลเล็ต</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary">
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

    {{-- ตารางแสดงลูกค้า --}}
    <div class="card">
        <div class="card-body">
            @if(request()->hasAny(['search', 'purchase_channel', 'payment_method']))
            <div class="alert alert-info mb-3">
                <i class="fas fa-filter"></i> 
                <strong>กำลังกรองข้อมูล:</strong>
                @if(request('search'))
                    ค้นหา "{{ request('search') }}"
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
                    | ช่องทาง: {{ $channelLabels[request('purchase_channel')] ?? request('purchase_channel') }}
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
                    | การชำระ: {{ $paymentLabels[request('payment_method')] ?? request('payment_method') }}
                @endif
                <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                    <i class="fas fa-times"></i> ล้างตัวกรอง
                </a>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ชื่อลูกค้า</th>
                            <th>เบอร์โทร</th>
                            <th>อีเมล</th>
                            <th>ช่องทางการซื้อ</th>
                            <th>วิธีชำระเงิน</th>
                            <th>จำนวนออเดอร์</th>
                            <th>วันที่เพิ่ม</th>
                            @if(auth()->user()->role !== 'stock')
                            <th width="150">จัดการ</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>
                                <strong>{{ $customer->name }}</strong>
                                @if($customer->notes)
                                <br><small class="text-muted"><i class="fas fa-sticky-note"></i> {{ Str::limit($customer->notes, 30) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($customer->phone)
                                <a href="tel:{{ $customer->phone }}" class="text-decoration-none">
                                    <i class="fas fa-phone text-success"></i> {{ $customer->phone }}
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($customer->email)
                                <a href="mailto:{{ $customer->email }}" class="text-decoration-none">
                                    <i class="fas fa-envelope text-primary"></i> {{ $customer->email }}
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $channelLabels = [
                                        'facebook' => 'Facebook',
                                        'line' => 'Line',
                                        'website' => 'เว็บไซต์',
                                        'shopee' => 'Shopee',
                                        'lazada' => 'Lazada',
                                        'offline' => 'หน้าร้าน',
                                    ];
                                    $channelIcons = [
                                        'facebook' => 'fab fa-facebook',
                                        'line' => 'fab fa-line',
                                        'website' => 'fas fa-globe',
                                        'shopee' => 'fas fa-shopping-bag',
                                        'lazada' => 'fas fa-store',
                                        'offline' => 'fas fa-shop',
                                    ];
                                @endphp
                                @if($customer->purchase_channel)
                                <span class="badge bg-info">
                                    <i class="{{ $channelIcons[$customer->purchase_channel] ?? 'fas fa-tag' }}"></i>
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
                                    {{ $paymentLabels[$customer->payment_method] ?? ucfirst($customer->payment_method) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $customer->orders_count ?? 0 }} ออเดอร์</span>
                            </td>
                            <td>
                                <small>{{ $customer->created_at->format('d/m/Y') }}</small>
                                <br><small class="text-muted">{{ $customer->created_at->diffForHumans() }}</small>
                            </td>
                            @if(auth()->user()->role !== 'stock')
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning" title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" 
                                          onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบลูกค้า {{ $customer->name }}?\n\n⚠️ หากลูกค้ามีออเดอร์อยู่ จะไม่สามารถลบได้')" 
                                          style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="ลบ">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role !== 'stock' ? '9' : '8' }}" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">
                                    @if(request()->hasAny(['search', 'purchase_channel', 'payment_method']))
                                        ไม่พบข้อมูลลูกค้าที่ค้นหา
                                    @else
                                        ยังไม่มีข้อมูลลูกค้า
                                    @endif
                                </p>
                                @if(request()->hasAny(['search', 'purchase_channel', 'payment_method']))
                                <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-redo"></i> แสดงทั้งหมด
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($customers->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    แสดง {{ $customers->firstItem() }} - {{ $customers->lastItem() }} จาก {{ $customers->total() }} รายการ
                </div>
                <div>
                    {{ $customers->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- สถิติสรุป --}}
    @if($customers->total() > 0)
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $customers->total() }}</h4>
                            <small>ลูกค้าทั้งหมด</small>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                $fbCount = \App\Models\Customer::where('purchase_channel', 'facebook')->count();
                            @endphp
                            <h4 class="mb-0">{{ $fbCount }}</h4>
                            <small>จาก Facebook</small>
                        </div>
                        <div>
                            <i class="fab fa-facebook fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                $lineCount = \App\Models\Customer::where('purchase_channel', 'line')->count();
                            @endphp
                            <h4 class="mb-0">{{ $lineCount }}</h4>
                            <small>จาก Line</small>
                        </div>
                        <div>
                            <i class="fab fa-line fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                $webCount = \App\Models\Customer::where('purchase_channel', 'website')->count();
                            @endphp
                            <h4 class="mb-0">{{ $webCount }}</h4>
                            <small>จากเว็บไซต์</small>
                        </div>
                        <div>
                            <i class="fas fa-globe fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection