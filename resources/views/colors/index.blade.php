@extends('layouts.app')
@include('layouts.navbarPD')

@section('content')
<style>
    /* ธีมสีดำสำหรับ Modal */
    .dark-modal .modal-content {
        background-color: #2c2c2c !important;
        color: #ffffff !important;
    }

    .dark-modal .modal-header,
    .dark-modal .modal-footer {
        background-color: #1e1e1e !important;
        color: #ffffff !important;
        border-color: #444;
    }

    .dark-modal .form-control {
        background-color: #444 !important;
        color: white !important;
        border: 1px solid #666;
    }

    .dark-modal .form-control:focus {
        background-color: #555 !important;
        border-color: #888;
        color: white !important;
    }

    .dark-modal label {
        color: #fff !important;
    }

    .dark-modal .btn-secondary {
        border: 1px solid #666;
        background-color: #444;
        color: #ccc;
    }
    
    .dark-modal .btn-secondary:hover {
        background-color: #555;
        color: white;
    }

    .dark-modal .btn-close {
        filter: invert(1);
    }

    .dark-modal .invalid-feedback {
        color: #ff6b6b !important;
    }
</style>

<div class="container">
    <h2>รายการสี</h2>

    {{-- Alert แจ้งเตือน Success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Alert แจ้งเตือน Error ทั่วไป --}}
    @if($errors->any() && !old('action'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addColorModal">
        <i class="fas fa-plus"></i> เพิ่มสีใหม่
    </button>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="30%">ชื่อสี</th>
                        <th width="20%">ตัวอย่างสี</th>
                        <th width="20%" class="text-center">จำนวนสินค้า</th>
                        <th width="30%" class="text-center">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($colors as $color)
                        @php
                            $count = 0;
                            if($color->relationLoaded('productColorSizes')) {
                                $count = $color->productColorSizes->count();
                            } elseif(method_exists($color, 'productColorSizes')) {
                                $count = $color->productColorSizes()->count();
                            }
                        @endphp
                        <tr>
                            <td>{{ $color->name }}</td>
                            <td>
                                <div style="width: 30px; height: 30px; background-color: {{ $color->hex_code }}; border: 1px solid #ccc; border-radius: 4px;"></div>
                            </td>
                            <td class="text-center">
                                @if($count > 0)
                                    <span class="badge bg-info text-dark">{{ $count }} รายการ</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{-- ปุ่มแก้ไข --}}
                                <button class="btn btn-warning btn-sm me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal{{ $color->id }}"
                                    {{ $count > 0 ? 'disabled' : '' }}
                                    title="{{ $count > 0 ? 'แก้ไขไม่ได้เพราะมีสินค้าใช้อยู่' : 'แก้ไข' }}">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </button>

                                <form action="{{ route('colors.destroy', $color->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('ยืนยันการลบสีนี้?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" 
                                        {{ $count > 0 ? 'disabled' : '' }}
                                        title="{{ $count > 0 ? 'ลบไม่ได้เพราะมีสินค้าใช้อยู่' : 'ลบ' }}">
                                        <i class="fas fa-trash"></i> ลบ
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- 1. Modal เพิ่มสี (Add) --}}
<div class="modal fade dark-modal" id="addColorModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('colors.store') }}">
      @csrf
      {{-- Hidden Field: บอกว่าเป็นฟอร์มเพิ่ม --}}
      <input type="hidden" name="action" value="create_color">

      <div class="modal-header">
        <h5 class="modal-title">เพิ่มสีใหม่</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>ชื่อสี <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="เช่น แดง, ดำ, น้ำเงิน">
          @if($errors->has('name') && old('action') == 'create_color')
              <div class="invalid-feedback">{{ $errors->first('name') }}</div>
          @endif
        </div>
        <div class="mb-3">
          <label>รหัสสี (เลือกจากแถบ) <span class="text-danger">*</span></label>
          <input type="color" name="hex_code" class="form-control form-control-color" value="{{ old('hex_code', '#000000') }}" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> บันทึก
        </button>
      </div>
    </form>
  </div>
</div>

{{-- 2. Modal แก้ไขสี (Edit) - แยกออกมานอกตาราง --}}
@foreach($colors as $color)
<div class="modal fade dark-modal" id="editModal{{ $color->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('colors.update', $color->id) }}">
            @csrf
            @method('PUT')
            {{-- Hidden Fields: บอกว่าเป็นฟอร์มแก้ไข และแก้ ID ไหน --}}
            <input type="hidden" name="action" value="edit_color">
            <input type="hidden" name="edit_id" value="{{ $color->id }}">

            <div class="modal-header">
                <h5 class="modal-title">แก้ไขสี</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-start">
                <div class="mb-3">
                    <label>ชื่อสี <span class="text-danger">*</span></label>
                    <input type="text" name="name" 
                           class="form-control {{ $errors->has('name') && old('edit_id') == $color->id ? 'is-invalid' : '' }}" 
                           value="{{ old('edit_id') == $color->id ? old('name') : $color->name }}" required>
                    
                    @if($errors->has('name') && old('edit_id') == $color->id)
                        <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                    @endif
                </div>
                <div class="mb-3">
                    <label>รหัสสี <span class="text-danger">*</span></label>
                    <input type="color" name="hex_code" class="form-control form-control-color" value="{{ $color->hex_code }}" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i> อัปเดต
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // กรณี Error จากการ "เพิ่มใหม่"
        @if($errors->any() && old('action') == 'create_color')
            var addModal = new bootstrap.Modal(document.getElementById('addColorModal'));
            addModal.show();
        @endif

        // กรณี Error จากการ "แก้ไข"
        @if($errors->any() && old('action') == 'edit_color')
            var editId = "{{ old('edit_id') }}";
            var editModalEl = document.getElementById('editModal' + editId);
            if(editModalEl) {
                var editModal = new bootstrap.Modal(editModalEl);
                editModal.show();
            }
        @endif
    });
</script>
@endpush