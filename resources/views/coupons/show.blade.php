@extends('layouts.app')

@section('breadcrumb_label', $coupon->title)

@section('content')
<div class="container">

    <div class="titleArea">
        <h2>COUPON</h2>
        <div class="title">{{ $coupon->title }}</div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="coupon-detail">
                @if($coupon->main_image)
                    <div class="coupon-image-wrap mb-4">
                        <img src="{{ Storage::url($coupon->main_image) }}" class="coupon-detail-img" alt="{{ $coupon->title }}">
                        @if($coupon->discount_type === 'percentage')
                            <div class="discount-overlay">
                                {{ $coupon->discount_value }}% OFF
                            </div>
                        @elseif($coupon->discount_type === 'fixed')
                            <div class="discount-overlay">
                                ¥{{ number_format($coupon->discount_value) }} OFF
                            </div>
                        @endif
                    </div>
                @endif

                <h1 class="coupon-title">{{ $coupon->title }}</h1>

                <div class="coupon-meta mb-4">
                    @if($coupon->discount_type === 'percentage')
                        <div class="meta-item">
                            <i class="fas fa-percentage"></i>
                            <span>{{ $coupon->discount_value }}% 割引</span>
                        </div>
                    @elseif($coupon->discount_type === 'fixed')
                        <div class="meta-item">
                            <i class="fas fa-yen-sign"></i>
                            <span>¥{{ number_format($coupon->discount_value) }} 割引</span>
                        </div>
                    @endif

                    @if($coupon->min_amount)
                        <div class="meta-item">
                            <i class="fas fa-info-circle"></i>
                            <span>最低利用金額: ¥{{ number_format($coupon->min_amount) }}</span>
                        </div>
                    @endif

                    @if($coupon->valid_from && $coupon->valid_until)
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{ $coupon->valid_from->format('Y年m月d日') }} - {{ $coupon->valid_until->format('Y年m月d日') }}</span>
                        </div>
                    @endif

                    @if($coupon->usage_limit)
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span>利用制限: {{ $coupon->usage_limit }}回まで</span>
                        </div>
                    @endif
                </div>

                @if($coupon->description)
                <div class="coupon-description mb-4">
                    <h3>クーポン詳細</h3>
                    <p>{{ $coupon->description }}</p>
                </div>
                @endif

                @if($coupon->terms)
                    <div class="coupon-terms mb-4">
                        <h3>利用条件</h3>
                        <p>{{ $coupon->terms }}</p>
                    </div>
                @endif

                @if($coupon->items->count() > 0)
                    <div class="coupon-casts mb-4">
                        <h3>対象キャスト</h3>
                        <div class="cast-grid">
                            @foreach($coupon->items as $item)
                                <div class="cast-item">
                                    <a href="{{ route('items.show', $item->id) }}" class="cast-link">
                                        @if($item->thumbnail && $item->thumbnail->filename)
                                            <img src="{{ Storage::url($item->thumbnail->filename) }}" alt="{{ $item->item_name }}" class="cast-image">
                                        @else
                                            <div class="cast-image-placeholder">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                        <span class="cast-name">{{ $item->item_name }}</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="coupon-actions">
                    <a href="{{ route('reservation.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-calendar-check"></i>
                        予約する
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4" style="display: none;">
            <div class="sidebar">
                @if($relatedCoupons->count() > 0)
                    <div class="related-coupons">
                        <h3>関連クーポン</h3>
                        <div class="related-list">
                            @foreach($relatedCoupons as $relatedCoupon)
                                <div class="related-item">
                                    <a href="{{ route('coupons.show', $relatedCoupon->id) }}" class="related-link">
                                        @if($relatedCoupon->main_image)
                                            <img src="{{ Storage::url($relatedCoupon->main_image) }}" alt="{{ $relatedCoupon->title }}" class="related-image">
                                        @endif
                                        <div class="related-info">
                                            <h4>{{ $relatedCoupon->title }}</h4>
                                            @if($relatedCoupon->discount_type === 'percentage')
                                                <span class="related-discount">{{ $relatedCoupon->discount_value }}% OFF</span>
                                            @elseif($relatedCoupon->discount_type === 'fixed')
                                                <span class="related-discount">¥{{ number_format($relatedCoupon->discount_value) }} OFF</span>
                                            @endif
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.coupon-detail {
    background: white;
    padding: 2rem 10%;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
}

