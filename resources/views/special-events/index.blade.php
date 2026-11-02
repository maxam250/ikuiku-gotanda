@extends('layouts.app')

@section('breadcrumb_label', '特設イベント')

@section('content')

    <div class="titleArea">
        <h2>SPECIAL EVENTS</h2>
        <div class="title">特設イベント</div>
    </div>
<div class="container special-events-index">

    @if($specialEvents->count() > 0)
        <header class="se-index-header">
            <h1 class="se-index-title">SPECIAL EVENTS</h1>
            <p class="se-index-desc">現在開催中、告知中のイベントのご案内</p>
        </header>
        <div class="se-grid">
            @foreach($specialEvents as $specialEvent)
                <article class="se-card">
                    <a href="{{ route('specialEvents.show', $specialEvent->id) }}" class="se-card-link">
                        <div class="se-card-image-wrap">
                            @if($specialEvent->main_image)
                                <img src="{{ Storage::url($specialEvent->main_image) }}" class="se-card-img" alt="{{ $specialEvent->title }}" loading="lazy">
                            @else
                                <div class="se-card-no-image">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>イベント</span>
                                </div>
                            @endif
                            @if($specialEvent->has_timer && $specialEvent->timer_start && $specialEvent->timer_end)
                                @php
                                    $now = now();
                                    $timerStart = $specialEvent->timer_start;
                                    $timerEnd = $specialEvent->timer_end;
                                @endphp
                                @if($now >= $timerStart && $now <= $timerEnd)
                                    <span class="se-card-badge se-card-badge-now">開催中</span>
                                @elseif($now < $timerStart)
                                    <span class="se-card-badge se-card-badge-upcoming">開催予定</span>
                                @endif
                            @endif
                        </div>
                        <div class="se-card-body">
                            <h2 class="se-card-title">{{ $specialEvent->title }}</h2>
                            <div class="se-card-meta">
                                <div class="se-meta-row"><i class="fas fa-calendar-alt"></i> {{ $specialEvent->event_date->format('Y年m月d日') }}</div>
                                <div class="se-meta-row"><i class="fas fa-clock"></i> {{ $specialEvent->start_time }} - {{ $specialEvent->end_time }}</div>
                                @if($specialEvent->venue)
                                    <div class="se-meta-row"><i class="fas fa-map-marker-alt"></i> {{ Str::limit($specialEvent->venue, 24) }}</div>
                                @endif
                                @if($specialEvent->fee)
                                    <div class="se-meta-row"><i class="fas fa-yen-sign"></i> ¥{{ number_format($specialEvent->fee) }}</div>
                                @endif
                            </div>
                            @if($specialEvent->description)
                                <p class="se-card-desc">{{ Str::limit(strip_tags(html_entity_decode($specialEvent->description, ENT_QUOTES | ENT_HTML5, 'UTF-8')), 80) }}</p>
                            @endif
                            @if($specialEvent->items->count() > 0)
                                <div class="se-card-casts">
                                    <h3 class="se-casts-title">参加キャスト</h3>
                                    <div class="se-cast-preview-list">
                                        @foreach($specialEvent->items->take(4) as $item)
                                            <div class="se-cast-preview-item">
                                                @if($item->thumbnail && $item->thumbnail->filename)
                                                    <img src="{{ Storage::url($item->thumbnail->filename) }}" alt="{{ $item->item_name }}" class="se-cast-preview-img">
                                                @else
                                                    <div class="se-cast-preview-placeholder"><i class="fas fa-user"></i></div>
                                                @endif
                                                <span class="se-cast-preview-name">{{ Str::limit($item->item_name, 8) }}</span>
                                            </div>
                                        @endforeach
                                        @if($specialEvent->items->count() > 4)
                                            <div class="se-cast-preview-item se-cast-preview-more">
                                                <div class="se-cast-preview-placeholder se-cast-more-num">+{{ $specialEvent->items->count() - 4 }}</div>
                                                <span class="se-cast-preview-name">他</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="se-card-cta-wrap">
                                <span class="se-card-cta">詳細を見る <i class="fas fa-chevron-right"></i></span>
                            </div>
                        </div>
                    </a>
                </article>
            @endforeach
        </div>

        @if($specialEvents->hasPages())
            <nav class="se-pagination" aria-label="特設イベントのページネーション">
                {{ $specialEvents->links() }}
            </nav>
        @endif
    @else
        <div class="se-empty">
            <div class="se-empty-icon"><i class="fas fa-calendar-alt"></i></div>
            <h2 class="se-empty-title">現在、開催予定の特設イベントはありません</h2>
            <p class="se-empty-text">しばらくしてから再度ご確認ください。</p>
            <a href="{{ route('top') }}" class="se-empty-link">トップへ戻る</a>
        </div>
    @endif
</div>

