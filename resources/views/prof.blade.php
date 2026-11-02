@extends('layouts.app')

@section('breadcrumb_label', $item->item_name)

@section('content')

<div class="titleArea">
    <h2>PROFILE</h2>
    <div class="title">プロフィール</div>
</div>
@php use Illuminate\Support\Str; @endphp
@php $reserveUrl = route('reservation.select', ['item_id' => $item->id]); @endphp

<div class="prof-wrap">

    {{-- ■ 名前枠 (w:100%) --}}
    <div class="prof-name-area">
        @if($item->category)
        <div class="prof-cate-badge-row">
            <span style="display:inline-flex;align-items:center;gap:4px;background:{{ $item->category->color }};color:#fff;font-size:0.75rem;padding:3px 10px;border-radius:9999px;">
                <i class="{{ $item->category->icon }}"></i>{{ $item->category->name }}
            </span>
        </div>
        @endif
        <div class="prof-name-sizes-row">
            <h2 class="prof-name-heading">{{ $item->item_name }}</h2>
            @if($item->item_cate != 3 && $item->item_age > 0)
            <span class="prof-age-badge">age.{{ $item->item_age }}</span>
            @endif
            @if($item->item_cate != 3)
            @php $szParts=[]; if($item->item_height)$szParts[]='T'.$item->item_height; if($item->item_b){$b='B'.$item->item_b;if($item->item_cup&&$item->item_cup!='-')$b.='('.$item->item_cup.')';$szParts[]=$b;} if($item->item_w)$szParts[]='W'.$item->item_w; if($item->item_h)$szParts[]='H'.$item->item_h; @endphp
            @if(!empty($szParts))
            <span class="prof-sizes-inline item-card-sizes">{{ implode(' ', $szParts) }}</span>
            @endif
            @endif
        </div>
        @if($item->item_catch)
        <p class="item-catch" style="color:{{ $item->category?->color ?? '#e91e8c' }};font-size:0.85rem;font-weight:600;margin:4px 0;">{{ $item->item_catch }}</p>
        @endif
    </div>


    {{-- ■ メインカラム2分割 --}}
    <div class="prof-main-grid">

        {{-- 左カラム：写真・動画 --}}
        <div class="prof-col-left">
            <div class="prof-photo-card">
                <div class="swiper profile-swiper">
                    <div class="swiper-wrapper">
                        @if(!empty($item->thumbnail_video))
                            <div class="swiper-slide prof-slide-video">
                                <div class="prof-photo-aspect">
                                    <video class="prof-photo-aspect-img prof-thumb-video-slide"
                                           src="{{ Storage::url($item->thumbnail_video) }}"
                                           muted loop playsinline autoplay></video>
                                </div>
                            </div>
                        @endif
                        @foreach ($images as $image)
                            <div class="swiper-slide">
                                <div class="prof-photo-aspect">
                                    <img src="{{ !empty($image->filename) ? Storage::url($image->filename) : asset('images/dmy.jpg') }}"
                                         alt="{{ $item->item_name }}"
                                         class="prof-photo-aspect-img"
                                         width="800"
                                         height="1200"
                                         loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next profile-next"></div>
                    <div class="swiper-button-prev profile-prev"></div>
                    <div class="swiper-pagination profile-pagination"></div>
                </div>
            </div>

            @if($item->item_video)
            <div class="prof-video-wrap">
                @if(str_starts_with($item->item_video, 'http'))
                    {{-- 外部URL（TikTok等） --}}
                    <a href="{{ $item->item_video }}" target="_blank" rel="noopener noreferrer" class="prof-video-external">
                        <i class="fas fa-play-circle"></i> 動画を見る
                    </a>
                @else
                    {{-- ローカルファイル --}}
                    <video src="{{ Storage::url($item->item_video) }}" controls playsinline class="prof-video">
                        お使いのブラウザは動画再生に対応していません。
                    </video>
                @endif
            </div>
            @endif
        </div>

        {{-- 右カラム --}}
        <div class="prof-col-right">

            {{-- ショップコメント --}}
            @if($item->item_comment2)
            <div class="prof-shop-comment">
                <div class="prof-heading shop">From SHOP</div>
                <div class="prof-comment-text">{!! ($item->item_comment2) !!}</div>
            </div>
            @endif

            {{-- プレミアランク --}}
            @if($item->premiumRank)
            @php
                $feeSwitchAt  = \Carbon\Carbon::parse('2026-07-15 06:00:00');
                $displayFee   = \Carbon\Carbon::now()->lt($feeSwitchAt)
                    ? $item->premiumRank->fee_sub
                    : $item->premiumRank->fee;
            @endphp
            <div class="prof-premium-rank-card">
                <h3 class="prof-premium-rank-heading">{{ $item->premiumRank->name }}</h3>
                @if($displayFee)
                <div class="prof-premium-rank-fee">{!! nl2br(e($displayFee)) !!}</div>
                @endif
                @if(mb_strlen((string)$item->premiumRank->note) > 0)
                <div class="prof-premium-rank-note">{{ $item->premiumRank->note }}</div>
                @endif
            </div>
            @endif

            {{-- プロフィール --}}
            @if($item->profiles->count() > 0)
            <div class="prof-profiles">
                @foreach($item->profiles as $profile)
                    @if($profile->pivot->content)
                    <div class="prof-profile-item">
                        <div class="prof-profile-content">
                            <h4 class="prof-profile-key">{{ $profile->title }}</h4>
                            <p class="prof-profile-val">{!! nl2br(e($profile->pivot->content)) !!}</p>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            @endif

            {{-- Q&A --}}
            @if($item->interviews->count() > 0)
            <div class="prof-qa-card">
                <h3 class="prof-qa-heading">Q&amp;A</h3>
                <div class="prof-qa-list">
                    @foreach($item->interviews as $interview)
                    <div class="prof-qa-item">
                        <h3 class="prof-qa-question"><i class="fas fa-question-circle"></i>{{ $interview->title }}</h3>
                        <div class="prof-qa-answer">
                            <p>{!! nl2br(e($interview->pivot->content)) !!}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- /prof-col-right --}}
    </div>{{-- /prof-main-grid --}}


    {{-- 指名コメント --}}
    @if($item->item_comment_nominate)
    <div class="prof-section-wrap">
        <div class="prof-text-comment">
            <div class="prof-heading text">指名コメント</div>
            <div class="prof-comment-text">{!! ($item->item_comment_nominate) !!}</div>
        </div>
    </div>
    @endif

    {{-- Q&Aコメント --}}
    @if($item->item_comment_faq)
    <div class="prof-section-wrap">
        <div class="prof-faq-comment">
            <div class="prof-heading faq">Q&amp;A</div>
            <div class="prof-comment-faq">{!! ($item->item_comment_faq) !!}</div>
        </div>
    </div>
    @endif

    {{-- ■ リアルタイムメッセージ --}}
    @if($item->item_p_comment)
    <div class="prof-section-wrap" id="realtime-comment">
        <div class="prof-message-card">
            <div class="prof-message-deco">
                <svg viewBox="0 0 100 100" style="width:100%;height:100%;fill:#f9a8d4;">
                    <path d="M50 20c16.569 0 30 13.431 30 30s-13.431 30-30 30-30-13.431-30-30S33.431 20 50 20zm0 45c8.284 0 15-6.716 15-15s-6.716-15-15-15-15 6.716-15 15 6.716 15 15 15z"/>
                </svg>
            </div>
            <div class="prof-message-inner">
                <img src="{{ $item->th_flename() }}" alt="{{ $item->item_name }}" class="prof-message-thumb-img">
                <div class="prof-message-header">
                    <h3 class="prof-message-title">本指名コメント</h3>
                </div>
                <div class="prof-message-bubble">
                    <div class="prof-message-text">{!! $item->item_p_comment !!}</div>
                </div>
                @if($item->item_p_comment_date)
                <p class="prof-message-date">
                    <i class="far fa-clock" style="margin-right:0.25rem;"></i>{{ $item->item_p_comment_date->format('Y/m/d H:i') }} 更新
                </p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ■ 外部リンク --}}
    @if($item->item_twitter || $item->item_url || $item->item_youtube)
    @php
        $twitter    = $item->item_twitter ?? '';
        $twitterUrl = Str::startsWith($twitter,'http') ? $twitter : 'https://x.com/' . ltrim($twitter, '/');
        $youtube    = $item->item_youtube ?? '';
        $youtubeUrl = Str::startsWith($youtube,'http') ? $youtube : 'https://youtube.com/' . ltrim($youtube, '/');
    @endphp
    <div class="prof-ext-links">
        @if($item->item_twitter)
        <a href="{{ $twitterUrl }}" target="_blank" class="prof-ext-link prof-ext-x">
            <i class="fab fa-x-twitter"></i><span>X</span>
        </a>
        @endif
        @if($item->item_url)
        <a href="{{ $item->item_url }}" target="_blank" class="prof-ext-link prof-ext-diary">
            <i class="fas fa-camera"></i><span>写メ日記</span>
        </a>
        @endif
        @if($item->item_youtube)
        <a href="{{ $youtubeUrl }}" target="_blank" class="prof-ext-link prof-ext-youtube">
            <i class="fab fa-youtube"></i><span>動画</span>
        </a>
        @endif
    </div>
    @endif


    {{-- ■ 写メ日記 (w:100%) --}}
    @if($item->item_url)
    <div class="prof-diary-wrap">
        <iframe src="{{ $item->item_url }}" class="prof-diary-iframe" title="{{ $item->item_name }}の写メ日記" sandbox="allow-scripts allow-same-origin allow-popups allow-forms"></iframe>
    </div>
    @endif


    {{-- ■ 対応オプション --}}
    @php
        $hasOptions = $item->options->isNotEmpty();
        $hasOptionPays = $item->option_pays->isNotEmpty();
    @endphp
    @if($hasOptions || $hasOptionPays)
    <div class="prof-options-section">
        <h3 class="prof-options-heading"><i class="fas fa-check-circle"></i> 対応オプション</h3>
        @foreach($optionCates as $cate)
            @php
                $cateOptions    = $item->options->where('cate', $cate->id);
                $cateOptionPays = $item->option_pays->where('cate', $cate->id);
                $allInCate      = $cateOptions->merge($cateOptionPays);
            @endphp
            @if($allInCate->isNotEmpty())
            <div class="prof-options-group">
                <span class="prof-options-cate-label">{{ $cate->title }}</span>
                <div class="prof-options-badges">
                    @foreach($allInCate as $opt)
                    <span class="prof-option-badge">{{ $opt->title }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    </div>
    @endif


    {{-- ■ 対応オプション一覧 --}}
    @if($mycostumes->count() > 0 || $myoptions->count() > 0)
    <div class="prof-tabs-wrap">
        <div class="prof-tabs-card">
            <div class="prof-tabs-nav-wrap">
                <nav class="prof-tabs-nav">
                    @if($mycostumes->count() > 0)
                    <button type="button" class="tab-button active" data-tab="costumes-tab">
                        <span class="tab-icon tab-icon-costume"><i class="fas fa-tshirt"></i></span>マイコスチューム
                    </button>
                    @endif
                    @if($myoptions->count() > 0)
                    <button type="button" class="tab-button{{ $mycostumes->count() == 0 ? ' active' : '' }}" data-tab="options-tab">
                        <span class="tab-icon tab-icon-option"><i class="fas fa-gift"></i></span>マイオプション
                    </button>
                    @endif
                </nav>
            </div>
            <div class="prof-tabs-body">
                @if($mycostumes->count() > 0)
                <div id="costumes-tab" class="tab-content">
                    <div class="prof-costumes-grid">
                        @foreach ($mycostumes as $mycostume)
                        <div class="prof-costume-card costume-item"
                             data-type="costume" data-id="{{ $mycostume->id }}"
                             data-name="{{ $mycostume->name }}"
                             data-image="{{ $mycostume->filename ? Storage::url('mycostume/' . $mycostume->filename) : asset('images/dmy.jpg') }}"
                             data-body="{{ $mycostume->body }}" data-price="{{ $mycostume->price }}"
                             data-category="{{ $mycostumeCates->where('id', $mycostume->mycostume_cate_id)->first()->name ?? '' }}">
                            <div class="prof-costume-img-wrap">
                                <img src="{{ $mycostume->filename ? Storage::url('mycostume/' . $mycostume->filename) : asset('images/dmy.jpg') }}"
                                     alt="{{ $mycostume->name }}" class="prof-costume-img">
                            </div>
                            <div class="prof-costume-body">
                                <h3 class="prof-costume-name">{{ $mycostume->name }}</h3>
                                <p class="prof-costume-cat">
                                    @foreach($mycostumeCates as $cate)
                                        @if($mycostume->mycostume_cate_id == $cate->id){{ $cate->name }}@endif
                                    @endforeach
                                </p>
                                @if($mycostume->body)<p class="prof-costume-desc">{{ $mycostume->body }}</p>@endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($myoptions->count() > 0)
                <div id="options-tab" class="tab-content{{ $mycostumes->count() > 0 ? ' hidden' : '' }}">
                    <div class="prof-options-grid">
                        @foreach ($myoptions as $myoption)
                        <div class="prof-costume-card option-item"
                             data-type="option" data-id="{{ $myoption->id }}"
                             data-name="{{ $myoption->name }}"
                             data-image="{{ $myoption->filename ? Storage::url('myoption/' . $myoption->filename) : asset('images/dmy.jpg') }}"
                             data-body="{{ $myoption->body }}" data-price="{{ $myoption->price }}"
                             data-category="{{ $myoptionCates->where('id', $myoption->myoption_cate_id)->first()->name ?? '' }}">
                            <div class="prof-costume-img-wrap">
                                <img src="{{ $myoption->filename ? Storage::url('myoption/' . $myoption->filename) : asset('images/dmy.jpg') }}"
                                     alt="{{ $myoption->name }}" class="prof-costume-img">
                            </div>
                            <div class="prof-costume-body">
                                <h3 class="prof-costume-name">{{ $myoption->name }}</h3>
                                <p class="prof-option-cat">
                                    @foreach($myoptionCates as $cate)
                                        @if($myoption->myoption_cate_id == $cate->id){{ $cate->name }}@endif
                                    @endforeach
                                </p>
                                @if($myoption->body)<p class="prof-costume-desc">{{ $myoption->body }}</p>@endif
                                @if($myoption->price > 0)<p style="font-weight:700;font-size:0.875rem;color:#374151;">{{ number_format($myoption->price) }}円</p>@endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif


    {{-- ■ スケジュール --}}
    <div class="prof-schedule-card">
        <div id="schedule">
            <h3 class="prof-sched-heading"><i class="fa-regular fa-calendar-check"></i> 出勤スケジュール</h3>
            <div class="sched-table-wrap" id="profDstrip"
                 data-base="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                <div class="sched-table-nav">
                    <button class="sched-nav-btn" type="button" onclick="dstripProf.slide(-1)" aria-label="前の7日間">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="sched-table-range" id="profDstripRange"></span>
                    <button class="sched-nav-btn" type="button" onclick="dstripProf.slide(1)" aria-label="次の7日間">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <ul class="sched-list" id="profDstripBody"></ul>
            </div>
        </div>
    </div>




    {{-- ■ ファンクラブ --}}
    @if(isset($fanclub) && $fanclub)
    <div class="prof-section-wrap">
        <div class="prof-fc-card">
            <div class="prof-fc-header">
                <h3 class="prof-fc-section-title">
                    <i class="fas fa-star" style="color:var(--color-bg-fanclub);margin-right:0.5rem;"></i>ファンクラブ
                </h3>
            </div>
            <div class="prof-fc-body">
                @if($fanclub->main_image_path)
                <div class="prof-fc-img-wrap">
                    <a href="{{ route('fanclub.show', $fanclub) }}">
                        <img src="{{ asset('storage/' . $fanclub->main_image_path) }}"
                             alt="{{ $fanclub->title }}" class="prof-fc-img">
                    </a>
                </div>
                @endif
                <div class="prof-fc-info">
                    <h4 class="prof-fc-name">{{ $fanclub->title }}</h4>
                    @if($fanclub->description)
                    <p class="prof-fc-desc">{{ Str::limit($fanclub->description, 100) }}</p>
                    @endif
                    <div class="prof-fc-meta">
                        <span class="prof-fc-members"><i class="fas fa-users"></i>{{ $fanclub->active_member_count }}人参加中</span>
                        <span class="prof-fc-price"><i class="fas fa-yen-sign"></i>入会費 {{ number_format($fanclub->price) }}円</span>
                    </div>
                    <div class="prof-fc-cta">
                        <a href="{{ route('fanclub.show', $fanclub) }}" class="prof-fc-btn-detail">
                            <i class="fas fa-info-circle"></i> 詳細を見る
                        </a>
                        @if($isFanMember)
                            <span class="prof-fc-badge-member"><i class="fas fa-check-circle"></i> 会員登録済み</span>
                        @elseif($member)
                            <form method="POST" action="{{ route('member.fanclubs.subscribe', $fanclub) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="prof-fc-btn-join">
                                    <i class="fas fa-heart"></i> ファンクラブに参加する
                                </button>
                            </form>
                        @else
                            <a href="{{ route('member.login') }}" class="prof-fc-btn-login">
                                <i class="fas fa-sign-in-alt"></i> ログインして参加
                            </a>
                            <a href="{{ route('member.register') }}" class="prof-fc-btn-register">
                                <i class="fas fa-user-plus"></i> 会員登録
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif


    {{-- ■ 料金表（個別料金制のみ表示） --}}
    @php
        $availableCourses = $item->courses->filter(fn($c) => $c->pivot->status !== 'ng');
        $courseFees = $item->courseFees;
    @endphp
    @if(($pricingType ?? 'unified') === 'individual' && $courseFees->count() > 0)
    <div class="prof-section-wrap">
        <div class="prof-price-card">
            <div class="prof-price-heading-wrap">
                <h3 class="prof-price-heading">● {{ $item->item_name }}のコース料金</h3>
            </div>
            <div class="prof-price-table-wrap">
                <table class="prof-price-table">
                    <thead>
                        <tr>
                            <th>コース</th>
                            <th>60分</th><th>90分</th><th>120分</th>
                            <th>150分</th><th>180分</th><th>以降30分</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courseFees as $course)
                            <tr>
                                <td class="course-name-cell">{{ $course->title }}</td>
                                @foreach(['fee_60','fee_90','fee_120','fee_150','fee_180','fee_extension'] as $col)
                                <td>
                                    @if($course->pivot->$col)
                                        ￥<span class="prof-price-val">{{ number_format($course->pivot->$col) }}</span>
                                        <span class="prof-price-tax">(税込 ￥{{ number_format(floor($course->pivot->$col * config('tax.intax'))) }})</span>
                                    @else
                                        <span class="prof-price-empty">-</span>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif


    {{-- ■ 特設イベント --}}
    @if($specialEvents->count() > 0)
    <div class="prof-events-card">
        <div class="prof-events-head">
            <h3 class="prof-events-title">
                <i class="fa-regular fa-calendar-days" style="color:#ffb6e1;margin-right:0.75rem;"></i>参加中の特設イベント
            </h3>
        </div>
        <div class="prof-events-body">
            <div class="prof-events-grid">
                @foreach($specialEvents as $specialEvent)
                <div class="prof-event-card">
                    @if($specialEvent->main_image)
                    <div class="prof-event-img-wrap">
                        <img src="{{ asset('storage/' . $specialEvent->main_image) }}" alt="{{ $specialEvent->title }}">
                    </div>
                    @endif
                    <h4 class="prof-event-name">{{ $specialEvent->title }}</h4>
                    <div class="prof-event-details">
                        <div class="prof-event-meta"><i class="fas fa-calendar-alt"></i><span>{{ $specialEvent->event_date->format('Y年m月d日') }}</span></div>
                        <div class="prof-event-meta"><i class="fas fa-clock"></i><span>{{ $specialEvent->start_time }} - {{ $specialEvent->end_time }}</span></div>
                        @if($specialEvent->venue)
                        <div class="prof-event-meta"><i class="fas fa-map-marker-alt"></i><span>{{ $specialEvent->venue }}</span></div>
                        @endif
                        @if($specialEvent->fee)
                        <div class="prof-event-meta"><i class="fas fa-yen-sign"></i><span class="prof-event-price">¥{{ number_format($specialEvent->fee) }}</span></div>
                        @endif
                    </div>
                    <a href="{{ route('specialEvents.show', $specialEvent->id) }}" class="prof-event-btn">
                        <i class="fas fa-external-link-alt" style="margin-right:0.5rem;"></i>詳細を見る
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif


    <div class="prof-back">
        <x-back-to-list href="{{ route('item.index') }}" text="キャスト一覧に戻る" />
    </div>
