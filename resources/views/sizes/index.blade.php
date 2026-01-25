@extends('layouts.app')
@include('layouts.navbarPD')

@section('content')
<style>
    .dark-modal .modal-content {
        background-color: #2c2c2c !important;
        color: #ffffff !important;
    }
    .dark-modal .modal-header,
    .dark-modal .modal-footer {
        background-color: #1e1e1e !important;
        color: #ffffff !important;
    }
    .dark-modal .form-control {
        background-color: #444 !important;
        color: white !important;
        border: 1px solid #666;
    }
    .dark-modal label {
        color: #fff !important;
    }
    .dark-modal .btn {
        border: 1px solid #ccc;
        color: white;
    }
    .dark-modal .btn-close {
        filter: invert(1);
    }
</style>

<div class="container">
    <h2>รายการขนาด (Size)</h2>

    {{-- Alert แจ้งเตือน --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- แสดง Error ทั่วไป (ที่ไม่ใช่จาก Modal เพิ่มขนาด) --}}
    @if($errors->any() && old('action') != 'create_size')
        <div class="alert alert-danger alert-dismissible fade show">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addSizeModal">
        <i class="fas fa-plus"></i> เพิ่มขนาดใหม่
    </button>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="40%">ชื่อขนาด</th>
                        <th width="30%" class="text-center">จำนวนสินค้า</th>
                        <th width="30%" class="text-center">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sizes as $size)
                        @php
                            // ✅ แก้ไข: ใช้ attribute ที่ withCount สร้างให้
                            $count = $size->product_color_sizes_count ?? 0;
                        @endphp
                        <tr>
                            <td>{{ $size->size_name }}</td>
                            <td class="text-center">
                                @if($count > 0)
                                    <span class="badge bg-info text-dark">{{ $count }} รายการ</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{-- ถ้ามีสินค้าใช้ขนาดนี้ ห้ามแก้ไข/ลบ --}}
                                <button class="btn btn-warning btn-sm me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editSizeModal{{ $size->id }}"
                                    {{ $count > 0 ? 'disabled' : '' }}
                                    title="{{ $count > 0 ? 'แก้ไขไม่ได้เพราะมีสินค้าใช้อยู่' : 'แก้ไข' }}">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </button>

                                <form action="{{ route('sizes.destroy', $size) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('ยืนยันการลบขนาดนี้?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" 
                                        {{ $count > 0 ? 'disabled' : '' }}
                                        title="{{ $count > 0 ? 'ลบไม่ได้เพราะมีสินค้าใช้อยู่' : 'ลบ' }}">
                                        <i class="fas fa-trash"></i> ลบ
                                    </button>
                                </form>

                                {{-- Modal แก้ไข (Edit) --}}
                                <div class="modal fade dark-modal" id="editSizeModal{{ $size->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form class="modal-content" method="POST" action="{{ route('sizes.update', $size) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">แก้ไขขนาด</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label>ชื่อขนาด</label>
                                                    <input type="text" name="size_name" class="form-control" value="{{ $size->size_name }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                <button type="submit" class="btn btn-warning">อัปเดต</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal เพิ่มขนาดใหม่ --}}
<div class="modal fade dark-modal" id="addSizeModal" tabindex="-1" aria-labelledby="addSizeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('sizes.store') }}" id="addSizeForm">
            @csrf
            {{-- ใส่ hidden field เพื่อให้ JS รู้ว่า Error มาจากฟอร์มนี้ --}}
            <input type="hidden" name="action" value="create_size">

            <div class="modal-header">
                <h5 class="modal-title" id="addSizeModalLabel">เพิ่มขนาดใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="sizeName" class="form-label">ชื่อขนาด <span class="text-danger">*</span></label>
                    <input type="text" id="sizeName" name="size_name" class="form-control @error('size_name') is-invalid @enderror" value="{{ old('size_name') }}" required placeholder="เช่น S, M, L, XL">
                    @error('size_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> บันทึก
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // สคริปต์สำหรับเปิด Modal อัตโนมัติ เมื่อมีการ submit แล้วเกิด Error
    document.addEventListener('DOMContentLoaded', function() {
        // เปิด Modal ถ้ามี error จากการ submit
        @if($errors->any() && old('action') == 'create_size')
            var myModal = new bootstrap.Modal(document.getElementById('addSizeModal'));
            myModal.show();
        @endif

        // Debug: ตรวจสอบว่า form submit ทำงานหรือไม่
        const form = document.getElementById('addSizeForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if(form) {
            form.addEventListener('submit', function(e) {
                console.log('Form is being submitted...');
                console.log('Action:', form.action);
                console.log('Method:', form.method);
                
                // ป้องกันการ submit ซ้ำ
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>กำลังบันทึก...';
            });
        }
    });
</script>
@endpush