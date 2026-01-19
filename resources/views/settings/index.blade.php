@extends('layouts.app')

@section('title', 'ตั้งค่าระบบ')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- Header --}}
            <div class="mb-4">
                <h2 class="mb-0">⚙️ ตั้งค่าระบบ</h2>
                <p class="text-muted">จัดการข้อมูลทั่วไปของร้านและระบบ</p>
            </div>

            {{-- แจ้งเตือน --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    ✅ {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>❌ พบข้อผิดพลาด:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ฟอร์มตั้งค่า --}}
            <form method="POST" action="{{ route('settings.update') }}">
                @csrf
                @method('PUT')

                {{-- ข้อมูลร้าน --}}
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">🏪 ข้อมูลร้านค้า</h5>
                    </div>
                    <div class="card-body">
                        {{-- ชื่อร้าน --}}
                        <div class="mb-3">
                            <label class="form-label">ชื่อร้าน <span class="text-danger">*</span></label>
                            <input type="text" name="shop_name" class="form-control @error('shop_name') is-invalid @enderror" 
                                   value="{{ old('shop_name', $settings['shop_name']) }}" required>
                            @error('shop_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- เบอร์โทรศัพท์ --}}
                        <div class="mb-3">
                            <label class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" name="shop_phone" class="form-control @error('shop_phone') is-invalid @enderror" 
                                   value="{{ old('shop_phone', $settings['shop_phone']) }}"
                                   placeholder="02-xxx-xxxx หรือ 08x-xxx-xxxx">
                            @error('shop_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ที่อยู่ร้าน --}}
                        <div class="mb-3">
                            <label class="form-label">ที่อยู่ร้าน</label>
                            <textarea name="shop_address" class="form-control @error('shop_address') is-invalid @enderror" 
                                      rows="3" placeholder="บ้านเลขที่ ซอย ถนน ตำบล อำเภอ จังหวัด รหัสไปรษณีย์">{{ old('shop_address', $settings['shop_address']) }}</textarea>
                            @error('shop_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- การแจ้งเตือนสต็อก --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">📦 การจัดการสต็อก</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">ค่าแจ้งเตือนสต็อกต่ำ (Low Stock Threshold) <span class="text-danger">*</span></label>
                            <input type="number" name="low_stock_threshold" 
                                   class="form-control @error('low_stock_threshold') is-invalid @enderror" 
                                   value="{{ old('low_stock_threshold', $settings['low_stock_threshold']) }}" 
                                   min="0" max="1000" required>
                            @error('low_stock_threshold')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                ระบบจะแจ้งเตือนเมื่อสต็อกสินค้า (available_stock) น้อยกว่าหรือเท่ากับค่านี้
                            </small>
                        </div>
                    </div>
                </div>

                {{-- ปุ่มบันทึก --}}
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                💾 บันทึกการตั้งค่า
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- คำแนะนำ --}}
            <div class="card mt-4 border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">ℹ️ คำแนะนำการใช้งาน</h5>
                </div>
                <div class="card-body">
                    <ul>
                        <li><strong>ชื่อร้าน:</strong> จะแสดงในส่วนหัวของระบบและรายงานต่างๆ</li>
                        <li><strong>เบอร์โทร & ที่อยู่:</strong> ใช้สำหรับพิมพ์ใบเสร็จและเอกสารต่างๆ</li>
                        <li><strong>Low Stock Threshold:</strong> เมื่อสต็อกลดลงต่ำกว่าค่านี้ ระบบจะแจ้งเตือนใน Dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection