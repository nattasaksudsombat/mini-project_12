{{-- resources/views/customers/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">

    {{-- หัวข้อ + ปุ่มเพิ่มลูกค้าใหม่ --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">จัดการลูกค้า (Customer Management)</h1>

        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> เพิ่มลูกค้าใหม่
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- แบบฟอร์มค้นหา --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('customers.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label mb-1">ค้นหาลูกค้า</label>
                    <input type="text"
                           name="q"
                           value="{{ old('q', $q ?? '') }}"
                           class="form-control"
                           placeholder="พิมพ์ชื่อ, เบอร์โทร หรือที่อยู่">
                </div>

                <div class="col-md-4">
                    <label class="form-label mb-1 d-none d-md-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> ค้นหา
                        </button>
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                            ล้างการค้นหา
                        </a>
                    </div>
                </div>

                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    @if(!empty($q))
                        <span class="text-muted">
                            ผลลัพธ์การค้นหา: <strong>{{ e($q) }}</strong>
                        </span>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ตารางรายชื่อลูกค้า --}}
    <div class="card">
        <div class="card-header">
            <strong>รายชื่อลูกค้า</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">#</th>
                        <th>ชื่อ - นามสกุล</th>
                        <th style="width: 160px;">เบอร์โทร</th>
                        <th style="width: 140px;" class="text-center">
                            จำนวนออเดอร์
                        </th>
                        <th style="width: 180px;">วันที่สร้าง</th>
                        <th style="width: 180px;" class="text-end">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($customers as $customer)
                    <tr>
                        {{-- running number แบบมี pagination --}}
                        <td>
                            {{ $customers->firstItem() + $loop->index }}
                        </td>

                        <td>
                            <strong>{{ $customer->name }}</strong>
                            @if(!empty($customer->purchase_channel))
                                <div class="small text-muted">
                                    ช่องทาง: {{ $customer->purchase_channel }}
                                </div>
                            @endif
                        </td>

                        <td>
                            {{ $customer->phone ?: '-' }}
                        </td>

                        <td class="text-center">
                            <span class="badge bg-info">
                                {{ $customer->orders_count ?? 0 }} ออเดอร์
                            </span>
                        </td>

                        <td>
                            {{ optional($customer->created_at)->format('Y-m-d H:i') }}
                        </td>

                        <td class="text-end">
                            {{-- ปุ่มดู/แก้ไข: ตอนนี้ใช้ edit แทน view เพื่อไม่อิง route customers.show --}}
                            <a href="{{ route('customers.edit', $customer->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> ดู/แก้ไข
                            </a>

                            {{-- ปุ่มลบ --}}
                            <form action="{{ route('customers.destroy', $customer->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('ต้องการลบลูกค้ารายนี้หรือไม่?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash-alt"></i> ลบ
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                            <div>ยังไม่มีข้อมูลลูกค้า</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($customers instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        แสดง {{ $customers->firstItem() }} - {{ $customers->lastItem() }}
                        จากทั้งหมด {{ $customers->total() }} รายการ
                    </div>
                    <div>
                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
