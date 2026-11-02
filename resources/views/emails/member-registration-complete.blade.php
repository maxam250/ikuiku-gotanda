<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>会員登録完了のお知らせ</title>
</head>
<body>
    <p>{{ $member->name ?? 'お客様' }}様</p>

    <p>この度は会員登録をしていただき、誠にありがとうございます。</p>
    <p>登録が正常に完了いたしました。</p>

    <h3>■ ご登録内容 ■</h3>
    <p>お名前：{{ $member->name ?? '登録名' }}</p>
    <p>ログインID：{{ $member->loginid }}</p>

    <p>※パスワードはセキュリティ上の理由により記載しておりません。<br>
    ログイン時には、ご自身で設定されたパスワードをご利用ください。</p>

    <hr>

    <p>今後ともどうぞよろしくお願いいたします。</p>

    @if($shops)
    <p>■■■ {{ $shops->name }} ■■■</p>
    @endif
</body>
</html>