.coupon-image-wrap {
    position: relative;
    text-align: center;
    border-radius: 12px;
    overflow: hidden;
}

.coupon-detail-img {
    width: 100%;
    max-width:800px;
    margin:0 auto 5rem;
    object-fit: cover;
    display: block;
}

.coupon-image-wrap .discount-overlay {
    position: absolute;
    top: 16px;
    right: 16px;
    color: white;
    padding: 15px 20px;
    border-radius: 25px;
    font-size: 1.5rem;
    font-weight: bold;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
}

.coupon-breadcrumb {
    font-size: 0.9rem;
    color: #666;
    margin-top: 0.5rem;
}
.coupon-breadcrumb a {
    color: #ff6b6b;
    text-decoration: none;
}
.coupon-breadcrumb a:hover {
    text-decoration: underline;
}
.breadcrumb-sep {
    margin: 0 0.35rem;
}
.coupon-title {
    font-size: 2rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 1rem;
}

.coupon-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
}

.meta-item {
    display: flex;
    align-items: center;
    font-size: 1rem;
    color: #666;
}

.meta-item i {
    width: 20px;
    margin-right: 8px;
    color: #ff6b6b;
}

.coupon-description,
.coupon-terms,
.coupon-casts {
    margin-bottom: 2rem;
}

.coupon-description h3,
.coupon-terms h3,
.coupon-casts h3 {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 1rem;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 0.5rem;
}

.cast-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 1rem;
}

.cast-item {
    text-align: center;
}

.cast-link {
    display: block;
    text-decoration: none;
    color: inherit;
    transition: transform 0.3s ease;
}

.cast-link:hover {
    transform: translateY(-5px);
    text-decoration: none;
    color: inherit;
}

.cast-image {
    width: 100%;
    aspect-ratio: 3 / 4;
    object-fit: cover;
    border-radius: 4px;
    margin-bottom: 0.5rem;
}

.cast-image-placeholder {
    width: 100%;
    height: 120px;
    background: #f8f9fa;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #ccc;
    margin-bottom: 0.5rem;
}

.cast-name {
    font-size: 0.9rem;
    font-weight: bold;
    color: #333;
}

.coupon-actions {
    text-align: center;
    padding: 2rem 0;
}

.btn-primary {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    border: none;
    border-radius: 30px;
    padding: 15px 30px;
    font-size: 1.2rem;
    font-weight: bold;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
}

.sidebar {
    position: sticky;
    top: 2rem;
}

.related-coupons {
    background: white;
    padding: 1.5rem;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.related-coupons h3 {
    font-size: 1.3rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 1rem;
    border-bottom: 2px solid #ff6b6b;
    padding-bottom: 0.5rem;
}

.related-item {
    margin-bottom: 1rem;
    border-bottom: 1px solid #eee;
    padding-bottom: 1rem;
}

.related-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.related-link {
    display: flex;
    text-decoration: none;
    color: inherit;
    transition: transform 0.3s ease;
}

.related-link:hover {
    transform: translateX(5px);
    text-decoration: none;
    color: inherit;
}

.related-image {
    width: 72px;
    min-width: 72px;
    height: 72px;
    object-fit: cover;
    border-radius: 10px;
    margin-right: 1rem;
}

.related-info h4 {
    font-size: 1rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 0.5rem;
}

.related-discount {
    font-size: 0.9rem;
    color: #ff6b6b;
    font-weight: bold;
}

@media (max-width: 768px) {
    .coupon-title {
        font-size: 2rem;
    }

    .coupon-meta {
        flex-direction: column;
        gap: 0.5rem;
    }

    .cast-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
}
</style>

@endsection

