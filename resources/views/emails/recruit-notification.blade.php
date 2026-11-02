<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>求人応募のお知らせ</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2563eb; border-bottom: 2px solid #2563eb; padding-bottom: 10px;">
            求人応募のお知らせ
        </h2>

        <p>求人応募がありました。詳細は以下の通りです。</p>

        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; width: 30%;">お名前</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $mailData['name'] }}</td>
            </tr>
            @if($mailData['company'])
            <tr style="background-color: #f8f9fa;">
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">会社名</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $mailData['company'] }}</td>
            </tr>
            @endif
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">年齢</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $mailData['age'] }}歳</td>
            </tr>
            <tr style="background-color: #f8f9fa;">
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">メールアドレス</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $mailData['mail'] }}</td>
            </tr>
            @if($mailData['tel'])
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">電話番号</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $mailData['tel'] }}</td>
            </tr>
            @endif
            @if($mailData['lineid'])
            <tr style="background-color: #f8f9fa;">
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">LINE ID</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $mailData['lineid'] }}</td>
            </tr>
            @endif
            @if($mailData['url'])
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">SNS・ブログ等1</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $mailData['url'] }}</td>
            </tr>
            @endif
            @if($mailData['url2'])
            <tr style="background-color: #f8f9fa;">
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">SNS・ブログ等2</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $mailData['url2'] }}</td>
            </tr>
            @endif
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">接客業経験</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $mailData['exp'] }}</td>
            </tr>
            <tr style="background-color: #f8f9fa;">
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">認知経路</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $mailData['media'] }}</td>
            </tr>
            @if($mailData['bikou'])
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold;">その他PR・質問・申告事項等</td>
                <td style="padding: 10px; border: 1px solid #dee2e6; white-space: pre-wrap;">{{ $mailData['bikou'] }}</td>
            </tr>
            @endif
        </table>

        <p style="margin-top: 20px; color: #666;">
            写真は添付ファイルとして送信されています。<br>
            応募者への返信は {{ $mailData['mail'] }} までお願いします。
        </p>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #dee2e6;">

        <p style="font-size: 12px; color: #666;">
            このメールは求人応募フォームから自動送信されました。<br>
            送信日時: {{ now()->format('Y年m月d日 H:i') }}
        </p>
    </div>
</body>
</html> 