@extends('layouts.app')
@section('breadcrumb_label', 'X・Twitter')

@push('head')
<style>.cast-image video.cast-thumb-video{width:100%;height:100%;object-fit:cover;display:block;}</style>
@endpush

@section('content')
<div class="titleArea">
    <h2>SNS</h2>
    <div class="title">公式SNS</div>
</div>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-2">

    <div class="pagination-wrap">
        {{ $items->links() }}
    </div>
    @if($items->count() > 0)
        <div class="cast-grid">
            @foreach ($items as $item)
            <div class="cast-card">
                <div class="cast-image">
                    <a href="https://x.com/{{ $item->item_twitter }}" target="_blank" rel="noopener noreferrer">
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
                        <a href="https://x.com/{{ $item->item_twitter }}" target="_blank" rel="noopener noreferrer">{{ $item->item_name }}@if($item->item_cate != 3 && $item->item_age > 0)<span class="cast-age">（{{ $item->item_age }}）</span>@endif</a>
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
                    <div class="mt-1">
                        <a href="https://x.com/{{ $item->item_twitter }}" target="_blank" rel="noopener noreferrer"
                           style="font-size:0.75rem;color:#1da1f2;">
                            <i class="fab fa-x-twitter"></i> {{ '@' . $item->item_twitter }}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="item-card-empty">
            <i class="fas fa-search"></i>
            <p class="item-card-empty-title">X・Twitterを登録しているキャストはいません</p>
        </div>
    @endif

    <div class="pagination-wrap">
        {{ $items->links() }}
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
                if (!v.src && v.dataset.src) { v.src = v.dataset.src; v.load(); }
                v.play().catch(function () {});
            } else { v.pause(); }
        });
    }, { rootMargin: '100px', threshold: 0.2 });
    videos.forEach(function (v) { observer.observe(v); });
}());
</script>
@endpush

@endsection
