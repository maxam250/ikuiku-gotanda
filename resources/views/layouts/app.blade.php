<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        // SEO設定（管理画面で設定した値を優先、なければデフォルト）
        $defaultTitle       = $shops->shop_area.' デリヘル | '.$shops->shop_name;
        $defaultDescription = $shops->shop_area.'のデリバリーヘルス' . ($shops->shop_copy ?? '');
        $defaultOgImage     = ($shops->shop_url ?? '') . '/favicon/thumb.jpg';

        // SEOテンプレート変数の置換 ({cast_name}, {shop_name})
        $seoVars = [
            '{cast_name}' => isset($item) ? ($item->item_name ?? '') : '',
            '{shop_name}' => $shops->shop_name ?? '',
        ];
        $resolveSeo = fn(?string $v) => $v ? strtr($v, $seoVars) : $v;

        $rawTitle       = $seoData?->meta_title       ? $resolveSeo($seoData->meta_title)       : null;
        $rawDescription = $seoData?->meta_description ? $resolveSeo($seoData->meta_description) : null;
        $rawOgTitle     = $seoData?->og_title         ? $resolveSeo($seoData->og_title)         : null;
        $rawOgDesc      = $seoData?->og_description   ? $resolveSeo($seoData->og_description)   : null;

        $seoTitle       = $rawTitle       ? $rawTitle . ' | ' . $shops->shop_name : $defaultTitle;
        $seoDescription = $rawDescription ?: $defaultDescription;
        $seoKeywords    = ($seoData && $seoData->meta_keywords)     ? $seoData->meta_keywords    : null;
        $seoRobots      = (!empty($forceNoindex) || ($seoData && $seoData->noindex)) ? 'noindex, nofollow' : 'index, follow';
        $seoCanonical   = ($seoData && $seoData->canonical_url)     ? $seoData->canonical_url    : url()->current();
        $ogTitle        = $rawOgTitle ?: $seoTitle;
        $ogDescription  = $rawOgDesc  ?: $seoDescription;
        $ogImage        = ($seoData && $seoData->og_image)          ? $seoData->og_image         : $defaultOgImage;
    @endphp

    {{-- Primary Meta Tags --}}
    <title>{{ $seoTitle }}</title>
    <meta name="title" content="{{ $seoTitle }}">
    <meta name="description" content="{{ $seoDescription }}">
    @if($seoKeywords)
    <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    <meta name="author" content="{{ $shops->shop_name }}">
    <meta name="robots" content="{{ $seoRobots }}">
    <meta name="language" content="Japanese">
    <meta name="revisit-after" content="7 days">

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $seoCanonical }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="{{ $shops->shop_name ?? '' }}">
    <meta property="og:locale" content="ja_JP">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $seoCanonical }}">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <meta name="thumbnail" content="{{ $shops->shop_url }}/favicon/thumb.jpg">

    {{-- Google Site Verification --}}
    <meta name="google-site-verification" content="google-site-verification=QFqnG8x6eYza0ShKxXrK1QKhodF_hTFhrSHu_svxY54">

    {{-- ViteでコンパイルされたTailwind CSS --}}
    @vite(['resources/css/frontend/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/base.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/layout/app.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/form.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/item_card.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/search-component.css') }}">

    {{-- External CSS Libraries --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    {{-- 見出し用の丸ゴシック（旧サイトの平成丸ゴシック相当） --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@500;700;800&display=swap" rel="stylesheet">

    @php
        $pageid = Route::currentRouteName() ?? '';
        $isMemberPage = str_starts_with($pageid, 'member.');
    @endphp

    @if($pageid == 'schedule' || $pageid == 'top')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/schedule.css') }}">
    @endif

    @if(str_contains($pageid, 'login'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/login.css') }}">
    @elseif($pageid == 'fanclub' || $pageid == 'fanclub.show' || str_contains($pageid, 'fanclubs'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/fanclub.css') }}">
    @if($isMemberPage)
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/mypage.css') }}">
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/member-buttons.css') }}"> --}}
    @endif
    @elseif($pageid == 'items.show')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/prof.css') }}">
    @elseif($pageid == 'news.index' || $pageid == 'news.show')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/news.css') }}">
    @elseif($pageid == 'events.index')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/event.css') }}">
    @elseif($pageid == 'top')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/event-slider.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/index.css') }}">
    @elseif($pageid == 'systems')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/system.css') }}">
    @elseif($pageid == 'rank.index')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/ranking.css') }}">
    @elseif($isMemberPage)
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/mypage.css') }}">
    @elseif($pageid == 'recruits.index')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/recruit.css') }}">
    @elseif($pageid == 'hotels')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/hotel.css') }}">
    @elseif($pageid == 'options')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/options.css') }}">
    @elseif(str_starts_with($pageid, 'reservation.'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/reservation.css') }}">
    @endif

    {{-- Google Analytics --}}
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $shops->shop_analytics }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $shops->shop_analytics }}');
    </script>

    {{-- Structured Data (JSON-LD) --}}
    @php
        // 03-XXXX-XXXX → +81-3-XXXX-XXXX（無店舗型用）
        $schemaPhone = preg_replace('/^0/', '+81-', $shops->shop_tel ?? '');
    @endphp
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "{{ $shops->shop_name }}",
        "url": "{{ $shops->shop_url }}",
        "logo": "{{ $shops->shop_url }}/images/common/header/logo.webp",
        "image": "{{ $shops->shop_url }}/images/common/header/logo.webp",
        "description": "{{ $shops->shop_area }}",
        "areaServed": "{{ $shops->shop_area }}",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "{{ $schemaPhone }}",
            "contactType": "customer support",
            "areaServed": "JP",
            "availableLanguage": ["Japanese"]
        },
        "sameAs": [
            @if($shops->shop_youtube)"{{ $shops->shop_youtube }}",@endif
            @if($shops->shop_x)"{{ $shops->shop_x }}",@endif
            @if($shops->shop_instagram)"{{ $shops->shop_instagram }}",@endif
            @if($shops->shop_tiktok)"{{ $shops->shop_tiktok }}",@endif
            @if($shops->shop_facebook)"{{ $shops->shop_facebook }}"@endif
        ],
        "openingHours": "{{ $shops->shop_open ?? '営業時間情報' }}"
    }
    </script>

    {{-- LocalBusiness Schema（無店舗型：住所・座標なし） --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "{{ $shops->shop_name }}",
        "image": "{{ $shops->shop_url }}/favicon/thumb.jpg",
        "@id": "{{ $shops->shop_url }}",
        "url": "{{ $shops->shop_url }}",
        "telephone": "{{ $schemaPhone }}",
        "priceRange": "$$",
        "areaServed": {
            "@type": "AdministrativeArea",
            "name": "{{ $shops->shop_area }}"
        },
        "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": [
                "Monday",
                "Tuesday",
                "Wednesday",
                "Thursday",
                "Friday",
                "Saturday",
                "Sunday"
            ],
            "opens": "06:00",
            "closes": "30:00"
        }
    }
    </script>
    {{-- 旧サイト（_gotanda）デザイン準拠のテーマ。他のCSSより後に読み込む --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('css/layout/theme-gotanda.css') }}?v={{ filemtime(public_path('css/layout/theme-gotanda.css')) }}">
    @stack('head')
</head>
<body class="app-body {{ ($pageid ?? '') === 'top' ? 'is-top' : 'is-subpage' }}">
    {{-- Skip to main content (アクセシビリティ) --}}
    <a href="#main-content" class="skip-link">
        メインコンテンツへスキップ
    </a>

    <div class="app-container">
        {{-- レスポンシブヘッダー --}}
        <header class="header-gradient header-main" style="z-index: 9999;" role="banner">
                {{-- スマホ用上段：ログイン・会員登録（右寄せ） --}}


            <div class="header-inner">

                <div class="header-content">
                    {{-- ロゴエリア --}}
                    <div class="header-logo-area">
                        {{-- メニューボタン (タブレット・モバイル用) --}}
                        <button
                            onclick="toggleMobileMenu()"
                            class="header-menu-btn border-none"
                            id="menuBtn"
                            aria-label="メニューを開く"
                            aria-expanded="false"
                            aria-controls="mobileMenu">
                            <img src="{{ asset('images/common/menu_sp.png') }}" alt="MENU" class="header-menu-icon">
                        </button>

                        {{-- ロゴ (左寄せ) --}}
                        <h1 class="header-logo">
                            <a href="{{ route('top') }}" class="header-logo-link" aria-label="{{ $shops->shop_name }} ホーム">
                                <img src="{{ asset('images/common/header/logo.webp') }}"
                                     alt="{{ $shops->shop_name }}"
                                     class="header-logo-img"
                                     width="240"
                                     height="114"
                                     loading="eager"
                                     onerror="this.style.display='none'">
                            </a>
                        </h1>

                    </div>

                    {{-- 右側：営業情報 + サブナビ --}}
                    <div class="header-aside">
                        <nav class="header-nav" aria-label="営業情報">
                            <div class="header-nav-info header-nav-info--open">
                                <div class="header-nav-info-body">
                                    <span class="header-nav-label header-nav-label--open">
                                        open/close
                                    </span>
                                    <span class="header-nav-value">{{ $shops->shop_open }}</span>
                                </div>
                            </div>
                            <div class="header-nav-sep" aria-hidden="true"></div>
                            <div class="header-nav-info header-nav-info--phone">
                                <div class="header-nav-info-body">
                                    <span class="header-nav-label header-nav-label--phone">
                                        tel.
                                    </span>
                                    <a href="tel:{{ str_replace(['-', ' '], '', $shops->shop_tel) }}"
                                       class="header-nav-value header-nav-value--tel">{{ $shops->shop_tel }}</a>
                                </div>
                            </div>
                        </nav>
                        {{-- 届出表示・お問い合わせ（旧 header.tpl .telArea .other） --}}
                        <div class="header-other">
                            <span class="header-other-text">無店舗型性風俗特殊営業届出済</span>
                            <a href="{{ route('contact') }}" class="header-other-contact"><i class="far fa-envelope" aria-hidden="true"></i> お問合わせはこちら</a>
                        </div>
                        <nav class="header-subnav" aria-label="サブナビゲーション">
                            @include('layouts.navigation')
                        </nav>
                    </div>
                </div>

                {{-- スマホ：日付・在籍人数・本日の出勤人数（旧 sp_tpl/header.tpl #count） --}}
                @php
                    $headerToday = \Carbon\Carbon::parse(adjustTimeWithShift())->startOfDay(); // 営業日（トップの出勤と同じ）
                    $headerItemCount = \App\Models\Item::where('item_active', 1)->count();
                    $headerSchCount = \App\Models\Schedule::whereDate('date', $headerToday)
                        ->whereHas('item', fn ($q) => $q->where('item_active', 1))
                        ->count();
                @endphp
                <div class="header-count">
                    <div class="header-count-date">{{ $headerToday->format('Y年m月d日') }}</div>
                    <p class="header-count-item"><strong>在籍人数</strong><span class="num">{{ $headerItemCount }}</span> 名</p>
                    <p class="header-count-sch"><strong>本日の出勤人数</strong><span class="num">{{ $headerSchCount }}</span> 名</p>
                </div>
            </div>

            {{-- PC: ナビが画面外になったときだけ表示する menu タブ --}}
            <div class="header-nav-toggle-wrap" id="headerNavToggleWrap">
                <button type="button"
                        class="header-nav-toggle"
                        id="headerNavToggle"
                        onclick="toggleTopNav()"
                        aria-expanded="false"
                        aria-controls="appTopnavFlyout"
                        aria-label="ナビゲーションを開く">
                    <span class="header-nav-toggle-label">menu</span>
                    <i class="fas fa-angle-down header-nav-toggle-icon" aria-hidden="true"></i>
                </button>
            </div>
        </header>

        {{-- PC: 通常時の横型ナビ（ヘッダの下・スクロールで流れる） --}}
        <nav class="app-topnav" id="appTopnav" aria-label="サイト内ナビゲーション" role="navigation">
            <div class="app-topnav-inner">
                @include('layouts.navigation')
            </div>
        </nav>

        {{-- PC: スクロール後、ヘッダ下からスライドするナビ --}}
        <nav class="app-topnav app-topnav-flyout" id="appTopnavFlyout" aria-label="サイト内ナビゲーション" aria-hidden="true" inert>
            <div class="app-topnav-inner">
                @include('layouts.navigation')
            </div>
        </nav>

        {{-- モバイルメニューオーバーレイ --}}
        <div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()" aria-hidden="true"></div>

        {{-- モバイルメニュー --}}
        <nav class="mobile-menu" id="mobileMenu" role="navigation" aria-label="モバイルメニュー" aria-hidden="true">
            <div class="mobile-menu-header">
                <div class="mobile-menu-header-content">
                    <h2 class="mobile-menu-title">メニュー</h2>
                    <button onclick="closeMobileMenu()"
                            class="mobile-menu-close-btn"
                            aria-label="メニューを閉じる">
                        <i class="fas fa-times mobile-menu-close-icon" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            {{-- ナビゲーションアイテム（PCサイドバーと共通） --}}
            <div class="mobile-menu-nav">
                @include('layouts.navigation')
            </div>


                {{-- ログイン・会員登録 (モバイル用) --}}
                {{--
                @if (Route::has('login'))
                <div class="mobile-menu-auth">
                    @auth
                        <a href="{{ url('/member/dashboard') }}" class="mobile-menu-auth-link">
                            マイページ
                        </a>
                    @else
                        <a href="{{ route('member.login') }}" class="mobile-menu-login-link">
                            ログイン
                        </a>
                        @if (Route::has('member.register'))
                        <a href="{{ route('member.register') }}" class="mobile-menu-register-link">
                            会員登録
                        </a>
                        @endif
                    @endauth
                </div>
                @endif
                 --}}
        </nav>

        {{-- スマホ: 会員メニューアコーディオン（≤1024px、会員ログイン時のみ） --}}
        @if($isMemberPage && Auth::guard('member')->check())
        <div class="member-mobile-accordion" id="memberMobileAccordion">
            <button class="member-mobile-accordion-toggle" id="memberAccordionToggle" aria-expanded="false" aria-controls="memberAccordionPanel">
                <span class="member-mobile-accordion-label">会員メニュー</span>
                <span class="member-mobile-accordion-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="14" viewBox="0 0 18 14" fill="currentColor" aria-hidden="true">
                        <rect y="0" width="18" height="2" rx="1"/>
                        <rect y="6" width="18" height="2" rx="1"/>
                        <rect y="12" width="18" height="2" rx="1"/>
                    </svg>
                </span>
            </button>

            <div class="member-mobile-accordion-panel" id="memberAccordionPanel" hidden>
                @if(Auth::guard('member')->user()->fanclubMemberships()->where('status', 'active')->exists())
                <div class="member-fc-shortcut">
                    <a href="{{ route('member.fanclubs.contents') }}" class="member-fc-shortcut-link">
                        <span class="member-fc-shortcut-icon">★</span>
                        <span class="member-fc-shortcut-text">ファンクラブコンテンツ</span>
                        <span class="member-fc-shortcut-arrow">›</span>
                    </a>
                </div>
                @endif
                <a href="{{ route('member.dashboard') }}" class="member-mobile-accordion-item">マイページTOP</a>
                <a href="{{ route('member.messages.index') }}" class="member-mobile-accordion-item">メッセージBOX</a>
                <a href="{{ route('member.history') }}" class="member-mobile-accordion-item">ご利用履歴</a>
                <a href="{{ route('member.fanclubs.joined') }}" class="member-mobile-accordion-item">入会中ファンクラブ</a>
                <a href="{{ route('member.account') }}" class="member-mobile-accordion-item">アカウント</a>
                <a href="{{ route('member.account.password') }}" class="member-mobile-accordion-item">パスワード</a>
                <form method="POST" action="{{ route('member.logout') }}" style="width:100% !important">
                    @csrf
                    <button type="submit" class="member-mobile-accordion-item member-mobile-accordion-logout">ログアウト</button>
                </form>
            </div>
        </div>
        @endif

        {{-- メインコンテンツ --}}
        <div class="app-body-wrap">
            <main id="main-content" class="app-main" role="main">
                @if($pageid !== 'top')
                <nav aria-label="パンくずリスト" class="breadcrumb">
                    <ol>
                        @foreach($breadcrumbs ?? [['label'=>'ホーム','url'=>'/']] as $crumb)
                            @if($loop->last)
                                <li>@yield('breadcrumb_label', $crumb['label'])</li>
                            @else
                                <li><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
                @endif
                @isset($slot)
                    @if($pageid === 'top')
                        {{ $slot }}
                    @else
                        <div class="content-panel">{{ $slot }}</div>
                    @endif
                @else
                    @if($pageid === 'top')
                        @yield('content')
                    @else
                        <div class="content-panel">@yield('content')</div>
                    @endif
                @endisset
            </main>
            {{-- 左カラム（旧 side.tpl: 縦ナビ＋外部バナー＋グループ店）。DOM はメインの後、PC では左に表示 --}}
            @include('layouts.site-side')
            @if($isMemberPage && Auth::guard('member')->check())
            <aside class="member-right-column member-right-column--pc" aria-label="会員専用メニュー">
                <section class="member-section">
                    <h2 class="member-section-title">会員メニュー</h2>

                    @if(Auth::guard('member')->user()->fanclubMemberships()->where('status', 'active')->exists())
                    <div>
                        <a href="{{ route('member.fanclubs.contents') }}" class="member-btn2">ファンクラブコンテンツ</a>
                    </div>
                    @endif
                    <div class="member-actions member-actions-column">
                        <a href="{{ route('member.dashboard') }}" class="member-btn">マイページTOP</a>
                        <a href="{{ route('member.messages.index') }}" class="member-btn">メッセージBOX</a>
                        <a href="{{ route('member.history') }}" class="member-btn">ご利用履歴</a>
                        <a href="{{ route('member.fanclubs.joined') }}" class="member-btn">入会中ファンクラブ</a>
                        <a href="{{ route('member.account') }}" class="member-btn">アカウント</a>
                        <a href="{{ route('member.account.password') }}" class="member-btn">パスワード</a>
                        <form method="POST" action="{{ route('member.logout') }}">
                            @csrf
                            <button type="submit" class="member-btn">ログアウト</button>
                        </form>
                    </div>
                </section>
            </aside>
            @endif
        </div>
    </div>

    {{-- ヘッダー高さを CSS 変数に自動反映 --}}
    <script>
    (function() {
        function updateHeaderOffset() {
            var h = document.querySelector('.header-main');
            var nav = document.getElementById('appTopnav');
            var headerH = h ? h.offsetHeight : 0;
            var navH = 0;
            if (nav && window.matchMedia('(min-width: 1025px)').matches) {
                var headerBottom = h ? h.getBoundingClientRect().bottom : 0;
                if (nav.getBoundingClientRect().bottom > headerBottom + 2) {
                    navH = nav.offsetHeight;
                }
            }
            document.documentElement.style.setProperty('--app-header-sticky-offset', headerH + 'px');
            document.documentElement.style.setProperty('--app-topnav-height', navH + 'px');
        }
        window.updateHeaderOffset = updateHeaderOffset;
        document.addEventListener('DOMContentLoaded', function() {
            updateHeaderOffset();
        });
        window.addEventListener('resize', function() {
            updateHeaderOffset();
        });
    })();
    </script>

    {{-- JavaScript: メニューの制御 --}}
    <script>
    // メニューの制御
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const overlay = document.getElementById('mobileMenuOverlay');
        const menuBtn = document.getElementById('menuBtn');

        const isActive = menu.classList.contains('active');

        if (isActive) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    }

    function openMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const overlay = document.getElementById('mobileMenuOverlay');
        const menuBtn = document.getElementById('menuBtn');

        menu.classList.add('active');
        overlay.classList.add('active');
        if (menuBtn) {
            menuBtn.setAttribute('aria-expanded', 'true');
        }
        menu.setAttribute('aria-hidden', 'false');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const overlay = document.getElementById('mobileMenuOverlay');
        const menuBtn = document.getElementById('menuBtn');

        menu.classList.remove('active');
        overlay.classList.remove('active');
        if (menuBtn) {
            menuBtn.setAttribute('aria-expanded', 'false');
        }
        menu.setAttribute('aria-hidden', 'true');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = 'auto';
    }

    // PCナビ：画面外になったら menu を出し、ヘッダ下からスライド表示
    function isPcTopNav() {
        return window.matchMedia('(min-width: 1025px)').matches;
    }

    function isInflowNavOffscreen() {
        var header = document.querySelector('.header-main');
        var nav = document.getElementById('appTopnav');
        if (!header || !nav || !isPcTopNav()) return false;
        return nav.getBoundingClientRect().bottom <= header.getBoundingClientRect().bottom + 2;
    }

    function setTopNavButtonVisible(visible) {
        var wrap = document.getElementById('headerNavToggleWrap');
        if (!wrap) return;
        wrap.classList.toggle('is-visible', !!visible);
    }

    function closeTopNavFlyout() {
        var flyout = document.getElementById('appTopnavFlyout');
        var btn = document.getElementById('headerNavToggle');
        if (flyout) {
            flyout.classList.remove('is-open');
            flyout.setAttribute('aria-hidden', 'true');
            flyout.setAttribute('inert', '');
        }
        if (btn) {
            btn.classList.remove('is-open');
            btn.setAttribute('aria-expanded', 'false');
            btn.setAttribute('aria-label', 'ナビゲーションを開く');
        }
    }

    function openTopNavFlyout() {
        var flyout = document.getElementById('appTopnavFlyout');
        var btn = document.getElementById('headerNavToggle');
        if (!flyout || !btn) return;
        flyout.classList.add('is-open');
        flyout.setAttribute('aria-hidden', 'false');
        flyout.removeAttribute('inert');
        btn.classList.add('is-open');
        btn.setAttribute('aria-expanded', 'true');
        btn.setAttribute('aria-label', 'ナビゲーションを閉じる');
    }

    function toggleTopNav() {
        if (!isPcTopNav() || !isInflowNavOffscreen()) return;
        var flyout = document.getElementById('appTopnavFlyout');
        if (!flyout) return;
        if (flyout.classList.contains('is-open')) {
            closeTopNavFlyout();
        } else {
            openTopNavFlyout();
        }
    }

    function syncTopNavFlyout() {
        if (!isPcTopNav()) {
            setTopNavButtonVisible(false);
            closeTopNavFlyout();
            return;
        }
        var offscreen = isInflowNavOffscreen();
        setTopNavButtonVisible(offscreen);
        if (!offscreen) closeTopNavFlyout();
    }

    (function initTopNavFlyout() {
        var ticking = false;
        function onScrollOrResize() {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(function() {
                syncTopNavFlyout();
                if (typeof updateHeaderOffset === 'function') updateHeaderOffset();
                ticking = false;
            });
        }
        window.addEventListener('scroll', onScrollOrResize, { passive: true });
        window.addEventListener('resize', onScrollOrResize);
        document.addEventListener('DOMContentLoaded', syncTopNavFlyout);

        document.addEventListener('click', function(e) {
            var flyout = document.getElementById('appTopnavFlyout');
            if (!flyout || !flyout.classList.contains('is-open')) return;
            if (e.target.closest('#appTopnavFlyout') || e.target.closest('#headerNavToggle')) return;
            closeTopNavFlyout();
        });

        var flyout = document.getElementById('appTopnavFlyout');
        if (flyout) {
            flyout.addEventListener('click', function(e) {
                if (e.target.closest('a[href]')) closeTopNavFlyout();
            });
        }
    })();

    // ESCキーでメニューを閉じる
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeMobileMenu();
            closeTopNavFlyout();
        }
    });

    // mobile-menu内のリンクをクリックしたらメニューを閉じる
    document.addEventListener('DOMContentLoaded', function() {
        var mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenu) {
            mobileMenu.addEventListener('click', function(e) {
                if (e.target.closest('a[href]')) {
                    closeMobileMenu();
                }
            });
        }
    });
    </script>

    {{-- スマホ: 会員メニューアコーディオン toggle --}}
    <script>
    (function() {
        var toggle = document.getElementById('memberAccordionToggle');
        if (!toggle) return;
        toggle.addEventListener('click', function() {
            var panel = document.getElementById('memberAccordionPanel');
            var expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', String(!expanded));
            panel.hidden = expanded;
        });
    })();
    </script>

    {{-- フッター --}}
    <footer class="site-footer" role="contentinfo">

        {{-- アクセントライン --}}
        <div class="sf-accent-line" aria-hidden="true"></div>

        {{-- メインボディ --}}
        <div class="sf-body">
            <div class="sf-grid">

                {{-- ブランド・コンタクトカラム --}}
                <div class="sf-brand">

                    {{-- ロゴ --}}
                    <a href="{{ route('top') }}" class="sf-logo-link" aria-label="{{ $shops->shop_name }} トップへ">
                        <img src="{{ asset('images/common/header/logo.png') }}"
                             alt="{{ $shops->shop_name }}"
                             class="sf-logo-img"
                             onerror="this.style.display='none'">
                    </a>
                    @if(!empty($shops->shop_copy))
                    <p class="sf-brand-copy">{{ $shops->shop_copy }}</p>
                    @endif

                    {{-- 電話番号（メインCTA） --}}
                    <address class="sf-tel-block not-italic">
                        <p class="sf-block-label">
                            <i class="fas fa-phone-alt" aria-hidden="true"></i>
                            お電話でのご予約
                        </p>
                        <a href="tel:{{ str_replace(['-', ' '], '', $shops->shop_tel) }}"
                           class="sf-tel">{{ $shops->shop_tel }}</a>
                    </address>

                    {{-- 営業時間 --}}
                    <div class="sf-hours-block">
                        <p class="sf-block-label">
                            <i class="fas fa-clock" aria-hidden="true"></i>
                            営業時間
                        </p>
                        <p class="sf-hours">{{ $shops->shop_open }}</p>
                    </div>

                    {{-- SNS --}}
                    @php $hasSns = $shops->shop_x || $shops->shop_youtube || $shops->shop_instagram || $shops->shop_tiktok || $shops->shop_facebook; @endphp
                    @if($hasSns)
                    <div class="sf-sns" aria-label="公式SNS">
                        @if($shops->shop_x)
                        <a href="{{ $shops->shop_x }}" target="_blank" rel="noopener noreferrer"
                           class="sf-sns-btn" aria-label="X (Twitter)">
                            <i class="fab fa-twitter" aria-hidden="true"></i>
                        </a>
                        @endif
                        @if($shops->shop_youtube)
                        <a href="{{ $shops->shop_youtube }}" target="_blank" rel="noopener noreferrer"
                           class="sf-sns-btn sf-sns-yt" aria-label="YouTube">
                            <i class="fab fa-youtube" aria-hidden="true"></i>
                        </a>
                        @endif
                        @if($shops->shop_instagram)
                        <a href="{{ $shops->shop_instagram }}" target="_blank" rel="noopener noreferrer"
                           class="sf-sns-btn sf-sns-ig" aria-label="Instagram">
                            <i class="fab fa-instagram" aria-hidden="true"></i>
                        </a>
                        @endif
                        @if($shops->shop_tiktok)
                        <a href="{{ $shops->shop_tiktok }}" target="_blank" rel="noopener noreferrer"
                           class="sf-sns-btn sf-sns-tt" aria-label="TikTok">
                            <i class="fab fa-tiktok" aria-hidden="true"></i>
                        </a>
                        @endif
                        @if($shops->shop_facebook)
                        <a href="{{ $shops->shop_facebook }}" target="_blank" rel="noopener noreferrer"
                           class="sf-sns-btn sf-sns-fb" aria-label="Facebook">
                            <i class="fab fa-facebook-f" aria-hidden="true"></i>
                        </a>
                        @endif
                    </div>
                    @endif

                </div>{{-- /.sf-brand --}}

                {{-- ナビゲーションカラム --}}
                <nav class="sf-nav" aria-label="フッターナビゲーション">
                    <p class="sf-nav-heading">CONTENTS</p>
                    <ul class="sf-nav-list">
                        <li><a href="{{ route('top') }}" class="sf-nav-link"><em>TOP</em><span>トップ</span></a></li>
                        <li><a href="{{ route('item.index') }}" class="sf-nav-link"><em>CAST</em><span>女の子一覧</span></a></li>
                        <li><a href="{{ route('schedule') }}" class="sf-nav-link"><em>SCHEDULE</em><span>スケジュール</span></a></li>
                        <li><a href="{{ route('events.index') }}" class="sf-nav-link"><em>EVENTS</em><span>イベント</span></a></li>
                        <li><a href="{{ route('systems') }}" class="sf-nav-link"><em>SYSTEM</em><span>料金システム</span></a></li>
                        <li><a href="{{ route('rank.index') }}" class="sf-nav-link"><em>RANKING</em><span>ランキング</span></a></li>
                        <li><a href="{{ route('newface.index') }}" class="sf-nav-link"><em>NEW FACE</em><span>新人情報</span></a></li>
                        <li><a href="{{ route('options') }}" class="sf-nav-link"><em>OPTIONS</em><span>無料オプション</span></a></li>
                        <li><a href="{{ route('hotels') }}" class="sf-nav-link"><em>HOTEL</em><span>ホテル案内</span></a></li>
                        <li><a href="{{ route('x.index') }}" class="sf-nav-link"><em>CAST SNS</em><span>キャストSNS</span></a></li>
                        <li><a href="{{ route('news.index') }}" class="sf-nav-link"><em>NEWS</em><span>お知らせ</span></a></li>
                        @if(!empty($shops->shop_shame))
                        <li><a href="{{ $shops->shop_shame }}" target="_blank" rel="noopener noreferrer" class="sf-nav-link"><em>DIARY</em><span>写メ日記</span></a></li>
                        @endif
                        @if(!empty($shops->shop_review))
                        <li><a href="{{ $shops->shop_review }}" target="_blank" rel="noopener noreferrer" class="sf-nav-link"><em>REVIEW</em><span>口コミ</span></a></li>
                        @endif
                        @if(!empty($shops->shop_reservation))
                        <li><a href="{{ $shops->shop_reservation }}" target="_blank" rel="noopener noreferrer" class="sf-nav-link"><em>RESERVE</em><span>NET予約</span></a></li>
                        @endif
                        @if(!empty($shops->shop_recruit))
                        <li><a href="{{ $shops->shop_recruit }}" target="_blank" rel="noopener noreferrer" class="sf-nav-link"><em>RECRUIT</em><span>求人情報</span></a></li>
                        @else
                        <li><a href="{{ route('recruits.index') }}" class="sf-nav-link"><em>RECRUIT</em><span>求人情報</span></a></li>
                        @endif
                    </ul>
                </nav>{{-- /.sf-nav --}}

            </div>{{-- /.sf-grid --}}
        </div>{{-- /.sf-body --}}

        {{-- コピーライトバー --}}
        <div class="sf-bottom">
            <p class="sf-copyright">&copy; {{ date('Y') }} {{ $shops->shop_name }}. All rights reserved.</p>
        </div>

    </footer>

    {{-- モバイル用固定ナビゲーション (スマホ用のみ) --}}
    <nav class="mobile-fixed-nav"
         role="navigation"
         aria-label="モバイル固定ナビゲーション">
        <div class="mobile-fixed-nav-grid">
            <a href="tel:{{ str_replace(['-', ' '], '', $shops->shop_tel) }}"
               class="mobile-fixed-nav-link mobile-fixed-nav-link-home"
               aria-label="お電話">
                <span class="mobile-fixed-nav-icon"><i class="fas fa-phone-flip" aria-hidden="true"></i></span>
                <span>お電話</span>
            </a>
            <a href="{{ route('item.index') }}"
               class="mobile-fixed-nav-link mobile-fixed-nav-link-cast"
               aria-label="キャスト">
                <span class="mobile-fixed-nav-icon"><i class="fas fa-heart" aria-hidden="true"></i></span>
                <span>キャスト</span>
            </a>
            <a href="{{ route('schedule') }}"
               class="mobile-fixed-nav-link mobile-fixed-nav-link-schedule"
               aria-label="スケジュール">
                <span class="mobile-fixed-nav-icon"><i class="fas fa-calendar-days" aria-hidden="true"></i></span>
                <span>スケジュール</span>
            </a>
            <button onclick="toggleMobileMenu()"
                    class="mobile-fixed-nav-link mobile-fixed-nav-link-menu"
                    aria-label="メニューを開く">
                <span class="mobile-fixed-nav-icon"><i class="fas fa-bars-staggered" aria-hidden="true"></i></span>
                <span>メニュー</span>
            </button>
        </div>
    </nav>

    {{-- Swiper JS --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    {{-- Main JS --}}
    <script src="{{ asset('js/main.js') }}"></script>

    {{-- イベントスライダー初期化 --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // イベントスライダーの初期化
        const eventSwiperElement = document.querySelector('.event-swiper');
        if (eventSwiperElement) {
            // スライド数を確認してloopを動的に設定
            const slideCount = eventSwiperElement.querySelectorAll('.swiper-slide').length;
            const eventSwiper = new Swiper('.event-swiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                loop: slideCount > 3, // スライドが3枚以上の場合のみループを有効化
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 30,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 30,
                    }
                },
            effect: 'slide',
            speed: 600,
            touchRatio: 1,
            touchAngle: 45,
            grabCursor: true,
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
            mousewheel: {
                invert: false,
            },
            a11y: {
                prevSlideMessage: '前のイベント',
                nextSlideMessage: '次のイベント',
                firstSlideMessage: '最初のイベント',
                lastSlideMessage: '最後のイベント',
            }
        });

            // ホバー時に自動再生を一時停止
            eventSwiperElement.addEventListener('mouseenter', function() {
                eventSwiper.autoplay.stop();
            });

            eventSwiperElement.addEventListener('mouseleave', function() {
                eventSwiper.autoplay.start();
            });
        }
    });

    // アコーディオン機能
    function toggleConcept() {
        const content = document.getElementById('conceptContent');
        const toggle = document.querySelector('.concept-toggle');
        const icon = toggle.querySelector('.toggle-icon');
        const text = toggle.querySelector('.toggle-text');

        if (content.classList.contains('active')) {
            content.classList.remove('active');
            content.classList.remove('concept-content-show');
            toggle.classList.remove('concept-toggle-active');
            text.textContent = '詳細を見る';
        } else {
            content.classList.add('active');
            content.classList.add('concept-content-show');
            toggle.classList.add('concept-toggle-active');
            text.textContent = '閉じる';
        }
    }

    // ページ読み込み時にアニメーションを適用
    document.addEventListener('DOMContentLoaded', function() {
        const sections = document.querySelectorAll('.section-container');
        sections.forEach((section, index) => {
            setTimeout(() => {
                section.classList.add('animate-fade-in-up');
            }, index * 200);
        });
    });
    </script>
    @stack('scripts')
</body>
</html>
