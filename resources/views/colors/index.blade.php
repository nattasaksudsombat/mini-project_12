@extends('layouts.app')
@include('layouts.navbarPD')

@section('content')

<div class="container">
    <h2>รายการสี</h2>

    {{-- Alert แจ้งเตือน --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- แสดง Error ทั่วไป (ที่ไม่ใช่จาก Modal เพิ่มสี) --}}
    @if($errors->any() && old('action') != 'create_color')
        <div class="alert alert-danger alert-dismissible fade show">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addColorModal">
        <i class="fas fa-plus"></i> เพิ่มสีใหม่
    </button>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="30%">ชื่อสี</th>
                        <th width="20%">ตัวอย่างสี</th>
                        <th width="20%" class="text-center">จำนวนสินค้า</th>
                        <th width="30%" class="text-center">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($colors as $color)
                        @php
                            // ✅ แก้จุดที่ 3: ดึงจำนวนสินค้าที่ใช้สีนี้ (ผ่าน Relation โดยตรง)
                            // เช็คทั้งตาราง pivot (product_color_size) หรือ products ตรงๆ
                            $count = 0;
                            if($color->relationLoaded('productColorSizes')) {
                                $count = $color->productColorSizes->count();
                            } elseif(method_exists($color, 'productColorSizes')) {
                                $count = $color->productColorSizes()->count();
                            }
                        @endphp
                        <tr>
                            <td>{{ $color->name }}</td>
                            <td>
                                <div style="width: 30px; height: 30px; background-color: {{ $color->hex_code }}; border: 1px solid #ccc; border-radius: 4px;"></div>
                            </td>
                            <td class="text-center">
                                @if($count > 0)
                                    <span class="badge bg-info text-dark">{{ $count }} รายการ</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{-- ✅ แก้จุดที่ 2: ถ้ามีสินค้าใช้สีนี้ (count > 0) ห้ามแก้ไข/ลบ --}}
                                <button class="btn btn-warning btn-sm me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal{{ $color->id }}"
                                    {{ $count > 0 ? 'disabled' : '' }}
                                    title="{{ $count > 0 ? 'แก้ไขไม่ได้เพราะมีสินค้าใช้อยู่' : 'แก้ไข' }}">
                                    <i class="fas fa-edit"></i> แก้ไข
                                </button>

                                <form action="{{ route('colors.destroy', $color->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('ยืนยันการลบสีนี้?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" 
                                        {{ $count > 0 ? 'disabled' : '' }}
                                        title="{{ $count > 0 ? 'ลบไม่ได้เพราะมีสินค้าใช้อยู่' : 'ลบ' }}">
                                        <i class="fas fa-trash"></i> ลบ
                                    </button>
                                </form>

                                {{-- Modal แก้ไข (Edit) --}}
                                <div class="modal fade" id="editModal{{ $color->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form class="modal-content" method="POST" action="{{ route('colors.update', $color->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">แก้ไขสี</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="mb-3">
                                                    <label>ชื่อสี</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $color->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>รหัสสี</label>
                                                    <input type="color" name="hex_code" class="form-control form-control-color" value="{{ $color->hex_code }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                                <button type="submit" class="btn btn-warning">อัปเดต</button>
                                            </div>
                                        </form>
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

@endsection

{{-- ย้าย Modal ออกมาไว้นอก section เพื่อหลีกเลี่ยงปัญหา --}}
<div class="modal fade dark-modal" id="addColorModal" tabindex="-1" aria-labelledby="addColorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('colors.store') }}" id="addColorForm">
      @csrf
      {{-- ใส่ hidden field เพื่อให้ JS รู้ว่า Error มาจากฟอร์มนี้ --}}
      <input type="hidden" name="action" value="create_color">

      <div class="modal-header">
        <h5 class="modal-title" id="addColorModalLabel">เพิ่มสีใหม่</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="colorName" class="form-label">ชื่อสี <span class="text-danger">*</span></label>
          <input type="text" id="colorName" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="เช่น แดง, ดำ, น้ำเงิน">
          @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <label for="colorHex" class="form-label">รหัสสี (เลือกจากแถบ) <span class="text-danger">*</span></label>
          <input type="color" id="colorHex" name="hex_code" class="form-control form-control-color" value="{{ old('hex_code', '#000000') }}" required>
          @error('hex_code')
              <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
        <button type="submit" class="btn btn-primary" id="submitBtn">
          <i class="fas fa-save"></i> บันทึก
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
    // ✅ สคริปต์สำหรับเปิด Modal อัตโนมัติ เมื่อมีการ submit แล้วเกิด Error
    document.addEventListener('DOMContentLoaded', function() {
        // เปิด Modal ถ้ามี error จากการ submit
        @if($errors->any() && old('action') == 'create_color')
            var myModal = new bootstrap.Modal(document.getElementById('addColorModal'));
            myModal.show();
        @endif

        // Debug: ตรวจสอบว่า form submit ทำงานหรือไม่
        const form = document.getElementById('addColorForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if(form) {
            form.addEventListener('submit', function(e) {
                console.log('Form is being submitted...');
                console.log('Action:', form.action);
                console.log('Method:', form.method);
                
                // ป้องกันการ submit ซ้ำ
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>กำลังบันทึก...';
                
                // หากต้องการ debug ให้ uncomment บรรทัดนี้
                // e.preventDefault();
                // console.log('Form data:', new FormData(form));
            });
        }
    });
</script>
@endpush