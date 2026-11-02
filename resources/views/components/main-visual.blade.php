@props(['mainVisualsPC', 'mainVisualsMobile'])

@if($mainVisualsPC->count() > 0 || $mainVisualsMobile->count() > 0)
<section class="main-visual-section">
    {{-- PC・タブレット用メインビジュアル --}}
    @if($mainVisualsPC->count() > 0)
    <div class="main-visual-pc hidden xl:block">
        @if($mainVisualsPC->count() == 1)
            {{-- 単一画像表示 --}}
            @php $visual = $mainVisualsPC->first(); @endphp



            <div class="main-visual-item">
                @if($visual->link_url)
                    <a href="{{ $visual->link_url }}" class="main-visual-link">
                        <img src="{{ Storage::url($visual->pc_image_path) }}"
                             alt="{{ $visual->title }}"
                             class="main-visual-image">
                    </a>
                @else
                    <img src="{{ Storage::url($visual->pc_image_path) }}"
                         alt="{{ $visual->title }}"
                         class="main-visual-image">
                @endif
            </div>
        @else
            {{-- スライダー表示 --}}
            <div class="swiper main-visual-swiper-pc">
                <div class="swiper-wrapper">
                    @foreach($mainVisualsPC as $visual)
                    <div class="swiper-slide">
                        @if($visual->link_url)
                            <a href="{{ $visual->link_url }}" class="main-visual-link">
                                <img src="{{ Storage::url($visual->pc_image_path) }}"
                                     alt="{{ $visual->title }}"
                                     class="main-visual-image">
                            </a>
                        @else
                            <img src="{{ Storage::url($visual->pc_image_path) }}"
                                 alt="{{ $visual->title }}"
                                 class="main-visual-image">
                        @endif
                    </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        @endif
    </div>
    @endif

    {{-- スマートフォン・タブレット用メインビジュアル --}}
    @if($mainVisualsMobile->count() > 0)
    <div class="main-visual-mobile block xl:hidden">
        @if($mainVisualsMobile->count() == 1)
            {{-- 単一画像表示 --}}
            @php $visual = $mainVisualsMobile->first(); @endphp
            <div class="main-visual-item">

                @if($visual->link_url)
                    <a href="{{ $visual->link_url }}" class="main-visual-link">
                        <img src="{{ Storage::url($visual->mobile_image_path) }}"
                             alt="{{ $visual->title }}"
                             class="main-visual-image">
                    </a>
                @else
                    <img src="{{ Storage::url($visual->mobile_image_path) }}"
                         alt="{{ $visual->title }}"
                         class="main-visual-image">
                @endif
            </div>
        @else
            {{-- スライダー表示 --}}
            <div class="swiper main-visual-swiper-mobile">
                <div class="swiper-wrapper">
                    @foreach($mainVisualsMobile as $visual)
                    <div class="swiper-slide">
                        @if($visual->link_url)
                            <a href="{{ $visual->link_url }}" class="main-visual-link">
                                <img src="{{ Storage::url($visual->mobile_image_path) }}"
                                     alt="{{ $visual->title }}"
                                     class="main-visual-image">
                            </a>
                        @else
                            <img src="{{ Storage::url($visual->mobile_image_path) }}"
                                 alt="{{ $visual->title }}"
                                 class="main-visual-image">
                        @endif
                    </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        @endif
    </div>
    @endif
</section>

<style>
.main-visual-section {
    position: fixed;
    top: calc(var(--app-header-sticky-offset, 96px) + var(--app-topnav-height, 0px));
    left: 50%;
    transform: translateX(-50%);
    width: calc(100% - 64px);
    max-width: var(--content-width, 1280px);
    height: calc(100svh - var(--app-header-sticky-offset, 96px) - var(--app-topnav-height, 0px));
    z-index: 0;
    overflow: hidden;
    margin: 0;
    padding: 0;
    transition: top 0.25s ease, height 0.25s ease;
}
@media (max-width: 1024px) {
    .main-visual-section {
        width: 100%;
    }
}

