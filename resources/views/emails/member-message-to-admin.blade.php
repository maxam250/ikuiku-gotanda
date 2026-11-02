<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>会員からのメッセージ通知</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .message-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin: 15px 0;
        }
        .footer {
            font-size: 12px;
            color: #6c757d;
            text-align: center;
            margin-top: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .info-table th, .info-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #dee2e6;
            text-align: left;
        }
        .info-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>会員からのメッセージ通知</h2>
        <p>会員から新しいメッセージが届きました。</p>
    </div>

    <div class="content">
        <table class="info-table">
            <tr>
                <th>送信者</th>
                <td>会員ID: {{ $messageData->sender_id }}</td>
            </tr>
            @if($messageData->title)
            <tr>
                <th>件名</th>
                <td>{{ $messageData->title }}</td>
            </tr>
            @endif
            <tr>
                <th>送信日時</th>
                <td>{{ $messageData->created_at->format('Y年m月d日 H:i') }}</td>
            </tr>
        </table>

        <div class="message-box">
            <h4>メッセージ内容:</h4>
            <p>{!! nl2br(e($messageData->message)) !!}</p>
        </div>

        @if($messageData->image_path)
        <p><strong>画像:</strong> 添付画像があります</p>
        @endif
    </div>

    <div class="footer">
        <p>このメールは自動送信されています。</p>
        <p>管理画面からメッセージの確認・返信を行ってください。</p>
    </div>
</body>
</html>




