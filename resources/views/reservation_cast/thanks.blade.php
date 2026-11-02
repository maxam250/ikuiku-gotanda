@extends('layouts.app')

@section('content')
<div class="titleArea">
<h2>RESERVATION</h2>
<div class="title">ご予約申し込み完了</div>
</div>
<section class="formArea">

    <h2>予約申し込み完了</h2>
    <p>ご予約ありがとうございました。確認メールをお送りしましたのでご確認ください。<br>
    ※まだ予約は確定していませんので確認後ご連絡します。</p>
    <a href="{{ route('top') }}">ホームに戻る</a>
</section>

@endsection
