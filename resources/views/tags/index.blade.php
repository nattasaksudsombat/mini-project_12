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

    .dark-modal .btn {
        border: 1px solid #ccc;
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
    <h2>รายการแท็ก</h2>

    <!-- แจ้งเตือน -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- ปุ่มเพิ่ม -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addTagModal">เพิ่มแท็กใหม่</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ชื่อแท็ก</th>
                <th>การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tags as $tag)
            <tr>
                <td>{{ $tag->tag_name }}</td>
                <td>
                    <!-- ปุ่มแก้ไข -->
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTagModal{{ $tag->id }}">แก้ไข</button>

                    <!-- ปุ่มลบ -->
                    <form action="{{ route('tags.destroy', $tag) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">ลบ</button>
                    </form>
                </td>
            </tr>


            @endforeach
        </tbody>
    </table>
</div>

{{-- Modal แก้ไขแท็ก (ย้ายออกมาข้างนอก) --}}
@foreach($tags as $tag)
<div class="modal fade dark-modal" id="editTagModal{{ $tag->id }}" tabindex="-1" aria-labelledby="editTagModalLabel{{ $tag->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('tags.update', $tag) }}">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editTagModalLabel{{ $tag->id }}">แก้ไขแท็ก</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="editTagName{{ $tag->id }}" class="form-label">ชื่อแท็ก <span class="text-danger">*</span></label>
                    <input type="text" id="editTagName{{ $tag->id }}" name="name" class="form-control" value="{{ $tag->tag_name }}" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> บันทึก
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection

{{-- ย้าย Modal ออกมาข้างนอก section --}}
<!-- Modal เพิ่มแท็ก -->
<div class="modal fade dark-modal" id="addTagModal" tabindex="-1" aria-labelledby="addTagModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('tags.store') }}" id="addTagForm">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="addTagModalLabel">เพิ่มแท็กใหม่</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="tagName" class="form-label">ชื่อแท็ก <span class="text-danger">*</span></label>
          <input type="text" id="tagName" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="กรอกชื่อแท็ก">
          @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-plus"></i> เพิ่ม
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
    // เปิด Modal อัตโนมัติเมื่อมี validation error
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any() && old('name'))
            var addTagModal = new bootstrap.Modal(document.getElementById('addTagModal'));
            addTagModal.show();
        @endif
    });
</script>
@endpush