@extends('layouts.app')

@section('breadcrumb_label', '出勤予定')
@push('head')
<style>.cast-image video.cast-thumb-video{width:100%;height:100%;object-fit:cover;display:block;}
.dstrip-day.is-disabled{opacity:.35;cursor:not-allowed;pointer-events:none;}
.dstrip-nav:disabled{opacity:.3;cursor:not-allowed;pointer-events:none;}
</style>
@endpush

@section('content')

<div class="titleArea">
    <h2>SCHEDULE</h2>
    <div class="title">スケジュール</div>
</div>



<!-- 日付選択（7日間ずつスライド） -->
@php
    $todayAdj  = \Carbon\Carbon::parse(adjustTimeWithShift())->startOfDay();
    $maxAdj    = $todayAdj->copy()->addMonths(2);
    $stripDate = $adjustedDate->copy()->startOfDay();
    if ($stripDate->lt($todayAdj)) $stripDate = $todayAdj->copy();
    if ($stripDate->gt($maxAdj))   $stripDate = $maxAdj->copy();
@endphp
<div class="calendar-section">
    <div class="dstrip" id="scheduleDstrip"
         data-base="{{ $stripDate->format('Y-m-d') }}"
         data-selected="{{ $stripDate->format('Y-m-d') }}"
         data-min="{{ $todayAdj->format('Y-m-d') }}"
         data-max="{{ $maxAdj->format('Y-m-d') }}"
         data-url="{{ url('/schedule') }}">
        <button class="dstrip-nav dstrip-prev" type="button" onclick="dstripSchedule.slide(-1)" aria-label="前の7日間">
            <i class="fas fa-chevron-left"></i>
        </button>
        <div class="dstrip-track" id="scheduleDstripTrack"></div>
        <button class="dstrip-nav dstrip-next" type="button" onclick="dstripSchedule.slide(1)" aria-label="次の7日間">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</div>

    <x-cast-search :option-cates="$optionCates" :item-categories="$item_categories"
        :options="$options" :option-pays="$optionPays"
        action="{{ url('/schedule/' . $adjustedDate->format('Y-m-d')) }}" />



<!-- タイムラインセクション -->
<section id="ListArea" class="modern-timeline">
    <div class="timeline-header">
        <h3 class="date">{{ $adjustedDate->isoFormat('YYYY年MM月DD日 (dd)') }}</h3>

    </div>


    <!-- キャストスケジュールセクション -->
    <section class="cast-section">
        <div class="cast-grid">
        @foreach ($schedules as $schedule)
        @if(!$schedule->item) @continue @endif
        <div class="cast-card">
            {{-- 角版画像（上段） --}}
            <div class="cast-image">
                <a href="{{ route('items.show', ['id' => $schedule->item->id]) }}">
                    @if(!empty($schedule->item->thumbnail_video))
                    <video class="cast-thumb-video"
                           data-src="{{ Storage::url($schedule->item->thumbnail_video) }}"
                           muted loop playsinline></video>
                    @else
                    <img src="{{ !empty($schedule->itemImage?->filename) ? Storage::url($schedule->itemImage->filename) : asset('images/dmy.jpg') }}" alt="{{ $schedule->item->item_name }}">
                    @endif
                </a>
            </div>

            {{-- 名前・情報・ボタン（下段） --}}
            <div class="cast-body">
                <h5 class="cast-name">
                    <a href="{{ route('items.show', ['id' => $schedule->item->id]) }}">{{ $schedule->item->item_name }}@if($schedule->item->item_age > 0)<span class="cast-age">（{{ $schedule->item->item_age }}）</span>@endif</a>
                </h5>
                @php
                    $szParts = [];
                    if ($schedule->item->item_height) $szParts[] = 'T' . $schedule->item->item_height;
                    if ($schedule->item->item_b) {
                        $b = 'B' . $schedule->item->item_b;
                        if ($schedule->item->item_cup) $b .= '(' . $schedule->item->item_cup . ')';
                        $szParts[] = $b;
                    }
                    if ($schedule->item->item_w) $szParts[] = 'W' . $schedule->item->item_w;
                    if ($schedule->item->item_h) $szParts[] = 'H' . $schedule->item->item_h;
                @endphp
                @if(!empty($szParts))
                <p class="item-card-sizes">{{ implode(' ', $szParts) }}</p>
                @endif
                @if($schedule->item->item_catch)<p class="item-catch" style="color:{{ $schedule->item->category?->color ?? '#e91e8c' }};font-size:0.75rem;font-weight:600;margin:2px 0;">{{ $schedule->item->item_catch }}</p>@endif
                @if($schedule->item->category)
                <div class="cast-category">
                    <span style="display:inline-flex;align-items:center;gap:3px;background:{{ $schedule->item->category->color }};color:#fff;font-size:0.62rem;padding:1px 6px;border-radius:9999px;">
                        <i class="{{ $schedule->item->category->icon }}"></i>
                        {{ $schedule->item->category->name }}
                    </span>
                </div>
                @endif
                @if($schedule->item->fanclub || $schedule->item->myoptions->isNotEmpty() || $schedule->item->mycostumes->isNotEmpty())
                <div class="item-card-badges">
                    @if($schedule->item->fanclub)<span class="item-card-badge item-card-badge--fc" title="ファンクラブ"><i class="fas fa-heart"></i></span>@endif
                    @if($schedule->item->myoptions->isNotEmpty())<span class="item-card-badge item-card-badge--opt" title="マイオプション"><i class="fas fa-star"></i></span>@endif
                    @if($schedule->item->mycostumes->isNotEmpty())<span class="item-card-badge item-card-badge--cos" title="マイコスチューム"><i class="fas fa-tshirt"></i></span>@endif
                </div>
                @endif
                <div class="cast-footer">
                    @if($schedule->status == 2)
                    <div class="work-time-display">
                        <span class="status-badge tel">TEL確認</span>
                    </div>
                    @elseif(!in_array($schedule->status, [3, 4, 5]))
                    @if($schedule->time_start && $schedule->time_end)
                    <div class="work-time-display">
                    <div class="work-time-frame">
                        <i class="fas fa-clock work-time-icon"></i>
                        <span class="work-time-text">{{ formatScheduleTime($schedule->time_start) }}～{{ formatScheduleTime($schedule->time_end) }}</span>
                    </div>
                    </div>
                    @endif
                    @endif
                    @if($schedule->status == 3)<span class="status-badge closed">受付終了</span>
                    @elseif($schedule->status == 4)<span class="status-badge holiday">当日休み</span>
                    @elseif($schedule->status == 5)<span class="status-badge sold">予約完売</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
        </div>
    </section>
