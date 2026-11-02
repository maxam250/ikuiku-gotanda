<li class="ranking-cast-item rank-{{ $itemRank->no }}">
    <div class="ranking-badge">
        @if ($itemRank->no <= 3)
            <i class="fas fa-crown ranking-crown"></i>
        @endif
        <div class="ranking-number">{{ $itemRank->no }}</div>
    </div>
    <div class="cast-image">
        <a href="{{ route('items.show', ['id' => $itemRank->items->id]) }}">
            @if ($itemRank->items->thumbnail && $itemRank->items->thumbnail->filename)
                <img src="{{ Storage::url($itemRank->items->thumbnail->filename) }}" alt="{{ $itemRank->items->item_name }}">
            @else
                <img src="{{ asset('images/dmy.jpg') }}" alt="{{ $itemRank->items->item_name }}">
            @endif
        </a>
    </div>
    <div class="cast-body">
        <h5 class="cast-name">
            <a href="{{ route('items.show', ['id' => $itemRank->items->id]) }}">{{ $itemRank->items->item_name }}@if($itemRank->items->item_cate != 3 && $itemRank->items->item_age > 0)<span class="cast-age">（{{ $itemRank->items->item_age }}）</span>@endif</a>
        </h5>
        @if($itemRank->items->item_cate != 3)
        @php $i=$itemRank->items; $szParts=[]; if($i->item_height)$szParts[]='T'.$i->item_height; if($i->item_b){$b='B'.$i->item_b;if($i->item_cup)$b.='('.$i->item_cup.')';$szParts[]=$b;} if($i->item_w)$szParts[]='W'.$i->item_w; if($i->item_h)$szParts[]='H'.$i->item_h; @endphp
        @if(!empty($szParts))<p class="item-card-sizes">{{ implode(' ',$szParts) }}</p>@endif
        @endif
        @php $i = $itemRank->items; @endphp
        @if($i->item_catch)<p class="item-catch" style="color:{{ $i->category?->color ?? '#ff2d78' }};font-size:0.68rem;font-weight:600;margin:2px 0;">{{ $i->item_catch }}</p>@endif
        @if($i->category)
        <div class="cast-category">
            <span style="display:inline-flex;align-items:center;gap:3px;background:{{ $i->category->color }};color:#fff;font-size:0.62rem;padding:1px 6px;border-radius:9999px;">
                <i class="{{ $i->category->icon }}"></i>
                {{ $i->category->name }}
            </span>
        </div>
        @endif
        @if ($itemRank->text && $itemRank->text !== 'NULL')
            <p class="ranking-cast-text">{!! nl2br(e($itemRank->text)) !!}</p>
        @endif
    </div>
</li>
