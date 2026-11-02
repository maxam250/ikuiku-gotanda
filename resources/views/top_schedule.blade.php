
<style>
.cast-image video.cast-thumb-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
</style>

<!-- キャストスケジュールセクション -->
<section id="ListArea" class="modern-timeline top-schedule-section">

    {{-- スタイリッシュなセクションヘッダー --}}
    <div class="schedule-section-header">
        <div class="schedule-title-wrap">
            <span class="schedule-title-en">TODAY'S SCHEDULE</span>
            <h2 class="schedule-title-text">当日スケジュール</h2>
            <a href="{{ route('schedule') }}" class="schedule-title-link">
                すべて見る <i class="fas fa-arrow-right fa-xs"></i>
            </a>
        </div>
    </div>

    {{-- キャスト検索 --}}
    <x-cast-search :option-cates="$optionCates" :item-categories="$item_categories"
        :options="$options" :option-pays="$optionPays"
        action="{{ route('top') }}" />


    {{-- タブナビゲーション --}}
    <div class="schedule-tabs" role="tablist">
        <button class="schedule-tab-btn active"
                role="tab" aria-selected="true" aria-controls="tab-today"
                onclick="switchScheduleTab(this, 'tab-today')">
            <span class="schedule-tab-day">今日</span>
            <span class="schedule-tab-date">{{ $adjustedDate->isoFormat('M月D日 (dd)') }}</span>
        </button>
        <button class="schedule-tab-btn"
                role="tab" aria-selected="false" aria-controls="tab-tomorrow"
                onclick="switchScheduleTab(this, 'tab-tomorrow')">
            <span class="schedule-tab-day">明日</span>
            <span class="schedule-tab-date">{{ $tomorrowDate->isoFormat('M月D日 (dd)') }}</span>
        </button>
    </div>

    {{-- 当日タブ --}}
    <div id="tab-today" class="schedule-tab-content active" role="tabpanel">
        <section class="cast-section">
            <div class="cast-grid">
            @forelse ($schedules as $schedule)
            @if(!$schedule->item) @continue @endif
            @php
                $images = $schedule->itemImages ?? collect();
                $thumbImage = $images->firstWhere('flag', 1) ?? $images->sortBy([['sort', 'asc'], ['id', 'asc']])->first(); // チェック無しなら先頭の画像
                $otherImages = ($thumbImage ? $images->reject(fn($img) => $img->id === $thumbImage->id) : $images)->values();
                $firstImage = $otherImages->get(0);
                $secondImage = $otherImages->get(1);
                $hasVideo = !empty($schedule->item->thumbnail_video);
                if ($hasVideo) {
                    $frontImage = null;
                    $backImage = $thumbImage ?? $firstImage ?? $secondImage;
                } elseif ($thumbImage) {
                    $frontImage = $thumbImage;
                    $backImage = $firstImage ?? $secondImage;
                } else {
                    $frontImage = $firstImage;
                    $backImage = $secondImage;
                }
            @endphp
            <div class="cast-card">
                <div class="cast-image">
                    <a class="cast-flip" href="{{ route('items.show', ['id' => $schedule->item->id]) }}">
                        <div class="cast-flip-front">
                            @if($hasVideo)
                            <video class="cast-thumb-video"
                                   data-src="{{ Storage::url($schedule->item->thumbnail_video) }}"
                                   muted loop playsinline></video>
                            @else
                            <img src="{{ !empty($frontImage?->filename) ? Storage::url($frontImage->filename) : asset('images/dmy.jpg') }}" alt="{{ $schedule->item->item_name }}">
                            @endif
                        </div>
                        <div class="cast-flip-back">
                            <img src="{{ !empty($backImage?->filename) ? Storage::url($backImage->filename) : asset('images/dmy.jpg') }}" alt="{{ $schedule->item->item_name }}">
                        </div>
                    </a>
                </div>
                <div class="cast-body">
                    <h5 class="cast-name">
                        <a href="{{ route('items.show', ['id' => $schedule->item->id]) }}">{{ $schedule->item->item_name }}@if($schedule->item->item_cate != 3 && $schedule->item->item_age > 0)<span class="cast-age">（{{ $schedule->item->item_age }}）</span>@endif</a>
                    </h5>
                    @if($schedule->item->item_cate != 3)
                    @php
                        $szParts = [];
                        if ($schedule->item->item_height) $szParts[] = 'T'.$schedule->item->item_height;
                        if ($schedule->item->item_b) {
                            $b = 'B'.$schedule->item->item_b;
                            if ($schedule->item->item_cup) $b .= '('.$schedule->item->item_cup.')';
                            $szParts[] = $b;
                        }
                        if ($schedule->item->item_w) $szParts[] = 'W'.$schedule->item->item_w;
                        if ($schedule->item->item_h) $szParts[] = 'H'.$schedule->item->item_h;
                    @endphp
                    @if(!empty($szParts))
                    <p class="item-card-sizes">{{ implode(' ', $szParts) }}</p>
                    @endif
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
            @empty
            <p class="schedule-empty">本日のスケジュールはありません</p>
            @endforelse
            </div>
        </section>
    </div>

    {{-- 翌日タブ --}}
    <div id="tab-tomorrow" class="schedule-tab-content" role="tabpanel">
        <section class="cast-section">
            <div class="cast-grid">
            @forelse ($tomorrowSchedules as $schedule)
            @if(!$schedule->item) @continue @endif
            @php
                $images = $schedule->itemImages ?? collect();
                $thumbImage = $images->firstWhere('flag', 1) ?? $images->sortBy([['sort', 'asc'], ['id', 'asc']])->first(); // チェック無しなら先頭の画像
                $otherImages = ($thumbImage ? $images->reject(fn($img) => $img->id === $thumbImage->id) : $images)->values();
                $firstImage = $otherImages->get(0);
                $secondImage = $otherImages->get(1);
                $hasVideo = !empty($schedule->item->thumbnail_video);
                if ($hasVideo) {
                    $frontImage = null;
                    $backImage = $thumbImage ?? $firstImage ?? $secondImage;
                } elseif ($thumbImage) {
                    $frontImage = $thumbImage;
                    $backImage = $firstImage ?? $secondImage;
                } else {
                    $frontImage = $firstImage;
                    $backImage = $secondImage;
                }
            @endphp
            <div class="cast-card">
                <div class="cast-image">
                    <a class="cast-flip" href="{{ route('items.show', ['id' => $schedule->item->id]) }}">
                        <div class="cast-flip-front">
                            @if($hasVideo)
                            <video class="cast-thumb-video"
                                   data-src="{{ Storage::url($schedule->item->thumbnail_video) }}"
                                   muted loop playsinline></video>
                            @else
                            <img src="{{ !empty($frontImage?->filename) ? Storage::url($frontImage->filename) : asset('images/dmy.jpg') }}" alt="{{ $schedule->item->item_name }}">
                            @endif
                        </div>
                        <div class="cast-flip-back">
                            <img src="{{ !empty($backImage?->filename) ? Storage::url($backImage->filename) : asset('images/dmy.jpg') }}" alt="{{ $schedule->item->item_name }}">
                        </div>
                    </a>
                </div>
                <div class="cast-body">
                    <h5 class="cast-name">
                        <a href="{{ route('items.show', ['id' => $schedule->item->id]) }}">{{ $schedule->item->item_name }}@if($schedule->item->item_cate != 3 && $schedule->item->item_age > 0)<span class="cast-age">（{{ $schedule->item->item_age }}）</span>@endif</a>
                    </h5>
                    @if($schedule->item->item_cate != 3)
                    @php
                        $szParts = [];
                        if ($schedule->item->item_height) $szParts[] = 'T'.$schedule->item->item_height;
                        if ($schedule->item->item_b) {
                            $b = 'B'.$schedule->item->item_b;
                            if ($schedule->item->item_cup) $b .= '('.$schedule->item->item_cup.')';
                            $szParts[] = $b;
                        }
                        if ($schedule->item->item_w) $szParts[] = 'W'.$schedule->item->item_w;
                        if ($schedule->item->item_h) $szParts[] = 'H'.$schedule->item->item_h;
                    @endphp
                    @if(!empty($szParts))
                    <p class="item-card-sizes">{{ implode(' ', $szParts) }}</p>
                    @endif
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
            @empty
            <p class="schedule-empty">翌日のスケジュールはありません</p>
            @endforelse
            </div>
        </section>
    </div>

