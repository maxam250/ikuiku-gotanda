<!DOCTYPE html>
<html lang="ja">
<head><meta charset="UTF-8"></head>
<body style="font-family: sans-serif; color: #333; line-height: 1.6;">
<p>{{ $membership->member->name ?? 'お客様' }} 様</p>

<p>
    <strong>{{ $membership->fanclub->title ?? 'ファンクラブ' }}</strong> において、利用規約違反が確認されましたので<br>
    運営より警告をお送りします。
</p>

<p><strong>理由：</strong> {{ $reason }}</p>

<p>
    繰り返し違反が確認された場合、会員資格の停止または即時退会処理となる場合があります。<br>
    ご不明点はサポートまでお問い合わせください。
</p>

<p style="font-size:12px; color:#999;">
    このメールはシステムから自動送信されています。<br>
    {{ config('app.name') }}
</p>
</body>
</html>
