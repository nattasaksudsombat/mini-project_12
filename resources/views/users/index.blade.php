@extends('layouts.app')

@section('title', 'จัดการผู้ใช้งาน')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            {{ auth()->user()->role === 'admin' ? '👥 จัดการผู้ใช้งาน' : '👤 ข้อมูลส่วนตัว' }}
        </h2>
        
        {{-- ปุ่มเพิ่มผู้ใช้ (เฉพาะ Admin) --}}
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('users.create') }}" class="btn btn-success">
                ➕ เพิ่มผู้ใช้ใหม่
            </a>
        @endif
    </div>

    {{-- แจ้งเตือน --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            ❌ {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ค้นหา (เฉพาะ Admin) --}}
    @if(auth()->user()->role === 'admin')
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" 
                               placeholder="ค้นหาชื่อผู้ใช้ หรืออีเมล..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">🔍 ค้นหา</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ตารางรายชื่อผู้ใช้ --}}
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                @if(auth()->user()->role === 'admin')
                    📋 รายชื่อผู้ใช้ทั้งหมด ({{ $users->total() }} คน)
                @else
                    📋 ข้อมูลของคุณ
                @endif
            </h5>
        </div>
        <div class="card-body">
            @if($users->isEmpty())
                <div class="alert alert-info text-center">ไม่พบข้อมูลผู้ใช้</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="60">ID</th>
                                <th>ชื่อผู้ใช้</th>
                                <th>อีเมล</th>
                                <th width="120">บทบาท</th>
                                <th width="180" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <strong>{{ $user->username }}</strong>
                                    @if($user->id === auth()->id())
                                        <span class="badge bg-primary">คุณ</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
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
                                    @endphp
                                    <span class="badge bg-{{ $roleBadge }}">{{ $roleName }}</span>
                                </td>
                                <td class="text-center">
                                    {{-- ✅ ปุ่มแก้ไข: Admin แก้ไขใครก็ได้, Sales/Stock แก้ไขแค่ตัวเอง --}}
                                    @if(auth()->user()->role === 'admin' || auth()->id() === $user->id)
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">
                                            ✏️ แก้ไข
                                        </a>
                                    @endif

                                    {{-- ✅ ปุ่มลบ: เฉพาะ Admin และไม่ใช่ตัวเอง --}}
                                    @if(auth()->user()->role === 'admin' && $user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('คุณแน่ใจว่าต้องการลบผู้ใช้นี้?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                🗑️ ลบ
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
                    <div class="mt-3">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection