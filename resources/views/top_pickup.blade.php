@if(isset($pickups) && $pickups->isNotEmpty())
<section class="top-section top-pickup-section">
    <div class="top-section-inner">
        <h2 class="section-title">
            <span class="section-title-en">PICK UP</span>
            <span class="section-title-ja">ピックアップ</span>
        </h2>
        <div class="top-cast-slider-wrap">
            <div class="swiper top-cast-swiper" id="pickupSwiper">
                <div class="swiper-wrapper">
                    @foreach($pickups as $pickup)
                    @if($pickup->item)
                    <div class="swiper-slide">
                        <a href="{{ route('items.show', $pickup->item->id) }}" class="top-pickup-card">
                            <div class="top-pickup-img">
                                <img src="{{ $pickup->item->thumbnail ? Storage::url($pickup->item->thumbnail->filename) : asset('images/dmy.jpg') }}"
                                     alt="{{ $pickup->item->item_name }}"
                                     loading="lazy">
                                <span class="top-pickup-label">PICKUP</span>
                            </div>
                            <p class="top-pickup-name item-card-name">{{ $pickup->item->item_name }}@if($pickup->item->item_cate != 3 && $pickup->item->item_age > 0)<span class="item-card-age">（{{ $pickup->item->item_age }}）</span>@endif</p>
                            @if($pickup->item->item_cate != 3)
                            @php $i=$pickup->item; $szParts=[]; if($i->item_height)$szParts[]='T'.$i->item_height; if($i->item_b){$b='B'.$i->item_b;if($i->item_cup)$b.='('.$i->item_cup.')';$szParts[]=$b;} if($i->item_w)$szParts[]='W'.$i->item_w; if($i->item_h)$szParts[]='H'.$i->item_h; @endphp
                            @if(!empty($szParts))<p class="item-card-sizes">{{ implode(' ',$szParts) }}</p>@endif
                            @endif
                        </a>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            <div class="top-cast-prev" id="pickupPrev" aria-label="前へ"></div>
            <div class="top-cast-next" id="pickupNext" aria-label="次へ"></div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    new Swiper('#pickupSwiper', {
        slidesPerView: 'auto',
        spaceBetween: 12,
        loop: {{ $pickups->count() > 5 ? 'true' : 'false' }},
        autoplay: { delay: 3200, disableOnInteraction: false },
        navigation: { prevEl: '#pickupPrev', nextEl: '#pickupNext' },
        speed: 600,
    });
});
</script>
@endif
