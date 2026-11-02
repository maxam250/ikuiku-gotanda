<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新しいメッセージが届きました</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #444; margin-bottom: 20px;">新しいメッセージが届きました</h1>

        <p>管理者からメッセージが届きました。</p>

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0;">{!! nl2br(e($messageData->message)) !!}</p>
        </div>

        <p>
            メッセージの確認・返信は以下のリンクからお願いします：<br>
            <a href="{{ route('member.messages.index') }}" style="color: #007bff; text-decoration: none;">メッセージを確認する</a>
        </p>

        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">

        <p style="font-size: 12px; color: #666;">
            ※このメールは自動送信されています。返信はできませんので、ご了承ください。
        </p>
    </div>
</body>
</html>
