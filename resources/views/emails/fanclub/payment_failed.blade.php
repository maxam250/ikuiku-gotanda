<!DOCTYPE html>
<html lang="ja">
<head><meta charset="UTF-8"></head>
<body style="font-family: sans-serif; color: #333; line-height: 1.6;">
<p>{{ $membership->member->name ?? 'お客様' }} 様</p>

<p>
    <strong>{{ $membership->fanclub->title ?? 'ファンクラブ' }}</strong> の会費お支払いに失敗しました。<br>
    カード情報・残高をご確認のうえ、お早めにご対応ください。
</p>

<p>
    お支払いが確認できない場合、会員資格が停止される場合があります。
</p>

<p style="font-size:12px; color:#999;">
    このメールはシステムから自動送信されています。<br>
    {{ config('app.name') }}
</p>
</body>
</html>
