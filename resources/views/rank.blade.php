@extends('layouts.app')
@section('breadcrumb_label', 'ランキング')
@section('content')
<div class="titleArea">
    <h2>RANKING</h2>
    <div class="title">ランキング</div>
</div>

{{-- 旧 ranking.tpl の構成：カテゴリボタン → (カテゴリ選択時) カテゴリ見出し → ランキングごとに No.1 大カード＋No.2 以降 2 列 --}}
<div class="rk-page">

    {{-- カテゴリボタン（ul.rank_cate） --}}
    @if($rankCates->isNotEmpty())
    <ul class="rk-cate">
        @foreach($rankCates as $rc)
        <li>
            <a href="{{ route('rank.index', $rc->id) }}" class="{{ $currentCate && $currentCate->id === $rc->id ? 'is-active' : '' }}">
                <img src="{{ asset('images/ranking/navi_crown.png') }}" alt="">{{ $rc->title }}
            </a>
        </li>
        @endforeach
    </ul>
    @endif

    {{-- カテゴリ見出し（.rank_titleArea）：カテゴリを選んだ時だけ --}}
    @if($currentCate)
    <section class="rk-cate-title">
        <div class="title">{{ $currentCate->title }}</div>
        @if($currentCate->body)
        <p class="text">{!! nl2br(e($currentCate->body)) !!}</p>
        @endif
    </section>
    @endif

    @forelse($ranks as $rank)
    @php $castRanks = $rank->itemRanks->filter(fn ($ir) => $ir->items)->sortBy('no')->values(); @endphp
    <section class="rk-section">
        <h3 class="rk-name">
            <span class="rk-name-icon"><img src="{{ asset('images/ranking/title_icon.png') }}" alt=""></span>
            {{ $rank->title }}
        </h3>
        @if($rank->body)
        <p class="rk-body">{!! nl2br(e($rank->body)) !!}</p>
        @endif

        <div class="rk-list">
            @foreach($castRanks as $ir)
            @php
                $cast  = $ir->items;
                $no    = (int) $ir->no;
                $img   = $cast->thumbnail ? Storage::url($cast->thumbnail->filename) : asset('images/dmy.jpg');
                $iconFile = public_path('images/ranking/title_r'.$no.'.png');
                $sizes = 'T'.$cast->item_height.' B'.$cast->item_b.($cast->item_cup ? '('.$cast->item_cup.')' : '').' W'.$cast->item_w.' H'.$cast->item_h;
            @endphp
            <div class="rk-box {{ $no === 1 ? 'rk-box--1' : '' }}">
                <div class="rk-img">
                    <a href="{{ route('items.show', $cast->id) }}">
                        <img src="{{ $img }}" alt="{{ $cast->item_name }}" loading="lazy">
                    </a>
                </div>
                <div class="rk-cont">
                    <div class="rk-icon">
                        @if(file_exists($iconFile))
                        <img src="{{ asset('images/ranking/title_r'.$no.'.png') }}" alt="No.{{ $no }}">
                        @else
                        <span class="rk-icon-text">No.{{ $no }}</span>
                        @endif
                    </div>
                    <div class="rk-name-area">
                        <div class="inner">
                            <div class="rk-cast-name">
                                <a href="{{ route('items.show', $cast->id) }}">{{ $cast->item_name }}@if($cast->item_age > 0) ({{ $cast->item_age }})@endif</a>
                            </div>
                            <div class="rk-cast-size">{{ $sizes }}</div>
                        </div>
                    </div>
                    @if($no === 1 && $ir->text)
                    <div class="rk-comment">{!! nl2br(e($ir->text)) !!}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @empty
    <p class="rk-empty">{{ $currentCate ? "現在、このカテゴリのランキングはありません。" : "現在、表示できるランキングはありません。" }}</p>
    @endforelse

</div>
@endsection
