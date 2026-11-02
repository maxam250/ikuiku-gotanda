@extends('layouts.app')

@section('breadcrumb_label', '入会完了')

@section('content')
<div class="titleArea">
    <h2>FAN CLUB</h2>
    <div class="title">入会完了</div>
</div>

<section class="contentArea text-center py-5">
    <div class="mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#28a745" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </svg>
    </div>

    <h3 class="mb-3">{{ $fanclub->title }} への入会が完了しました！</h3>
    <p class="text-muted mb-4">
        ご登録ありがとうございます。<br>
        これよりコンテンツをお楽しみいただけます。
    </p>

    <div class="d-flex justify-content-center gap-3">
        <a href="{{ route('member.fanclubs.show', $fanclub) }}" class="btn btn-primary">
            コンテンツを見る
        </a>
        <a href="{{ route('member.fanclubs.joined') }}" class="btn btn-outline-secondary">
            加入中のファンクラブ一覧
        </a>
    </div>
</section>
@endsection
