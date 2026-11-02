@extends('layouts.app')
@section('breadcrumb_label', 'ランキング')
@section('content')
<div class="titleArea">
    <h2>RANKING</h2>
    <div class="title">ランキング</div>
</div>

{{-- カテゴリタブ --}}
@php
    $firstCateId = null;
    foreach ($rankCates as $rc) {
        if ($ranks->where('cate_id', $rc->id)->count() > 0) {
            $firstCateId = $rc->id;
            break;
        }
    }
@endphp

<div class="rank-tab-wrap">
    {{-- スマホ用アコーディオントグル --}}
    <button class="rank-tab-accordion-btn" id="rankTabAccordionBtn" aria-expanded="false" aria-controls="rankTabList">
        <span class="rank-tab-accordion-current">
            {{ optional($rankCates->first(fn($rc) => $rc->id === $firstCateId))->title ?? 'カテゴリ選択' }}
        </span>
        <span class="rank-tab-accordion-arrow" aria-hidden="true">&#9660;</span>
    </button>
    <div class="rank-tab-list" id="rankTabList">
        @foreach ($rankCates as $rc)
            @if ($ranks->where('cate_id', $rc->id)->count() > 0)
            <button
                class="rank-tab-btn {{ $rc->id === $firstCateId ? 'is-active' : '' }}"
                data-target="rank-cate-{{ $rc->id }}"
            >{{ $rc->title }}</button>
            @endif
        @endforeach
    </div>
</div>

<section id="rankingArea" class="ranking-section">
    <div class="ranking-container">

        @foreach ($rankCates as $rankcate)
            @php $categoryRanks = $ranks->where('cate_id', $rankcate->id); @endphp

            @if($categoryRanks->count() > 0)
                <div
                    class="ranking-category rank-cate-panel {{ $rankcate->id === $firstCateId ? 'is-visible' : '' }}"
                    id="rank-cate-{{ $rankcate->id }}"
                >
                    <div class="ranking-list">
                        @foreach($categoryRanks as $rank)
                            @php
                                $sortedCasts = $rank->itemRanks->sortBy('no')->filter(fn ($ir) => $ir->items);
                                $topRowCasts = $sortedCasts->where('no', '<=', 5);
                                $moreRowCasts = $sortedCasts->where('no', '>', 5);
                            @endphp

                            <div class="ranking-item">
                                <div class="ranking-header">
                                    @if ($rank->filename)
                                        <h4 class="ranking-title-image">
                                            <img src="{{ asset('images/rank/' . $rank->filename) }}" alt="{{ $rank->title }}">
                                        </h4>
                                    @else
                                        <h4 class="ranking-title">{{ $rank->title }}</h4>
                                    @endif
                                    @if($rank->body)
                                        <p class="ranking-description">{!! nl2br(e($rank->body)) !!}</p>
                                    @endif
                                </div>

                                @if($topRowCasts->isNotEmpty())
                                    <ul class="ranking-cast-list ranking-row">
                                        @foreach($topRowCasts as $itemRank)
                                            @include('partials.ranking-cast-item', ['itemRank' => $itemRank])
                                        @endforeach
                                    </ul>
                                @endif

                                @if($moreRowCasts->isNotEmpty())
                                    <ul class="ranking-cast-list ranking-row ranking-row--more">
                                        @foreach($moreRowCasts as $itemRank)
                                            @include('partials.ranking-cast-item', ['itemRank' => $itemRank])
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

    </div>
</section>

