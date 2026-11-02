<!DOCTYPE html>
<html lang="ja">
<head><meta charset="UTF-8"></head>
<body style="font-family: sans-serif; color: #333; line-height: 1.6;">
<p>管理者様</p>

<p>ファンクラブへの新規入会がありました。</p>

<dl>
    <dt>ファンクラブ</dt>
    <dd>{{ $membership->fanclub->title ?? '—' }}</dd>
    <dt>キャスト</dt>
    <dd>{{ optional($membership->fanclub->cast)->name ?? '—' }}</dd>
    <dt>会員名</dt>
    <dd>{{ optional($membership->member)->name ?? '—' }}</dd>
    <dt>会員メール</dt>
    <dd>{{ optional($membership->member)->email ?? '—' }}</dd>
    <dt>入会日</dt>
    <dd>{{ optional($membership->started_at)->format('Y年m月d日 H:i') ?? now()->format('Y年m月d日 H:i') }}</dd>
</dl>

<p style="font-size:12px; color:#999;">
    このメールはシステムから自動送信されています。<br>
    {{ config('app.name') }}
</p>
</body>
</html>
