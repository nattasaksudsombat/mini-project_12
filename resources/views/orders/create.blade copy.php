@extends('layouts.app')

@section('content')
<div class="container">
    <h1>สร้างออเดอร์ใหม่</h1>

    <form id="order-form" action="{{ route('orders.store') }}" method="POST">
        @csrf

        <h4>ข้อมูลลูกค้า</h4>
        <div class="mb-3">
            <label>ชื่อลูกค้า</label>
            <input type="text" name="customer[name]" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>ที่อยู่</label>
            <textarea name="customer[address]" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>ช่องทางการซื้อ</label>
            <select name="customer[purchase_channel]" class="form-control" required>
                <option value="facebook">Facebook</option>
                <option value="line">LINE</option>
                <option value="website">Website</option>
                <option value="shopee">Shopee</option>
                <option value="lazada">Lazada</option>
                <option value="offline">หน้าร้าน</option>
            </select>
        </div>
        <div class="mb-3">
            <label>วิธีชำระเงิน</label>
            <select name="customer[payment_method]" class="form-control" required>
                <option value="bank_transfer">โอนเงิน</option>
                <option value="cash_on_delivery">เก็บเงินปลายทาง</option>
                <option value="credit_card">บัตรเครดิต</option>
                <option value="e_wallet">E-Wallet</option>
            </select>
        </div>
        <div class="mb-3">
            <label>หมายเหตุ</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
        </div>

        <hr>

        <h5>ค้นหาสินค้า</h5>
        <input type="text" id="product-search" class="form-control" placeholder="ค้นหาชื่อสินค้า...">
        <div id="search-results" class="mt-2"></div>

        <h5 class="mt-4">สินค้าในออเดอร์</h5>
        <table class="table" id="order-items-table">
            <thead>
                <tr>
                    <th>สินค้า</th>
                    <th>สี-ไซส์</th>
                    <th>จำนวน</th>
                    <th>ราคาต่อหน่วย</th>
                    <th>รวม</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody id="order-items-body"></tbody>
        </table>

        <input type="hidden" name="items_json" id="items-json">

        <div class="mb-3">
            <label>ค่าจัดส่ง</label>
            <input type="number" name="shipping_fee" class="form-control" value="0" required>
        </div>
        <div class="mb-3">
            <label>ส่วนลด</label>
            <input type="number" name="discount" class="form-control" value="0">
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <button type="button" class="btn btn-success" onclick="submitOrder()">บันทึกออเดอร์</button>
    </form>
</div>

<!-- Modal เลือกสี-ไซส์ -->
<div class="modal fade" id="variantModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เลือกสี-ไซส์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <p><strong id="selected-product-name"></strong></p>
                <div class="mb-3">
                    <label>เลือกสี-ไซส์</label>
                    <select id="variant-select" class="form-control">
                        <option value="">-- เลือก --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>จำนวน</label>
                    <input type="number" id="variant-quantity" class="form-control" value="1" min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary" onclick="confirmAddProduct()">เพิ่มสินค้า</button>
            </div>
        </div>
    </div>
</div>
@include('orders.partials.order-script')

@endsection
