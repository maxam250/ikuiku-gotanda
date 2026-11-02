@extends('layouts.app')

@section('content')
<div class="titleArea">
	<h2>REGISTRATION</h2>
	<div class="title">会員登録</div>
</div>

    <section class="MessageArea">
        <div id="recaptcha-client-error" class="member-register-client-error" role="alert" hidden></div>

        @if($errors->any())
            <div class="member-register-validation-errors" role="alert">
                <strong>入力内容をご確認ください</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>

    <section class="w-96 mx-auto mt-10 px-4 sm:px-0">
    <form id="member-register-form" method="POST" action="{{ route('member.register.submit') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

       <!-- Birth Date -->
       {{-- <div class="mt-4">
            <x-input-label for="birth" :value="__('Birth Date')" />
            <x-text-input id="birth" class="block mt-1 w-full" type="date" name="birth" :value="old('birth')" required />
            <x-input-error :messages="$errors->get('birth')" class="mt-2" />
        </div> --}}
        <div class="mt-4">
            <x-input-label for="loginid" :value="__('ログインID')" />
            <x-text-input id="loginid" class="block mt-1 w-full" type="text" name="loginid" :value="old('loginid')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('loginid')" class="mt-2" />
        </div>
        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-center mt-4">
            <a class="af-btn af-btn-cancel" href="{{ route('member.login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>

        <div class="mt-4">
            <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-2" />
        </div>

        <x-honeypot />
    </form>

    <x-recaptcha action="member_register" form-id="member-register-form" error-container-id="recaptcha-client-error" />
    </section>





    <section class="resend-section">
        <hr class="divider">
        <div class="resend-container">
            <p class="resend-text">確認メールが届かない場合は、こちらから再送できます。</p>
            <form method="POST" action="{{ route('member.resend.verification') }}" class="resend-form">
                @csrf
                <div class="form-row">
                    <label class="resend-label">登録したメールアドレス</label>
                    <input type="email" name="email" required class="resend-input">
                </div>
                <button type="submit" class="af-btn af-btn-primary">確認メールを再送</button>
            </form>
            @if (session('message'))
                <div class="resend-message">{{ session('message') }}</div>
            @endif
        </div>
    </section>

<style>
.member-register-client-error {
    color: #b91c1c;
    background: #fee2e2;
    border: 1px solid #fecaca;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    line-height: 1.5;
}
.member-register-validation-errors {
    color: #b91c1c;
    background: #fef2f2;
    border: 1px solid #fecaca;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    text-align: left;
}
.member-register-validation-errors strong {
    display: block;
    margin-bottom: 0.5rem;
}
.member-register-validation-errors ul {
    margin: 0;
    padding-left: 1.25rem;
    list-style: disc;
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
    background: #6c757d;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.resend-btn:hover {
    background: #5a6268;
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
    /* フォームの幅を調整 */
    section {
        width: 100% !important;
        max-width: 100%;
        padding: 0 1rem;
    }

    /* フォーム内の余白を調整 */
    form {
        padding: 0 0.5rem;
    }

    /* 入力フィールドの余白を調整 */
    .block {
        margin-bottom: 1rem;
    }

    /* ボタンエリアの余白を調整 */
    .flex.items-center.justify-center {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        margin-top: 2rem;
        text-align: center;
    }

    .flex.items-center.justify-center a {
        text-align: center;
        padding: 0.5rem;
    }

    .flex.items-center.justify-center button {
        width: 100%;
        padding: 0.75rem 1rem;
        text-align: center;
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



    <!-- <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->

@endsection
