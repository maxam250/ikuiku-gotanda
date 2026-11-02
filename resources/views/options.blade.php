@extends('layouts.app')

@section('breadcrumb_label', 'オプション')

@section('title', 'オプション | ' . ($shops->shop_name ?? ''))

@section('content')

<div class="titleArea">
    <h2>OPTIONS</h2>
    <div class="title">オプション</div>
</div>

<div class="opt-page">

    {{-- ═══ 無料オプション ═══ --}}
    @php
        $hasFreeContent = $cates->filter(fn($c) => $c->options->isNotEmpty())->isNotEmpty()
                       || $freeOptions->isNotEmpty();
    @endphp

    <section class="opt-section" id="opt-free">
        <header class="opt-section-header">
            <div class="opt-section-deco" aria-hidden="true"></div>
            <div class="opt-section-meta">
                <span class="opt-type-chip opt-type-chip--free">OPTION</span>
                <h2 class="opt-section-title">オプション</h2>
            </div>
            <div class="opt-section-line"></div>
        </header>

        @if(!$hasFreeContent)
        <p class="opt-empty"><i class="fas fa-info-circle"></i> 現在、オプションは登録されていません。</p>
        @else

        {{-- カテゴリあり --}}
        @foreach($cates as $cate)
        @if($cate->options->isNotEmpty())
        <div class="opt-cate-group">
            @if($cate->title)
            <div class="opt-cate-divider">
                <span class="opt-cate-name opt-cate-name--free">
                    <i class="fas fa-chevron-right"></i>{{ $cate->title }}
                </span>
            </div>
            @endif
            <div class="opt-grid">
                @foreach($cate->options as $opt)
                <article class="opt-card opt-card--free"
                         data-title="{{ e($opt->title) }}"
                         data-body="{{ e($opt->body) }}"
                         data-type="free"
                         data-price=""
                         data-img="{{ $opt->filename ? Storage::url($opt->filename) : '' }}"
                         onclick="optOpenModal(this)"
                         role="button" tabindex="0">
                    <div class="opt-card-media">
                        @if($opt->filename)
                        <img class="opt-card-img"
                             src="{{ Storage::url($opt->filename) }}"
                             alt="{{ $opt->title }}"
                             loading="lazy"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="opt-card-no-img opt-card-no-img--free" style="display:none;"><i class="fa-solid fa-heart-circle-plus"></i></div>
                        @else
                        <div class="opt-card-no-img opt-card-no-img--free"><i class="fa-solid fa-heart-circle-plus"></i></div>
                        @endif
                        <span class="opt-card-chip opt-card-chip--free">FREE</span>
                    </div>
                    <div class="opt-card-info">
                        <div class="opt-card-title">{{ $opt->title }}</div>
                        @if($opt->body)
                        <div class="opt-card-excerpt">{{ $opt->body }}</div>
                        @endif
                        <div class="opt-card-cta opt-card-cta--free">詳細を見る <i class="fas fa-arrow-right"></i></div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach

        {{-- カテゴリなし --}}
        @if($freeOptions->isNotEmpty())
        <div class="opt-grid">
            @foreach($freeOptions as $opt)
            <article class="opt-card opt-card--free"
                     data-title="{{ e($opt->title) }}"
                     data-body="{{ e($opt->body) }}"
                     data-type="free"
                     data-price=""
                     data-img="{{ $opt->filename ? Storage::url($opt->filename) : '' }}"
                     onclick="optOpenModal(this)"
                     role="button" tabindex="0">
                <div class="opt-card-media">
                    @if($opt->filename)
                    <img class="opt-card-img"
                         src="{{ Storage::url($opt->filename) }}"
                         alt="{{ $opt->title }}"
                         loading="lazy"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="opt-card-no-img opt-card-no-img--free" style="display:none;"><i class="fa-solid fa-heart-circle-plus"></i></div>
                    @else
                    <div class="opt-card-no-img opt-card-no-img--free"><i class="fa-solid fa-heart-circle-plus"></i></div>
                    @endif
                    <span class="opt-card-chip opt-card-chip--free">FREE</span>
                </div>
                <div class="opt-card-info">
                    <div class="opt-card-title">{{ $opt->title }}</div>
                    @if($opt->body)
                    <div class="opt-card-excerpt">{{ $opt->body }}</div>
                    @endif
                    <div class="opt-card-cta opt-card-cta--free">詳細を見る <i class="fas fa-arrow-right"></i></div>
                </div>
            </article>
            @endforeach
        </div>
        @endif

        @endif
    </section>


    {{-- ═══ 有料オプション ═══ --}}
    {{-- -- 
    @php
        $hasPayContent = $cates->filter(fn($c) => $c->option_pay->isNotEmpty())->isNotEmpty()
                      || $payOptions->isNotEmpty();
    @endphp

    <section class="opt-section" id="opt-pay">
        <header class="opt-section-header">
            <div class="opt-section-deco opt-section-deco--pay" aria-hidden="true">PAID</div>
            <div class="opt-section-meta">
                <span class="opt-type-chip opt-type-chip--pay">PAID OPTION</span>
                <h2 class="opt-section-title">有料オプション</h2>
            </div>
            <div class="opt-section-line opt-section-line--pay"></div>
        </header>

        @if(!$hasPayContent)
        <p class="opt-empty"><i class="fas fa-info-circle"></i> 現在、有料オプションは登録されていません。</p>
        @else

        <!-- カテゴリあり -->
        @foreach($cates as $cate)
        @if($cate->option_pay->isNotEmpty())
        <div class="opt-cate-group">
            @if($cate->title)
            <div class="opt-cate-divider">
                <span class="opt-cate-name opt-cate-name--pay">
                    <i class="fas fa-chevron-right"></i>{{ $cate->title }}
                </span>
            </div>
            @endif
            <div class="opt-grid">
                @foreach($cate->option_pay as $opt)
                <article class="opt-card opt-card--pay"
                         data-title="{{ e($opt->title) }}"
                         data-body="{{ e($opt->body) }}"
                         data-type="pay"
                         data-price="{{ $opt->price }}"
                         data-img="{{ $opt->filename ? Storage::url($opt->filename) : '' }}"
                         onclick="optOpenModal(this)"
                         role="button" tabindex="0">
                    <div class="opt-card-media">
                        @if($opt->filename)
                        <img class="opt-card-img"
                             src="{{ Storage::url($opt->filename) }}"
                             alt="{{ $opt->title }}"
                             loading="lazy"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="opt-card-no-img opt-card-no-img--pay" style="display:none;"><i class="fas fa-star"></i></div>
                        @else
                        <div class="opt-card-no-img opt-card-no-img--pay"><i class="fas fa-star"></i></div>
                        @endif
                        @if($opt->price)
                        <span class="opt-card-chip opt-card-chip--pay">+{{ number_format($opt->price) }}<small>円</small></span>
                        @endif
                    </div>
                    <div class="opt-card-info">
                        <div class="opt-card-title">{{ $opt->title }}</div>
                        @if($opt->body)
                        <div class="opt-card-excerpt">{{ $opt->body }}</div>
                        @endif
                        <div class="opt-card-cta opt-card-cta--pay">詳細を見る <i class="fas fa-arrow-right"></i></div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach

        <!--カテゴリなし -->
        @if($payOptions->isNotEmpty())
        <div class="opt-grid">
            @foreach($payOptions as $opt)
            <article class="opt-card opt-card--pay"
                     data-title="{{ e($opt->title) }}"
                     data-body="{{ e($opt->body) }}"
                     data-type="pay"
                     data-price="{{ $opt->price }}"
                     data-img="{{ $opt->filename ? Storage::url($opt->filename) : '' }}"
                     onclick="optOpenModal(this)"
                     role="button" tabindex="0">
                <div class="opt-card-media">
                    @if($opt->filename)
                    <img class="opt-card-img"
                         src="{{ Storage::url($opt->filename) }}"
                         alt="{{ $opt->title }}"
                         loading="lazy"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="opt-card-no-img opt-card-no-img--pay" style="display:none;"><i class="fas fa-star"></i></div>
                    @else
                    <div class="opt-card-no-img opt-card-no-img--pay"><i class="fas fa-star"></i></div>
                    @endif
                    @if($opt->price)
                    <span class="opt-card-chip opt-card-chip--pay">+{{ number_format($opt->price) }}<small>円</small></span>
                    @endif
                </div>
                <div class="opt-card-info">
                    <div class="opt-card-title">{{ $opt->title }}</div>
                    @if($opt->body)
                    <div class="opt-card-excerpt">{{ $opt->body }}</div>
                    @endif
                    <div class="opt-card-cta opt-card-cta--pay">詳細を見る <i class="fas fa-arrow-right"></i></div>
                </div>
            </article>
            @endforeach
        </div>
        @endif

        @endif
    </section>
--}}
</div>{{-- /.opt-page --}}


