@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm" style="max-width: 800px; margin: 0 auto;">
        <div class="card-body p-5">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-6">
                    <h2 class="mb-1 text-primary">ใบเสร็จรับเงิน</h2>
                    <h6 class="text-muted">RECEIPT / INVOICE</h6>
                </div>
                <div class="col-6 text-end">
                    <h4 class="mb-1">#{{ $order->order_number }}</h4>
                    <p class="text-muted mb-0">วันที่: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <hr class="my-4">

            <!-- Address Info -->
            <div class="row mb-5">
                <div class="col-6">
                    <h6 class="text-uppercase text-muted fw-bold mb-3">ผู้ส่ง (Sender)</h6>
                    <address class="mb-0">
                        <strong>My Shop Name</strong><br>
                        123 Main Street<br>
                        Bangkok, Thailand 10110<br>
                        โทร: 081-234-5678<br>
                        อีเมล: contact@myshop.com
                    </address>
                </div>
                <div class="col-6">
                    <h6 class="text-uppercase text-muted fw-bold mb-3">ลูกค้า (Customer)</h6>
                    <address class="mb-0">
                        <strong>{{ $order->customer->name }}</strong><br>
                        {{ $order->shipping_address }}<br>
                        โทร: {{ $order->customer->phone ?? '-' }}
                    </address>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive mb-5">
                <table class="table table-borderless">
                    <thead class="border-bottom">
                        <tr>
                            <th scope="col" class="py-3">รายการสินค้า (Description)</th>
                            <th scope="col" class="py-3 text-end" style="width: 100px;">จำนวน</th>
                            <th scope="col" class="py-3 text-end" style="width: 120px;">ราคาต่อหน่วย</th>
                            <th scope="col" class="py-3 text-end" style="width: 120px;">รวม (Total)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td class="py-3">
                                <div class="fw-bold">{{ $item->product_name }}</div>
                                <small class="text-muted">{{ $item->variant_name }}</small>
                            </td>
                            <td class="py-3 text-end">{{ $item->quantity }}</td>
                            <td class="py-3 text-end">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-3 text-end fw-bold">{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-top">
                        <tr>
                            <td colspan="3" class="text-end pt-4">รวมเป็นเงิน (Subtotal)</td>
                            <td class="text-end pt-4">{{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end text-muted">ค่าจัดส่ง (Shipping Fee)</td>
                            <td class="text-end text-muted">{{ number_format($order->shipping_fee, 2) }}</td>
                        </tr>
                        @if($order->discount > 0)
                        <tr>
                            <td colspan="3" class="text-end text-success">ส่วนลด (Discount)</td>
                            <td class="text-end text-success">-{{ number_format($order->discount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="text-end fw-bold h5 pt-3">ยอดสุทธิ (Grand Total)</td>
                            <td class="text-end fw-bold h5 pt-3 text-primary">{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Footer -->
            <div class="row mt-5">
                <div class="col-12 text-center text-muted">
                    <p class="mb-0">ขอบคุณที่อุดหนุนสินค้าของเรา</p>
                    <small>Thank you for your business</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body {
            background-color: white !important;
            -webkit-print-color-adjust: exact;
        }
        nav, .navbar, .btn, footer, .sidebar { /* Adjust selectors based on your layout */
            display: none !important;
        }
        .container {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>

<script>
    window.onload = function() {
        window.print();
    }
</script>
@endsection
