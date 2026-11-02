@extends('layouts.app')

@section('content')
<div class="titleArea">
    <h2>LOGOUT</h2>
    <div class="title">ログアウト</div>
</div>

<section class="w-96 max-w-full mx-auto mt-10 px-4">
    <p class="text-slate-700 mb-6 text-center">ログアウトしますか？</p>
    <form method="POST" action="{{ route('member.logout') }}" class="flex flex-col gap-3">
        @csrf
        <button type="submit" class="af-btn af-btn-cancel">
            ログアウトする
        </button>
        <a href="{{ route('member.dashboard') }}" class="af-btn af-btn-cancel af-btn-sm">キャンセル（マイページへ）</a>
    </form>
</section>
@endsection
