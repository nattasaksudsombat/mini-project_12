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
    <h2>รายการไซส์</h2>

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
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ปุ่มเปิด Modal เพิ่มไซส์ --}}
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addSizeModal">
        <i class="fas fa-plus"></i> เพิ่มไซส์ใหม่
    </button>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="40%">ชื่อไซส์</th>
                        <th width="30%" class="text-center">จำนวนสินค้า</th>
                        <th width="30%" class="text-center">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sizes as $size)
                        @php
                            // นับจำนวนสินค้าที่ใช้ไซส์นี้
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
                                {{-- ปุ่มแก้ไข --}}
                                <button class="btn btn-warning btn-sm me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal{{ $size->id }}"
                                    {{ $count > 0 ? 'disabled' : '' }}
                                    title="{{ $count > 0 ? 'แก้ไขไม่ได้เพราะมีสินค้าใช้อยู่' : 'แก้ไข' }}">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </button>

                                {{-- ปุ่มลบ --}}
                                <form action="{{ route('sizes.destroy', $size->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('ยืนยันการลบไซส์นี้?');">
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

{{-- 1. Modal เพิ่มไซส์ (Add) --}}
<div class="modal fade dark-modal" id="addSizeModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('sizes.store') }}">
      @csrf
      <input type="hidden" name="action" value="create_size">

      <div class="modal-header">
        <h5 class="modal-title">เพิ่มไซส์ใหม่</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>ชื่อไซส์</label>
          <input type="text" name="size_name" 
                 class="form-control {{ $errors->has('size_name') && old('action') == 'create_size' ? 'is-invalid' : '' }}" 
                 value="{{ old('action') == 'create_size' ? old('size_name') : '' }}" required 
                 placeholder="เช่น S, M, L, XL, 38, 40">
          
          @if($errors->has('size_name') && old('action') == 'create_size')
              <div class="invalid-feedback">{{ $errors->first('size_name') }}</div>
          @endif
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
        <button type="submit" class="btn btn-primary">บันทึก</button>
      </div>
    </form>
  </div>
</div>

{{-- 2. Modal แก้ไขไซส์ (Edit) - แยกออกมานอกตารางเพื่อความสะอาด --}}
@foreach($sizes as $size)
<div class="modal fade dark-modal" id="editModal{{ $size->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('sizes.update', $size->id) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="edit_size">
            <input type="hidden" name="edit_id" value="{{ $size->id }}">

            <div class="modal-header">
                <h5 class="modal-title">แก้ไขไซส์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-start">
                <div class="mb-3">
                    <label>ชื่อไซส์</label>
                    <input type="text" name="size_name" 
                           class="form-control {{ $errors->has('size_name') && old('edit_id') == $size->id ? 'is-invalid' : '' }}" 
                           value="{{ old('edit_id') == $size->id ? old('size_name') : $size->size_name }}" required>
                    
                    @if($errors->has('size_name') && old('edit_id') == $size->id)
                        <div class="invalid-feedback">{{ $errors->first('size_name') }}</div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-warning">อัปเดต</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // กรณี Error จากการเพิ่มใหม่
        @if($errors->any() && old('action') == 'create_size')
            var addModal = new bootstrap.Modal(document.getElementById('addSizeModal'));
            addModal.show();
        @endif

        // กรณี Error จากการแก้ไข
        @if($errors->any() && old('action') == 'edit_size')
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