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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>รายการหมวดหมู่</h2>
        {{-- ปุ่มเปิด Modal เพิ่ม --}}
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus"></i> เพิ่มหมวดหมู่ใหม่
        </button>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- แจ้งเตือน Error ทั่วไป --}}
    @if($errors->any() && !old('action'))
        <div class="alert alert-danger alert-dismissible fade show">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50%">ชื่อหมวดหมู่</th>
                        <th width="20%" class="text-center">จำนวนสินค้า</th>
                        <th width="30%" class="text-center">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->category_name }}</td>
                        <td class="text-center">
                            @if($category->products->count() > 0)
                                <span class="badge bg-info text-dark">{{ $category->products->count() }} รายการ</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $isDisabled = $category->products->count() > 0;
                            @endphp

                            {{-- ปุ่มแก้ไข --}}
                            <button class="btn btn-warning btn-sm me-1" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editCategoryModal{{ $category->id }}"
                                {{ $isDisabled ? 'disabled' : '' }} 
                                title="{{ $isDisabled ? 'แก้ไขไม่ได้เนื่องจากมีสินค้าใช้งานอยู่' : 'แก้ไข' }}">
                                <i class="fas fa-edit"></i> แก้ไข
                            </button>

                            {{-- ปุ่มลบ --}}
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('ยืนยันการลบหมวดหมู่นี้?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" 
                                    {{ $isDisabled ? 'disabled' : '' }}
                                    title="{{ $isDisabled ? 'ลบไม่ได้เนื่องจากมีสินค้าใช้งานอยู่' : 'ลบ' }}">
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

{{-- =========================================================
     MODAL SECTION (แยกออกมานอก Loop เพื่อลดการกระพริบ)
========================================================= --}}

{{-- 1. Modal เพิ่มหมวดหมู่ --}}
<div class="modal fade dark-modal" id="addCategoryModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        {{-- ใช้ action เพื่อบอกว่าเป็นฟอร์มเพิ่ม --}}
        <input type="hidden" name="action" value="create_category">
        
        <div class="modal-header">
          <h5 class="modal-title">เพิ่มหมวดหมู่ใหม่</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>ชื่อหมวดหมู่ <span class="text-danger">*</span></label>
            <input type="text" name="category_name" 
                   class="form-control @if($errors->has('category_name') && old('action') == 'create_category') is-invalid @endif" 
                   value="{{ old('action') == 'create_category' ? old('category_name') : '' }}" required>
            
            @if($errors->has('category_name') && old('action') == 'create_category')
              <div class="invalid-feedback">{{ $errors->first('category_name') }}</div>
            @endif
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
</div>

{{-- 2. Modal แก้ไขหมวดหมู่ (วนลูปสร้าง Modal) --}}
@foreach($categories as $category)
<div class="modal fade dark-modal" id="editCategoryModal{{ $category->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                {{-- ใช้ action และ edit_id เพื่อระบุ Modal ที่ต้องเปิดกลับ --}}
                <input type="hidden" name="action" value="edit_category">
                <input type="hidden" name="edit_id" value="{{ $category->id }}">

                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขหมวดหมู่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label>ชื่อหมวดหมู่ <span class="text-danger">*</span></label>
                        <input type="text" name="category_name" 
                               class="form-control @if($errors->has('category_name') && old('edit_id') == $category->id) is-invalid @endif" 
                               value="{{ old('edit_id') == $category->id ? old('category_name') : $category->category_name }}" required>
                        
                        @if($errors->has('category_name') && old('edit_id') == $category->id)
                            <div class="invalid-feedback">{{ $errors->first('category_name') }}</div>
                        @endif
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
</div>
@endforeach

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // กรณี Error จากการ "เพิ่มใหม่"
        @if($errors->any() && old('action') == 'create_category')
            var addModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
            addModal.show();
        @endif

        // กรณี Error จากการ "แก้ไข"
        @if($errors->any() && old('action') == 'edit_category')
            var editId = "{{ old('edit_id') }}";
            var editModalEl = document.getElementById('editCategoryModal' + editId);
            if(editModalEl) {
                var editModal = new bootstrap.Modal(editModalEl);
                editModal.show();
            }
        @endif
    });
</script>
@endpush