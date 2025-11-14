<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>พิมพ์บาร์โค้ด</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 5mm;
            }
            body {
                margin: 0;
                padding: 0;
            }
        }

        body {
            font-family: 'Tahoma', sans-serif;
        }

        .barcode-label {
            width: 5cm;
            height: 3cm;
            padding: 5px;
            margin: 2mm;
            float: left;
            text-align: center;
            border: 1px dashed #ccc;
            box-sizing: border-box;
        }

        .barcode-img {
            width: 100%;
            max-height: 1.6cm;
            object-fit: contain;
        }

        .barcode-info {
            font-size: 12px;
            margin-top: 4px;
        }
    </style>
</head>
<body onload="window.print()">
    @for($i = 0; $i < $quantity; $i++)
        <div class="barcode-label">
            <img class="barcode-img" src="data:image/png;base64,{{ $barcode }}" alt="Barcode">
            <div class="barcode-info">{{ $codeText }}</div>
        </div>
    @endfor
</body>
</html>
