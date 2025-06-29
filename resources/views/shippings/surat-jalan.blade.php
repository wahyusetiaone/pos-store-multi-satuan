<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $shipping->number_shipping }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; margin: 0; padding: 0; }
        .a5 { width: 100%; max-width: 200mm; height: 139mm; margin: 0 auto; padding: 12px 10px 0 10px; box-sizing: border-box; }
        @page { size: A5 landscape; margin: 0; }
        .header { text-align: center; margin-bottom: 0; }
        .header h2 { margin: 0; }
        .kop-title { font-size: 16px; font-weight: bold; }
        .kop-address { font-size: 10px; }
        .kop-phone { font-size: 10px; }
        hr { border: 0; border-top: 2px solid #333; margin: 10px 0 18px 0; }
        .info-table { width: 100%; margin-bottom: 18px; font-size: 10px; }
        .info-table td { padding: 4px 8px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10px; }
        .items-table th, .items-table td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        .items-table th { background: #f2f2f2; }
        .footer { margin-top: 30px; width: 100%; font-size: 10px; }
        .footer td { text-align: center; padding: 20px 0 0 0; }
        .signature { height: 40px; }
    </style>
</head>
<body>
<div class="a5">
    <div class="header">
        @if(isset($shipping->store->logo) && $shipping->store->logo)
            <img src="{{ public_path('storage/' . $shipping->store->logo) }}" alt="Logo" style="height:50px; display:block; margin:0 auto 6px auto;">
        @endif
        <div class="kop-title">{{ $shipping->store->name ?? '-' }}</div>
        <div class="kop-address">{{ $shipping->store->address ?? '-' }}</div>
        <div class="kop-phone">Telp: {{ $shipping->store->phone ?? '-' }}</div>
    </div>
    <hr>
    <h4 style="text-align:center; margin-bottom:0;">SURAT JALAN</h4>
    <div style="text-align:center; margin-bottom:12px;">No: <strong>{{ $shipping->number_shipping }}</strong> &nbsp; | &nbsp; Tanggal: <strong>{{ date('d/m/Y', strtotime($shipping->shipping_date)) }}</strong></div>
    <table class="info-table">
        <tr>
            <td><strong>Tanggal Pengiriman</strong></td>
            <td>: {{ date('d/m/Y', strtotime($shipping->shipping_date)) }}</td>
            <td><strong>Tanggal Penerimaan</strong></td>
            <td>: ....................................</td>
        </tr>
        <tr>
            <td><strong>Supplier</strong></td>
            <td>: {{ $shipping->supplier }}</td>
            <td><strong>User</strong></td>
            <td>: {{ $shipping->user->name }}</td>
        </tr>
        <tr>
            <td><strong>Status</strong></td>
            <td>: {{ ucfirst($shipping->status) }}</td>
            <td><strong>Catatan</strong></td>
            <td>: {{ $shipping->note ?? '-' }}</td>
        </tr>
    </table>
    <table class="items-table">
        <thead>
            <tr>
                <th>No</th>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th>Qty Dikirim</th>
                <th>Checklist <span style="font-size:14px;">&#x2713;</span></th>
            </tr>
        </thead>
        <tbody>
            @foreach($shipping->items as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item->product->sku ?? '-' }}</td>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td style="text-align:center;"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <table class="footer">
        <tr>
            <td style="width:50%">&nbsp;</td>
            <td style="width:50%">&nbsp;</td>
        </tr>
        <tr>
            <td style="text-align:center;">Yang Menerima</td>
            <td style="text-align:center;">Yang Mengirim</td>
        </tr>
        <tr class="signature">
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="text-align:center;">(___________________)</td>
            <td style="text-align:center;">(___________________)</td>
        </tr>
    </table>
</div>
</body>
</html>
