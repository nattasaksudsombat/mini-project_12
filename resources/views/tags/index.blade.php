@extends('layouts.app')
@include('layouts.navbarPD')

@section('content')
<style>
    /* Tags Page - Black & Gold Theme */
    .tags-header {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(30, 30, 30, 0.9));
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 215, 0, 0.1);
    }

    .tags-header h2 {
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
    .table-tags {
        margin: 0;
        color: var(--text-primary);
    }

    .table-tags thead th {
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

    .table-tags tbody td {
        background: rgba(18, 18, 18, 0.6);
        border: 1px solid rgba(255, 215, 0, 0.15);
        padding: 1rem;
        vertical-align: middle;
        color: #e8e8e8 !important;
    }

    .table-tags tbody tr:hover td {
        background: rgba(30, 30, 30, 0.8);
        border-color: rgba(255, 215, 0, 0.3);
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

    .dark-modal .btn {
        border: 1px solid rgba(255, 215, 0, 0.3);
        font-weight: 600;
    }

    .dark-modal .btn-secondary {
        background-color: rgba(30, 30, 30, 0.8);
        color: var(--text-primary);
    }

    .dark-modal .btn-secondary:hover {
        background-color: rgba(40, 40, 40, 0.9);
        border-color: var(--gold);
        color: var(--gold);
    }

    .dark-modal .btn-success {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        color: #fff !important;
    }

    .dark-modal .btn-success:hover {
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
    }

    .dark-modal .btn-close {
        filter: invert(1) brightness(1.5);
    }

    .dark-modal .invalid-feedback {
        color: #ff6b7d !important;
    }
</style>

<div class="container py-4">
    <div class="tags-header d-flex justify-content-between align-items-center">
        <h2>
            <i class="fas fa-tags me-2"></i>รายการแท็ก
        </h2>
        <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addTagModal">
            <i class="fas fa-plus me-2"></i>เพิ่มแท็กใหม่
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-tags mb-0">
                    <thead>
                        <tr>
                            <th width="70%">
                                <i class="fas fa-tag me-2"></i>ชื่อแท็ก
                            </th>
                            <th width="30%" class="text-center">
                                <i class="fas fa-cogs me-2"></i>การจัดการ
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tags as $tag)
                        <tr>
                            <td>
                                <strong>{{ $tag->tag_name }}</strong>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-action btn-sm me-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editTagModal{{ $tag->id }}">
                                    <i class="fas fa-edit me-1"></i>แก้ไข
                                </button>

                                <form action="{{ route('tags.destroy', $tag) }}" 
                                      method="POST" 
                                      style="display:inline-block;" 
                                      onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบแท็กนี้?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-action btn-sm">
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

{{-- Modal เพิ่มแท็ก --}}
<div class="modal fade dark-modal" id="addTagModal" tabindex="-1" aria-labelledby="addTagModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('tags.store') }}" id="addTagForm">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="addTagModalLabel">
            <i class="fas fa-plus-circle me-2"></i>เพิ่มแท็กใหม่
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="tagName" class="form-label">
              <i class="fas fa-tag me-2"></i>ชื่อแท็ก <span class="text-danger">*</span>
          </label>
          <input type="text" 
                 id="tagName" 
                 name="name" 
                 class="form-control @error('name') is-invalid @enderror" 
                 value="{{ old('name') }}" 
                 required 
                 placeholder="กรอกชื่อแท็ก">
          @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>ยกเลิก
        </button>
        <button type="submit" class="btn btn-add">
          <i class="fas fa-plus me-2"></i>เพิ่ม
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Modal แก้ไขแท็ก --}}
@foreach($tags as $tag)
<div class="modal fade dark-modal" id="editTagModal{{ $tag->id }}" tabindex="-1" aria-labelledby="editTagModalLabel{{ $tag->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('tags.update', $tag) }}">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editTagModalLabel{{ $tag->id }}">
                    <i class="fas fa-edit me-2"></i>แก้ไขแท็ก
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="editTagName{{ $tag->id }}" class="form-label">
                        <i class="fas fa-tag me-2"></i>ชื่อแท็ก <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           id="editTagName{{ $tag->id }}" 
                           name="name" 
                           class="form-control" 
                           value="{{ $tag->tag_name }}" 
                           required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>ยกเลิก
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>บันทึก
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
        @if($errors->any() && old('name'))
            var addTagModal = new bootstrap.Modal(document.getElementById('addTagModal'));
            addTagModal.show();
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