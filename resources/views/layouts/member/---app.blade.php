<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>会員マイページ{{ config('app.name', 'shop') }}</title>
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <!-- css -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/common.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/base.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/layout/app.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/form.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/tab.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/member-buttons.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/mypage.css') }}">
@php
        //$pageid = Request::path();
        $pageid = Route::currentRouteName();
@endphp

    @if(str_contains($pageid, 'member.index'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/index.css') }}">
    @elseif(str_contains($pageid, 'member.members'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/members.css') }}">
    @elseif(str_contains($pageid, 'member.schedules_month'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/schedules_month.css') }}">
    @elseif(str_contains($pageid, 'member.schedules'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/schedules.css') }}">
    @elseif(str_contains($pageid, 'member.pickups'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/pickup.css') }}">
    @elseif(str_contains($pageid, 'member.events'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/event.css') }}">
    @elseif(str_contains($pageid, 'member.ranks'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/rank.css') }}">
    @elseif(str_contains($pageid, 'member.gallery'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/gallery.css') }}">
    @elseif(str_contains($pageid, 'member.links'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/link.css') }}">
    @elseif(str_contains($pageid, 'member.reservations') || str_contains($pageid, 'member.history') || str_contains($pageid, 'member.members'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/reservation.css') }}">
    @elseif(str_contains($pageid, 'member.members'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/member.css') }}">
    @elseif(str_contains($pageid, 'login'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/member/login.css') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/all.min.css"> --}}

        <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/dashboard/member.css', 'resources/js/app.js'])

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script src="{{ asset('js/main.js') }}"></script>

    </head>
    <body class="font-sans antialiased member-page">
        <div class="app-container">
            <header class="header-gradient header-main" style="z-index: 9999;" role="banner">
                <div class="header-inner">
                    <div class="header-content">
                        <div class="header-logo-area">
                            <button onclick="toggleMobileMenu()" class="header-menu-btn" id="menuBtn" aria-label="メニューを開く" aria-expanded="false" aria-controls="mobileMenu">
                                <img src="{{ asset('images/menu.svg') }}?v={{ time() }}" alt="メニュー" class="header-menu-icon">
                            </button>

                            <h1 class="header-logo">
                                <a href="/" class="header-logo-link" aria-label="{{ $shops->shop_name }} ホーム">
                                    <img src="{{ asset('images/common/header/logo.png') }}" alt="{{ $shops->shop_name }}" class="header-logo-img" width="144" height="48" loading="eager">
                                </a>
                            </h1>

                            <div class="header-catchphrase">
                                <p class="header-catchphrase-text">{{ $shops->shop_copy }}</p>
                            </div>
                        </div>

                        <nav class="header-nav" aria-label="メインナビゲーション">
                            <div class="header-sns" aria-label="ソーシャルメディア">
                                @if ($shops->shop_youtube)
                                <a href="{{ $shops->shop_youtube }}" class="header-sns-link" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset('/images/common/header/youtube.png') }}" alt="YOUTUBE" class="header-sns-img" width="32" height="32">
                                </a>
                                @endif
                                @if ($shops->shop_x)
                                <a href="{{ $shops->shop_x }}" class="header-sns-link" aria-label="X (旧Twitter)" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset('/images/common/header/twitter.png') }}" alt="X 旧twitter" class="header-sns-img" width="32" height="32">
                                </a>
                                @endif
                                @if ($shops->shop_instagram)
                                <a href="{{ $shops->shop_instagram }}" class="header-sns-link" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset('/images/common/header/instagram.png') }}" alt="instagram" class="header-sns-img" width="32" height="32">
                                </a>
                                @endif
                                @if ($shops->shop_tiktok)
                                <a href="{{ $shops->shop_tiktok }}" class="header-sns-link" aria-label="TikTok" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset('/images/common/header/tiktok.png') }}" alt="tiktok" class="header-sns-img" width="32" height="32">
                                </a>
                                @endif
                                @if ($shops->shop_facebook)
                                <a href="{{ $shops->shop_facebook }}" class="header-sns-link" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset('/images/common/header/facebook.png') }}" alt="facebook" class="header-sns-img" width="32" height="32">
                                </a>
                                @endif
                            </div>

                            <div class="header-auth-area">
                                <a href="{{ route('member.dashboard') }}" class="header-auth-link">マイページ</a>
                            </div>

                            <a href="{{ asset('/reservation/select') }}" class="header-reservation-btn" aria-label="オンライン予約">
                                <span class="header-reservation-text">オンライン予約</span>
                                <span class="header-reservation-text-mobile">WEB予約</span>
                            </a>

                            <a href="{{ $shops->shop_recruit ?: asset('/recruit') }}"
                               class="header-recruit-btn"
                               aria-label="キャスト募集"
                               @if($shops->shop_recruit) target="_blank" rel="noopener noreferrer" @endif>
                                <span class="header-recruit-text">キャスト募集</span>
                                <span class="header-recruit-text-mobile">キャスト募集</span>
                            </a>
                        </nav>
                    </div>
                </div>
            </header>

            <div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()" aria-hidden="true"></div>
            <nav class="mobile-menu" id="mobileMenu" role="navigation" aria-label="モバイルメニュー" aria-hidden="true">
                <div class="mobile-menu-header">
                    <div class="mobile-menu-header-content">
                        <h2 class="mobile-menu-title">メニュー</h2>
                        <button onclick="closeMobileMenu()" class="mobile-menu-close-btn" aria-label="メニューを閉じる">
                            <i class="fas fa-times mobile-menu-close-icon" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="py-4">
                    <a href="{{ route('schedule') }}" class="nav-item bg-pink-100" onclick="closeMobileMenu()"><img src="{{ asset('images/common/navi/schedule.png') }}" alt="スケジュール" width="24" height="24"><div class="nav-item-text"><span class="nav-item-en">SCHEDULE</span><span class="nav-item-jp">スケジュール</span></div></a>
                    <a href="{{ route('item.index') }}" class="nav-item" onclick="closeMobileMenu()"><img src="{{ asset('images/common/navi/cast.png') }}" alt="キャスト" width="24" height="24"><div class="nav-item-text"><span class="nav-item-en">CAST</span><span class="nav-item-jp">キャスト</span></div></a>
                    <a href="{{ route('gallery.index') }}" class="nav-item" onclick="closeMobileMenu()"><img src="{{ asset('images/common/navi/gallery.png') }}" alt="ギャラリー" width="24" height="24"><div class="nav-item-text"><span class="nav-item-en">GALLERY</span><span class="nav-item-jp">ギャラリー</span></div></a>
                    <a href="{{ route('events.index') }}" class="nav-item" onclick="closeMobileMenu()"><img src="{{ asset('images/common/navi/event.png') }}" alt="イベント" width="24" height="24"><div class="nav-item-text"><span class="nav-item-en">EVENT</span><span class="nav-item-jp">イベント</span></div></a>
                    <a href="{{ route('fanclub') }}" class="nav-item" onclick="closeMobileMenu()"><img src="{{ asset('images/common/navi/fanclub.png') }}" alt="ファンクラブ" width="24" height="24"><div class="nav-item-text"><span class="nav-item-en">FANCLUB</span><span class="nav-item-jp">ファンクラブ</span></div></a>
                </div>

                <div class="border-t border-gray-200 p-6">
                    <section class="member-section member-mobile-membernav">
                        <h3 class="member-section-title">会員メニュー</h3>
                        <div class="member-actions member-actions-column">
                            <a href="{{ route('member.dashboard') }}" class="member-btn">ホーム</a>
                            <a href="{{ route('member.messages.index') }}" class="member-btn">メッセージ</a>
                            <a href="{{ route('member.history') }}" class="member-btn">ご利用履歴</a>
                            <a href="{{ route('member.fanclubs.joined') }}" class="member-btn">入会中ファンクラブ</a>
                            <a href="{{ route('member.fanclubs.contents') }}" class="member-btn">ファンクラブコンテンツ</a>
                            <a href="{{ route('member.account') }}" class="member-btn">アカウント</a>
                            <form method="POST" action="{{ route('member.logout') }}">
                                @csrf
                                <button type="submit" class="member-btn">ログアウト</button>
                            </form>
                        </div>
                    </section>
                </div>
            </nav>

            <div class="app-body-wrap">
                <aside class="app-sidebar" aria-label="サイト内ナビゲーション">
                    @include('layouts.navigation')
                </aside>

                <main id="main-content" class="app-main member-page-main" role="main">
                    @isset($header)
                        <header class="member-page-header">
                            <div class="member-page-header-inner">
                                <h1 class="member-page-title">{{ $header }}</h1>
                                <p class="member-page-user">{{ Auth::guard('member')->user()->name ?? '' }} さん</p>
                            </div>
                        </header>
                    @endisset
                    {{ $slot }}
                </main>

                <aside class="member-right-column" aria-label="会員専用コンテンツ">
                    <section class="member-section">
                        <h2 class="member-section-title">会員メニュー</h2>
                        <div class="member-actions member-actions-column">
                            <a href="{{ route('member.dashboard') }}" class="member-btn">ホーム</a>
                            <a href="{{ route('member.messages.index') }}" class="member-btn">メッセージ</a>
                            <a href="{{ route('member.history') }}" class="member-btn">ご利用履歴</a>
                            <a href="{{ route('member.fanclubs.joined') }}" class="member-btn">入会中ファンクラブ</a>
                            <a href="{{ route('member.fanclubs.contents') }}" class="member-btn">ファンクラブコンテンツ</a>
                            <a href="{{ route('member.account') }}" class="member-btn">アカウント</a>
                            <a href="{{ route('member.account.password') }}" class="member-btn">パスワード</a>
                            <form method="POST" action="{{ route('member.logout') }}">
                                @csrf
                                <button type="submit" class="member-btn">ログアウト</button>
                            </form>
                        </div>
                    </section>
                </aside>
            </div>
        </div>
        {{-- <script>
            document.addEventListener('DOMContentLoaded', function () {
                function updatePendingCount() {
                    fetch("{{ route('member.reservations.pendingCount') }}")
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                throw new Error('Response is not JSON');
                            }
                            return response.json().catch(() => {
                                throw new Error('Failed to parse JSON response');
                            });
                        })
                        .then(data => {
                            if (!data) return;
                            const badge = document.getElementById('pending-count-badge');
                            if (badge) {
                                if (parseInt(data.count) > 0) {
                                    badge.textContent = data.display;
                                    badge.style.display = 'inline';
                                } else {
                                    badge.style.display = 'none';
                                }
                            }
                        })
                        .catch(error => {
                            // JSONパースエラーは無視（BOMなどの問題）
                            if (error.name === 'SyntaxError' || error.message.includes('JSON')) {
                                return;
                            }
                            console.error('Error fetching pending count:', error);
                        });
                }

                // 初回実行
                updatePendingCount();

                // 30秒ごとに更新
                setInterval(updatePendingCount, 30000);
            });
            </script> --}}

        <script>
            function toggleMobileMenu() {
                const menu = document.getElementById('mobileMenu');
                const overlay = document.getElementById('mobileMenuOverlay');
                const menuBtn = document.getElementById('menuBtn');
                const isActive = menu.classList.contains('active');
                if (isActive) {
                    closeMobileMenu();
                } else {
                    menu.classList.add('active');
                    overlay.classList.add('active');
                    menuBtn.setAttribute('aria-expanded', 'true');
                    menu.setAttribute('aria-hidden', 'false');
                    overlay.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                }
            }
            function closeMobileMenu() {
                const menu = document.getElementById('mobileMenu');
                const overlay = document.getElementById('mobileMenuOverlay');
                const menuBtn = document.getElementById('menuBtn');
                menu.classList.remove('active');
                overlay.classList.remove('active');
                menuBtn.setAttribute('aria-expanded', 'false');
                menu.setAttribute('aria-hidden', 'true');
                overlay.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = 'auto';
            }
        </script>

        <footer class="member-page-footer text-center">
            <small>&copy; {{ $shops->shop_name }} All Rights Reserved.</small>
        </footer>

    </body>
</html>
