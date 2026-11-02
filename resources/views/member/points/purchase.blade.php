@extends('layouts.app')

@section('breadcrumb_label', 'ポイント購入')

@section('content')
<div class="titleArea">
    <h2>POINT</h2>
    <div class="title">ポイントを購入する</div>
</div>

<section class="contentArea">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <p class="mb-0">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <p class="mb-4">ポイントは各種サービスのご利用にお使いいただけます。<br>下記のパッケージからお選びください。</p>

    <div class="row g-3">
        @foreach($packages as $package)
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card h-100 text-center p-3">
                <div class="fw-bold fs-5 mb-1">{{ $package['label'] }}</div>
                <div class="text-muted mb-2">¥{{ number_format($package['price']) }}</div>
                <div class="fs-4 fw-bold text-primary mb-3">
                    {{ number_format($package['points']) }}<small class="fs-6">pt</small> 付与
                </div>
                <a href="{{ route('member.points.purchase.confirm', ['package' => $package['id']]) }}"
                   class="btn btn-primary mt-auto">
                    このパッケージを選択
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        <a href="{{ route('member.point-history') }}" class="btn btn-secondary btn-sm">ポイント履歴を見る</a>
    </div>
</section>
@endsection
