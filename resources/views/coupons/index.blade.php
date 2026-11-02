@extends('layouts.app')

@section('content')

    <div class="titleArea">
        <h2>COUPON</h2>
        <div class="title">クーポン</div>
    </div>
<div class="container coupon-index">
    @if($coupons->count() > 0)
        <ul class="coupon-list">
            @foreach($coupons as $coupon)
                <li class="coupon-row">
                    <a href="{{ route('coupons.show', $coupon->id) }}" class="coupon-row-link">
                        <div class="coupon-row-image">
                            @if($coupon->main_image)
                                <img src="{{ Storage::url($coupon->main_image) }}" class="coupon-row-img" alt="{{ $coupon->title }}" loading="lazy">
                            @else
                                <div class="coupon-row-no-image">
                                    <i class="fas fa-ticket-alt"></i>
                                    <span>クーポン</span>
                                </div>
                            @endif
                            @if(isset($coupon->discount_type) && $coupon->discount_type === 'percentage')
                                <span class="coupon-row-badge">{{ $coupon->discount_value }}% OFF</span>
                            @elseif(isset($coupon->discount_type) && $coupon->discount_type === 'fixed')
                                <span class="coupon-row-badge">¥{{ number_format($coupon->discount_value) }} OFF</span>
                            @endif
                        </div>
                        <div class="coupon-row-body">
                            <h2 class="coupon-row-title">{{ $coupon->title }}</h2>
                            @if(isset($coupon->discount_type))
                                <div class="coupon-row-meta">
                                    @if($coupon->discount_type === 'percentage')
                                        <span class="coupon-meta-item"><i class="fas fa-percentage"></i> {{ $coupon->discount_value }}% 割引</span>
                                    @elseif($coupon->discount_type === 'fixed')
                                        <span class="coupon-meta-item"><i class="fas fa-yen-sign"></i> ¥{{ number_format($coupon->discount_value) }} 割引</span>
                                    @endif
                                </div>
                            @endif
                            @if($coupon->description)
                                <p class="coupon-row-desc">{{ Str::limit($coupon->description, 120) }}</p>
                            @endif

                            <span class="coupon-row-cta">詳細を見る <i class="fas fa-chevron-right"></i></span>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>

        @if($coupons->hasPages())
            <nav class="coupon-pagination" aria-label="クーポン一覧のページネーション">
                {{ $coupons->links() }}
            </nav>
        @endif
    @else
        <div class="coupon-empty">
            <div class="coupon-empty-icon"><i class="fas fa-ticket-alt"></i></div>
            <h2 class="coupon-empty-title">現在、利用可能なクーポンはありません</h2>
            <p class="coupon-empty-text">しばらくしてから再度ご確認ください。</p>
            <a href="{{ route('top') }}" class="coupon-empty-link">トップへ戻る</a>
        </div>
    @endif
</div>

<style>
/* ---- ヘッダー ---- */
.coupon-index {
    width:94% !important;
    margin: 0 auto;
    padding-bottom: 3rem;

}
.coupon-index-header {
    text-align: center;
    margin-bottom: 2.5rem;
}
.coupon-index-title {
    font-size: 1.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: #333;
    margin: 0 0 0.25rem 0;
}
.coupon-index-subtitle {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    margin: 0 0 0.5rem 0;
}
.coupon-index-desc {
    font-size: 1rem;
    color: #666;
    margin: 0;
}

/* ---- 1列リスト（画像左・内容右） ---- */
.coupon-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}
.coupon-row {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: box-shadow 0.25s ease;
}
.coupon-row:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
}
.coupon-row-link {
    display: flex;
    align-items: stretch;
    min-height: 180px;
    text-decoration: none;
    color: inherit;
}

/* 行：画像（左） */
.coupon-row-image {
    position: relative;
    width: 280px;
    min-width: 280px;
    flex-shrink: 0;
    background: #f5f5f5;
    overflow: hidden;
}
.coupon-row-img {
    width: 100%;
    height: 100%;
    min-height: 180px;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.coupon-row:hover .coupon-row-img {
    transform: scale(1.03);
}
.coupon-row-no-image {
    width: 100%;
    height: 100%;
    min-height: 180px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #ccc;
    font-size: 0.9rem;
}
.coupon-row-no-image i {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}
.coupon-row-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ff2d78;
    color: #fff;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 700;
}

/* 行：本文（右） */
.coupon-row-body {
    flex: 1;
    padding: 1.25rem 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.coupon-row-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #333;
    margin: 0 0 0.5rem 0;
    line-height: 1.4;
}
.coupon-row-meta {
    margin-bottom: 0.5rem;
}
.coupon-meta-item {
    font-size: 0.9rem;
    color: #ff2d78;
    font-weight: 600;
}
.coupon-meta-item i {
    margin-right: 4px;
    opacity: 0.9;
}
.coupon-row-desc {
    font-size: 0.95rem;
    color: #555;
    line-height: 1.55;
    margin: 0 0 0.75rem 0;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.coupon-row-casts {
    margin-bottom: 0.75rem;
}
.coupon-casts-label {
    font-size: 0.75rem;
    color: #888;
    display: block;
    margin-bottom: 6px;
}
.coupon-cast-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.coupon-cast-badge {
    background: #f0f0f0;
    color: #555;
    padding: 4px 10px;
    border-radius: 14px;
    font-size: 0.8rem;
}
.coupon-cast-badge-more {
    background: #ff2d78;
    color: #fff;
}
.coupon-row-cta {
    font-size: 0.95rem;
    font-weight: 600;
    color: #ff2d78;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: auto;
}
.coupon-row-cta i {
    font-size: 0.75rem;
    transition: transform 0.2s ease;
}
.coupon-row:hover .coupon-row-cta i {
    transform: translateX(4px);
}

/* ページネーション */
.coupon-pagination {
    margin-top: 2.5rem;
    display: flex;
    justify-content: center;
}
.coupon-pagination nav {
    display: flex;
    gap: 4px;
}
.coupon-pagination .pagination {
    margin: 0;
}

/* 空状態 */
.coupon-empty {
    text-align: center;
    padding: 3rem 1.5rem;
    background: #fafafa;
    border-radius: 12px;
}
.coupon-empty-icon {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 1rem;
}
.coupon-empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
    margin: 0 0 0.5rem 0;
}
.coupon-empty-text {
    font-size: 0.95rem;
    color: #666;
    margin: 0 0 1.5rem 0;
}
.coupon-empty-link {
    display: inline-block;
    padding: 10px 24px;
    background: #ff2d78;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: opacity 0.2s ease;
}
.coupon-empty-link:hover {
    opacity: 0.9;
    color: #fff;
}

@media (max-width: 768px) {
    .coupon-index-header {
        margin-bottom: 1.75rem;
    }
    .coupon-index-title {
        font-size: 1.5rem;
    }
    .coupon-index-subtitle {
        font-size: 1.25rem;
    }
    .coupon-row-link {
        flex-direction: column;
        min-height: 0;
    }
    .coupon-row-image {
        width: 100%;
        min-width: 100%;
        height: 180px;
    }
    .coupon-row-img,
    .coupon-row-no-image {
        min-height: 180px;
        height: 180px;
    }
    .coupon-row-body {
        padding: 1rem 1.25rem;
    }
    .coupon-row-title {
        font-size: 1.1rem;
    }
}
</style>
@endsection
