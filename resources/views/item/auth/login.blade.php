<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>キャストログイン</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/item/login.css') }}">
<style>

</style>


</head>
<body>


{{--
@auth
    <p>こんにちは、{{ Auth::user()->name }}さん！</p>
@endauth

@guest
    <p>ログインしていません。<a href="{{ route('item.login') }}">ログイン</a></p>
@endguest
--}}



<section id="loginArea">

<section class="title">
    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
    <div class="text"><strong>{{ $shops->shop_name }}</strong><span>CAST LOGIN</span></div>
</section>


@if ($errors->any())
    <div class="error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif


    <form method="POST" action="{{ route('item.login') }}">
        @csrf
        <input id="text" type="text" name="loginid" required autofocus placeholder="ログインID" required autofocus>
        <input id="password" type="password" name="password" required placeholder="パスワード">
        <button type="submit">ログイン</button>
    </form>

    @if (Route::has('login'))   {{-- キャストログインの場合はログインページにリダイレクトする --}}
    <nav class="-mx-3 flex flex-1 justify-end">
        @auth
            <a
                href="{{ url('/item/dashboard') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
            >
                マイページ
            </a>
        @else
        {{--
            <a
                href="{{ route('item.login') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
            >
               ログイン
            </a>

            @if (Route::has('register'))
                <a
                    href="{{ route('item.register') }}"
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
</body>
</html>
