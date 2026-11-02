<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新しいメッセージが届きました</title>
    <style>
        body {
            font-family: 'Hiragino Sans', 'Hiragino Kaku Gothic ProN', 'Meiryo', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .message-content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .message-title {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 15px;
            color: #495057;
        }
        .message-body {
            white-space: pre-wrap;
            font-size: 14px;
            line-height: 1.8;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
        .reply-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .reply-notice strong {
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>新しいメッセージが届きました</h1>
        </div>

        <p>いつもご利用いただき、ありがとうございます。</p>
        <p>新しいメッセージが届きましたのでお知らせいたします。</p>

        <div class="message-content">
            @if($title)
                <div class="message-title">{{ $title }}</div>
            @endif
            <div class="message-body">{{ $messageBody }}</div>
        </div>

        <div class="reply-notice">
            <strong>返信について：</strong><br>
            このメールに直接返信していただくことで、メッセージのやり取りが可能です。
            お気軽にご返信ください。
        </div>

        <div class="footer">
            <p>このメールは自動送信されています。</p>
            <p>ご不明な点がございましたら、お気軽にお問い合わせください。</p>
        </div>
    </div>
</body>
</html>
