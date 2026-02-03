@extends('layouts.app')

@section('title', 'ตั้งค่าระบบ')

@section('content')
<style>
    /* Custom Styles for Settings Page - Black & Gold Theme */
    .settings-header {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.1), rgba(30, 30, 30, 0.9));
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 215, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .settings-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.05) 0%, transparent 70%);
        animation: headerGlow 8s infinite ease-in-out;
    }

    @keyframes headerGlow {
        0%, 100% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-10%, -10%) scale(1.1); }
    }

    .settings-header h2 {
        color: var(--gold);
        text-shadow: 0 0 15px rgba(255, 215, 0, 0.5);
        font-weight: 600;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .settings-header p {
        color: #ccc !important;
        margin: 0.5rem 0 0 0;
        position: relative;
        z-index: 1;
    }

    /* Alert Styling */
    .alert-success {
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.2), rgba(30, 30, 30, 0.9));
        color: #7de5a4 !important;
        border: 1px solid rgba(25, 135, 84, 0.5);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: 0 5px 15px rgba(25, 135, 84, 0.2);
        animation: slideDown 0.5s ease;
    }

    .alert-danger {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.2), rgba(30, 30, 30, 0.9));
        color: #ff6b7d !important;
        border: 1px solid rgba(220, 53, 69, 0.5);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.2);
        animation: slideDown 0.5s ease;
    }

    .alert-danger strong,
    .alert-danger ul,
    .alert-danger li {
        color: #ff6b7d !important;
    }

    /* Settings Card */
    .settings-card {
        background: linear-gradient(135deg, rgba(30, 30, 30, 0.95), rgba(18, 18, 18, 0.95));
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 215, 0, 0.1);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .settings-card .card-header {
        padding: 1.5rem;
        border-bottom: 2px solid rgba(255, 215, 0, 0.3);
    }

    /* Different header colors for each section */
    .settings-card.shop-info .card-header {
        background: linear-gradient(135deg, rgba(76, 154, 255, 0.25), rgba(30, 30, 30, 0.8));
    }

    .settings-card.shop-info .card-header h5 {
        color: #5c9aff;
        text-shadow: 0 0 10px rgba(76, 154, 255, 0.5);
    }

    .settings-card.shipping-info .card-header {
        background: linear-gradient(135deg, rgba(95, 237, 255, 0.25), rgba(30, 30, 30, 0.8));
    }

    .settings-card.shipping-info .card-header h5 {
        color: #5fedff;
        text-shadow: 0 0 10px rgba(95, 237, 255, 0.5);
    }

    .settings-card.bank-info .card-header {
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.25), rgba(30, 30, 30, 0.8));
    }

    .settings-card.bank-info .card-header h5 {
        color: #7de5a4;
        text-shadow: 0 0 10px rgba(25, 135, 84, 0.5);
    }

    .settings-card.stock-info .card-header {
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.25), rgba(30, 30, 30, 0.8));
    }

    .settings-card.stock-info .card-header h5 {
        color: var(--gold);
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
    }

    .settings-card .card-header h5 {
        font-weight: 600;
        margin: 0;
    }

    .settings-card .card-body {
        padding: 2rem;
    }

    /* Form Labels */
    .form-label {
        color: var(--gold) !important;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.95rem;
        text-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
    }

    .form-label .text-danger {
        color: #ff6b7d !important;
    }

    /* Form Inputs */
    .form-control,
    .form-select {
        background: rgba(10, 10, 10, 0.8) !important;
        border: 1px solid rgba(255, 215, 0, 0.3) !important;
        color: #e8e8e8 !important;
        padding: 0.75rem 1rem;
        border-radius: 10px;
    }

    .form-control:focus,
    .form-select:focus {
        background: rgba(10, 10, 10, 0.9) !important;
        border-color: var(--gold) !important;
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.3) !important;
        color: #e8e8e8 !important;
    }

    .form-control::placeholder {
        color: rgba(204, 204, 204, 0.5) !important;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    /* Small Text */
    .text-muted,
    small.text-muted {
        color: #aaa !important;
        font-size: 0.85rem;
    }

    /* Invalid Feedback */
    .invalid-feedback {
        color: #ff6b7d !important;
        font-size: 0.85rem;
    }

    /* Bank Account Row */
    .bank-account-row {
        background: rgba(10, 10, 10, 0.6);
        border: 1px solid rgba(255, 215, 0, 0.2) !important;
        border-radius: 12px;
        padding: 1.5rem;
        position: relative;
    }

    .bank-account-row .btn-remove {
        background: linear-gradient(135deg, #ff5c8d, #ff3366);
        border: none;
        color: #fff !important;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        box-shadow: 0 3px 10px rgba(255, 51, 102, 0.3);
    }

    .bank-account-row .btn-remove:hover {
        box-shadow: 0 5px 15px rgba(255, 51, 102, 0.5);
    }

    /* Buttons */
    .btn-add-bank {
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.3), rgba(25, 135, 84, 0.2));
        border: 1px solid rgba(25, 135, 84, 0.5);
        color: #7de5a4 !important;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .btn-add-bank:hover {
        background: linear-gradient(135deg, #28a745, #20c997);
        border-color: #28a745;
        color: #fff !important;
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
    }

    .btn-save-settings {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        color: #fff !important;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        width: 100%;
    }

    .btn-save-settings:hover {
        background: linear-gradient(135deg, #20c997, #28a745);
        color: #fff !important;
        box-shadow: 0 12px 30px rgba(40, 167, 69, 0.5);
    }

    /* Info Card */
    .info-card {
        background: linear-gradient(135deg, rgba(95, 237, 255, 0.15), rgba(30, 30, 30, 0.95));
        border: 1px solid rgba(95, 237, 255, 0.4);
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(95, 237, 255, 0.2);
    }

    .info-card .card-header {
        background: linear-gradient(135deg, rgba(95, 237, 255, 0.25), rgba(30, 30, 30, 0.8));
        border-bottom: 2px solid rgba(95, 237, 255, 0.4);
        padding: 1.5rem;
    }

    .info-card .card-header h5 {
        color: #5fedff;
        text-shadow: 0 0 10px rgba(95, 237, 255, 0.5);
        font-weight: 600;
        margin: 0;
    }

    .info-card .card-body {
        padding: 2rem;
    }

    .info-card ul {
        color: #ddd !important;
        margin: 0;
        padding-left: 1.5rem;
    }

    .info-card li {
        margin-bottom: 0.75rem;
        color: #ddd !important;
    }

    .info-card li strong {
        color: var(--gold);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #aaa !important;
    }

    /* Save Card */
    .save-card {
        background: linear-gradient(135deg, rgba(30, 30, 30, 0.95), rgba(18, 18, 18, 0.95));
        border: 1px solid rgba(25, 135, 84, 0.5);
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4), 0 0 25px rgba(25, 135, 84, 0.2);
    }

    /* Animations */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .settings-header {
            padding: 1.5rem;
        }

        .settings-card .card-body {
            padding: 1.5rem;
        }

        .bank-account-row {
            padding: 1rem;
        }
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- Header --}}
            <div class="settings-header">
                <h2 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>ตั้งค่าระบบ
                </h2>
                <p class="text-muted">
                    <i class="fas fa-info-circle me-2"></i>จัดการข้อมูลทั่วไปของร้านและระบบ
                </p>
            </div>

            {{-- แจ้งเตือน --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i>พบข้อผิดพลาด:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ฟอร์มตั้งค่า --}}
            <form method="POST" action="{{ route('settings.update') }}" id="settingsForm">
                @csrf
                @method('PUT')

                {{-- 1. ข้อมูลร้านค้า --}}
                <div class="card settings-card shop-info">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-store me-2"></i>ข้อมูลร้านค้า
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- ชื่อร้าน --}}
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-shop me-2"></i>ชื่อร้าน <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="shop_name" 
                                   class="form-control @error('shop_name') is-invalid @enderror" 
                                   value="{{ old('shop_name', $settings['shop_name']) }}" 
                                   required
                                   placeholder="กรอกชื่อร้านค้า">
                            @error('shop_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>จะแสดงในหัวใบเสร็จและรายงานต่างๆ
                            </small>
                        </div>

                        {{-- เบอร์โทรศัพท์ --}}
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-phone me-2"></i>เบอร์โทรศัพท์
                            </label>
                            <input type="text" 
                                   name="shop_phone" 
                                   class="form-control @error('shop_phone') is-invalid @enderror" 
                                   value="{{ old('shop_phone', $settings['shop_phone']) }}"
                                   placeholder="02-xxx-xxxx หรือ 08x-xxx-xxxx">
                            @error('shop_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ที่อยู่ร้าน --}}
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-map-marker-alt me-2"></i>ที่อยู่ร้าน
                            </label>
                            <textarea name="shop_address" 
                                      class="form-control @error('shop_address') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder="บ้านเลขที่ ซอย ถนน ตำบล อำเภอ จังหวัด รหัสไปรษณีย์">{{ old('shop_address', $settings['shop_address']) }}</textarea>
                            @error('shop_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>ใช้สำหรับพิมพ์ใบเสร็จและเอกสาร
                            </small>
                        </div>
                    </div>
                </div>

                {{-- 2. การจัดส่ง --}}
                <div class="card settings-card shipping-info">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shipping-fast me-2"></i>การจัดส่ง
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-dollar-sign me-2"></i>ค่าส่งเริ่มต้น (บาท) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   name="default_shipping_fee" 
                                   class="form-control @error('default_shipping_fee') is-invalid @enderror" 
                                   value="{{ old('default_shipping_fee', $settings['default_shipping_fee']) }}" 
                                   min="0" 
                                   step="0.01" 
                                   required
                                   placeholder="0.00">
                            @error('default_shipping_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>ค่าจัดส่งที่จะแสดงเป็นค่าเริ่มต้นเมื่อสร้างออเดอร์ใหม่
                            </small>
                        </div>
                    </div>
                </div>

                {{-- 3. บัญชีธนาคาร --}}
                <div class="card settings-card bank-info">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-university me-2"></i>บัญชีธนาคาร
                        </h5>
                        <button type="button" class="btn btn-add-bank" onclick="addBankAccount()">
                            <i class="fas fa-plus me-2"></i>เพิ่มบัญชี
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="bank-accounts-container">
                            @if(count($settings['bank_accounts']) > 0)
                                @foreach($settings['bank_accounts'] as $index => $account)
                                    <div class="bank-account-row mb-3">
                                        <button type="button" class="btn btn-remove position-absolute top-0 end-0 m-2" 
                                                onclick="removeBankAccount(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-building me-2"></i>ธนาคาร <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" 
                                                       name="bank_accounts[{{ $index }}][bank_name]" 
                                                       class="form-control" 
                                                       value="{{ old('bank_accounts.'.$index.'.bank_name', $account['bank_name'] ?? '') }}"
                                                       placeholder="ธนาคารกสิกรไทย" 
                                                       required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-credit-card me-2"></i>เลขที่บัญชี <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" 
                                                       name="bank_accounts[{{ $index }}][account_number]" 
                                                       class="form-control" 
                                                       value="{{ old('bank_accounts.'.$index.'.account_number', $account['account_number'] ?? '') }}"
                                                       placeholder="123-4-56789-0" 
                                                       required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-user me-2"></i>ชื่อบัญชี <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" 
                                                       name="bank_accounts[{{ $index }}][account_name]" 
                                                       class="form-control" 
                                                       value="{{ old('bank_accounts.'.$index.'.account_name', $account['account_name'] ?? '') }}"
                                                       placeholder="นาย/นาง..." 
                                                       required>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted empty-state">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    ยังไม่มีบัญชีธนาคาร คลิก "เพิ่มบัญชี" เพื่อเพิ่ม
                                </p>
                            @endif
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>ใช้สำหรับให้พนักงาน Sales เลือกบัญชีที่รับโอนตอนสร้างออเดอร์
                        </small>
                    </div>
                </div>

                {{-- 4. การแจ้งเตือนสต็อก --}}
                <div class="card settings-card stock-info">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-boxes me-2"></i>การแจ้งเตือนสต็อก
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-exclamation-triangle me-2"></i>ค่าแจ้งเตือนสต็อกต่ำ (Low Stock Threshold) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   name="low_stock_threshold" 
                                   class="form-control @error('low_stock_threshold') is-invalid @enderror" 
                                   value="{{ old('low_stock_threshold', $settings['low_stock_threshold']) }}" 
                                   min="0" 
                                   max="1000" 
                                   required
                                   placeholder="10">
                            @error('low_stock_threshold')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>เมื่อสต็อกสินค้า (available_stock) ≤ ค่านี้ ระบบจะขึ้นเตือนสีแดงใน Dashboard
                            </small>
                        </div>
                    </div>
                </div>

                {{-- ปุ่มบันทึก --}}
                <div class="card save-card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-save-settings">
                            <i class="fas fa-save me-2"></i>บันทึกการตั้งค่าทั้งหมด
                        </button>
                    </div>
                </div>
            </form>

            {{-- คำแนะนำ --}}
            <div class="card info-card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>คำแนะนำการใช้งาน
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>
                            <strong><i class="fas fa-store me-2"></i>ข้อมูลร้านค้า:</strong> 
                            จะแสดงในหัวใบเสร็จ รายงาน และเอกสารต่างๆ
                        </li>
                        <li>
                            <strong><i class="fas fa-shipping-fast me-2"></i>ค่าส่งเริ่มต้น:</strong> 
                            จะนำมาแสดงอัตโนมัติเมื่อสร้างออเดอร์ใหม่
                        </li>
                        <li>
                            <strong><i class="fas fa-university me-2"></i>บัญชีธนาคาร:</strong> 
                            พนักงาน Sales สามารถเลือกบัญชีที่ต้องการแจ้งลูกค้าให้โอนเงิน
                        </li>
                        <li>
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Threshold:</strong> 
                            เมื่อสต็อกลดลงต่ำกว่าค่านี้ ระบบจะแจ้งเตือนใน Dashboard
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript สำหรับจัดการบัญชีธนาคาร --}}
@push('scripts')
<script>
    let bankAccountIndex = {{ count($settings['bank_accounts']) }};

    // Handle form submission - Convert bank_accounts array to JSON
    document.getElementById('settingsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('Form submitting...'); // Debug
        
        const form = this;
        const bankAccounts = [];
        
        // Collect all bank account inputs
        const bankRows = document.querySelectorAll('.bank-account-row');
        console.log('Found bank rows:', bankRows.length); // Debug
        
        bankRows.forEach(function(row, index) {
            const bankName = row.querySelector('input[name*="[bank_name]"]');
            const accountNumber = row.querySelector('input[name*="[account_number]"]');
            const accountName = row.querySelector('input[name*="[account_name]"]');
            
            if (bankName && accountNumber && accountName) {
                const account = {
                    bank_name: bankName.value,
                    account_number: accountNumber.value,
                    account_name: accountName.value
                };
                bankAccounts.push(account);
                console.log('Account ' + index + ':', account); // Debug
            }
        });
        
        // Remove all individual bank_accounts inputs
        const existingInputs = document.querySelectorAll('input[name^="bank_accounts"]');
        console.log('Removing inputs:', existingInputs.length); // Debug
        existingInputs.forEach(function(input) {
            input.remove();
        });
        
        // Add single hidden input with JSON data
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'bank_accounts';
        hiddenInput.value = JSON.stringify(bankAccounts);
        form.appendChild(hiddenInput);
        
        console.log('Bank accounts JSON:', hiddenInput.value); // Debug
        console.log('Submitting form now...'); // Debug
        
        // Submit the form
        form.submit();
    });

    function addBankAccount() {
        const container = document.getElementById('bank-accounts-container');
        
        // ลบข้อความ "ยังไม่มีบัญชี" ถ้ามี
        const emptyMsg = container.querySelector('p.empty-state');
        if (emptyMsg) {
            emptyMsg.remove();
        }
        
        const html = `
            <div class="bank-account-row mb-3">
                <button type="button" class="btn btn-remove position-absolute top-0 end-0 m-2" 
                        onclick="removeBankAccount(this)">
                    <i class="fas fa-times"></i>
                </button>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="fas fa-building me-2"></i>ธนาคาร <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="bank_accounts[${bankAccountIndex}][bank_name]" 
                               class="form-control" 
                               placeholder="ธนาคารกสิกรไทย" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="fas fa-credit-card me-2"></i>เลขที่บัญชี <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="bank_accounts[${bankAccountIndex}][account_number]" 
                               class="form-control" 
                               placeholder="123-4-56789-0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            <i class="fas fa-user me-2"></i>ชื่อบัญชี <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="bank_accounts[${bankAccountIndex}][account_name]" 
                               class="form-control" 
                               placeholder="นาย/นาง..." required>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', html);
        bankAccountIndex++;
        
        console.log('Added bank account row, new index:', bankAccountIndex); // Debug
    }

    function removeBankAccount(button) {
        const row = button.closest('.bank-account-row');
        row.remove();
        
        // ถ้าไม่มีบัญชีเหลือเลย แสดงข้อความ
        const container = document.getElementById('bank-accounts-container');
        if (container.children.length === 0) {
            container.innerHTML = `
                <p class="text-muted empty-state">
                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                    ยังไม่มีบัญชีธนาคาร คลิก "เพิ่มบัญชี" เพื่อเพิ่ม
                </p>
            `;
        }
        
        console.log('Removed bank account row'); // Debug
    }

    // Auto dismiss alert after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Debug: Log when script loads
    console.log('Settings form script loaded');
    console.log('Initial bank account index:', bankAccountIndex);
</script>
@endpush
@endsection