@extends('layouts.app')
@include('layouts.navbarPD')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>รายการหมวดหมู่</h2>
        {{-- ปุ่มเปิด Modal เพิ่ม --}}
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus"></i> เพิ่มหมวดหมู่ใหม่
        </button>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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
                            {{-- 
                                ✅ เช็คเงื่อนไข: ถ้ามีสินค้า (count > 0) ห้ามแก้ไข/ลบ 
                                เพราะอาจมีออเดอร์ผูกอยู่ เดี๋ยวระบบรวน
                            --}}
                            @php
                                $isDisabled = $category->products->count() > 0;
                            @endphp

                            <button class="btn btn-warning btn-sm me-1" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal{{ $category->id }}"
                                {{ $isDisabled ? 'disabled' : '' }} 
                                title="{{ $isDisabled ? 'แก้ไขไม่ได้เนื่องจากมีสินค้าใช้งานอยู่' : 'แก้ไข' }}">
                                <i class="fas fa-edit"></i> แก้ไข
                            </button>

                            <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('ยืนยันการลบหมวดหมู่นี้?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" 
                                    {{ $isDisabled ? 'disabled' : '' }}
                                    title="{{ $isDisabled ? 'ลบไม่ได้เนื่องจากมีสินค้าใช้งานอยู่' : 'ลบ' }}">
                                    <i class="fas fa-trash"></i> ลบ
                                </button>
                            </form>

                            <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('categories.update', $category) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">แก้ไขหมวดหมู่</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="form-group">
                                                    <label>ชื่อหมวดหมู่</label>
                                                    <input type="text" name="category_name" class="form-control" value="{{ $category->category_name }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                <button type="submit" class="btn btn-warning">อัปเดต</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        {{-- ใส่ตัวบอกว่ามาจากฟอร์ม create เพื่อให้ JS รู้ --}}
        <input type="hidden" name="from" value="create">
        
        <div class="modal-header">
          <h5 class="modal-title" id="addCategoryModalLabel">เพิ่มหมวดหมู่ใหม่</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="category_name">ชื่อหมวดหมู่</label>
            <input type="text" name="category_name" 
                   class="form-control @error('category_name') is-invalid @enderror" 
                   value="{{ old('category_name') }}" required>
            
            {{-- ✅ แสดง Error ตรงนี้ --}}
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

{{-- ✅ Script สำหรับเปิด Modal อัตโนมัติถ้ามี Error --}}
@if($errors->any() && old('from') == 'create')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
        myModal.show();
    });
</script>
@endif

@endsection