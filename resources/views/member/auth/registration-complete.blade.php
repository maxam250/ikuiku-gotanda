@extends('layouts.app')

@section('content')
<div class="titleArea">
	<h2>REGISTRATION COMPLETE</h2>
	<div class="title">会員登録完了</div>
</div>

<section class="w-96 mx-auto my-10 px-4 sm:px-0">
    <div class="completion-container">
        <div class="success-icon">
            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark__check" fill="none" d="m14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>

        <h3 class="completion-title">会員登録が完了しました！</h3>

        <div class="completion-message">
            <p>ご登録いただき、ありがとうございます。</p>
            <p>会員サービスをご利用いただけるようになりました。</p>
        </div>

        <div class="completion-actions">
            <a href="{{ route('member.dashboard') }}" class="btn-dashboard">
                マイページへ
            </a>
            <a href="{{ route('top') }}" class="btn-home">
                トップページへ
            </a>
        </div>
    </div>
</section>

<style>
.completion-container {
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.success-icon {
    margin-bottom: 1.5rem;
}

.checkmark {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    display: block;
}

.checkmark__circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke-miterlimit: 10;
    stroke: #4CAF50;
    fill: none;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.checkmark__check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    stroke-width: 3;
    stroke-miterlimit: 10;
    stroke: #4CAF50;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}

@keyframes stroke {
    100% {
        stroke-dashoffset: 0;
    }
}

.completion-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 1rem;
}

.completion-message {
    margin-bottom: 2rem;
    color: #666;
    line-height: 1.6;
}

.completion-message p {
    margin-bottom: 0.5rem;
}

.completion-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-dashboard, .btn-home {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    min-width: 120px;
}

.btn-dashboard {
    background: #e91e63;
    color: white;
}

.btn-dashboard:hover {
    background: #c2185b;
    transform: translateY(-1px);
}

.btn-home {
    background: #f8f9fa;
    color: #333;
    border: 1px solid #dee2e6;
}

.btn-home:hover {
    background: #e9ecef;
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .completion-container {
        padding: 1.5rem;
        margin: 0 1rem;
    }

    .completion-title {
        font-size: 1.25rem;
    }

    .completion-actions {
        flex-direction: column;
        align-items: center;
    }

    .btn-dashboard, .btn-home {
        width: 100%;
        max-width: 200px;
    }
}
</style>

@endsection
