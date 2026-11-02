@extends('layouts.app')

@section('content')
<div class="titleArea">
    <h2>MAIL MAGAZINE</h2>
    <div class="title">メールマガジン解除</div>
</div>
<div class="container px-4">
    <div class="newsletter-unsubscribe">
        @if($success)
            <div class="alert alert-success">
                <h3>解除完了</h3>
                <p>{{ $message }}</p>
            </div>
        @else
            <div class="alert alert-danger">
                <h3>エラー</h3>
                <p>{{ $message }}</p>
            </div>
        @endif

        <div class="back-link">
            <a href="{{ route('top') }}" class="btn btn-secondary">トップページに戻る</a>
        </div>
    </div>
</div>
@endsection