</section>

<script>
// 7日間ずつスライドする日付ストリップ
const dstripSchedule = (function () {
    const root = document.getElementById('scheduleDstrip');
    const track = document.getElementById('scheduleDstripTrack');
    if (!root || !track) return { slide: function () {} };

    const base     = root.dataset.base;
    const selected = root.dataset.selected;
    const baseUrl  = root.dataset.url;
    const minDate  = new Date(root.dataset.min + 'T00:00:00');
    const maxDate  = new Date(root.dataset.max + 'T00:00:00');
    const params   = new URLSearchParams(window.location.search);
    const categoryId = params.get('category_id');
    const query    = categoryId ? `?category_id=${categoryId}` : '';
    const dows     = ['日', '月', '火', '水', '木', '金', '土'];
    let offset = 0;

    function ymd(d) {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const da = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${da}`;
    }

    function render() {
        const start = new Date(base + 'T00:00:00');
        start.setDate(start.getDate() + offset);
        const todayStr = ymd(new Date());
        let html = '';
        for (let i = 0; i < 7; i++) {
            const d = new Date(start);
            d.setDate(start.getDate() + i);
            const ds = ymd(d);
            const dow = d.getDay();
            let cls = 'dstrip-day';
            if (ds === selected) cls += ' is-active';
            if (ds === todayStr) cls += ' is-today';
            if (dow === 0) cls += ' sun';
            if (dow === 6) cls += ' sat';
            if (d < minDate || d > maxDate) {
                cls += ' is-disabled';
                html += `<span class="${cls}">`
                    + `<span class="dstrip-dow">${dows[dow]}</span>`
                    + `<span class="dstrip-date">${d.getMonth() + 1}/${d.getDate()}</span>`
                    + `</span>`;
            } else {
                html += `<a class="${cls}" href="${baseUrl}/${ds}${query}">`
                    + `<span class="dstrip-dow">${dows[dow]}</span>`
                    + `<span class="dstrip-date">${d.getMonth() + 1}/${d.getDate()}</span>`
                    + `</a>`;
            }
        }
        track.innerHTML = html;
        track.classList.remove('dstrip-anim');
        void track.offsetWidth;
        track.classList.add('dstrip-anim');

        // ナビゲーションボタンの有効/無効
        const prevBtn = root.querySelector('.dstrip-prev');
        const nextBtn = root.querySelector('.dstrip-next');
        if (prevBtn) {
            const prevEnd = new Date(base + 'T00:00:00');
            prevEnd.setDate(prevEnd.getDate() + offset - 1);
            prevBtn.disabled = prevEnd < minDate;
        }
        if (nextBtn) {
            const nextStart = new Date(base + 'T00:00:00');
            nextStart.setDate(nextStart.getDate() + offset + 7);
            nextBtn.disabled = nextStart > maxDate;
        }
    }

    render();
    return {
        slide: function (dir) {
            const newOffset = offset + dir * 7;
            const wStart = new Date(base + 'T00:00:00');
            wStart.setDate(wStart.getDate() + newOffset);
            const wEnd = new Date(wStart);
            wEnd.setDate(wStart.getDate() + 6);
            if (wEnd < minDate || wStart > maxDate) return;
            offset = newOffset;
            render();
        }
    };
})();
</script>

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
