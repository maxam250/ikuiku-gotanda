<x-app-layout>
    <x-slot name="header">
        予約完了
    </x-slot>

    <div class="complete-container">
        <div class="complete-box">
            <div class="icon-check">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>

            <h2>ご予約ありがとうございます</h2>

            <p class="message">
                予約が完了しました。<br>
                ご登録いただいたメールアドレスに確認メールをお送りしましたので、ご確認ください。
            </p>

            <div class="button-group">
                <a href="{{ route('home') }}" class="btn primary">トップページへ戻る</a>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
.complete-container {
    max-width: 600px;
    margin: 3rem auto;
    padding: 0 1rem;
}

.complete-box {
    background: white;
    padding: 3rem 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.icon-check {
    color: #4CAF50;
    margin-bottom: 1.5rem;
}

h2 {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 1.5rem;
}

.message {
    color: #666;
    line-height: 1.6;
    margin-bottom: 2rem;
}

.button-group {
    margin-top: 2rem;
}

.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    font-weight: bold;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn.primary {
    background: #4CAF50;
    color: white;
}

.btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}
</style>