</section>


<script>
// タブ切り替え
function switchScheduleTab(btn, targetId) {
    document.querySelectorAll('.schedule-tab-btn').forEach(function(b) {
        b.classList.remove('active');
        b.setAttribute('aria-selected', 'false');
    });
    document.querySelectorAll('.schedule-tab-content').forEach(function(c) {
        c.classList.remove('active');
    });
    btn.classList.add('active');
    btn.setAttribute('aria-selected', 'true');
    document.getElementById(targetId).classList.add('active');
}

let currentSlide = 0;
document.addEventListener('DOMContentLoaded', function () {
    const slides = document.querySelectorAll('.calendar-slide');
    slides.forEach((slide, index) => {
        slide.classList.toggle('active', index === 0);
    });
    setTimeout(() => {
        if (typeof renderScheduleCalendar === 'function') renderScheduleCalendar();
        if (typeof updateCalendarDisplay === 'function') updateCalendarDisplay();
    }, 100);
});
function changeMonth(direction) {
    if (typeof changeCalendarMonth === 'function') changeCalendarMonth(direction);
}
function slideCalendar(direction) {
    const slides = document.querySelectorAll('.calendar-slide');
    currentSlide = (currentSlide + direction + slides.length) % slides.length;
    slides.forEach((slide, index) => slide.classList.toggle('active', index === currentSlide));
    const spc = window.calendarInstances?.['calendar-sp-current'];
    const spn = window.calendarInstances?.['calendar-sp-next'];
    if (spc && spn) { direction > 0 ? (spc.next(), spn.next()) : (spc.prev(), spn.prev()); }
    if (typeof updateCalendarDisplay === 'function') updateCalendarDisplay();
}
</script>

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
