@extends('layouts.app')
@section('breadcrumb_label', '女の子一覧')
@push('head')
<style>.cast-image video.cast-thumb-video{width:100%;height:100%;object-fit:cover;display:block;}</style>
@endpush

@section('content')
<div class="titleArea">
    <h2>CASTS</h2>
    <div class="title">女の子一覧</div>
</div>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-2">


    <x-cast-search :option-cates="$optionCates" :item-categories="$item_categories" :options="$options" :option-pays="$optionPays" />
    <div class="pagination-wrap">
        {{ $items->appends(request()->query())->links() }}
    </div>
    <!-- キャスト一覧 -->
    @if($items->count() > 0)
        <div class="cast-grid">
            @foreach ($items as $item)
            <div class="cast-card">
                <div class="cast-image">
                    <a href="{{ route('items.show', ['id' => $item->id]) }}">
                        @if(!empty($item->thumbnail_video))
                        <video class="cast-thumb-video"
                               data-src="{{ Storage::url($item->thumbnail_video) }}"
                               muted loop playsinline></video>
                        @else
                        <img src="{{ !empty($item->filename) ? Storage::url($item->filename) : asset('images/dmy.jpg') }}"
                             alt="{{ $item->item_name }}">
                        @endif
                    </a>
                </div>
                <div class="cast-body">
                    <h5 class="cast-name">
                        <a href="{{ route('items.show', ['id' => $item->id]) }}">{{ $item->item_name }}@if($item->item_cate != 3 && $item->item_age > 0)<span class="cast-age">（{{ $item->item_age }}）</span>@endif</a>
                    </h5>
                    @if($item->item_cate != 3)
                    @php $szParts=[]; if($item->item_height)$szParts[]='T'.$item->item_height; if($item->item_b){$b='B'.$item->item_b;if($item->item_cup)$b.='('.$item->item_cup.')';$szParts[]=$b;} if($item->item_w)$szParts[]='W'.$item->item_w; if($item->item_h)$szParts[]='H'.$item->item_h; @endphp
                    @if(!empty($szParts))<p class="item-card-sizes">{{ implode(' ',$szParts) }}</p>@endif
                    @endif
                    @if($item->item_catch)<p class="item-catch" style="color:{{ $item->category?->color ?? '#e91e8c' }};font-size:0.75rem;font-weight:600;margin:2px 0;">{{ $item->item_catch }}</p>@endif
                    @if($item->category)
                    <div class="cast-category">
                        <span style="display:inline-flex;align-items:center;gap:3px;background:{{ $item->category->color }};color:#fff;font-size:0.62rem;padding:1px 6px;border-radius:9999px;">
                            <i class="{{ $item->category->icon }}"></i>
                            {{ $item->category->name }}
                        </span>
                    </div>
                    @endif
                    @if($item->fanclub || $item->myoptions->isNotEmpty() || $item->mycostumes->isNotEmpty())
                    <div class="item-card-badges">
                        @if($item->fanclub)<span class="item-card-badge item-card-badge--fc" title="ファンクラブ"><i class="fas fa-heart"></i></span>@endif
                        @if($item->myoptions->isNotEmpty())<span class="item-card-badge item-card-badge--opt" title="マイオプション"><i class="fas fa-star"></i></span>@endif
                        @if($item->mycostumes->isNotEmpty())<span class="item-card-badge item-card-badge--cos" title="マイコスチューム"><i class="fas fa-tshirt"></i></span>@endif
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="item-card-empty">
            <i class="fas fa-search"></i>
            <p class="item-card-empty-title">検索結果が見つかりませんでした</p>
            <p class="item-card-empty-sub">検索条件を変更してお試しください</p>
            <a href="{{ route('item.index') }}" class="item-card-empty-btn">
                <i class="fas fa-undo"></i>
                全てのキャストを表示
            </a>
        </div>
    @endif

    @if(request()->hasAny(['name', 'height', 'bust', 'waist', 'hip', 'options', 'option_pays']))
        <div class="item-card-search-count">
            <i class="fas fa-filter"></i>
            検索結果: <strong>{{ $items->total() }}件</strong>のキャストが見つかりました
        </div>
    @endif

    <div class="pagination-wrap">
        {{ $items->appends(request()->query())->links() }}
    </div>

</section>
@push('scripts')
<script>
(function () {
    var videos = document.querySelectorAll('video.cast-thumb-video');
    if (!videos.length) return;

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            var v = entry.target;
            if (entry.isIntersecting) {
                if (!v.src && v.dataset.src) {
                    v.src = v.dataset.src;
                    v.load();
                }
                v.play().catch(function () {});
            } else {
                v.pause();
            }
        });
    }, { rootMargin: '100px', threshold: 0.2 });

    videos.forEach(function (v) { observer.observe(v); });
}());
</script>
@endpush

@endsection
