@extends('layouts.app')

@section('breadcrumb_label', $specialEvent->title)

@section('content')
<div class="titleArea">
    <h2>SPECIAL EVENT</h2>
    <div class="title">{{ $specialEvent->title }}</div>
</div>

<div class="se-page">

    {{-- メイン画像 --}}
    @if($specialEvent->main_image)
    <div class="se-main-image-wrap">
        <img src="{{ Storage::url($specialEvent->main_image) }}" class="se-main-image" alt="{{ $specialEvent->title }}">
        @if($specialEvent->has_timer && $specialEvent->timer_start && $specialEvent->timer_end)
            @php
                $now = now();
                $timerStart = $specialEvent->timer_start;
                $timerEnd = $specialEvent->timer_end;
            @endphp
            @if($now >= $timerStart && $now <= $timerEnd)
                <span class="se-timer-badge se-timer-now">開催中</span>
            @elseif($now < $timerStart)
                <span class="se-timer-badge se-timer-upcoming">開催予定</span>
            @endif
        @endif
    </div>
    @endif

    {{-- メタ情報 --}}
    <div class="event-card se-meta-card">
        <div class="se-meta-grid">
            <div class="se-meta-item">
                <i class="fas fa-calendar-alt"></i>
                <span>{{ $specialEvent->event_date->format('Y年m月d日') }}</span>
            </div>
            <div class="se-meta-item">
                <i class="fas fa-clock"></i>
                <span>{{ $specialEvent->start_time }}〜 {{ $specialEvent->end_time }}</span>
            </div>
            @if($specialEvent->venue)
            <div class="se-meta-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ $specialEvent->venue }}</span>
            </div>
            @endif
            @if($specialEvent->fee)
            <div class="se-meta-item">
                <i class="fas fa-yen-sign"></i>
                <span>¥{{ number_format($specialEvent->fee) }}
                    @if($specialEvent->fee_note) <small>（{{ $specialEvent->fee_note }}）</small>@endif
                </span>
            </div>
            @endif
        </div>
    </div>

    {{-- イベント内容 --}}
    @if($specialEvent->description)
    <div class="event-card">
        <h3>イベント内容</h3>
        <div>{!! html_entity_decode($specialEvent->description, ENT_QUOTES | ENT_HTML5, 'UTF-8') !!}</div>
    </div>
    @endif

    {{-- アクセス案内 --}}
    @if($specialEvent->access_info)
    <div class="event-card">
        <h3>アクセス案内</h3>
        <p>{!! nl2br(e($specialEvent->access_info)) !!}</p>
    </div>
    @endif

    {{-- マップ --}}
    @if($specialEvent->map_url)
    <div class="event-card se-map-card">
        <h3>地図</h3>
        <div class="se-map-wrap">
            <iframe
                src="{{ $specialEvent->map_url }}"
                width="100%"
                height="350"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
    @endif

    {{-- 外部リンク --}}
    @if($specialEvent->external_link)
    <div class="event-card">
        <h3>詳細サイト</h3>
        <a href="{{ $specialEvent->external_link }}" target="_blank" rel="noopener" class="se-link-btn">
            <i class="fas fa-external-link-alt"></i> 詳細サイトを見る
        </a>
    </div>
    @endif

    {{-- 備考・注意事項 --}}
    @if($specialEvent->notes)
    <div class="event-card">
        <h3>備考・注意事項</h3>
        <p>{!! nl2br(e($specialEvent->notes)) !!}</p>
    </div>
    @endif

    {{-- 参加キャスト --}}
    @if($specialEvent->items->count() > 0)
    <div class="event-card">
        <h3>参加キャスト</h3>
        <div class="se-cast-grid">
            @foreach($specialEvent->items as $item)
            <a href="{{ route('items.show', $item->id) }}" class="se-cast-item">
                @if($item->thumbnail && $item->thumbnail->filename)
                    <img src="{{ Storage::url($item->thumbnail->filename) }}" alt="{{ $item->item_name }}" class="se-cast-img">
                @else
                    <div class="se-cast-placeholder"><i class="fas fa-user"></i></div>
                @endif
                <span class="se-cast-name">{{ $item->item_name }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 関連イベント --}}
    @if($relatedEvents->count() > 0)
    <div class="event-card">
        <h3>関連イベント</h3>
        <ul class="se-related-list">
            @foreach($relatedEvents as $relatedEvent)
            <li>
                <a href="{{ route('specialEvents.show', $relatedEvent->id) }}" class="se-related-link">
                    @if($relatedEvent->main_image)
                        <img src="{{ Storage::url($relatedEvent->main_image) }}" alt="{{ $relatedEvent->title }}" class="se-related-img">
                    @endif
                    <div class="se-related-info">
                        <span class="se-related-title">{{ $relatedEvent->title }}</span>
                        <span class="se-related-date">{{ $relatedEvent->event_date->format('Y年m月d日') }}</span>
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="se-back-link">
        <a href="{{ route('specialEvents.index') }}">← イベント一覧に戻る</a>
    </div>

