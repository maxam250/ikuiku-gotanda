<!DOCTYPE html>
<html lang="ja">
<head><meta charset="UTF-8"></head>
<body style="font-family: sans-serif; color: #333; line-height: 1.6;">
<p>{{ optional($membership->fanclub->cast)->name ?? 'キャスト' }} 様</p>

<p>
    あなたのファンクラブ <strong>「{{ $membership->fanclub->title ?? '' }}」</strong> に<br>
    新しい会員が入会しました。
</p>

<dl>
    <dt>会員名</dt>
    <dd>{{ optional($membership->member)->name ?? '—' }}</dd>
    <dt>入会日</dt>
    <dd>{{ optional($membership->started_at)->format('Y年m月d日') ?? now()->format('Y年m月d日') }}</dd>
</dl>

<p style="font-size:12px; color:#999;">
    このメールはシステムから自動送信されています。<br>
    {{ config('app.name') }}
</p>
</body>
</html>
