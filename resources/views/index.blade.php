@extends('layouts.app')

@section('content')
<div>

    {{-- 旧サイトはヘッダー＋左ナビ構成のためメインビジュアルは使わない（管理画面で登録しても表示しない） --}}
    <div class="mv-scroll-content">

    {{-- 特設イベント --}}
    @if($specialEvents->isNotEmpty())
    <section class="top-special-events">
        <div class="tse-inner">
            <div class="tse-heading">
                <span class="tse-heading-en">SPECIAL EVENT</span>
                <span class="tse-heading-ja">スペシャルイベント</span>
            </div>
            <div class="tse-list {{ $specialEvents->count() === 1 ? 'tse-list--single' : '' }}">
                @foreach($specialEvents as $seEvent)
                <div class="tse-card">
                    <a href="{{ route('specialEvents.show', $seEvent->id) }}" class="tse-card-img-link">
                        @if($seEvent->main_image)
                            <img src="{{ Storage::url($seEvent->main_image) }}" alt="{{ $seEvent->title }}" class="tse-card-img" loading="lazy">
                        @else
                            <div class="tse-card-img-placeholder"><i class="fas fa-calendar-star"></i></div>
                        @endif
                    </a>
                    <div class="tse-card-body">
                        <h3 class="tse-card-title">{{ $seEvent->title }}</h3>
                        <div class="tse-card-meta">
                            <span><i class="fas fa-calendar-alt"></i> {{ $seEvent->event_date->format('Y年m月d日') }}</span>
                            <span><i class="fas fa-clock"></i> {{ $seEvent->start_time }}</span>
                        </div>
                        <a href="{{ route('specialEvents.show', $seEvent->id) }}" class="tse-card-btn">詳細はこちら &rsaquo;</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ─── 旧 top.tpl の順：イベント → 新着ニュース → 新人情報 → ピックアップ → ランキング → 当日スケジュール ─── --}}
    <div class="top-layout-wrap">

        {{-- イベントスライダー（top_event_sliderpro.tpl：メイン＋右に縦サムネイル） --}}
        @include('top_event')

        {{-- 新着ニュース（top_info.tpl）。公開中の記事がある時だけ表示 --}}
        @php $topNews = $news->where('active', 1)->take(3); @endphp
        @if($topNews->isNotEmpty())
        <section class="gtd-section" id="info">
            <h2 class="section-title">
                <span class="section-title-ja">新着ニュース</span>
                <span class="section-title-en">TOPICS</span>
            </h2>
            <div class="gtd-info-list">
                @foreach($topNews as $n)
                <article class="gtd-info-item" data-gtd-more>
                    <div class="gtd-info-body">
                        @if($n->filename)
                        <div class="gtd-info-img">
                            @if($n->link)<a href="{{ $n->link }}">@endif
                            <img src="{{ Storage::url('news/'.$n->filename) }}" alt="{{ $n->title }}" loading="lazy">
                            @if($n->link)</a>@endif
                        </div>
                        @endif
                        <div class="gtd-info-text">{!! $n->body !!}</div>
                    </div>
                    <button type="button" class="gtd-info-more"><i class="fas fa-chevron-down" aria-hidden="true"></i> <span>もっと見る</span></button>
                </article>
                @endforeach
            </div>
            <script>
            document.querySelectorAll('[data-gtd-more] .gtd-info-more').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var item = btn.closest('[data-gtd-more]');
                    var open = item.classList.toggle('is-open');
                    btn.querySelector('span').textContent = open ? '閉じる' : 'もっと見る';
                    btn.querySelector('i').className = 'fas ' + (open ? 'fa-chevron-up' : 'fa-chevron-down');
                });
            });
            </script>
        </section>
        @endif

        {{-- 新人情報（top_newface.tpl：PC 5枚・スマホ 3枚のスライダー） --}}
        @if($newfaces->isNotEmpty())
        <section class="gtd-section gtd-newface" id="newface">
            <h2 class="section-title">
                <span class="section-title-ja">新人情報</span>
                <span class="section-title-en">NEW FACE</span>
            </h2>
            <div class="gtd-newface-slider">
                <div class="swiper gtd-newface-swiper" id="newfaceSwiper">
                    <div class="swiper-wrapper">
                        @foreach($newfaces as $nf)
                        @continue(!$nf->item)
                        @php
                            $ni = $nf->item;
                            $nfEntry = $nf->date;
                        @endphp
                        <div class="swiper-slide">
                            <div class="gtd-card">
                                <div class="gtd-card-entry">
                                    @if($nfEntry){{ \Carbon\Carbon::parse($nfEntry)->format('m月d日') }} 入店@else&nbsp;@endif
                                </div>
                                <div class="gtd-card-img">
                                    <a href="{{ route('items.show', $ni->id) }}">
                                        <img src="{{ $ni->thumbnail ? Storage::url($ni->thumbnail->filename) : asset('images/dmy.jpg') }}"
                                             alt="{{ $ni->item_name }}" loading="lazy">
                                    </a>
                                    <span class="gtd-card-new"><img src="{{ asset('images/new.png') }}" alt="new"></span>
                                    @include('partials.gtd-cast-icons', ['item' => $ni])
                                </div>
                                <p class="gtd-card-name"><a href="{{ route('items.show', $ni->id) }}">{{ $ni->item_name }}@if($ni->item_age > 0)（{{ $ni->item_age }}）@endif</a></p>
                                <p class="gtd-card-size">T{{ $ni->item_height }} B{{ $ni->item_b }}@if($ni->item_cup)({{ $ni->item_cup }})@endif W{{ $ni->item_w }} H{{ $ni->item_h }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <div class="swiper-button-prev gtd-newface-prev"></div>
                <div class="swiper-button-next gtd-newface-next"></div>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function () {
                new Swiper('#newfaceSwiper', {
                    slidesPerView: 3,
                    spaceBetween: 5,
                    freeMode: true,
                    grabCursor: true,
                    pagination: { el: '#newfaceSwiper .swiper-pagination', clickable: true, dynamicBullets: true },
                    navigation: { nextEl: '.gtd-newface-next', prevEl: '.gtd-newface-prev' },
                    breakpoints: { 768: { slidesPerView: 4 }, 1100: { slidesPerView: 5 } },
                });
            });
            </script>
        </section>
        @endif

        {{-- ピックアップガール（top_pickup.tpl：カードを並べる） --}}
        @php $topPickups = $pickups->filter(fn ($p) => $p->item && $p->item->item_active == 1); @endphp
        @if($topPickups->isNotEmpty())
        <section class="gtd-section gtd-pickup" id="pickup">
            <h2 class="section-title">
                <span class="section-title-ja">ピックアップガール</span>
                <span class="section-title-en">PICKUP GIRL</span>
            </h2>
            <div class="gtd-card-list">
                @foreach($topPickups as $pickup)
                @php $pi = $pickup->item; @endphp
                <div class="gtd-card">
                    <div class="gtd-card-img">
                        <a href="{{ route('items.show', $pi->id) }}">
                            <img src="{{ $pi->thumbnail ? Storage::url($pi->thumbnail->filename) : asset('images/dmy.jpg') }}"
                                 alt="{{ $pi->item_name }}" loading="lazy">
                        </a>
                        @include('partials.gtd-cast-icons', ['item' => $pi])
                    </div>
                    <p class="gtd-card-name"><a href="{{ route('items.show', $pi->id) }}"><strong>{{ $pi->item_name }}</strong>@if($pi->item_age > 0)({{ $pi->item_age }})@endif</a></p>
                    <p class="gtd-card-size">T{{ $pi->item_height }} B{{ $pi->item_b }}@if($pi->item_cup)({{ $pi->item_cup }})@endif W{{ $pi->item_w }} H{{ $pi->item_h }}</p>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        {{-- ランキング（top_ranking.tpl） --}}
        @include('top_ranking')

        {{-- 当日スケジュール（top_sch.tpl） --}}
        <div id="today_sch">
            @include('top_schedule')
        </div>

    </div>{{-- /top-layout-wrap --}}

    </div>{{-- /mv-scroll-content --}}

</div>
@endsection
