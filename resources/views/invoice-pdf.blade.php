<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice PDF — {{ $order->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #111; padding: 40px; margin: 0; background: #fff; }
        .header { display: flex; justify-content: space-between; border-b: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
        .eny-leather { font-size: 28px; font-weight: bold; letter-spacing: 2px; }
        .inv-title { text-align: right; }
        .inv-number { font-size: 18px; font-weight: bold; margin: 0; }
        .date { font-size: 12px; color: #666; }
        .grid { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .col { width: 48%; }
        .label { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #666; margin-bottom: 5px; }
        .val { font-size: 14px; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f4f4f4; text-align: left; padding: 10px; font-size: 11px; text-transform: uppercase; border-bottom: 1px solid #ccc; }
        td { padding: 12px 10px; font-size: 13px; border-bottom: 1px solid #eee; }
        .total-box { width: 300px; margin-left: auto; font-size: 13px; }
        .total-row { display: flex; justify-content: space-between; padding: 6px 0; }
        .grand-total { font-size: 16px; font-weight: bold; border-top: 2px solid #000; padding-top: 10px; margin-top: 10px; }
        .footer { margin-top: 50px; text-align: center; font-size: 11px; color: #888; border-top: 1px solid #eee; padding-top: 20px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #000; color: #fff; border: none; font-size: 12px; font-weight: bold; cursor: pointer;">🖨️ Cetak / Simpan sebagai PDF</button>
    </div>

    <div class="header">
        <div>
            <div class="eny-leather">ENY LEATHER BOUTIQUE</div>
            <div class="date">Official Commercial Invoice</div>
        </div>
        <div class="inv-title">
            <div class="inv-number">{{ $order->invoice_number }}</div>
            <div class="date">{{ $order->created_at->format('d F Y, H:i') }} WIB</div>
        </div>
    </div>

    <div class="grid">
        <div class="col">
            <div class="label">Penerima & Alamat Pengiriman:</div>
            <div class="val">
                <strong>{{ $order->shipping_name }}</strong><br>
                {{ $order->shipping_phone }}<br>
                {{ $order->shipping_email }}<br>
                {{ $order->shipping_address }}
            </div>
        </div>
        <div class="col">
            <div class="label">Status Pesanan:</div>
            <div class="val">
                <strong style="text-transform: uppercase; color: #059669;">{{ $order->status }}</strong><br>
                Payment: {{ $order->payments->last()->payment_method ?? 'Gateway / Transfer' }}
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produk / Item</th>
                <th>Harga Satuan</th>
                <th style="text-align: center;">Jumlah</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>Rp{{ number_format($item->product_price, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Ongkos Kirim:</span>
            <span>Rp{{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}</span>
        </div>
        @if($order->discount_amount > 0)
            <div class="total-row" style="color: #dc2626;">
                <span>Diskon Voucher ({{ $order->voucher_code }}):</span>
                <span>-Rp{{ number_format($order->discount_amount, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="total-row grand-total">
            <span>TOTAL BAYAR:</span>
            <span>Rp{{ number_format($order->grand_total ?? $order->subtotal, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="footer">
        Terima kasih telah berbelanja di ENY LEATHER Boutique.<br>
        Dokumen ini merupakan bukti transaksi yang sah dari ENY LEATHER Commercial Store.
    </div>

    <script>
        // Auto trigger print dialogue when opened with ?print=1
        if (window.location.search.includes('print=1')) {
            window.onload = function() { window.print(); };
        }
    </script>

</body>
</html>