</div>{{-- /prof-wrap --}}


{{-- コスチューム / オプション モーダル --}}
<div id="itemModal" class="prof-modal-overlay hidden" style="z-index:9999;">
    <div class="prof-modal-center">
        <div class="prof-modal-box" style="position:relative;">
            <img id="modalImage" src="" alt="" class="prof-modal-img">
            <button id="closeItemModal" class="prof-modal-close" style="z-index:10;">
                <i class="fas fa-times" style="font-size:0.875rem;"></i>
            </button>
            <div class="prof-modal-body">
                <h3 id="modalTitle" style="font-size:1.25rem;font-weight:700;color:#111827;margin-bottom:0.5rem;"></h3>
                <p id="modalCategory" style="font-size:0.875rem;color:#9333ea;margin-bottom:1rem;"></p>
                <p id="modalBody" style="font-size:0.75rem;color:#374151;white-space:pre-line;word-break:break-all;margin-bottom:1rem;"></p>
                <p id="modalPrice" style="font-size:1.5rem;font-weight:700;color:#374151;"></p>
            </div>
        </div>
    </div>
</div>

<script>
// 7日間ずつスライドする出勤リスト（日付・曜日・時間・ステータス）
const dstripProf = (function () {
    const root = document.getElementById('profDstrip');
    const body = document.getElementById('profDstripBody');
    const rangeEl = document.getElementById('profDstripRange');
    if (!root || !body) return { slide: function () {} };

    const schedules = @json($schedules);
    const base = root.dataset.base;
    const dows = ['日', '月', '火', '水', '木', '金', '土'];
    const STATUS = {
        1: { label: '受付中',   cls: 'request' },
        2: { label: 'TEL確認',  cls: 'tel' },
        3: { label: '受付終了', cls: 'closed' },
        4: { label: '当日休み', cls: 'holiday' },
        5: { label: '完売',     cls: 'sold' },
    };
    let offset = 0;

    function ymd(d) {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const da = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${da}`;
    }
    function fmtTime(v) {
        if (!v) return '';
        try {
            let timeStr = String(v);
            if (/^\d{4}-\d{2}-\d{2}[ T]/.test(timeStr)) {
                timeStr = timeStr.split(/[ T]/)[1] || '';
            }
            const parts = timeStr.split(':');
            const h = parseInt(parts[0], 10);
            const m = parseInt(parts[1] || '0', 10);
            if (isNaN(h) || isNaN(m)) return '';
            if (h >= 24) {
                return '翌' + String(h - 24).padStart(2, '0') + ':' + String(m).padStart(2, '0');
            }
            return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
        } catch (e) { return ''; }
    }

    function render() {
        const start = new Date(base + 'T00:00:00');
        start.setDate(start.getDate() + offset);
        const todayStr = ymd(new Date());
        let rows = '';
        for (let i = 0; i < 7; i++) {
            const d = new Date(start);
            d.setDate(start.getDate() + i);
            const ds = ymd(d);
            const dow = d.getDay();
            const day = schedules.filter(s => (s.date || '').slice(0, 10) === ds);
            const has = day.length > 0;
            const isToday = ds === todayStr;

            let cls = 'sched-day';
            if (isToday) cls += ' is-today';
            if (dow === 0) cls += ' sun';
            if (dow === 6) cls += ' sat';

            let timeCell, statusCell;
            if (has) {
                cls += ' is-work';
                const s = day[0];
                const st = fmtTime(s.time_start), et = fmtTime(s.time_end);
                timeCell = (st && et)
                    ? `<span class="sched-time-val">${st}</span><span class="sched-time-sep">〜</span><span class="sched-time-val">${et}</span>`
                    : '<span class="sched-time-tbd">時間未定</span>';
                const stt = STATUS[s.status] || { label: '受付中', cls: 'request' };
                statusCell = `<span class="sched-status ${stt.cls}">${stt.label}</span>`;
            } else {
                cls += ' is-off';
                timeCell = '<span class="sched-time-off">お休み</span>';
                statusCell = '';
            }

            rows += `<li class="${cls}">`
                +  `<div class="sched-date">`
                +    `<span class="sched-date-md">${d.getMonth() + 1}/${d.getDate()}</span>`
                +    `<span class="sched-date-dow">${dows[dow]}</span>`
                +  `</div>`
                +  `<div class="sched-time">${isToday ? '<span class="sched-today-badge">本日</span>' : ''}${timeCell}</div>`
                +  `<div class="sched-state">${statusCell}</div>`
                +  `</li>`;
        }
        body.innerHTML = rows;

        if (rangeEl) {
            const end = new Date(start);
            end.setDate(start.getDate() + 6);
            rangeEl.textContent = `${start.getMonth() + 1}/${start.getDate()} 〜 ${end.getMonth() + 1}/${end.getDate()}`;
        }

        body.classList.remove('sched-anim');
        void body.offsetWidth;
        body.classList.add('sched-anim');
    }

    render();
    return { slide: function (dir) { offset += dir * 7; render(); } };
})();

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('closeItemModal')?.addEventListener('click', () => { document.getElementById('itemModal').classList.add('hidden'); });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.costume-item, .option-item').forEach(el => {
        el.addEventListener('click', function() {
            const modal = document.getElementById('itemModal');
            document.getElementById('modalImage').src = this.dataset.image;
            document.getElementById('modalImage').style.objectFit = 'contain';
            document.getElementById('modalTitle').textContent    = this.dataset.name;
            document.getElementById('modalCategory').textContent = this.dataset.category;
            document.getElementById('modalBody').textContent     = this.dataset.body;
            document.getElementById('modalPrice').textContent    = '';
            modal.classList.remove('hidden');
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            this.classList.add('active');
            const target = document.getElementById(this.dataset.tab);
            if (target) target.classList.remove('hidden');
        });
    });
});
</script>

@endsection
