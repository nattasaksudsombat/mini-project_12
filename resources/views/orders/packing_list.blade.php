<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing List #{{ $order->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; }
        .dashed-box { border: 2px dashed #000; padding: 20px; margin-bottom: 20px; border-radius: 8px; }
        .tracking-number { font-size: 2.5rem; font-weight: bold; letter-spacing: 2px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="bg-light p-4">

    <div class="container bg-white p-5 shadow-sm rounded">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h1 class="mb-0">ใบปะหน้าพัสดุ (Packing List)</h1>
            <button class="btn btn-primary no-print" onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer me-2" viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                </svg>
                พิมพ์เอกสาร
            </button>
        </div>

        <div class="row">
            <!-- Sender -->
            <div class="col-6">
                <div class="border p-3 h-100">
                    <small class="text-muted text-uppercase">ผู้ส่ง (From)</small>
                    <h5 class="fw-bold mt-2">My Shop Name</h5>
                    <p class="mb-0">
                        123 Main Street<br>
                        Bangkok, Thailand 10110<br>
                        Tel: 081-234-5678
                    </p>
                </div>
            </div>

            <!-- Receiver -->
            <div class="col-6">
                <div class="dashed-box h-100">
                    <small class="text-muted text-uppercase">ผู้รับ (To)</small>
                    <h4 class="fw-bold mt-2">{{ $order->customer->name }}</h4>
                    <p class="lead mb-2" style="font-size: 1.2rem; line-height: 1.6;">
                        {{ $order->shipping_address }}
                    </p>
                    <p class="fw-bold mb-0">โทร: {{ $order->customer->phone ?? '-' }}</p>
                </div>
            </div>
        </div>

        @if($order->tracking_number)
        <div class="mt-4 text-center border p-4 bg-light">
            <small class="text-muted text-uppercase d-block mb-1">Tracking Number</small>
            <div class="tracking-number">{{ $order->tracking_number }}</div>
            <!-- Barcode Placeholder -->
            <div class="mt-2" style="height: 50px; background: repeating-linear-gradient(90deg, #000 0px, #000 2px, #fff 2px, #fff 4px); width: 300px; margin: 0 auto; opacity: 0.5;"></div>
        </div>
        @endif

        <div class="mt-5">
            <h5 class="border-bottom pb-2 mb-3">รายการสินค้าที่ต้องจัดส่ง (Order Items)</h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 65%">สินค้า</th>
                        <th style="width: 30%" class="text-center">จำนวน</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->product_name }}</strong><br>
                            <small class="text-muted">{{ $item->variant_name }}</small>
                        </td>
                        <td class="text-center fw-bold fs-5">{{ $item->quantity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
             <p><strong>หมายเหตุ:</strong> {{ $order->notes ?? '-' }}</p>
             <p><strong>Order No:</strong> {{ $order->order_number }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Uncomment to auto-print
            // window.print();
        }
    </script>
</body>
</html>
