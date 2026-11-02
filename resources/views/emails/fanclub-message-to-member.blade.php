<!DOCTYPE html>
<html lang="ja">
<head><meta charset="UTF-8"></head>
<body style="font-family:sans-serif;color:#333;max-width:600px;margin:0 auto;padding:20px;">
    <h2 style="color:#0f172a;">キャストからメッセージが届きました</h2>
    <p>ファンクラブ「{{ $fanclubMessage->fanclub->title }}」のキャストから返信が届きました。</p>
    <table style="width:100%;border-collapse:collapse;margin:16px 0;">
        <tr>
            <th style="text-align:left;padding:8px 12px;background:#f8fafc;width:120px;">キャスト</th>
            <td style="padding:8px 12px;border-bottom:1px solid #e2e8f0;">{{ $fanclubMessage->fanclub->cast->item_name ?? '' }}</td>
        </tr>
        <tr>
            <th style="text-align:left;padding:8px 12px;background:#f8fafc;">メッセージ</th>
            <td style="padding:8px 12px;border-bottom:1px solid #e2e8f0;">{{ $fanclubMessage->message }}</td>
        </tr>
        <tr>
            <th style="text-align:left;padding:8px 12px;background:#f8fafc;">送信日時</th>
            <td style="padding:8px 12px;">{{ $fanclubMessage->created_at->format('Y/m/d H:i') }}</td>
        </tr>
    </table>
    <p>会員マイページのファンクラブページからご確認ください。</p>
</body>
</html>