<style>
/* ---- ヘッダー・余白 ---- */
.special-events-index {
    padding-bottom: 3rem;
    padding-top: 1rem;
}
.se-index-header {
    text-align: center;
    margin-bottom: 2rem;
    padding: 0 0.5rem;
}
.se-index-title {
    font-size: 1.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: #333;
    margin: 0 0 0.35rem 0;
}
.se-index-subtitle {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    margin: 0 0 0.35rem 0;
}
.se-index-desc {
    font-size: 1rem;
    color: #666;
    margin: 0;
}

/* ---- グリッド（PC/タブレット3列・モバイル2列） ---- */
.se-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin: 0 auto;
    max-width: 1200px;
}
.se-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: box-shadow 0.25s ease, transform 0.25s ease;
    display: flex;
    flex-direction: column;
}
.se-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    transform: translateY(-4px);
}
.se-card-link {
    display: flex;
    flex-direction: column;
    height: 100%;
    text-decoration: none;
    color: inherit;
}

.se-card-image-wrap {
    position: relative;
    aspect-ratio: 16 / 10;
    background: #f0f0f0;
    overflow: hidden;
}
.se-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.se-card:hover .se-card-img {
    transform: scale(1.03);
}
.se-card-no-image {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #ccc;
    font-size: 0.9rem;
}
.se-card-no-image i {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}
.se-card-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #fff;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
}
.se-card-badge-now {
    background: #ff2d78;
}
.se-card-badge-upcoming {
    background: #4ecdc4;
}

.se-card-body {
    padding: 1.25rem 1.25rem 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
}
.se-card-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #333;
    margin: 0 0 0.75rem 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.se-card-meta {
    margin-bottom: 0.75rem;
    font-size: 0.8rem;
    color: #666;
}
.se-meta-row {
    margin-bottom: 0.35rem;
}
.se-meta-row:last-child {
    margin-bottom: 0;
}
.se-meta-row i {
    margin-right: 6px;
    color: #22c5b8;
    width: 1em;
    text-align: center;
}
.se-card-desc {
    font-size: 0.875rem;
    color: #555;
    line-height: 1.5;
    margin: 0 0 1rem 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex-shrink: 0;
}
/* 参加キャスト：下段に配置 */
.se-card-casts {
    margin-top: auto;
    margin-bottom: 1rem;
    padding-top: 0.75rem;
    border-top: 1px solid #eee;
}
.se-casts-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #333;
    margin: 0 0 0.5rem 0;
}
.se-cast-preview-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 12px;
    align-items: flex-start;
}
.se-cast-preview-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    flex: 0 0 auto;
}
.se-cast-preview-img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #dee2e6;
    margin-bottom: 4px;
    display: block;
}
.se-cast-preview-placeholder {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #dee2e6;
    color: #999;
    font-size: 0.7rem;
    margin-bottom: 4px;
}
.se-cast-preview-placeholder.se-cast-more-num {
    background: #22c5b8;
    color: #fff;
    border-color: #22c5b8;
    font-size: 0.65rem;
    font-weight: 700;
}
.se-cast-preview-name {
    font-size: 0.7rem;
    color: #666;
    line-height: 1.2;
    max-width: 44px;
    word-break: break-all;
}
.se-card-cta-wrap {
    margin-top: auto;
    padding-top: 0.5rem;
}
.se-card-cta {
    font-size: 0.9rem;
    font-weight: 600;
    color: #22c5b8;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.se-card-cta i {
    font-size: 0.75rem;
    transition: transform 0.2s ease;
}
.se-card:hover .se-card-cta i {
    transform: translateX(4px);
}

.se-pagination {
    margin-top: 2.5rem;
    display: flex;
    justify-content: center;
}

.se-empty {
    text-align: center;
    padding: 3rem 1.5rem;
    background: #fafafa;
    border-radius: 12px;
}
.se-empty-icon {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 1rem;
}
.se-empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
    margin: 0 0 0.5rem 0;
}
.se-empty-text {
    font-size: 0.95rem;
    color: #666;
    margin: 0 0 1.5rem 0;
}
.se-empty-link {
    display: inline-block;
    padding: 10px 24px;
    background: #22c5b8;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: opacity 0.2s ease;
}
.se-empty-link:hover {
    opacity: 0.9;
    color: #fff;
}

/* タブレット: 3列維持 */
@media (max-width: 992px) {
    .se-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }
}

/* モバイル: 2列・余白調整 */
@media (max-width: 768px) {
    .special-events-index {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
    .se-index-header {
        margin-bottom: 1.5rem;
    }
    .se-index-title {
        font-size: 1.5rem;
    }
    .se-index-subtitle {
        font-size: 1.25rem;
    }
    .se-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    .se-card-body {
        padding: 1rem 1rem 1.25rem;
    }
    .se-card-title {
        font-size: 0.95rem;
    }
    .se-card-meta {
        font-size: 0.75rem;
        margin-bottom: 0.5rem;
    }
    .se-card-casts {
        margin-bottom: 0.75rem;
        padding-top: 0.5rem;
    }
    .se-cast-preview-list {
        gap: 8px 10px;
    }
}
</style>
@endsection