{{-- ═══ モーダル ═══ --}}
<div class="opt-overlay" id="optModal" onclick="optCloseOnOverlay(event)" aria-hidden="true">
    <div class="opt-modal" role="dialog" aria-modal="true" aria-labelledby="optModalTitle">

        <div class="opt-modal-accent-bar" id="optModalBar"></div>

        <button class="opt-modal-close" onclick="optCloseModal()" aria-label="閉じる">
            <i class="fas fa-xmark"></i>
        </button>

        <div class="opt-modal-media" id="optModalMedia">
            <img class="opt-modal-img" id="optModalImg" src="" alt=""
                 style="display:none;"
                 onerror="this.style.display='none';document.getElementById('optModalNoImg').style.display='flex'">
            <div class="opt-modal-no-img" id="optModalNoImg" style="display:none;">
                <i id="optModalNoImgIcon" class="fa-solid fa-heart-circle-plus"></i>
            </div>
        </div>

        <div class="opt-modal-body">
            <div class="opt-modal-chip" id="optModalChip"></div>
            <h3 class="opt-modal-title" id="optModalTitle"></h3>
            <div class="opt-modal-price" id="optModalPrice" style="display:none;">
                <i class="fas fa-yen-sign"></i>
                <span id="optModalPriceNum"></span>
                <span class="opt-modal-price-unit">円</span>
            </div>
            <div class="opt-modal-text" id="optModalText"></div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function optOpenModal(card) {
    const title  = card.dataset.title  || '';
    const body   = card.dataset.body   || '';
    const type   = card.dataset.type;
    const price  = card.dataset.price;
    const img    = card.dataset.img    || '';
    const isFree = (type === 'free');

    // アクセントバー
    const bar = document.getElementById('optModalBar');
    bar.className = 'opt-modal-accent-bar ' + (isFree ? 'opt-modal-accent-bar--free' : 'opt-modal-accent-bar--pay');

    // チップ
    const chip = document.getElementById('optModalChip');
    chip.textContent = isFree ? 'FREE OPTION' : 'PAID OPTION';
    chip.className = 'opt-modal-chip ' + (isFree ? 'opt-modal-chip--free' : 'opt-modal-chip--pay');

    // タイトル・本文
    document.getElementById('optModalTitle').textContent = title;
    document.getElementById('optModalText').textContent  = body;

    // 料金
    const priceEl = document.getElementById('optModalPrice');
    if (!isFree && price) {
        document.getElementById('optModalPriceNum').textContent = '+' + Number(price).toLocaleString();
        priceEl.style.display = 'flex';
    } else {
        priceEl.style.display = 'none';
    }

    // 画像
    const imgEl   = document.getElementById('optModalImg');
    const noImgEl = document.getElementById('optModalNoImg');
    const iconEl  = document.getElementById('optModalNoImgIcon');
    if (img) {
        imgEl.src             = img;
        imgEl.alt             = title;
        imgEl.style.display   = 'block';
        noImgEl.style.display = 'none';
    } else {
        imgEl.style.display   = 'none';
        noImgEl.style.display = 'flex';
        iconEl.className = isFree ? 'fa-solid fa-heart-circle-plus' : 'fa-solid fa-star';
    }

    const modal = document.getElementById('optModal');
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}

function optCloseModal() {
    const modal = document.getElementById('optModal');
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
}

function optCloseOnOverlay(e) {
    if (e.target === document.getElementById('optModal')) optCloseModal();
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') optCloseModal(); });

document.querySelectorAll('.opt-card').forEach(card => {
    card.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); optOpenModal(card); }
    });
});
</script>
@endpush

@endsection
