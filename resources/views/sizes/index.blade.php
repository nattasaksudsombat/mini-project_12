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

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addSizeModal">เพิ่มขนาดใหม่</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ชื่อขนาด</th>
                <th>การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sizes as $size)
            <tr>
                <td>{{ $size->size_name }}</td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSizeModal{{ $size->id }}">แก้ไข</button>
                    <form action="{{ route('sizes.destroy', $size) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">ลบ</button>
                    </form>
                </td>
            </tr>

            <!-- Modal แก้ไขขนาด -->
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
                            <button type="submit" class="btn btn-success">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal เพิ่มขนาด -->
<div class="modal fade dark-modal {{ old('size_name') && $errors->any() ? 'show d-block' : '' }}" id="addSizeModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('sizes.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">เพิ่มขนาดใหม่</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>ชื่อขนาด</label>
          <input type="text" name="size_name" class="form-control @error('size_name') is-invalid @enderror" value="{{ old('size_name') }}" required>
          @error('size_name')
              <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
        <button type="submit" class="btn btn-primary">เพิ่ม</button>
      </div>
    </form>
  </div>
</div>
@endsection
