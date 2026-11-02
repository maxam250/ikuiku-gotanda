<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $shopName = $shop->shop_name ?? '';
        $shopUrl  = $shop->shop_url ?? url('/');

        $defaultTitle       = $shopName . ' | 年齢確認';
        $defaultDescription = $shopName . 'の年齢確認ページです。当サイトには成人向けコンテンツが含まれます。18歳以上の方のみご利用ください。';
        $defaultOgImage     = $shopUrl . '/favicon/thumb.jpg';

        $seoTitle       = ($seoData && $seoData->meta_title)       ? $seoData->meta_title . ' | ' . $shopName : $defaultTitle;
        $seoDescription = ($seoData && $seoData->meta_description)  ? $seoData->meta_description : $defaultDescription;
        $seoKeywords    = ($seoData && $seoData->meta_keywords)     ? $seoData->meta_keywords : null;
        $seoRobots      = ($seoData && $seoData->noindex)           ? 'noindex, nofollow' : 'index, follow';
        $seoCanonical   = ($seoData && $seoData->canonical_url)     ? $seoData->canonical_url : url()->current();
        $ogTitle        = ($seoData && $seoData->og_title)          ? $seoData->og_title : $seoTitle;
        $ogDescription  = ($seoData && $seoData->og_description)    ? $seoData->og_description : $seoDescription;
        $ogImage        = ($seoData && $seoData->og_image)          ? $seoData->og_image : $defaultOgImage;
    @endphp

    {{-- Primary Meta Tags --}}
    <title>{{ $seoTitle }}</title>
    <meta name="title" content="{{ $seoTitle }}">
    <meta name="description" content="{{ $seoDescription }}">
    @if($seoKeywords)
    <meta name="keywords" content="{{ $seoKeywords }}">
    @endif
    <meta name="author" content="{{ $shopName }}">
    <meta name="robots" content="{{ $seoRobots }}">
    <meta name="language" content="Japanese">
    <meta name="revisit-after" content="7 days">

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $seoCanonical }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="{{ $shopName }}">
    <meta property="og:locale" content="ja_JP">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $seoCanonical }}">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <meta name="thumbnail" content="{{ $shopUrl }}/favicon/thumb.jpg">

    {{-- Structured Data (JSON-LD) --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "{{ $seoTitle }}",
        "description": "{{ $seoDescription }}",
        "url": "{{ $seoCanonical }}",
        "inLanguage": "ja",
        "isPartOf": {
            "@type": "WebSite",
            "name": "{{ $shopName }}",
            "url": "{{ $shopUrl }}"
        }
    }
    </script>

    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/frontend/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/frontend/age_verification.css') }}">
    {{-- 旧サイト（_gotanda/css/index.css）準拠。base の後に読み込む --}}
    <link rel="stylesheet" href="{{ asset('css/frontend/age_verification_gotanda.css') }}?v={{ filemtime(public_path('css/frontend/age_verification_gotanda.css')) }}">
    {{-- Google Analytics --}}
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $shop->shop_analytics }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $shop->shop_analytics }}');
    </script>
</head>
<body class="age-verify-page av-gtd">
@php
    $avTel = $shop->shop_tel ?? '';
    $avTelLink = str_replace(['-', ' '], '', $avTel);
