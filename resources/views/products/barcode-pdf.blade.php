<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Barcode {{ $product->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
        }
        .barcode-container {
            display: inline-block;
            width: 180px;
            height: 100px;
            margin: 5px;
            padding: 5px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .product-name {
            font-size: 10px;
            margin: 2px 0;
        }
        .product-price {
            font-size: 10px;
            font-weight: bold;
            margin: 2px 0;
        }
        .barcode-image {
            margin: 5px auto;
        }
        .sku {
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    @foreach($barcodes as $barcode)
    <div class="barcode-container">
        <div class="product-name">{{ $barcode['name'] }}</div>
        <div class="barcode-image">
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcode['sku'], 'C128', 1.2, 30) }}" alt="Barcode">
        </div>
        <div class="sku">{{ $barcode['sku'] }}</div>
        <div class="product-price">Rp {{ number_format($barcode['price'], 0, ',', '.') }}</div>
    </div>
    @endforeach
</body>
</html>

