<!DOCTYPE html>
<html lang="ja">
<head><meta charset="UTF-8"></head>
<body style="font-family: sans-serif; color: #333; line-height: 1.6;">
<p>{{ $membership->member->name ?? 'お客様' }} 様</p>

<p>
    <strong>{{ $membership->fanclub->title ?? 'ファンクラブ' }}</strong> の会費お支払いが完了しました。<br>
    引き続きコンテンツをお楽しみください。
</p>

<dl>
    <dt>入会日</dt>
    <dd>{{ optional($membership->started_at)->format('Y年m月d日') ?? now()->format('Y年m月d日') }}</dd>
</dl>

<p style="font-size:12px; color:#999;">
    このメールはシステムから自動送信されています。<br>
    {{ config('app.name') }}
</p>
</body>
</html>
