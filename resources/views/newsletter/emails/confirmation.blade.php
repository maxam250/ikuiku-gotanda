<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メールマガジン登録確認</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f9f9f9; padding: 20px; border-radius: 5px;">
        <h2 style="color: #333; margin-top: 0;">メールマガジン登録確認</h2>
        
        <p>この度は、メールマガジンにご登録いただき、ありがとうございます。</p>
        
        <p>以下のリンクをクリックして、登録を完了してください。</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $confirmUrl }}" style="display: inline-block; padding: 12px 30px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">登録を完了する</a>
        </div>
        
        <p style="font-size: 12px; color: #666;">もしこのメールに心当たりがない場合は、無視していただいて構いません。</p>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">
        
        <p style="font-size: 12px; color: #666;">
            メールマガジンの配信を解除する場合は、<a href="{{ $unsubscribeUrl }}">こちら</a>をクリックしてください。
        </p>
    </div>
</body>
</html>


