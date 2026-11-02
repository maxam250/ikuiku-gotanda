@extends('layouts.app')

@section('breadcrumb_label', 'ポイント購入完了')

@section('content')
<div class="titleArea">
    <h2>POINT</h2>
    <div class="title">購入手続き完了</div>
</div>

<section class="contentArea text-center py-5">
    <div class="mb-4" style="font-size: 3rem; color: #28a745;">&#10003;</div>
    <h3 class="mb-3">購入手続きが完了しました</h3>

    @if(session('success'))
        <p class="mb-3">{{ session('success') }}</p>
    @endif

    <p class="mb-4">
        決済の確認が完了次第、ポイントが付与されます。<br>
        通常は数分以内に反映されます。
    </p>

    <div class="d-flex gap-3 justify-content-center">
        <a href="{{ route('member.point-history') }}" class="btn btn-primary">ポイント履歴を確認</a>
        <a href="{{ route('member.points.purchase') }}" class="btn btn-secondary">続けて購入する</a>
    </div>
</section>
@endsection
