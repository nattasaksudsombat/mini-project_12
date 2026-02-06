@extends('layouts.app')
@include('layouts.navbarPD')

@section('content')
<style>
    /* Sizes Page - Black & Gold Theme */
    .sizes-header {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(30, 30, 30, 0.9));
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 215, 0, 0.1);
    }

    .sizes-header h2 {
        color: var(--gold);
        text-shadow: 0 0 15px rgba(255, 215, 0, 0.5);
        font-weight: 600;
        margin: 0;
    }

    /* Alert Styling */
    .alert-success {
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.2), rgba(30, 30, 30, 0.9));
        color: #7de5a4 !important;
        border: 1px solid rgba(25, 135, 84, 0.5);
        border-radius: 12px;
    }

    .alert-danger {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.2), rgba(30, 30, 30, 0.9));
        color: #ff6b7d !important;
        border: 1px solid rgba(220, 53, 69, 0.5);
        border-radius: 12px;
    }

    .alert-danger li {
        color: #ff6b7d !important;
    }

    /* Button */
    .btn-add {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        border: none;
        color: #000 !important;
        font-weight: 600;
        padding: 0.6rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
    }

    .btn-add:hover {
        color: #000 !important;
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.5);
    }

    /* Table Card */
    .table-card {
        background: linear-gradient(135deg, rgba(30, 30, 30, 0.95), rgba(18, 18, 18, 0.95));
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 25px rgba(255, 215, 0, 0.15);
        overflow: hidden;
    }

    /* Table */
    .table-sizes {
        margin: 0;
        color: var(--text-primary);
    }

    .table-sizes thead th {
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

    .table-sizes tbody td {
        background: rgba(18, 18, 18, 0.6);
        border: 1px solid rgba(255, 215, 0, 0.15);
        padding: 1rem;
        vertical-align: middle;
        color: #e8e8e8 !important;
    }

    .table-sizes tbody tr:hover td {
        background: rgba(30, 30, 30, 0.8);
        border-color: rgba(255, 215, 0, 0.3);
    }

    /* Badge */
    .badge {
        padding: 0.5rem 0.8rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .badge.bg-info {
        background: linear-gradient(135deg, #5fedff, #36d8ff) !important;
        color: #000 !important;
        border: 1px solid rgba(95, 237, 255, 0.5);
    }

    .text-muted {
        color: #888 !important;
    }

    /* Buttons in Table */
    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        border: none;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
    }

    .btn-warning {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark)) !important;
        color: #000 !important;
    }

    .btn-warning:hover {
        color: #000 !important;
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ff5c8d, #ff3366) !important;
        color: #fff !important;
    }

    .btn-danger:hover {
        color: #fff !important;
        box-shadow: 0 5px 15px rgba(255, 51, 102, 0.5);
    }

    .btn-action:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Dark Modal */
    .dark-modal .modal-content {
        background: linear-gradient(135deg, rgba(30, 30, 30, 0.98), rgba(18, 18, 18, 0.98));
        border: 1px solid rgba(255, 215, 0, 0.3);
        color: #ffffff !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }

    .dark-modal .modal-header {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.15), rgba(30, 30, 30, 0.8));
        border-bottom: 2px solid rgba(255, 215, 0, 0.4);
        color: #ffffff !important;
    }

    .dark-modal .modal-header .modal-title {
        color: var(--gold);
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.4);
        font-weight: 600;
    }

    .dark-modal .modal-footer {
        background-color: rgba(30, 30, 30, 0.8);
        border-top: 1px solid rgba(255, 215, 0, 0.2);
    }

    .dark-modal .form-control {
        background-color: rgba(10, 10, 10, 0.8) !important;
        color: #e8e8e8 !important;
        border: 1px solid rgba(255, 215, 0, 0.3) !important;
        border-radius: 10px;
        padding: 0.75rem 1rem;
    }

    .dark-modal .form-control:focus {
        background-color: rgba(10, 10, 10, 0.9) !important;
        border-color: var(--gold) !important;
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.3) !important;
        color: #e8e8e8 !important;
    }

    .dark-modal label {
        color: var(--gold) !important;
        font-weight: 600;
        text-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
    }

    .dark-modal .text-danger {
        color: #ff6b7d !important;
    }

    .dark-modal .btn-secondary {
        border: 1px solid rgba(255, 215, 0, 0.3);
        background-color: rgba(30, 30, 30, 0.8);
        color: var(--text-primary);
    }

    .dark-modal .btn-secondary:hover {
        background-color: rgba(40, 40, 40, 0.9);
        border-color: var(--gold);
        color: var(--gold);
    }

    .dark-modal .btn-close {
        filter: invert(1) brightness(1.5);
    }

    .dark-modal .invalid-feedback {
        color: #ff6b7d !important;
    }
