@php
    $navIs = fn (...$names) => request()->routeIs(...$names) ? ' is-current' : '';

    // 旧サイト（_gotanda/www_tpl/navi.tpl）準拠のメニュー。外部リンクは URL 未設定なら出さない
    $naviMain = [
        ['jp' => 'トップ',             'en' => 'TOP',                  'icon' => 'fa-house',             'url' => route('top'),            'route' => 'top'],
        ['jp' => '料金システム',       'en' => 'SYSTEM',               'icon' => 'fa-yen-sign',          'url' => route('systems'),        'route' => 'systems'],
        ['jp' => '当日スケジュール',   'en' => "TODAY'S SCHEDULE",     'icon' => 'fa-clock',             'url' => route('schedule', now()->toDateString())],
        ['jp' => '週間スケジュール',   'en' => 'WEEKLY SCHEDULE',      'icon' => 'fa-calendar-days',     'url' => route('schedule'),       'route' => 'schedule'],
        ['jp' => 'イベント&クーポン',  'en' => 'EVENT & COUPON',       'icon' => 'fa-gift',              'url' => route('events.index'),   'route' => 'events.index'],
        ['jp' => '女の子一覧',         'en' => 'COMPANION',            'icon' => 'fa-heart',             'url' => route('item.index'),     'route' => 'item.index'],
        ['jp' => 'ツイッター 一覧',    'en' => 'TWITTER',              'icon' => 'fa-x-twitter fab',     'url' => route('x.index'),        'route' => 'x.index'],
        ['jp' => '新人紹介',           'en' => 'NEWFACE',              'icon' => 'fa-wand-magic-sparkles', 'url' => route('newface.index'), 'route' => 'newface.index'],
        ['jp' => 'ランキング',         'en' => 'RANKING',              'icon' => 'fa-crown',             'url' => route('rank.index'),     'route' => 'rank.index'],
        ['jp' => '写メ日記',           'en' => 'PHOTO DIARY',          'icon' => 'fa-camera',            'url' => $shops->shop_shame,      'external' => true],
        ['jp' => '口コミ',             'en' => 'REVIEW',               'icon' => 'fa-comment-dots',      'url' => $shops->shop_review,     'external' => true],
        ['jp' => 'ネット予約',         'en' => 'RESERVATION',          'icon' => 'fa-calendar-check',    'url' => $shops->shop_reservation, 'external' => true],
        ['jp' => 'ホテル案内・交通費', 'en' => 'HOTEL / DELIVERY FEE', 'icon' => 'fa-bed',               'url' => $shops->shop_link1 ?: route('hotels'), 'route' => 'hotels', 'external' => !empty($shops->shop_link1)],
        ['jp' => '無料オプション',     'en' => 'OPTION',               'icon' => 'fa-star',              'url' => route('options'),        'route' => 'options'],
        ['jp' => '動画',               'en' => 'MOVIE',                'icon' => 'fa-video',             'url' => $shops->shop_movie ?: route('movie.index'), 'route' => 'movie.index', 'external' => !empty($shops->shop_movie)],
        ['jp' => '女性求人情報',       'en' => 'RECRUIT',              'icon' => 'fa-paper-plane',       'url' => $shops->shop_recruit ?: route('recruits.index'), 'route' => 'recruits.index', 'external' => !empty($shops->shop_recruit)],
        ['jp' => '男性求人情報',       'en' => 'MENS RECRUIT',         'icon' => 'fa-user-tie',          'url' => $shops->shop_recruit_mens, 'external' => true],
        ['jp' => 'お問い合わせ',       'en' => 'CONTACT',              'icon' => 'fa-envelope',          'url' => route('contact'),        'route' => 'contact'],
    ];
@endphp

{{-- ============================================================
     メインナビゲーション（JP → EN の順。PC は左カラムの縦並び）
     ============================================================ --}}
<ul class="navi navi--main navi--sidebar">
    @foreach($naviMain as $navi)
        @continue(empty($navi['url']))
        @php $isExternal = !empty($navi['external']); @endphp
        <li>
            <a href="{{ $navi['url'] }}"
               class="{{ !$isExternal && !empty($navi['route']) ? $navIs($navi['route']) : '' }}"
               @if($isExternal) target="_blank" rel="noopener noreferrer" @endif>
                <span class="navi-link-icon" aria-hidden="true"><i class="{{ str_contains($navi['icon'], 'fab') ? '' : 'fas ' }}{{ $navi['icon'] }}"></i></span>
                <span class="navi-link-meta"><strong class="navi-link-jp">{{ $navi['jp'] }}</strong><span class="navi-link-en">{{ $navi['en'] }}</span></span>
            </a>
        </li>
    @endforeach
</ul>
