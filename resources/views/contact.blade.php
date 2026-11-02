@extends('layouts.app')

@section('content')
<div class="titleArea">
<h2>CONTACT</h2>
<div class="title">お問い合わせ</div>
</div>
<div class="container">

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('contact.confirm') }}" class="contact-form">
        @csrf

        <div class="form-group">
            <label for="name">お名前 <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="form-control">
        </div>

        <div class="form-group">
            <label for="email">メールアドレス <span class="required">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="form-control">
        </div>

        <div class="form-group">
            <label for="phone">電話番号</label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="form-control">
        </div>

        <div class="form-group">
            <label for="inquiry_type">問い合わせ種別 <span class="required">*</span></label>
            <select id="inquiry_type" name="inquiry_type" required class="form-control">
                <option value="">選択してください</option>
                <option value="general" {{ old('inquiry_type') == 'general' ? 'selected' : '' }}>一般的なお問い合わせ</option>
                <option value="reservation" {{ old('inquiry_type') == 'reservation' ? 'selected' : '' }}>予約について</option>
                <option value="cast" {{ old('inquiry_type') == 'cast' ? 'selected' : '' }}>キャストについてのお問い合わせ</option>
                <option value="other" {{ old('inquiry_type') == 'other' ? 'selected' : '' }}>その他</option>
            </select>
        </div>

        <div class="form-group">
            <label for="subject">件名 <span class="required">*</span></label>
            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required class="form-control">
        </div>

        <div class="form-group">
            <label for="message">お問い合わせ内容 <span class="required">*</span></label>
            <textarea id="message" name="message" rows="8" required class="form-control">{{ old('message') }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">確認画面へ</button>
        </div>
    </form>
</div>



<style>
.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.contact-form {
    background: #f9f9f9;
    padding: 30px;
    border-radius: 8px;
    margin-top: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.required {
    color: red;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

textarea.form-control {
    resize: vertical;
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

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.alert ul {
    margin: 0;
    padding-left: 20px;
}
</style>

@endsection
