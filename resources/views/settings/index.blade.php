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

                {{-- 1. ข้อมูลร้านค้า --}}
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
                            <small class="text-muted">จะแสดงในหัวใบเสร็จและรายงานต่างๆ</small>
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
                            <small class="text-muted">ใช้สำหรับพิมพ์ใบเสร็จและเอกสาร</small>
                        </div>
                    </div>
                </div>

                {{-- 2. การจัดส่ง --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">🚚 การจัดส่ง</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">ค่าส่งเริ่มต้น (บาท) <span class="text-danger">*</span></label>
                            <input type="number" name="default_shipping_fee" 
                                   class="form-control @error('default_shipping_fee') is-invalid @enderror" 
                                   value="{{ old('default_shipping_fee', $settings['default_shipping_fee']) }}" 
                                   min="0" step="0.01" required>
                            @error('default_shipping_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                ค่าจัดส่งที่จะแสดงเป็นค่าเริ่มต้นเมื่อสร้างออเดอร์ใหม่
                            </small>
                        </div>
                    </div>
                </div>

                {{-- 3. บัญชีธนาคาร --}}
                <div class="card mb-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">🏦 บัญชีธนาคาร</h5>
                        <button type="button" class="btn btn-light btn-sm" onclick="addBankAccount()">
                            ➕ เพิ่มบัญชี
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="bank-accounts-container">
                            @if(count($settings['bank_accounts']) > 0)
                                @foreach($settings['bank_accounts'] as $index => $account)
                                    <div class="bank-account-row mb-3 p-3 border rounded position-relative">
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" 
                                                onclick="removeBankAccount(this)">
                                            ✖️
                                        </button>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">ธนาคาร <span class="text-danger">*</span></label>
                                                <input type="text" name="bank_accounts[{{ $index }}][bank_name]" 
                                                       class="form-control" 
                                                       value="{{ old('bank_accounts.'.$index.'.bank_name', $account['bank_name'] ?? '') }}"
                                                       placeholder="ธนาคารกสิกรไทย" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">เลขที่บัญชี <span class="text-danger">*</span></label>
                                                <input type="text" name="bank_accounts[{{ $index }}][account_number]" 
                                                       class="form-control" 
                                                       value="{{ old('bank_accounts.'.$index.'.account_number', $account['account_number'] ?? '') }}"
                                                       placeholder="123-4-56789-0" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">ชื่อบัญชี <span class="text-danger">*</span></label>
                                                <input type="text" name="bank_accounts[{{ $index }}][account_name]" 
                                                       class="form-control" 
                                                       value="{{ old('bank_accounts.'.$index.'.account_name', $account['account_name'] ?? '') }}"
                                                       placeholder="นาย/นาง..." required>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center">ยังไม่มีบัญชีธนาคาร คลิก "➕ เพิ่มบัญชี" เพื่อเพิ่ม</p>
                            @endif
                        </div>
                        <small class="text-muted">
                            ใช้สำหรับให้พนักงาน Sales เลือกบัญชีที่รับโอนตอนสร้างออเดอร์
                        </small>
                    </div>
                </div>

                {{-- 4. การแจ้งเตือนสต็อก --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">📦 การแจ้งเตือนสต็อก</h5>
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
                                เมื่อสต็อกสินค้า (available_stock) ≤ ค่านี้ ระบบจะขึ้นเตือนสีแดงใน Dashboard
                            </small>
                        </div>
                    </div>
                </div>

                {{-- ปุ่มบันทึก --}}
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                💾 บันทึกการตั้งค่าทั้งหมด
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
                    <ul class="mb-0">
                        <li><strong>ข้อมูลร้านค้า:</strong> จะแสดงในหัวใบเสร็จ รายงาน และเอกสารต่างๆ</li>
                        <li><strong>ค่าส่งเริ่มต้น:</strong> จะนำมาแสดงอัตโนมัติเมื่อสร้างออเดอร์ใหม่</li>
                        <li><strong>บัญชีธนาคาร:</strong> พนักงาน Sales สามารถเลือกบัญชีที่ต้องการแจ้งลูกค้าให้โอนเงิน</li>
                        <li><strong>Low Stock Threshold:</strong> เมื่อสต็อกลดลงต่ำกว่าค่านี้ ระบบจะแจ้งเตือนใน Dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript สำหรับจัดการบัญชีธนาคาร --}}
<script>
let bankAccountIndex = {{ count($settings['bank_accounts']) }};

function addBankAccount() {
    const container = document.getElementById('bank-accounts-container');
    
    // ลบข้อความ "ยังไม่มีบัญชี" ถ้ามี
    const emptyMsg = container.querySelector('p.text-muted');
    if (emptyMsg) {
        emptyMsg.remove();
    }
    
    const html = `
        <div class="bank-account-row mb-3 p-3 border rounded position-relative">
            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" 
                    onclick="removeBankAccount(this)">
                ✖️
            </button>
            
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">ธนาคาร <span class="text-danger">*</span></label>
                    <input type="text" name="bank_accounts[${bankAccountIndex}][bank_name]" 
                           class="form-control" 
                           placeholder="ธนาคารกสิกรไทย" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">เลขที่บัญชี <span class="text-danger">*</span></label>
                    <input type="text" name="bank_accounts[${bankAccountIndex}][account_number]" 
                           class="form-control" 
                           placeholder="123-4-56789-0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ชื่อบัญชี <span class="text-danger">*</span></label>
                    <input type="text" name="bank_accounts[${bankAccountIndex}][account_name]" 
                           class="form-control" 
                           placeholder="นาย/นาง..." required>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
    bankAccountIndex++;
}

function removeBankAccount(button) {
    const row = button.closest('.bank-account-row');
    row.remove();
    
    // ถ้าไม่มีบัญชีเหลือเลย แสดงข้อความ
    const container = document.getElementById('bank-accounts-container');
    if (container.children.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">ยังไม่มีบัญชีธนาคาร คลิก "➕ เพิ่มบัญชี" เพื่อเพิ่ม</p>';
    }
}
</script>
@endsection