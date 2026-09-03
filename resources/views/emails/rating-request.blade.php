<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Beri Ulasan - ENY LEATHER</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .logo { font-size: 24px; font-weight: bold; letter-spacing: -0.05em; margin: 0; }
        .greeting { font-size: 18px; margin-bottom: 15px; }
        .message-box { background: #f9f9f9; padding: 25px; border-radius: 5px; text-align: center; margin-bottom: 25px; }
        .btn { display: inline-block; background: #111; color: #fff; text-decoration: none; padding: 15px 30px; border-radius: 4px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.1em; font-weight: bold; margin-top: 15px; }
        .stars { color: #fbbf24; font-size: 24px; margin: 15px 0; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #888; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="logo">ENY LEATHER</h1>
        <p style="margin:0; font-size: 12px; letter-spacing: 0.2em; text-transform: uppercase;">Store</p>
    </div>
    
    <div class="greeting">
        Halo {{ $order->shipping_name }},
    </div>
    
    <div class="message-box">
        <p style="margin-top: 0;">Pesanan <strong>{{ $order->invoice_number }}</strong> Anda telah selesai. Kami harap Anda puas dengan produk kami.</p>
        
        <div class="stars">★★★★★</div>
        
        <p>Kami sangat menghargai jika Anda bersedia meluangkan waktu untuk memberikan ulasan. Ulasan Anda sangat berarti untuk membantu kami terus berkembang.</p>
        
        <a href="{{ $ratingUrl }}" class="btn">Berikan Rating</a>
    </div>
    
    <p style="font-size: 14px; text-align: center; color: #666;">Jika tombol di atas tidak berfungsi, Anda bisa menyalin link berikut ke browser Anda:<br>
    <a href="{{ $ratingUrl }}" style="word-break: break-all; color: #3b82f6;">{{ $ratingUrl }}</a></p>
    
    <div class="footer">
        Terima kasih atas kepercayaan Anda - ENY LEATHER Store<br>
        <a href="{{ url('/') }}" style="color: #888;">www.enyleather.com</a>
    </div>
</body>
</html>
