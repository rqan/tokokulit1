<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice TOKO RAFI</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .logo { font-size: 24px; font-weight: bold; letter-spacing: -0.05em; margin: 0; }
        .greeting { font-size: 18px; margin-bottom: 15px; }
        .invoice-details { background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 25px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        .table th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em; color: #666; }
        .text-right { text-align: right !important; }
        .totals { width: 100%; margin-left: auto; margin-top: 20px; border-top: 2px solid #333; padding-top: 15px; }
        .totals-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 14px; }
        .grand-total { font-weight: bold; font-size: 16px; margin-top: 10px; }
        .payment-methods { margin-top: 30px; border: 1px solid #eee; padding: 20px; border-radius: 5px; }
        .method { margin-bottom: 15px; }
        .method:last-child { margin-bottom: 0; }
        .btn { display: inline-block; background: #111; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 4px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.1em; font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #888; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="logo">TOKO RAFI</h1>
        <p style="margin:0; font-size: 12px; letter-spacing: 0.2em; text-transform: uppercase;">Store</p>
    </div>
    
    <div class="greeting">
        Halo {{ $order->shipping_name }},
    </div>
    
    <p>Invoice untuk pesanan Anda sudah siap. Silakan lakukan pembayaran agar pesanan Anda dapat segera kami proses.</p>
    
    <div class="invoice-details">
        <p style="margin: 0;"><strong>No. Invoice:</strong> <span style="font-family: monospace;">{{ $order->invoice_number }}</span></p>
        <p style="margin: 5px 0 0 0;"><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y') }}</p>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th style="text-align: center;">Qty</th>
                <th class="text-right">Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td class="text-right" style="font-family: monospace;">Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="width: 250px; float: right;">
        <div class="totals-row">
            <span style="color: #666;">Subtotal:</span>
            <span style="font-family: monospace;">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="totals-row">
            <span style="color: #666;">Ongkos Kirim:</span>
            <span style="font-family: monospace;">Rp{{ number_format($order->shipping_fee, 0, ',', '.') }}</span>
        </div>
        @if($order->additional_fee > 0)
        <div class="totals-row">
            <span style="color: #666;">Biaya Tambahan:</span>
            <span style="font-family: monospace;">Rp{{ number_format($order->additional_fee, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="totals-row grand-total">
            <span>Total:</span>
            <span style="font-family: monospace;">Rp{{ number_format($order->grand_total, 0, ',', '.') }}</span>
        </div>
    </div>
    <div style="clear: both;"></div>
    
    <div class="payment-methods">
        <h3 style="margin-top: 0; font-size: 14px; text-transform: uppercase; letter-spacing: 0.1em;">Metode Pembayaran</h3>
        <p style="font-size: 14px; color: #666; margin-bottom: 20px;">Silakan transfer ke salah satu rekening berikut atau gunakan QRIS yang tersedia di halaman invoice:</p>
        
        @foreach($paymentMethods as $method)
            @if($method->type == 'bank_transfer')
            <div class="method">
                <strong style="display: block;">{{ $method->bank_name }}</strong>
                <span style="font-family: monospace; font-size: 16px;">{{ $method->account_number }}</span><br>
                <span style="font-size: 12px; color: #666;">a.n. {{ $method->account_holder }}</span>
            </div>
            @endif
        @endforeach
        
        @if($paymentMethods->where('type', 'qris')->count() > 0)
        <div class="method" style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed #ccc;">
            <strong>QRIS</strong><br>
            <span style="font-size: 12px; color: #666;">Scan kode QRIS melalui link di bawah ini.</span>
        </div>
        @endif
    </div>
    
    <div style="text-align: center;">
        <a href="{{ url('/invoice/' . $order->invoice_number) }}" class="btn">Lihat Invoice & Upload Bukti</a>
    </div>
    
    <div class="footer">
        Terima kasih telah berbelanja di TOKO RAFI<br>
        <a href="{{ url('/') }}" style="color: #888;">www.enyleather.com</a>
    </div>
</body>
</html>