</style>

<div class="container py-4">
    <div class="sizes-header d-flex justify-content-between align-items-center">
        <h2>
            <i class="fas fa-ruler-combined me-2"></i>รายการไซส์
        </h2>
        <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addSizeModal">
            <i class="fas fa-plus me-2"></i>เพิ่มไซส์ใหม่
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any() && !old('action')) 
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sizes mb-0">
                    <thead>
                        <tr>
                            <th width="40%">
                                <i class="fas fa-tag me-2"></i>ชื่อไซส์
                            </th>
                            <th width="30%" class="text-center">
                                <i class="fas fa-box me-2"></i>จำนวนสินค้า
                            </th>
                            <th width="30%" class="text-center">
                                <i class="fas fa-cogs me-2"></i>การจัดการ
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sizes as $size)
                            @php
                                $count = $size->product_color_sizes_count ?? 0;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $size->size_name }}</strong>
                                </td>
                                <td class="text-center">
                                    @if($count > 0)
                                        <span class="badge bg-info">
                                            <i class="fas fa-boxes me-1"></i>{{ $count }} รายการ
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-action btn-sm me-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal{{ $size->id }}"
                                        {{ $count > 0 ? 'disabled' : '' }}
                                        title="{{ $count > 0 ? 'แก้ไขไม่ได้เพราะมีสินค้าใช้อยู่' : 'แก้ไข' }}">
                                        <i class="fas fa-edit me-1"></i>แก้ไข
                                    </button>

                                    <form action="{{ route('sizes.destroy', $size->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('ยืนยันการลบไซส์นี้?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-action btn-sm" 
                                            {{ $count > 0 ? 'disabled' : '' }}
                                            title="{{ $count > 0 ? 'ลบไม่ได้เพราะมีสินค้าใช้อยู่' : 'ลบ' }}">
                                            <i class="fas fa-trash-alt me-1"></i>ลบ
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
</div>

{{-- Modal เพิ่มไซส์ --}}
<div class="modal fade dark-modal" id="addSizeModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('sizes.store') }}">
      @csrf
      <input type="hidden" name="action" value="create_size">

      <div class="modal-header">
        <h5 class="modal-title">
            <i class="fas fa-plus-circle me-2"></i>เพิ่มไซส์ใหม่
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label><i class="fas fa-ruler me-2"></i>ชื่อไซส์</label>
          <input type="text" name="size_name" 
                 class="form-control {{ $errors->has('size_name') && old('action') == 'create_size' ? 'is-invalid' : '' }}" 
                 value="{{ old('action') == 'create_size' ? old('size_name') : '' }}" 
                 required 
                 placeholder="เช่น S, M, L, XL, 38, 40">
          
          @if($errors->has('size_name') && old('action') == 'create_size')
              <div class="invalid-feedback">{{ $errors->first('size_name') }}</div>
          @endif
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>ยกเลิก
        </button>
        <button type="submit" class="btn btn-add">
            <i class="fas fa-save me-2"></i>บันทึก
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modal แก้ไขไซส์ --}}
@foreach($sizes as $size)
<div class="modal fade dark-modal" id="editModal{{ $size->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('sizes.update', $size->id) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" value="edit_size">
            <input type="hidden" name="edit_id" value="{{ $size->id }}">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>แก้ไขไซส์
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label><i class="fas fa-ruler me-2"></i>ชื่อไซส์</label>
                    <input type="text" name="size_name" 
                           class="form-control {{ $errors->has('size_name') && old('edit_id') == $size->id ? 'is-invalid' : '' }}" 
                           value="{{ old('edit_id') == $size->id ? old('size_name') : $size->size_name }}" 
                           required>
                    
                    @if($errors->has('size_name') && old('edit_id') == $size->id)
                        <div class="invalid-feedback">{{ $errors->first('size_name') }}</div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>ยกเลิก
                </button>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save me-2"></i>อัปเดต
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
        @if($errors->any() && old('action') == 'create_size')
            var addModal = new bootstrap.Modal(document.getElementById('addSizeModal'));
            addModal.show();
        @endif

        @if($errors->any() && old('action') == 'edit_size')
            var editId = "{{ old('edit_id') }}";
            var editModalEl = document.getElementById('editModal' + editId);
            if(editModalEl) {
                var editModal = new bootstrap.Modal(editModalEl);
                editModal.show();
            }
        @endif

        // Auto dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
@endpush