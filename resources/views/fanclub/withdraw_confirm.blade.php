@extends('layouts.app')

@section('breadcrumb_label', '退会確認')

@section('content')
<div class="titleArea">
    <h2>FAN CLUB</h2>
    <div class="title">退会確認</div>
</div>

<section class="contentArea">
    <h3 class="mb-3">{{ $fanclub->title }} を退会しますか？</h3>

    <div class="mb-4 p-3 border rounded bg-light">
        <p style="white-space: pre-line;">{{ $setting?->fanclub_withdraw_message ?: '退会は即時反映されます。退会後は会員限定コンテンツを閲覧できません。' }}</p>
    </div>

    <form method="POST" action="{{ route('member.fanclubs.membership.update', $fanclub) }}">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" value="canceled">

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-danger">即時退会する</button>
            <a href="{{ route('fanclub.show', $fanclub) }}" class="btn btn-secondary">戻る</a>
        </div>
    </form>
</section>
@endsection
