<x-member-layout>
<div class="titleArea">
	<h2>EMAIL SENT</h2>
	<div class="title">確認メール送信完了</div>
</div>

<section class="w-4/5 mx-auto mt-10 px-4 sm:px-0">
    <div class="email-sent-container">
        <div class="email-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mail-icon">
                <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" />
                <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" />
            </svg>
        </div>

        <h3 class="email-sent-title">確認メールを送信しました</h3>

        <div class="email-sent-message">
            <p>ご入力いただいたメールアドレスに、会員登録確認メールを送信いたしました。</p>

            <div class="instructions">
                <h4>次のステップ</h4>
                <ol>
                    <li>メールボックスをご確認ください</li>
                    <li>「【本登録のご案内】」という件名のメールを開いてください</li>
                    <li>メール内のリンクをクリックして、会員登録を完了してください</li>
                </ol>
            </div>

            <div class="notice">
                <p><strong>メールが届かない場合：</strong></p>
                <ul>
                    <li>迷惑メールフォルダをご確認ください</li>
                    <li>メールアドレスが正しく入力されているかご確認ください</li>
                    <li>数分経ってもメールが届かない場合は、下記から再送信してください</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="resend-section">
    <hr class="divider">
    <div class="resend-container">
        <p class="resend-text">確認メールが届かない場合は、こちらから再送できます。</p>
        <form method="POST" action="{{ route('member.resend.verification') }}" class="resend-form">
            @csrf
            <div class="form-row">
                <label class="resend-label">登録したメールアドレス</label>
                <input type="email" name="email" required class="resend-input" value="{{ old('email', $email ?? '') }}">
            </div>
            <button type="submit" class="resend-btn">確認メールを再送</button>
        </form>
        @if (session('message'))
            <div class="resend-message">{{ session('message') }}</div>
        @endif
    </div>
</section>

<style>
.email-sent-container {
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.email-icon {
    margin-bottom: 1.5rem;
}

.mail-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    display: block;
    color: #75b1da;
    animation: mailBounce 2s ease-in-out infinite;
}

@keyframes mailBounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

.email-sent-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 1.5rem;
}

.email-sent-message {
    text-align: left;
    color: #666;
    line-height: 1.6;
}

.email-sent-message > p {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 1rem;
    color: #333;
}

.instructions {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
    border-left: 4px solid #75b1da;
}

.instructions h4 {
    font-weight: bold;
    margin-bottom: 1rem;
    color: #333;
    font-size: 1rem;
}

.instructions ol {
    padding-left: 1.5rem;
    margin: 0;
}

.instructions li {
    margin-bottom: 0.5rem;
    color: #555;
}

.notice {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 6px;
    padding: 1rem;
}

.notice p {
    margin-bottom: 0.5rem !important;
    text-align: left !important;
    color: #856404 !important;
    font-weight: bold;
}

.notice ul {
    padding-left: 1.5rem;
    margin: 0;
}

.notice li {
    margin-bottom: 0.25rem;
    color: #856404;
    font-size: 0.9rem;
}

.resend-section {
    margin-top: 2rem;
    padding-top: 1rem;
}

.divider {
    border: none;
    border-top: 1px solid #e9ecef;
    margin: 1rem 0;
}

.resend-container {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.resend-text {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 1rem;
    text-align: center;
}

.resend-form {
    max-width: 400px;
    margin: 0 auto;
}

.form-row {
    margin-bottom: 1rem;
}

.resend-label {
    display: block;
    font-size: 0.85rem;
    color: #495057;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.resend-input {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 0.9rem;
    background: white;
}

.resend-input:focus {
    outline: none;
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.resend-btn {
    width: 100%;
    padding: 0.5rem 1rem;
    background: #3b68c7;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.resend-btn:hover {
    background: #1d4ed8;
}

.resend-message {
    margin-top: 1rem;
    padding: 0.75rem;
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    border-radius: 4px;
    font-size: 0.85rem;
    text-align: center;
}

@media (max-width: 768px) {
    .email-sent-container {
        padding: 1.5rem;
        margin: 0 1rem 2rem 1rem;
    }

    .email-sent-title {
        font-size: 1.25rem;
    }

    .instructions {
        padding: 1rem;
    }

    .resend-container {
        padding: 1rem;
        margin: 0 1rem;
    }

    .resend-text {
        font-size: 0.85rem;
    }

    .resend-input {
        font-size: 0.85rem;
    }

    .resend-btn {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
}
</style>


</x-member-layout>