@endphp

    {{-- ロゴ（旧 index.tpl #mainArea） --}}
    <header class="av-gtd-main">
        <h1>
            <img src="{{ asset('images/common/header/logo.webp') }}" alt="{{ $shop->shop_name ?? '' }}" width="800" height="380">
        </h1>
    </header>

    <main class="av-gtd-contents" role="main">

        {{-- YES / NO（旧 #btnArea） --}}
        <section class="av-gtd-btns" aria-label="年齢確認">
            <div class="av-gtd-btn-item">
                <form method="POST" action="{{ route('age.verify') }}">
                    @csrf
                    <input type="hidden" name="agree" value="yes">
                    <button type="submit" class="av-gtd-btn av-gtd-btn--yes">YES</button>
                </form>
            </div>
            <p class="av-gtd-question">あなたは<br>18歳以上ですか？</p>
            <div class="av-gtd-btn-item">
                <a href="https://yahoo.co.jp" class="av-gtd-btn av-gtd-btn--no" target="_blank" rel="nofollow noopener noreferrer">NO</a>
            </div>
        </section>

        {{-- 注意書き（旧 #caution） --}}
        <section class="av-gtd-caution">
            当サイトは<strong>{{ $shop->shop_name ?? '' }}</strong>のウェブサイトです。<br>
            18歳未満の方はこれより先の閲覧は固くお断りいたします。
        </section>

        {{-- スマホ：お問い合わせ（旧 sp の #contactArea） --}}
        <section class="av-gtd-contact">
            <h2>お問い合わせ</h2>
            <ul>
                @if($avTel)
                <li><a href="tel:{{ $avTelLink }}">電話<br>{{ $avTel }}</a></li>
                @endif
                @if(!empty($shop->shop_reservation))
                <li><a href="{{ $shop->shop_reservation }}" target="_blank" rel="noopener noreferrer">ネット<br>予約</a></li>
                @endif
            </ul>
        </section>

        {{-- 求人バナー（旧 #bannerArea） --}}
        <div class="av-gtd-banners">
            <a href="{{ !empty($shop->shop_recruit) ? $shop->shop_recruit : route('recruits.index') }}" target="_blank" rel="nofollow noopener"><img src="{{ asset('images/index/banner_recruit.jpg') }}" alt="女性求人情報" loading="lazy"></a>
            @if(!empty($shop->shop_recruit_mens))
            <a href="{{ $shop->shop_recruit_mens }}" target="_blank" rel="noopener"><img src="{{ asset('images/index/banner_naikin.jpg') }}" alt="男性求人情報" loading="lazy"></a>
            @endif
        </div>

        {{-- グループ店（inc_group.tpl） --}}
        @if(isset($shopGroups) && $shopGroups->isNotEmpty())
        <div class="av-gtd-groups">
            @foreach($shopGroups as $sg)
            <a href="{{ $sg->url ?: '#' }}" target="_blank" rel="noopener noreferrer">
                @if($sg->filename)
                    <img src="{{ Storage::url(str_contains($sg->filename, '/') ? $sg->filename : 'shop-groups/' . $sg->filename) }}" alt="{{ $sg->name }}" loading="lazy">
                @elseif($sg->image_url)
                    <img src="{{ $sg->image_url }}" alt="{{ $sg->name }}" loading="lazy">
                @else
                    <span class="av-gtd-group-name">{{ $sg->name }}</span>
                @endif
            </a>
            @endforeach
        </div>
        @endif

        {{-- 各種媒体（旧 #linkArea / link_index_inc.tpl → 管理画面「リンク」） --}}
        @if(isset($links) && $links->isNotEmpty())
        <section class="av-gtd-links">
            <h2>各種媒体</h2>
            <x-links-grouped-list :links="$links" title="" />
        </section>
        @endif

    </main>

    {{-- フッター（旧 #footer #contactArea） --}}
    <footer class="av-gtd-footer">
        <h3>{{ $shop->shop_name ?? '' }}</h3>
        @if($avTel)
        <p class="av-gtd-footer-tel"><a href="tel:{{ $avTelLink }}">{{ $avTel }}</a></p>
        @endif
        @if(!empty($shop->shop_open))
        <p class="av-gtd-footer-open">営業時間：<span>{{ $shop->shop_open }}</span></p>
        @endif
        <p class="av-gtd-footer-text">無店舗型風俗特殊営業届出済みの出張型風俗優良店</p>
    </footer>
    <div class="av-gtd-copyright">Copyright &copy; {{ $shop->shop_name ?? '' }} All Rights Reserved.</div>

</body>
</html>
