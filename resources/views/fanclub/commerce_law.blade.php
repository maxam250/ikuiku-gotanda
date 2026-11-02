@extends('layouts.app')

@section('breadcrumb_label', '特定商取引法に基づく表記')

@section('content')
<div class="titleArea">
    <h2>LEGAL</h2>
    <div class="title">特定商取引法に基づく表記</div>
</div>

<section class="contentArea">
    <div class="mb-4 p-3 border rounded bg-light">
        <p style="white-space: pre-line;">{{ $setting?->specified_commercial_transactions ?: '特定商取引法に基づく表記は現在準備中です。' }}</p>
    </div>
</section>
@endsection
