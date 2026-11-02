@extends('layouts.app')

@section('breadcrumb_label', '入会確認')

@section('content')
<div class="titleArea">
    <h2>FAN CLUB</h2>
    <div class="title">入会確認・お支払い</div>
</div>

<section class="contentArea fanclub-section">
    <h3 class="mb-3">{{ $fanclub->title }} に入会する</h3>

    {{-- 入会規約 --}}
    <div class="mb-4 p-3 border rounded bg-light">
        <h4 class="mb-2">入会規約</h4>
        <p style="white-space: pre-line;">{{ $setting?->fanclub_join_terms ?: '入会規約は現在準備中です。内容をご確認のうえお進みください。' }}</p>
    </div>

    {{-- 支払い説明 --}}
    @if($fanclub->price == 0)
    <div class="mb-4 p-3 border rounded bg-light">
        <p class="mb-1 text-success fw-bold">このファンクラブの入会費は無料です</p>
        <p class="mb-0">入会後すぐにコンテンツをご利用いただけます。</p>
    </div>
    @else
    <div class="mb-4 p-3 border rounded bg-light">
        <h4 class="mb-2">支払い・決済について</h4>
        <p style="white-space: pre-line;">{{ $setting?->payment_description ?: '一度のみのお支払いです。入会後はコンテンツをご利用いただけます。' }}</p>
        <p class="mt-2 mb-0"><strong>入会費：¥{{ number_format($fanclub->price) }}</strong></p>
    </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('legal.sct') }}" class="text-gray-500 text-xs p-0">特定商取引法に基づく表記を見る</a>
    </div>

    {{-- エラー表示 --}}
    @if($errors->has('subscribe'))
        <div class="alert alert-danger">{{ $errors->first('subscribe') }}</div>
    @endif

    @if($isDummy)
    <div class="alert alert-warning mb-3">
        <strong>[開発用ダミーモード]</strong> 実際の決済は行われません。
    </div>
    @endif

    <form method="POST" action="{{ route('member.fanclubs.subscribe', $fanclub) }}">
        @csrf

        <div class="mb-3">
            <label class="d-flex align-items-center gap-2">
                <input type="checkbox" name="agreed_terms" value="1" id="agreed-terms"
                       {{ old('agreed_terms') ? 'checked' : '' }}>
                <span>入会規約に同意します</span>
            </label>
            @error('agreed_terms')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <button id="submit-btn" type="submit" class="btn btn-primary" disabled>
                @if($fanclub->price == 0)
                    同意して入会する
                @else
                    同意してお支払い画面へ進む（¥{{ number_format($fanclub->price) }}）
                @endif
            </button>
            <a href="{{ route('fanclub.show', $fanclub) }}" class="btn btn-secondary">戻る</a>
        </div>
    </form>

    @if($fanclub->price > 0)
    <p class="mt-3 mb-0" style="font-size:0.85rem; color:#6b7280;">
        銀行振り込みまたは店頭支払いをご希望の方は
        <a href="{{ route('contact') }}">メールにてお知らせください</a>。
    </p>
    @endif
</section>
@endsection

@push('scripts')
<script>
(function () {
    const checkbox = document.getElementById('agreed-terms');
    const btn      = document.getElementById('submit-btn');
    function sync() { btn.disabled = !checkbox.checked; }
    checkbox.addEventListener('change', sync);
    sync();
})();
</script>
@endpush
