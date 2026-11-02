@if(isset($topRanks) && $topRanks->isNotEmpty())
<section class="top-section top-ranking-section">
    <div class="top-section-inner">
        <h2 class="section-title">
            <span class="section-title-en">RANKING</span>
            <span class="section-title-ja">ランキング</span>
        </h2>

        @php
            // rank_cates.sort を第1順位、ranks.sort を第2順位として並び替え
            $orderedTopRanks = collect();
            if (isset($rankCates)) {
                foreach ($rankCates as $rc) {
                    $cateRanks = $topRanks->where('cate_id', $rc->id)->sortBy('sort');
                    $orderedTopRanks = $orderedTopRanks->merge($cateRanks);
                }
                // cate_id が未設定のランクは末尾に追加
                $cateIds = $rankCates->pluck('id');
                $noCateRanks = $topRanks->filter(fn($r) => !$cateIds->contains($r->cate_id))->sortBy('sort');
                $orderedTopRanks = $orderedTopRanks->merge($noCateRanks);
            } else {
                $orderedTopRanks = $topRanks;
            }
            $orderedTopRanks = $orderedTopRanks->take(3);
        @endphp

        {{-- ランキングごとにスライド、各スライドに1〜3位を横一列 --}}
        <div class="swiper top-ranks-swiper">
            <div class="swiper-wrapper">
                @foreach($orderedTopRanks as $rank)
                @php $rankItems = $rank->itemRanks->sortBy('no')->take(3)->values(); @endphp
                @if($rankItems->isNotEmpty())
                <div class="swiper-slide">
                    <h3 class="top-ranks-title">{{ $rank->title }}</h3>
                    <div class="top-ranks-row">
                        @foreach($rankItems as $ir)
                        @if($ir->items)
                        <div class="top-ranks-col">
                            <span class="top-rank-no top-rank-no--{{ $ir->no }}">{{ $ir->no }}</span>
                            <a href="{{ route('items.show', $ir->items->id) }}" class="top-rank-cast-link">
                                <img src="{{ $ir->items->thumbnail ? Storage::url($ir->items->thumbnail->filename) : asset('images/dmy.jpg') }}"
                                     alt="{{ $ir->items->item_name }}"
                                     loading="lazy"
                                     class="top-rank-cast-img">
                                <span class="top-rank-cast-name item-card-name">{{ $ir->items->item_name }}@if($ir->items->item_age > 0)<span class="item-card-age">（{{ $ir->items->item_age }}）</span>@endif</span>
                                @php $i=$ir->items; $szParts=[]; if($i->item_height)$szParts[]='T'.$i->item_height; if($i->item_b){$b='B'.$i->item_b;if($i->item_cup)$b.='('.$i->item_cup.')';$szParts[]=$b;} if($i->item_w)$szParts[]='W'.$i->item_w; if($i->item_h)$szParts[]='H'.$i->item_h; @endphp
                                @if(!empty($szParts))<p class="item-card-sizes">{{ implode(' ',$szParts) }}</p>@endif
                                @if($i->item_catch)<p class="item-catch" style="color:{{ $i->category?->color ?? '#e91e8c' }};font-size:0.75rem;font-weight:600;margin:2px 0;">{{ $i->item_catch }}</p>@endif
                            </a>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            <div class="swiper-button-prev top-ranks-prev"></div>
            <div class="swiper-button-next top-ranks-next"></div>
        </div>

        <div class="top-section-more">
            <a href="{{ route('rank.index') }}" class="top-more-link">ランキング詳細 &rsaquo;</a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var count = {{ $orderedTopRanks->count() }};
    new Swiper('.top-ranks-swiper', {
        slidesPerView: 1,
        loop: count > 1,
        autoplay: count > 1 ? { delay: 3500, disableOnInteraction: false } : false,
        speed: 700,
        navigation: {
            prevEl: '.top-ranks-prev',
            nextEl: '.top-ranks-next',
        },
    });
});
</script>
@endif
