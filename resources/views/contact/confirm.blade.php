@extends('layouts.app')

@section('breadcrumb_label', 'お問い合わせ内容確認')

@section('content')

<div class="container">
    <div class="text-center">お問い合わせ内容確認</div>

    <div class="confirmation-content">
        <div class="confirmation-item">
            <label>お名前:</label>
            <span>{{ $contact['name'] }}</span>
        </div>

        <div class="confirmation-item">
            <label>メールアドレス:</label>
            <span>{{ $contact['email'] }}</span>
        </div>

        @if(!empty($contact['phone']))
        <div class="confirmation-item">
            <label>電話番号:</label>
            <span>{{ $contact['phone'] }}</span>
        </div>
        @endif

        <div class="confirmation-item">
            <label>問い合わせ種別:</label>
            <span>
                @php
                    $inquiryTypes = [
                        'general' => '一般的なお問い合わせ',
                        'reservation' => '予約について',
                        'cast' => 'キャストについてのお問い合わせ',
                        'other' => 'その他',
                    ];
                @endphp
                {{ $inquiryTypes[$contact['inquiry_type']] ?? $contact['inquiry_type'] }}
            </span>
        </div>

        <div class="confirmation-item">
            <label>件名:</label>
            <span>{{ $contact['subject'] }}</span>
        </div>

        <div class="confirmation-item">
            <label>お問い合わせ内容:</label>
            <div class="message-content">{{ e($contact['message']) }}</div>
        </div>

        @if($isSpam)
        <div class="alert alert-warning">
            <strong>注意:</strong> このメッセージはスパムの可能性があります。内容を確認してから送信してください。
        </div>
        @endif
    </div>

    <div class="form-actions">

        <form method="POST" action="{{ route('contact.confirm') }}" style="display: inline; margin-left: 10px;">
            @csrf
            <input type="hidden" name="name" value="{{ $contact['name'] }}">
            <input type="hidden" name="email" value="{{ $contact['email'] }}">
            <input type="hidden" name="phone" value="{{ $contact['phone'] }}">
            <input type="hidden" name="inquiry_type" value="{{ $contact['inquiry_type'] }}">
            <input type="hidden" name="subject" value="{{ $contact['subject'] }}">
            <input type="hidden" name="message" value="{{ $contact['message'] }}">
            <button type="submit" class="btn btn-secondary">修正する</button>
        </form>

        <form method="POST" action="{{ route('contact.store') }}" style="display: inline;">
            @csrf
            <input type="hidden" name="name" value="{{ $contact['name'] }}">
            <input type="hidden" name="email" value="{{ $contact['email'] }}">
            <input type="hidden" name="phone" value="{{ $contact['phone'] }}">
            <input type="hidden" name="inquiry_type" value="{{ $contact['inquiry_type'] }}">
            <input type="hidden" name="subject" value="{{ $contact['subject'] }}">
            <input type="hidden" name="message" value="{{ $contact['message'] }}">
            <button type="submit" class="btn btn-primary">送信する</button>
        </form>
    </div>
</div>

<style>
.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.confirmation-content {
    background: #f9f9f9;
    padding: 30px;
    border-radius: 8px;
    margin: 20px 0;
}

.confirmation-item {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.confirmation-item:last-child {
    border-bottom: none;
}

.confirmation-item label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
}

.confirmation-item span {
    display: block;
    color: #666;
    line-height: 1.5;
}

.message-content {
    white-space: pre-wrap;
    background: white;
    padding: 15px;
    border-radius: 4px;
    border: 1px solid #ddd;
    min-height: 100px;
}

.form-actions {
    text-align: center;
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

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
}

.alert {
    padding: 15px;
    margin: 20px 0;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeaa7;
}
</style>

@endsection
