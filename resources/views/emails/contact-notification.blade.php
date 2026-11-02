<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新しいお問い合わせ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .field {
            margin-bottom: 15px;
        }
        .field-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        .field-value {
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 3px;
            border-left: 4px solid #007bff;
        }
        .message-content {
            white-space: pre-wrap;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 3px;
            border: 1px solid #dee2e6;
            min-height: 100px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>新しいお問い合わせが届きました</h2>
        <p>受信日時: {{ $contact->created_at->format('Y年m月d日 H:i') }}</p>
    </div>

    <div class="content">
        <div class="field">
            <div class="field-label">お名前</div>
            <div class="field-value">{{ $contact->name }}</div>
        </div>

        <div class="field">
            <div class="field-label">メールアドレス</div>
            <div class="field-value">{{ $contact->email }}</div>
        </div>

        @if(!empty($contact->phone))
        <div class="field">
            <div class="field-label">電話番号</div>
            <div class="field-value">{{ $contact->phone }}</div>
        </div>
        @endif

        <div class="field">
            <div class="field-label">問い合わせ種別</div>
            <div class="field-value">{{ $contact->inquiry_type_label }}</div>
        </div>

        <div class="field">
            <div class="field-label">件名</div>
            <div class="field-value">{{ $contact->subject }}</div>
        </div>

        <div class="field">
            <div class="field-label">お問い合わせ内容</div>
            <div class="message-content">{{ $contact->message }}</div>
        </div>

        @if($contact->is_spam)
        <div class="field">
            <div class="field-label" style="color: #dc3545;">⚠️ スパムの可能性</div>
            <div class="field-value" style="border-left-color: #dc3545; color: #dc3545;">
                このメッセージはスパムの可能性があります。内容を確認してください。
            </div>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>このメールは自動送信されています。</p>
        <p>管理画面から詳細を確認できます。</p>
    </div>
</body>
</html>