<style>
/* ==============================
   カテゴリタブ
============================== */
.rank-tab-wrap {
    position: sticky;
    top: 0;
    z-index: 100;
    background: linear-gradient(135deg, #fff8fb 0%, #fff0f6 100%);
    padding: 14px 16px;
    box-shadow: 0 4px 20px rgba(255, 45, 120, .10);
}
.rank-tab-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
    max-width: 900px;
    margin: 0 auto;
}
.rank-tab-btn {
    position: relative;
    padding: 8px 22px;
    border: 1.5px solid rgba(255, 45, 120, .35);
    border-radius: 999px;
    background: transparent;
    color: #ff2d78;
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: .08em;
    cursor: pointer;
    transition: background .22s, color .22s, border-color .22s, box-shadow .22s;
    white-space: nowrap;
}
.rank-tab-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 999px;
    background: radial-gradient(circle at 60% 40%, rgba(212,175,55,.15), transparent 70%);
    opacity: 0;
    transition: opacity .22s;
}
.rank-tab-btn:hover {
    border-color: #ff2d78;
    color: #ff2d78;
    box-shadow: 0 0 12px rgba(255, 45, 120, .25);
}
.rank-tab-btn:hover::after { opacity: 1; }
.rank-tab-btn.is-active {
    background: linear-gradient(135deg, #ff2d78 0%, #ff6fa3 100%);
    border-color: transparent;
    color: #fff;
    box-shadow: 0 0 18px rgba(255, 45, 120, .35), 0 2px 8px rgba(255, 45, 120, .15);
}
.rank-tab-btn.is-active::after { opacity: 0; }

/* ==============================
   スマホ：アコーディオン
============================== */
.rank-tab-accordion-btn {
    display: none; /* PC では非表示 */
}

@media (max-width: 640px) {
    .rank-tab-wrap {
        padding: 0;
    }

    /* トグルボタン */
    .rank-tab-accordion-btn {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 12px 16px;
        background: transparent;
        border: none;
        border-bottom: 1px solid rgba(212,175,55,.2);
        color: #d4af37;
        font-size: 0.88rem;
        font-weight: 600;
        letter-spacing: .05em;
        cursor: pointer;
        gap: 8px;
    }
    .rank-tab-accordion-current {
        flex: 1;
        text-align: left;
    }
    .rank-tab-accordion-arrow {
        font-style: normal;
        font-size: 0.7rem;
        transition: transform 0.25s ease;
        flex-shrink: 0;
    }
    .rank-tab-wrap.is-open .rank-tab-accordion-arrow {
        transform: rotate(180deg);
    }

    /* タブリスト：デフォルト非表示 */
    .rank-tab-list {
        display: none;
        flex-direction: column;
        gap: 6px;
        padding: 10px 12px 14px;
        max-height: 60vh;
        overflow-y: auto;
    }
    .rank-tab-wrap.is-open .rank-tab-list {
        display: flex;
    }

    /* タブボタン：全幅・左寄せ */
    .rank-tab-btn {
        width: 100%;
        text-align: left;
        border-radius: 6px;
        padding: 9px 14px;
        justify-content: flex-start;
    }
}

/* ==============================
   パネル表示切替
============================== */
.rank-cate-panel {
    display: none;
}
.rank-cate-panel.is-visible {
    display: block;
    animation: rankFadeIn .3s ease;
}
@keyframes rankFadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>

<script>
(function () {
    var accordionBtn = document.getElementById('rankTabAccordionBtn');
    var tabWrap      = document.querySelector('.rank-tab-wrap');
    var currentLabel = tabWrap ? tabWrap.querySelector('.rank-tab-accordion-current') : null;

    /* ---- アコーディオン開閉 ---- */
    if (accordionBtn && tabWrap) {
        accordionBtn.addEventListener('click', function () {
            var isOpen = tabWrap.classList.toggle('is-open');
            accordionBtn.setAttribute('aria-expanded', String(isOpen));
        });
    }

    /* ---- タブ切替 ---- */
    document.querySelectorAll('.rank-tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            // ボタンのactive切替
            document.querySelectorAll('.rank-tab-btn').forEach(function (b) {
                b.classList.remove('is-active');
            });
            this.classList.add('is-active');

            // パネルの表示切替
            document.querySelectorAll('.rank-cate-panel').forEach(function (p) {
                p.classList.remove('is-visible');
            });
            document.getElementById(this.dataset.target).classList.add('is-visible');

            // スマホ：選択後にアコーディオンを閉じてラベルを更新
            if (tabWrap) {
                tabWrap.classList.remove('is-open');
                if (accordionBtn) accordionBtn.setAttribute('aria-expanded', 'false');
            }
            if (currentLabel) {
                currentLabel.textContent = this.textContent.trim();
            }
        });
    });
})();
</script>

@endsection
