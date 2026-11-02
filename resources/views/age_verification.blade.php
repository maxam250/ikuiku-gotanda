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
    {{-- Google Analytics --}}
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $shop->shop_analytics }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $shop->shop_analytics }}');
    </script>
</head>
<body class="age-verify-page">

    {{-- 年齢確認カード（画像バナーなし） --}}
    <div class="av-card" role="main">
        <div class="av-brand">
            <a href="#" class="sf-logo-link" aria-label="{{ $shops->shop_name }} トップへ">
                <img src="{{ asset('images/logo.webp') }}"
                     alt="{{ $shops->shop_name }}"
                     class="sf-logo-img"
                     onerror="this.style.display='none'">
            </a>

        </div>

        {{-- 入場 / 退出 --}}
        <div class="av-card-actions">
            <div class="av-action-item">
                <form method="POST" action="{{ route('age.verify') }}">
                    @csrf
                    <input type="hidden" name="agree" value="yes">
                    <button type="submit" class="av-round-btn av-round-btn--enter" aria-label="Enter">
                        <i class="fas fa-heart" aria-hidden="true"></i>
                        <span class="av-round-btn-en">Enter</span>
                    </button>
                </form>
            </div>
            <div class="av-action-item">
                <a href="https://www.google.com" target="_blank" rel="noopener noreferrer" class="av-round-btn av-round-btn--exit" aria-label="Exit">
                    <span class="av-round-btn-en">Exit</span>
                </a>
            </div>
        </div>

        {{-- 下段：説明テキスト --}}
        <div class="av-card-body">
            <div class="av-age-badge" aria-hidden="true">18+</div>
            <h1 class="av-title">年齢確認</h1>
            <p class="av-description">
                このサイトには成人向けコンテンツが含まれます。<br>
                あなたは18歳以上ですか？
            </p>
            <p class="av-note">
                当サイトへのアクセスには年齢確認が必要です。18歳未満の方のご利用はお断りしております。上記ボタンをクリックすることで、あなたが18歳以上であることを確認したものとみなします。
            </p>
        </div>

        {{-- 退出リンク --}}
        <div class="av-exit">
            <a href="https://www.google.com" target="_blank" rel="noopener noreferrer" class="av-exit-link">
                いいえ、サイトを離れる
            </a>
        </div>

    </div>

    {{-- 優先外部リンク --}}
    {{-- <section class="flex flex-col items-center gap-1 py-2 space-y-2">
        <div class="block sm:hidden outlink morenova">
            <a href="https://www.ginza1-clinic.com/lp/morenova-agent/?media=yaricir" target="_blank"><img src="{{ asset('images/link/morenova/465x68_1.png') }}" alt="モアノヴァエージェント銀座一丁目クリニック"></a>
        </div>
    </section> --}}


    {{-- 系列店 --}}
    @if(isset($shopGroups) && $shopGroups->isNotEmpty())
    <div class="av-shop-groups">
        @foreach($shopGroups as $sg)
        <a href="{{ $sg->url ?: '#' }}" target="_blank" rel="noopener noreferrer" class="av-shop-group-item">
            @if($sg->filename)
                <img src="{{ Storage::url($sg->filename) }}" alt="{{ $sg->name }}">
            @elseif($sg->image_url)
                <img src="{{ $sg->image_url }}" alt="{{ $sg->name }}">
            @else
                <span class="av-shop-group-name">{{ $sg->name }}</span>
            @endif
        </a>
        @endforeach
    </div>
    @endif

    <x-links-grouped-list :links="$links ?? collect()" title="" />

    <footer class="av-footer">
        <p class="av-footer-copy">&copy; {{ date('Y') }} {{ $shop->shop_name ?? '' }}. All rights reserved.</p>
    </footer>

</body>
</html>
