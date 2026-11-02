@extends('layouts.app')

@section('content')
<div class="titleArea">
    <h2>MAIL MAGAZINE</h2>
    <div class="title">メールマガジン登録</div>
</div>
<div class="container px-4">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('newsletter.register.post') }}" class="contact-form">
        @csrf
        <input type="hidden" name="form_time" value="{{ time() }}">

        <!-- ハニーポット -->
        <div style="display:none;">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
            <input type="text" name="url" tabindex="-1" autocomplete="off">
            <input type="text" name="phone_number" tabindex="-1" autocomplete="off">
        </div>

        <div class="form-group">
            <label for="email">メールアドレス <span class="required">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="form-control" placeholder="example@example.com">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">登録する</button>
        </div>
    </form>

    <div class="newsletter-info">
        <p>メールマガジンにご登録いただくと、最新情報やお得な情報をお届けします。</p>
        <p>登録後、確認メールをお送りしますので、メール内のリンクをクリックして登録を完了してください。</p>
    </div>
</div>

<x-recaptcha action="newsletter_register" />
@endsection


