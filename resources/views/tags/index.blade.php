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
@foreach($tags as $tag)
            <!-- Modal แก้ไขแท็ก -->
            <div class="modal fade dark-modal" id="editTagModal{{ $tag->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form class="modal-content" method="POST" action="{{ route('tags.update', $tag) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">แก้ไขแท็ก</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>ชื่อแท็ก</label>
                                <input type="text" name="name" class="form-control" value="{{ $tag->tag_name }}" required>
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
<!-- Modal เพิ่มแท็ก -->
<div class="modal fade dark-modal {{ old('name') && $errors->any() ? 'show d-block' : '' }}" id="addTagModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('tags.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">เพิ่มแท็กใหม่</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>ชื่อแท็ก</label>
          <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
          @error('name')
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
