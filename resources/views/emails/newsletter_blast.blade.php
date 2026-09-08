<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="text-align: center; color: #111;">{{ config('app.name') }} Newsletter</h2>
        <div style="margin-top: 20px;">
            {!! nl2br(e($campaign->content)) !!}
        </div>
        <div style="margin-top: 40px; font-size: 12px; color: #777; text-align: center;">
            <p>Anda menerima email ini karena telah berlangganan newsletter kami.</p>
        </div>
    </div>
</body>
</html>
