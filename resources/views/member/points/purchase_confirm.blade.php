@extends('layouts.app')

@section('breadcrumb_label', 'ポイント購入確認')

@section('content')
<div class="titleArea">
    <h2>POINT</h2>
    <div class="title">ポイント購入</div>
</div>

<section class="contentArea">
    <h3 class="mb-3">購入内容の確認</h3>

    <div class="mb-4 p-3 border rounded bg-light">
        <table class="table table-borderless mb-0">
            <tr>
                <th>パッケージ</th>
                <td>{{ $package['label'] }}</td>
            </tr>
            <tr>
                <th>付与ポイント</th>
                <td><strong>{{ number_format($package['points']) }}pt</strong></td>
            </tr>
            <tr>
                <th>決済金額</th>
                <td><strong>¥{{ number_format($package['price']) }}（一回限りの単発決済）</strong></td>
            </tr>
        </table>
    </div>

    @if($errors->has('purchase'))
        <div class="alert alert-danger">{{ $errors->first('purchase') }}</div>
    @endif

    @if($isDummy)
    <div class="alert alert-warning mb-3">
        <strong>[開発用ダミーモード]</strong> 実際の決済は行われません。
    </div>
    @endif

    <form method="POST" action="{{ route('member.points.purchase.post') }}">
        @csrf
        <input type="hidden" name="package_id" value="{{ $package['id'] }}">

        <div class="mb-3">
            <label class="d-flex align-items-center gap-2">
                <input type="checkbox" name="agreed_terms" value="1" id="agreed-terms"
                       {{ old('agreed_terms') ? 'checked' : '' }}>
                <span>内容を確認し、購入に同意します</span>
            </label>
            @error('agreed_terms')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <button id="submit-btn" type="submit" class="btn btn-primary" disabled>
                同意してお支払い画面へ進む（¥{{ number_format($package['price']) }}）
            </button>
            <a href="{{ route('member.points.purchase') }}" class="btn btn-secondary">戻る</a>
        </div>
    </form>
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
