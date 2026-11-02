<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/admin/login.css') }}">
<style>

</style>


</head>
<body>


{{--
@auth
    <p>こんにちは、{{ Auth::user()->name }}さん！</p>
@endauth

@guest
    <p>ログインしていません。<a href="{{ route('admin.login') }}">ログイン</a></p>
@endguest
--}}
<section id="loginArea">

<section class="title">
    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
    <div class="text"><strong>CAST UP SYSTEM</strong><span>LOGIN</span></div>
</section>


    <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <input id="login_name" type="text" name="login_name" required autofocus placeholder="ログインID">
        <input id="password" type="password" name="password" required placeholder="パスワード">
        <button type="submit">ログイン</button>
    </form>
    @error('login_name')
        <div>{{ $message }}</div>
    @enderror
{{--
    @if (Route::has('login'))
    <nav class="-mx-3 flex flex-1 justify-end">
        @auth
            <a
                href="{{ url('/dashboard') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
            >
                マイページ
            </a>
        @else

        @endauth
    </nav>
    @endif
--}}
</section>
</body>
</html>
