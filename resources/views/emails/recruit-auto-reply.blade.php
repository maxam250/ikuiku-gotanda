<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>求人応募ありがとうございます</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2563eb; border-bottom: 2px solid #2563eb; padding-bottom: 10px;">
            求人応募ありがとうございます
        </h2>

        <p>{{ $mailData['name'] }} 様</p>

        <p>この度は、KIRARI撮影会の求人応募をお送りいただき、ありがとうございます。</p>

        <p>以下の内容で応募を受け付けいたしました。</p>

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
            送信いただいた写真は添付ファイルとして受け取りました。
        </p>

        <h3 style="color: #2563eb; margin-top: 30px;">今後の流れ</h3>
        <ol style="margin-left: 20px; color: #666;">
            <li>送信いただいた情報を確認し、一次審査を行います。</li>
            <li>一次審査をパスされた方は面談の日程を調整いたします。</li>
            <li>面談後、採用とさせていただきましたら、宣材写真の撮影やスケジュールなど打ち合わせをいたします。</li>
        </ol>

        <p style="margin-top: 20px; color: #666;">
            審査結果は通常1週間以内にご連絡いたしますが、応募状況により遅れる場合がございます。<br>
            ご了承ください。
        </p>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #dee2e6;">

        <p style="font-size: 12px; color: #666;">
            このメールは自動送信されています。<br>
            送信日時: {{ now()->format('Y年m月d日 H:i') }}
        </p>

        <p style="font-size: 12px; color: #666;">
            ※このメールに返信いただいても回答できませんのでご了承ください。<br>
            お問い合わせは求人ページのフォームまたはLINEからお願いいたします。
        </p>
    </div>
</body>
</html> 