.main-visual-item,
.main-visual-swiper-pc,
.main-visual-swiper-mobile {
    width: 100%;
    height: 100%;
    position: relative;
}
.main-visual-description{
    position: absolute;
    bottom:0.5rem;
    right: 0.5rem;
    padding: 0.74rem;
    background: rgba(255, 240, 253, 0.75);
    color: #8f8086;
    border-radius: 50vmax;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-width: 90%;
    z-index: 48;
}
.main-visual-description .title {
    font-size: 0.95rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}
.main-visual-description .description {
    font-size: 0.75rem;
    line-height: 1.5;
}
.main-visual-image {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
    border: none;
    outline: none;
}

.main-visual-link {
    display: block;
    text-decoration: none;
    border: none;
    outline: none;
}

/* PC・スマホ共通（固定フルスクリーンのため min-height 不要） */
.main-visual-pc .main-visual-image,
.main-visual-mobile .main-visual-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Swiper カスタマイズ */
.main-visual-swiper-pc,
.main-visual-swiper-mobile {
    width: 100%;
    height: auto;
}

.main-visual-swiper-pc .swiper-pagination,
.main-visual-swiper-mobile .swiper-pagination {
    bottom: 20px;
    z-index: 10;
}

.main-visual-swiper-pc .swiper-pagination-bullet,
.main-visual-swiper-mobile .swiper-pagination-bullet {
    background: rgba(255, 255, 255, 0.7);
    opacity: 1;
    width: 12px;
    height: 12px;
}

.main-visual-swiper-pc .swiper-pagination-bullet-active,
.main-visual-swiper-mobile .swiper-pagination-bullet-active {
    background: #fff;
}

.main-visual-swiper-pc .swiper-button-next,
.main-visual-swiper-pc .swiper-button-prev,
.main-visual-swiper-mobile .swiper-button-next,
.main-visual-swiper-mobile .swiper-button-prev {
    color: #ff75a3;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    z-index: 10;
}

.main-visual-swiper-pc .swiper-button-next:after,
.main-visual-swiper-pc .swiper-button-prev:after,
.main-visual-swiper-mobile .swiper-button-next:after,
.main-visual-swiper-mobile .swiper-button-prev:after {
    font-size: 16px;
}

/* ホバーエフェクト */
.main-visual-link:hover .main-visual-image {
    transform: scale(1.02);
    transition: transform 0.3s ease;
}

.main-visual-image {
    transition: transform 0.3s ease;
}

/* レスポンシブブレークポイント調整 */
@media (max-width: 1279px) {
    .main-visual-pc {
        display: none !important;
    }
}

@media (min-width: 1280px) {
    .main-visual-mobile {
        display: none !important;
    }
}

@media (max-width: 1279px) {
    .main-visual-section {
        height: auto;
        aspect-ratio: 16 / 9;
    }

    .main-visual-mobile,
    .main-visual-mobile .main-visual-item,
    .main-visual-swiper-mobile {
        height: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($mainVisualsPC->count() > 1)
    new Swiper('.main-visual-swiper-pc', {
        loop: true,
        autoplay: { delay: 5000, disableOnInteraction: false },
        pagination: { el: '.main-visual-swiper-pc .swiper-pagination', clickable: true },
        navigation: {
            nextEl: '.main-visual-swiper-pc .swiper-button-next',
            prevEl: '.main-visual-swiper-pc .swiper-button-prev',
        },
    });
    @endif

    @if($mainVisualsMobile->count() > 1)
    new Swiper('.main-visual-swiper-mobile', {
        loop: true,
        autoplay: { delay: 5000, disableOnInteraction: false },
        pagination: { el: '.main-visual-swiper-mobile .swiper-pagination', clickable: true },
        navigation: {
            nextEl: '.main-visual-swiper-mobile .swiper-button-next',
            prevEl: '.main-visual-swiper-mobile .swiper-button-prev',
        },
    });
    @endif
});
</script>
@endif