</div>

<style>
.se-page {
    width: 1100px;
    max-width:90%;
    margin: 0 auto;
    padding: 0 1rem 3rem;
}

/* メイン画像 */
.se-main-image-wrap {
    position: relative;
    margin-bottom: 1.5rem;
    border-radius: 12px;
    overflow: hidden;
    background: #fff0f6;
}
.se-main-image {
    width: 100%;
    height: auto;
    display: block;
}
.se-timer-badge {
    position: absolute;
    top: 14px;
    right: 14px;
    color: #fff;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 700;
}
.se-timer-now { background: #ff2d78; }
.se-timer-upcoming { background: #4ecdc4; }

/* メタ情報 */
.se-meta-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem 1.5rem;
}
.se-meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.0rem;
    font-size: 0.95rem;
    color: #7a3b5a;
}
.se-meta-item i {
    color: #ff2d78;
    width: 16px;
    text-align: center;
}

/* event-card */
.event-card {
    border: 1.5px solid #ffe0ee;
    border-radius: 1.25rem;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #ffffff;
    color: #3d1a2e;
    box-shadow: 0 4px 24px rgba(255, 45, 120, 0.08);
}
.event-card h3{
    margin-bottom: 1.0rem;
    font-weight: bold;
    color: #ff2d78;
}

/* マップ */
.se-map-wrap {
    border-radius: 8px;
    overflow: hidden;
}

/* 外部リンクボタン */
.se-link-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #22c5b8;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    transition: opacity 0.2s;
}
.se-link-btn:hover {
    opacity: 0.85;
    color: #fff;
}

/* キャスト */
.se-cast-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: 1rem;
}
.se-cast-item {
    text-align: center;
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s;
}
.se-cast-item:hover { transform: translateY(-3px); }
.se-cast-img {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: cover;
    display: block;
    margin: 0 auto 0.4rem;
    border: 2px solid #2a2a3e;
}
.se-cast-placeholder {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: #252535;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.4rem;
    color: #555570;
    font-size: 1.5rem;
}
.se-cast-name {
    font-size: 0.82rem;
    font-weight: 600;
    color: #d8dcf0;
}

/* 関連イベント */
.se-related-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.se-related-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    color: inherit;
    padding: 0.5rem;
    border-radius: 8px;
    transition: background 0.2s;
}
.se-related-link:hover { background: #252535; }
.se-related-img {
    width: 56px;
    height: 56px;
    min-width: 56px;
    border-radius: 8px;
    object-fit: cover;
}
.se-related-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.se-related-title {
    font-weight: 600;
    font-size: 0.95rem;
    color: #d8dcf0;
}
.se-related-date {
    font-size: 0.82rem;
    color: #6a70a0;
}

/* 戻るリンク */
.se-back-link {
    margin-top: 1rem;
    text-align: center;
}
.se-back-link a {
    color: #22c5b8;
    text-decoration: none;
    font-size: 0.9rem;
}
.se-back-link a:hover { text-decoration: underline; }

@media (max-width: 600px) {
    .se-cast-grid {
        grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
    }
    .se-cast-img, .se-cast-placeholder {
        width: 60px;
        height: 60px;
    }
}
</style>
@endsection
