@extends('layouts.app')

@section('content')
<style>
    /* Modern Gradient Backgrounds */
    .gradient-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        margin-bottom: 2rem;
    }
    
    /* Enhanced Cards */
    .form-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }
    .form-card:hover {
        box-shadow: 0 12px 35px rgba(0,0,0,0.12);
    }
    .form-card .card-header {
        padding: 1.5rem 2rem;
        font-weight: 600;
        font-size: 1.1rem;
        border: none;
    }
    .form-card .card-header i {
        margin-right: 0.5rem;
    }
    .form-card .card-body {
        padding: 2rem;
    }
    
    /* Primary Card Header */
    .card-header-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    /* Success Card Header */
    .card-header-success {
        background: linear-gradient(135deg, #5cb85c 0%, #27ae60 100%);
        color: white;
    }
    
    /* Form Controls */
    .form-control-modern, .form-select-modern {
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        background-color: white;
        color: #1a202c;
    }
    .form-control-modern:focus, .form-select-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        background-color: white;
        color: #1a202c;
    }
    .form-control-modern::placeholder {
        color: #a0aec0;
    }
    .form-select-modern option {
        color: #1a202c;
        background-color: white;
    }
    
    /* Labels */
    .form-label-modern {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    /* Address Card - New */
    .address-card-new {
        border: 2px dashed #27ae60;
        border-radius: 15px;
        background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
        transition: all 0.3s ease;
        position: relative;
    }
    .address-card-new:hover {
        border-color: #27ae60;
        box-shadow: 0 5px 20px rgba(39, 174, 96, 0.15);
        transform: translateY(-3px);
    }
    
    .address-card .card-body {
        padding: 2rem;
    }
    
    /* Address Title - New */
    .address-title-new {
        color: #27ae60;
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .address-title i {
        font-size: 1.2rem;
    }
    
    /* Close Button */
    .btn-close-modern {
        background: #ef4444;
        opacity: 1;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    .btn-close-modern:hover {
        background: #dc2626;
        transform: scale(1.1);
    }
    
    /* Buttons */
    .btn-modern {
        border-radius: 50px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border: none;
    }
    .btn-modern i {
        margin-right: 0.5rem;
    }
    
    .btn-modern-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .btn-modern-primary:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .btn-modern-success {
        background: linear-gradient(135deg, #5cb85c 0%, #27ae60 100%);
        color: white;
    }
    .btn-modern-success:hover {
        background: linear-gradient(135deg, #27ae60 0%, #5cb85c 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(92, 184, 92, 0.4);
        color: white;
    }
    
    .btn-modern-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
        color: white;
    }
    .btn-modern-secondary:hover {
        background: linear-gradient(135deg, #545b62 0%, #6c757d 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        color: white;
    }
    
    /* Add Address Button */
    .btn-add-address {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }
    .btn-add-address:hover {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        color: white;
    }
    .btn-add-address i {
        margin-right: 0.5rem;
    }

    /* Empty State */
    .empty-addresses {
        text-align: center;
        padding: 3rem 1rem;
        color: #ffffff;
    }
    .empty-addresses i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.8;
        color: #ffffff;
    }
    
    /* Info Text */
    .info-text {
        background: transparent;
        border: none;
        padding: 0.5rem 0;
        border-radius: 0;
        margin-bottom: 1rem;
    }
    .info-text i {
        color: #1a202c;
        margin-right: 0.5rem;
    }
    .info-text strong,
    .info-text {
        color: #1a202c;
    }
    .tt-suggestion {
        color: black;
    }
</style>

<div class="container">
    {{-- Header --}}
    <div class="gradient-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2 fw-bold"><i class="fas fa-user-plus"></i> เพิ่มลูกค้าใหม่</h1>
                <p class="mb-0 opacity-90">กรอกข้อมูลลูกค้าและที่อยู่จัดส่ง</p>
            </div>
            <a href="{{ route('customers.index') }}" class="btn btn-light btn-lg">
                <i class="fas fa-arrow-left"></i> กลับ
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> พบข้อผิดพลาด</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('customers.store') }}" method="POST">
        @csrf

        {{-- Customer Info Card --}}
        <div class="card form-card">
            <div class="card-header card-header-primary">
                <i class="fas fa-user-circle"></i> ข้อมูลลูกค้า
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label form-label-modern">
                            ชื่อลูกค้า <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-modern @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required placeholder="กรอกชื่อลูกค้า">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label form-label-modern">เบอร์โทร</label>
                        <input type="text" class="form-control form-control-modern @error('phone') is-invalid @enderror" 
                               name="phone" value="{{ old('phone') }}" 
                               placeholder="เช่น 0812345678" maxlength="10"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label form-label-modern">อีเมล</label>
                        <input type="email" class="form-control form-control-modern @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" placeholder="example@email.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label form-label-modern">ช่องทางซื้อ</label>
                        <select class="form-select form-select-modern @error('purchase_channel') is-invalid @enderror" 
                                name="purchase_channel">
                            <option value="">-- เลือกช่องทางซื้อ --</option>
                            <option value="facebook" {{ old('purchase_channel') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                            <option value="line" {{ old('purchase_channel') == 'line' ? 'selected' : '' }}>Line</option>
                            <option value="website" {{ old('purchase_channel') == 'website' ? 'selected' : '' }}>เว็บไซต์</option>
                            <option value="shopee" {{ old('purchase_channel') == 'shopee' ? 'selected' : '' }}>Shopee</option>
                            <option value="lazada" {{ old('purchase_channel') == 'lazada' ? 'selected' : '' }}>Lazada</option>
                            <option value="offline" {{ old('purchase_channel') == 'offline' ? 'selected' : '' }}>หน้าร้าน</option>
                        </select>
                        @error('purchase_channel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label form-label-modern">วิธีชำระเงิน</label>
                        <select class="form-select form-select-modern @error('payment_method') is-invalid @enderror" 
                                name="payment_method">
                            <option value="">-- เลือกวิธีชำระเงิน --</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>เงินสด (Cash)</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>โอน/พร้อมเพย์</option>
                            <option value="cash_on_delivery" {{ old('payment_method') == 'cash_on_delivery' ? 'selected' : '' }}>ชำระปลายทาง (COD)</option>
                            <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>บัตรเครดิต/เดบิต</option>
                            <option value="e_wallet" {{ old('payment_method') == 'e_wallet' ? 'selected' : '' }}>วอลเล็ต</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label form-label-modern">หมายเหตุ</label>
                        <textarea class="form-control form-control-modern @error('notes') is-invalid @enderror" 
                                  name="notes" rows="3" placeholder="เพิ่มหมายเหตุเกี่ยวกับลูกค้า (ถ้ามี)">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Address Card --}}
        <div class="card form-card">
            <div class="card-header card-header-success d-flex justify-content-between align-items-center">
                <span><i class="fas fa-map-marked-alt"></i> ที่อยู่จัดส่ง</span>
                <button type="button" class="btn btn-add-address" id="btn-add-address">
                    <i class="fas fa-plus-circle"></i> เพิ่มที่อยู่ใหม่
                </button>
            </div>
            <div class="card-body">
                <div class="">
                    <i class="fas fa-lightbulb"></i>
                    <strong>คำแนะนำ:</strong> กรอกรหัสไปรษณีย์ก่อน ข้อมูลตำบล/อำเภอ/จังหวัด จะขึ้นอัตโนมัติ
                </div>
                <div id="addresses-wrapper">
                    <div class="empty-addresses">
                        <i class="fas fa-map-marked-alt"></i>
                        <p class="mb-0">ยังไม่มีที่อยู่จัดส่ง<br>กดปุ่ม "เพิ่มที่อยู่ใหม่" เพื่อเพิ่มที่อยู่</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end gap-3 mb-5">
            <a href="{{ route('customers.index') }}" class="btn btn-modern btn-modern-secondary btn-lg px-5">
                <i class="fas fa-times"></i> ยกเลิก
            </a>
            <button type="submit" class="btn btn-modern btn-modern-success btn-lg px-5">
                <i class="fas fa-save"></i> บันทึกลูกค้า
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- jQuery Thailand.js Library --}}
<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/JQL.min.js"></script>
<script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/typeahead.bundle.js"></script>
<link rel="stylesheet" href="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.css">
<script type="text/javascript" src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.js"></script>

<script>
(function(){
    let addressIndex = 0;

    function escapeHtml(str){
        if (str === null || str === undefined) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    }

    function addAddressRow(data = {}) {
        const wrapper = document.getElementById('addresses-wrapper');
        const alertBox = wrapper.querySelector('.empty-addresses');
        if(alertBox) alertBox.remove();

        const idx = addressIndex++;

        const name        = data.name        || '';
        const address     = data.address     || '';
        const soi         = data.soi         || '';
        const road        = data.road        || '';
        const subdistrict = data.subdistrict || '';
        const district    = data.district    || '';
        const province    = data.province    || '';
        const postal_code = data.postal_code || '';

        const html = `
            <div class="address-card address-card-new mb-4" data-index="${idx}">
                <div class="card-body">
                    <button type="button" class="btn-close btn-close-modern position-absolute top-0 end-0 m-3 btn-remove-address" aria-label="Close"></button>
                    
                    <h6 class="address-title address-title-new">
                        <i class="fas fa-map-pin"></i>
                        ที่อยู่ที่ ${idx + 1}
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-12 mb-2">
                            <label class="form-label form-label-modern">ชื่อที่อยู่ (เช่น บ้าน, ที่ทำงาน)</label>
                            <input type="text" class="form-control form-control-modern" 
                                   name="addresses[${idx}][name]" 
                                   value="${escapeHtml(name)}" 
                                   placeholder="ระบุชื่อเรียกสถานที่...">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label form-label-modern">
                                รหัสไปรษณีย์ <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-modern address-postal" 
                                   id="address-postal-${idx}"
                                   name="addresses[${idx}][postal_code]" 
                                   value="${escapeHtml(postal_code)}" 
                                   placeholder="กรอกเลขไปรษณีย์..." 
                                   autocomplete="off"
                                   required>
                        </div>
                        <div class="col-md-8 d-flex align-items-center">
                            <small class=" ms-2"><i class="fas fa-info-circle"></i> พิมพ์รหัสไปรษณีย์ ข้อมูลตำบล/อำเภอ/จังหวัด จะขึ้นอัตโนมัติ</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label form-label-modern">
                                ตำบล/แขวง <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-modern address-subdistrict" 
                                   id="address-subdistrict-${idx}"
                                   name="addresses[${idx}][subdistrict]" 
                                   value="${escapeHtml(subdistrict)}" 
                                   required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label form-label-modern">
                                อำเภอ/เขต <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-modern address-district" 
                                   id="address-district-${idx}"
                                   name="addresses[${idx}][district]" 
                                   value="${escapeHtml(district)}" 
                                   required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label form-label-modern">
                                จังหวัด <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-modern address-province" 
                                   id="address-province-${idx}"
                                   name="addresses[${idx}][province]" 
                                   value="${escapeHtml(province)}" 
                                   required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label form-label-modern">
                                ที่อยู่ (บ้านเลขที่ / หมู่ / ซอย / ถนน) <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control form-control-modern address-text" 
                                      id="address-text-${idx}"
                                      name="addresses[${idx}][address]" 
                                      rows="2" 
                                      placeholder="บ้านเลขที่ หมู่ ซอย ถนน..."
                                      required>${escapeHtml(address)}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        `;

        wrapper.insertAdjacentHTML('beforeend', html);

        // เปิดใช้งาน jQuery Thailand.js สำหรับที่อยู่ใหม่
        setupThailandAddress(idx);
    }

    function setupThailandAddress(idx) {
        $.Thailand({
            $zipcode: $(`#address-postal-${idx}`),
            $district: $(`#address-subdistrict-${idx}`),
            $amphoe: $(`#address-district-${idx}`),
            $province: $(`#address-province-${idx}`),
            onDataFill: function(data){
                console.log('เลือกที่อยู่แล้ว:', data);
                $(`#address-text-${idx}`).focus();
            }
        });
    }

    document.getElementById('btn-add-address').addEventListener('click', function(){
        addAddressRow();
    });

    document.getElementById('addresses-wrapper').addEventListener('click', function(e){
        if (e.target.classList.contains('btn-remove-address') || e.target.closest('.btn-remove-address')) {
            const card = e.target.closest('.address-card');
            if (!card) return;
            
            if(!confirm('ยืนยันที่จะลบที่อยู่นี้?')) return;
            
            card.remove();
            
            // Show empty state if no addresses
            if(document.querySelectorAll('.address-card').length === 0) {
                document.getElementById('addresses-wrapper').innerHTML = `
                    <div class="empty-addresses">
                        <i class="fas fa-map-marked-alt"></i>
                        <p class="mb-0 ">ยังไม่มีที่อยู่จัดส่ง<br>กดปุ่ม "เพิ่มที่อยู่ใหม่" เพื่อเพิ่มที่อยู่</p>
                    </div>
                `;
            }
        }
    });
})();
</script>
@endpush