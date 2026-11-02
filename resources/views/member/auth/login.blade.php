@extends('layouts.app')

@section('content')
{{--
@auth
    <p>こんにちは、{{ Auth::user()->name }}さん！</p>
@endauth

@guest
    <p>ログインしていません。<a href="{{ route('member.login') }}">ログイン</a></p>
@endguest
--}}



<section id="loginArea">

<section class="title">
    <div class="text"><span>MEMBER LOGIN</span></div>
</section>


@if ($errors->any())
    <div class="error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif


    <form method="POST" action="{{ route('member.login') }}">
        @csrf
        @if(request()->has('redirect'))
            <input type="hidden" name="redirect" value="{{ request()->get('redirect') }}">
        @endif
        <input id="text" type="text" name="loginid" required autofocus placeholder="ログインID" required autofocus>
        <input id="password" type="password" name="password" required placeholder="パスワード">
        <button type="submit">ログイン</button>
    </form>


    <div class="forgot-password" style="margin-bottom:1rem;">
        <a href="{{ route('member.password.request') }}" class="forgot-link">パスワードを忘れた方はこちら</a>
    </div>
    <div class="register" style="text-align:center;">
        <a href="{{ route('member.register') }}" class="forgot-link">[ 会員新規登録 ]</a>
    </div>
    @if (Route::has('login'))
    <nav class="-mx-3 flex flex-1 justify-end">
        @auth('member')
            <a
                href="{{ route('member.dashboard') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
            >
                マイページ
            </a>
        @else
        {{--
            <a
                href="{{ route('member.login') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
            >
               ログイン
            </a>

            @if (Route::has('register'))
                <a
                    href="{{ route('member.register') }}"
                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
                >
                    会員登録
                </a>
            @endif
        --}}
        @endauth
    </nav>
    @endif
</section>
@endsection
