@extends('layouts.app')

@section('breadcrumb_label', 'お問い合わせ完了')

@section('content')

<div class="container">
    <div class="complete-content">
        <div class="complete-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#28a745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <h1>お問い合わせありがとうございます</h1>

        <div class="complete-message">
            <p>お問い合わせを受け付けました。</p>
            <p>内容を確認の上、担当者よりご連絡いたします。</p>
            <p>しばらくお待ちください。</p>
        </div>

        <div class="complete-actions">
            <a href="{{ route('top') }}" class="btn btn-primary">トップページへ戻る</a>
        </div>
    </div>
</div>

<style>
.container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.complete-content {
    text-align: center;
    background: #f9f9f9;
    padding: 40px;
    border-radius: 8px;
    margin-top: 40px;
}

.complete-icon {
    margin-bottom: 20px;
}

.complete-content h1 {
    color: #333;
    margin-bottom: 20px;
    font-size: 24px;
}

.complete-message {
    margin: 30px 0;
    line-height: 1.8;
    color: #666;
}

.complete-message p {
    margin-bottom: 10px;
}

.complete-actions {
    margin-top: 30px;
}

.btn {
    padding: 12px 30px;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}
</style>

@endsection
