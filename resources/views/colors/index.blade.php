@extends('layouts.app')
@include('layouts.navbarPD')
@section('content')

<div class="container">
    <h2>รายการสี</h2>

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

    <!-- ปุ่มเปิด Modal เพิ่มสี -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addColorModal">เพิ่มสีใหม่</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ชื่อสี</th>
                <th>สี</th>
                <th>จำนวนสินค้าที่ใช้สีนี้</th>
                <th>การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($colors as $color)
                <tr>
                    <td>{{ $color->name }}</td>
                    <td>
                        <div style="width: 30px; height: 30px; background-color: {{ $color->hex_code }}; border: 1px solid #ccc;"></div>
                    </td>
                    <td>{{ $color->products_count ?? 0 }}</td>
                    <td>
                        <!-- ปุ่มแก้ไข -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editColorModal{{ $color->id }}">แก้ไข</button>

                        <!-- ปุ่มลบ -->
                        <form action="{{ route('colors.destroy', $color) }}" method="POST" style="display:inline-block;"
                              onsubmit="return confirm('ยืนยันการลบ?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" {{ ($color->products_count ?? 0) > 0 ? 'disabled' : '' }}>
                                ลบ
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
        </tbody>
    </table>
</div>
<style>
    /* ใช้ id หรือ class ที่เจาะจง modal ของคุณ */
    #addColorModal .modal-content {
        background-color: #2c2c2c !important; /* สีพื้นหลังเข้ม */
        color: #ffffff !important; /* ตัวหนังสือสีขาว */
    }

    #addColorModal .modal-header,
    #addColorModal .modal-footer {
        background-color: #1e1e1e !important;
        color: #ffffff !important;
    }

    #addColorModal .form-control {
        background-color: #444 !important;
        color: white !important;
        border: 1px solid #666;
    }

    #addColorModal label {
        color: #fff !important;
    }

    #addColorModal .btn {
        border: 1px solid #ccc;
        color: white;
    }

    #addColorModal .btn-close {
        filter: invert(1); /* ทำให้ปุ่มปิดมองเห็นได้ในพื้นหลังมืด */
    }
</style>
@foreach($colors as $color)
<!-- Modal แก้ไข -->
<div class="modal fade dark-modal" id="editColorModal{{ $color->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('colors.update', $color) }}"
              style="background-color: #333333; color: #ffffff;">
            @csrf
            @method('PUT')
            <div class="modal-header" style="background-color: #222222; color: #ffffff;">
                <h5 class="modal-title">แก้ไขสี</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label style="color: #ffffff;">ชื่อสี</label>
                    <input type="text" name="color_name" class="form-control"
                           value="{{ $color->name }}"
                           style="background-color: #444444; color: #ffffff; border: 1px solid #666;" required>
                </div>
                <div class="mb-3">
                    <label style="color: #ffffff;">สี</label>
                    <input type="color" name="hex_code" class="form-control form-control-color"
                           value="{{ $color->hex_code }}"
                           style="background-color: #444444; border: 1px solid #666;" required>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #222222;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-success">บันทึก</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Modal เพิ่มสี -->
<div class="modal fade dark-modal {{ old('color_name') && $errors->any() ? 'show d-block' : '' }}" id="addColorModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('colors.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">เพิ่มสีใหม่</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>ชื่อสี</label>
          <input type="text" name="color_name" class="form-control @error('color_name') is-invalid @enderror" value="{{ old('color_name') }}" required>
          @error('color_name')
              <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label>สี</label>
          <input type="color" name="hex_code" class="form-control form-control-color" value="{{ old('hex_code', '#000000') }}" required>
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