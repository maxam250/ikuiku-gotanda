<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f9f9f9; padding: 20px; border-radius: 5px;">
        {!! $body !!}
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p style="font-size: 12px; color: #666;">
            メールマガジンの配信を解除する場合は、<a href="{{ $unsubscribeUrl }}">こちら</a>をクリックしてください。
        </p>
    </div>
</body>
</html>


