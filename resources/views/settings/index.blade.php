@extends('layouts.app')
@include('layouts.navbarDB')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>ตั้งค่าระบบ (System Settings)</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <h6 class="text-primary mb-3">ข้อมูลร้านค้า (Shop Information)</h6>

                        <div class="mb-3">
                            <label for="shop_name" class="form-label">ชื่อร้าน (Shop Name)</label>
                            <input type="text" class="form-control" id="shop_name" name="shop_name"
                                   value="{{ $settings['shop_name'] ?? '' }}" placeholder="ระบุชื่อร้าน">
                        </div>

                        <div class="mb-3">
                            <label for="shop_address" class="form-label">ที่อยู่ร้าน (Address)</label>
                            <textarea class="form-control" id="shop_address" name="shop_address" rows="3"
                                      placeholder="ระบุที่อยู่ร้าน">{{ $settings['shop_address'] ?? '' }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="shop_phone" class="form-label">เบอร์โทรศัพท์ (Phone)</label>
                            <input type="text" class="form-control" id="shop_phone" name="shop_phone"
                                   value="{{ $settings['shop_phone'] ?? '' }}" placeholder="08x-xxx-xxxx">
                        </div>

                        <div class="mb-3">
                            <label for="shop_email" class="form-label">อีเมล (Email)</label>
                            <input type="email" class="form-control" id="shop_email" name="shop_email"
                                   value="{{ $settings['shop_email'] ?? '' }}" placeholder="contact@myshop.com">
                        </div>

                        <div class="mb-3">
                            <label for="shop_logo" class="form-label">โลโก้ร้าน (Logo)</label>
                            <input type="file" class="form-control" id="shop_logo" name="shop_logo" accept="image/*">
                            @if(isset($settings['shop_logo']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $settings['shop_logo']) }}" alt="Logo" style="height: 80px;" class="border rounded p-1">
                                    <small class="d-block text-muted">โลโก้ปัจจุบัน</small>
                                </div>
                            @endif
                        </div>

                        <hr class="my-4">

                        <h6 class="text-primary mb-3">การตั้งค่าอื่นๆ (General Settings)</h6>

                        <div class="mb-3">
                            <label for="low_stock_threshold" class="form-label">แจ้งเตือนสินค้าใกล้หมดเมื่อต่ำกว่า (ชิ้น)</label>
                            <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold"
                                   value="{{ $settings['low_stock_threshold'] ?? '10' }}" min="1">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> บันทึกการตั้งค่า
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
