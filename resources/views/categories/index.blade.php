@extends('layouts.app')
@include('layouts.navbarPD')
@section('content')
<div class="container">
    <h2>รายการหมวดหมู่</h2>
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif


    {{-- ปุ่มเปิด Modal เพิ่ม --}}
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        เพิ่มหมวดหมู่ใหม่
    </button>
    <table class="table table-bordered">
    <thead>
        <tr>
            <th>ชื่อหมวดหมู่</th>
            <th>จำนวนสินค้า</th>
            <th>การจัดการ</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $category)
        <tr>
            <td>{{ $category->category_name }}</td>
            <td>{{ $category->products->count() }}</td>
            <td>
                <!-- ปุ่มแก้ไข -->
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}">
                    แก้ไข
                </button>

                <!-- ปุ่มลบ -->
                <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('ยืนยันการลบ?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" {{ $category->products->count() > 0 ? 'disabled' : '' }}>ลบ</button>
                </form>
            </td>
        </tr>   
<!-- Edit Modal -->
<div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" 
     aria-labelledby="editModalLabel{{ $category->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel{{ $category->id }}">แก้ไขหมวดหมู่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category_name">ชื่อหมวดหมู่</label>
                        <input type="text" name="category_name" class="form-control 
                               @if(session('edit_id') == $category->id) @error('category_name') is-invalid @enderror @endif" 
                               value="{{ old('category_name', $category->category_name) }}" required>
                        @if(session('edit_id') == $category->id)
                            @error('category_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success">อัปเดต</button>
                </div>
            </form>
        </div>
    </div>
</div>




            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal เพิ่มหมวดหมู่ -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <input type="hidden" name="from" value="create">
        <div class="modal-header">
          <h5 class="modal-title" id="addCategoryModalLabel">เพิ่มหมวดหมู่ใหม่</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="category_name">ชื่อหมวดหมู่</label>
            <input type="text" name="category_name" class="form-control @error('category_name') is-invalid @enderror" 
                   value="{{ old('category_name') }}" required>
            @error('category_name')
              <div class="invalid-feedback">
                {{ $message }}
              </div>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึก</button>
        </div>
      </form>
    </div>
  </div>
</div>


@endsection