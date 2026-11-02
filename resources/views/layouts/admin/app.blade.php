<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'castup') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon/favicon_sys.png') }}">

    <!-- css -->
    <link rel="stylesheet" type="text/css" href="/css/admin/common.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/form.css') }}">
    <link rel="stylesheet" type="text/css" href="/css/tab.css">
    @php
    //$pageid = Request::path();
    $pageid = Route::currentRouteName();
    @endphp

    @if(str_contains($pageid, 'admin.index'))
    <link rel="stylesheet" type="text/css" href="/css/admin/index.css">
    @elseif(str_contains($pageid, 'admin.items'))
    <link rel="stylesheet" type="text/css" href="/css/admin/items.css">
    @elseif(str_contains($pageid, 'admin.schedules_month'))
    <link rel="stylesheet" type="text/css" href="/css/admin/schedules_month.css">
    @elseif(str_contains($pageid, 'admin.schedules'))
    <link rel="stylesheet" type="text/css" href="/css/admin/schedules.css">
    @elseif(str_contains($pageid, 'admin.pickups') || str_contains($pageid, 'admin.newfaces'))
    <link rel="stylesheet" type="text/css" href="/css/admin/pickup.css">
    @elseif(str_contains($pageid, 'admin.events'))
    <link rel="stylesheet" type="text/css" href="/css/admin/event.css">
    @elseif(str_contains($pageid, 'admin.ranks'))
    <link rel="stylesheet" type="text/css" href="/css/admin/rank.css">
    @elseif(str_contains($pageid, 'admin.gallery'))
    <link rel="stylesheet" type="text/css" href="/css/admin/gallery.css">
    @elseif(str_contains($pageid, 'admin.links'))
    <link rel="stylesheet" type="text/css" href="/css/admin/link.css">
    @elseif(str_contains($pageid, 'admin.reservations') || str_contains($pageid, 'admin.histories') ||
    str_contains($pageid, 'admin.members'))
    <link rel="stylesheet" type="text/css" href="/css/admin/reservation.css">
    @elseif(str_contains($pageid, 'admin.members'))
    <link rel="stylesheet" type="text/css" href="/css/admin/member.css">
    @endif
    {{--
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/all.min.css"> --}}

    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @vite(['resources/css/admin/admin.css', 'resources/js/app.js'])

    {{-- 各管理画面の @push('styles') をここで出力 --}}
    @stack('styles')

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/af-modal.js') }}" defer></script>


</head>

<body class="font-sans antialiased admin-layout">

    <div class="admin-content-wrap">
        @include('layouts.admin.navigation')

        <!-- Page Heading -->
        @isset($header)
        <header>
            <div class="titleArea">
                <h2> {{ $header }}</h2>
            </div>
        </header>
        @endisset

        <div class="flex admin-columns">

            <nav id="side">

                <ul class="navi">
                    <li><a href="{{asset('kanrigamen/dashboard')}}">ダッシュボード</a></li>
                    <li><a href="{{asset('kanrigamen/items')}}">キャスト管理</a></li>
                    <li><a href="{{asset('kanrigamen/schedules')}}">日別出勤</a></li>
                    <li><a href="{{asset('kanrigamen/schedules_month')}}">月間出勤</a></li>
                    <li><a href="{{asset('kanrigamen/news')}}">ニュース管理</a></li>
                    {{-- <li><a href="{{asset('kanrigamen/reservation')}}">予約管理<span>
                                @if ($pendingReservationsCount > 0 )
                                <span class="badge" id="pending-reservation-badge">
                                    {{ $pendingReservationsCount > 5 ? '5+' : $pendingReservationsCount }}
                                </span>
                                @endif
                    </a></li> --}}
                    {{--
                    <li><a href="{{asset('kanrigamen/request_schedules')}}">出勤リクエスト
                            @if ($pendingRequestSchCount > 0 )
                            <span class="badge" id="pending-schedule-badge">
                                {{ $pendingRequestSchCount > 5 ? '5+' : $pendingRequestSchCount }}
                            </span>
                            @endif </a></li>
                    --}}
                    {{-- <li><a href="{{route('admin.messages.index')}}">メッセージ（会員）
                            <span class="badge" id="unread-member-messages-badge" style="display: none;"></span>
                        </a></li> --}}
                    {{-- <li><a href="{{route('admin.message_item.index')}}">メッセージ（キャスト）
                            <span class="badge" id="unread-item-messages-badge" style="display: none;"></span>
                        </a></li> --}}
                    {{-- <li><a href="{{asset('kanrigamen/members')}}">会員管理</a></li> --}}
                    {{-- <li><a href="{{asset('kanrigamen/member_points')}}">会員ポイント管理</a></li> --}}
                    {{-- <li><a href="{{asset('kanrigamen/history')}}">予約履歴</a></li> --}}
                    {{-- <li><a href="{{asset('kanrigamen/contacts')}}">お問い合わせ管理<span>
                                @if ($pendingContactsCount > 0 )
                                <span class="badge" id="pending-contact-badge">
                                    {{ $pendingContactsCount > 5 ? '5+' : $pendingContactsCount }}
                                </span>
                                @endif
                            </span></a></li> --}}
                    {{-- <li><a href="{{route('admin.newsletter.campaigns.index')}}">メールマガジン管理</a></li> --}}

                    <li><a href="{{asset('kanrigamen/events')}}">イベントバナー</a></li>
                    <li><a href="{{asset('kanrigamen/main-visuals')}}">メインビジュアル</a></li>
                    {{--
                    <li><a href="{{asset('kanrigamen/gallery')}}">ギャラリー</a></li>
                    --}}
                    {{-- <li><a href="{{asset('kanrigamen/costume')}}">コスチューム</a></li> --}}
                    <li><a href="{{asset('kanrigamen/ranks')}}">ランキング</a></li>
                    <li><a href="{{asset('kanrigamen/pickups')}}">ピックアップ</a></li>
                    <li><a href="{{asset('kanrigamen/newfaces')}}">新人情報</a></li>
                    <li><a href="{{asset('kanrigamen/links')}}">リンク</a></li>
                    {{--
                    <li class="bg-yellow-50"><a href="{{asset('kanrigamen/special-events')}}">特設イベント</a></li>
                    <li class="bg-yellow-50"><a href="{{route('admin.coupons.index')}}">クーポン</a></li>
                    <li class="bg-yellow-50"><a href="{{route('admin.mycostumes.index')}}">マイコスチューム</a></li>
                    <li class="bg-yellow-50"><a href="{{route('admin.myoptions.index')}}">マイオプション</a></li>
                    <li class="bg-yellow-50"><a href="{{ route('admin.fanclubs.index') }}">ファンクラブ管理</a></li>
                    <li class="bg-yellow-50"><a href="{{ route('admin.fanclub-messages.index') }}">└ ファンクラブメッセージリスト</a></li>
                    --}}
                    {{-- <li><a href="{{asset('kanrigamen/blogs')}}">ブログ</a></li>--}}


                    {{--
                    <li><a href="{{asset('kanrigamen/review')}}">--レビュー</a></li>
                    <li><a href="{{asset('kanrigamen/movie')}}">--動画アップロード</a></li>
                    <li><a href="{{asset('kanrigamen/newface')}}">--新人</a></li>
                    --}}
                </ul>
                <ul class="navi" id="settings">
                    <li class="setting-title">◆ 設定項目</li>
                    {{-- ■ キャスト --}}
                    <li class="navi-accordion">
                        <button type="button" class="navi-accordion-btn">キャスト <span class="navi-accordion-arrow">▾</span></button>
                        <ul class="navi-accordion-body">
                            <li><a href="{{ route('admin.item-categories.index') }}">キャストタイプ</a></li>
                            <li><a href="{{asset('kanrigamen/icon_items')}}">キャストアイコン</a></li>
                            <li><a href="{{asset('kanrigamen/profiles')}}">キャストプロフィール</a></li>
                            <li><a href="{{asset('kanrigamen/interviews')}}">キャストQ&A</a></li>
                            <li><a href="{{ route('admin.premium-ranks.index') }}">プレミアランク</a></li>
                        </ul>
                    </li>
                    {{-- ■ オプション --}}
                    <li class="navi-accordion">
                        <button type="button" class="navi-accordion-btn">オプション <span class="navi-accordion-arrow">▾</span></button>
                        <ul class="navi-accordion-body">
                            <li><a href="{{asset('kanrigamen/option_cates')}}">オプションカテゴリ</a></li>
                            <li><a href="{{asset('kanrigamen/options')}}">オプション（無料）</a></li>
                            <li><a href="{{asset('kanrigamen/option_pays')}}">オプション（有料）</a></li>
                        </ul>
                    </li>
                    {{-- ■ コース --}}
                    <li><a href="{{asset('kanrigamen/course_fee')}}">コース設定</a></li>
                    {{-- ■ ホテル --}}
                    <li><a href="{{asset('kanrigamen/equipment')}}">ホテル設定</a></li>
                    {{-- ■ 注意事項 --}}
                    <li><a href="{{ route('admin.notes.index') }}">注意事項</a></li>
                    {{-- ■ 事務所/スタッフ --}}
                    <li class="navi-accordion">
                        <button type="button" class="navi-accordion-btn">事務所/スタッフ <span class="navi-accordion-arrow">▾</span></button>
                        <ul class="navi-accordion-body">
                            <li><a href="{{ route('admin.offices.index') }}">事務所設定</a></li>
                            <li><a href="{{asset('kanrigamen/staff')}}">スタッフ設定</a></li>
                            <li><a href="{{ route('admin.staff_schedule.index') }}">スタッフスケジュール</a></li>
                        </ul>
                    </li>
                    {{-- <li><a href="{{ route('admin.access-logs.index') }}">アクセスログ</a></li> --}}
                    <li><a href="{{asset('kanrigamen/backup')}}">バックアップ</a></li>
                    <li><a href="{{ route('admin.seo-settings.index') }}">SEO設定</a></li>
                </ul>

                <ul class="navi setting">
                    <li><a href="{{ route('admin.admins.index') }}"><i class="fa-solid fa-users-gear"></i> 管理者設定</a></li>
                    @if(auth()->guard('admin')->user()->isSuperAdmin())
                    <li><a href="{{ route('admin.settings.index') }}"><i class="fas fa-cog"></i> サーバー設定</a></li>
                    @endif
                    <li class="logout"><!-- Authentication -->
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('admin.logout')" onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </li>
                </ul>

            </nav>

            <!-- Page Content -->
            <main class="admin-main">
                {{ $slot }}
            </main>

        </div>
    </div>
    <script>
    // -----------------------------------------------------------------------
    // ヘッダー スタッフ選択
    // -----------------------------------------------------------------------
    (function () {
        const API_LIST    = '/kanrigamen/staff-api/list';
        const API_SET     = '/kanrigamen/staff-api/set-session';
        const API_CURRENT = '/kanrigamen/staff-api/current';
        const csrfMeta    = document.querySelector('meta[name="csrf-token"]');
        const csrf        = csrfMeta ? csrfMeta.content : '';

        const select    = document.getElementById('header-staff-select');
        const indicator = document.getElementById('header-staff-indicator');
        if (!select) return;

        // スタッフ一覧をロード
        fetch(API_LIST, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
            .then(r => r.json())
            .then(staffs => {
                if (!staffs || staffs.length === 0) {
                    const wrap = document.getElementById('staff-select-wrap');
                    if (wrap) wrap.style.display = 'none';
                    return Promise.resolve(null);
                }
                staffs.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value       = s.id;
                    opt.textContent = s.name;
                    select.appendChild(opt);
                });
                // 現在のセッション値を復元
                return fetch(API_CURRENT, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
            })
            .then(r => r ? r.json() : null)
            .then(data => {
                if (data && data.success && data.staff) {
                    select.value = data.staff.id;
                    showIndicator(data.staff.name);
                }
            })
            .catch(() => {});

        select.addEventListener('change', function () {
            const staffId = this.value;
            fetch(API_SET, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
                body: JSON.stringify({ staff_id: staffId || null }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (data.staff) {
                        showIndicator(data.staff.name);
                    } else {
                        hideIndicator();
                    }
                }
            })
            .catch(() => {});
        });

        function showIndicator(name) {
            if (indicator) {
                indicator.textContent = '✓ ' + name;
                indicator.classList.remove('hidden');
            }
        }
        function hideIndicator() {
            if (indicator) {
                indicator.textContent = '';
                indicator.classList.add('hidden');
            }
        }
    })();
    </script>

    <script>
    // -----------------------------------------------------------------------
    // サイドナビ アコーディオン
    // -----------------------------------------------------------------------
    (function () {
        document.querySelectorAll('.navi-accordion-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var li = this.closest('.navi-accordion');
                li.classList.toggle('is-open');
            });
        });
        // 現在ページが含まれるアコーディオンを自動展開
        var currentPath = location.pathname;
        document.querySelectorAll('.navi-accordion-body a').forEach(function (a) {
            if (currentPath.startsWith(a.getAttribute('href') || '__none__')) {
                a.closest('.navi-accordion').classList.add('is-open');
            }
        });
    })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // CSRFトークンの取得
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            function updatePendingCount() {
                fetch('/kanrigamen/reservation/pending-count', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                    .then(async response => {
                        if (response.redirected) {
                            // リダイレクトされた場合（認証切れなど）
                            window.location.href = response.url;
                            return null;
                        }
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Response is not JSON');
                        }
                        const text = await response.text();
                        const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                        try {
                            return JSON.parse(cleanedText);
                        } catch (e) {
                            // JSONパースエラーは無視（BOMなどの問題）
                            return null;
                        }
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

            function updateUnreadMemberMessagesCount() {
                fetch('/kanrigamen/messages/unread-count', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                    .then(async response => {
                        if (response.redirected) {
                            // リダイレクトされた場合（認証切れなど）
                            window.location.href = response.url;
                            return null;
                        }
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Response is not JSON');
                        }
                        const text = await response.text();
                        const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                        try {
                            return JSON.parse(cleanedText);
                        } catch (e) {
                            // JSONパースエラーは無視（BOMなどの問題）
                            return null;
                        }
                    })
                    .then(data => {
                        if (!data) return;
                        const badge = document.getElementById('unread-member-messages-badge');
                        if (badge) {
                            if (data.count > 0) {
                                badge.textContent = data.count > 5 ? '5+' : data.count;
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
                        console.error('Error fetching member messages unread count:', error);
                    });
            }

            function updateUnreadItemMessagesCount() {
                fetch('/kanrigamen/message_item/unread-count', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                    .then(async response => {
                        if (response.redirected) {
                            // リダイレクトされた場合（認証切れなど）
                            window.location.href = response.url;
                            return null;
                        }
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Response is not JSON');
                        }
                        const text = await response.text();
                        const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                        try {
                            return JSON.parse(cleanedText);
                        } catch (e) {
                            // JSONパースエラーは無視（BOMなどの問題）
                            return null;
                        }
                    })
                    .then(data => {
                        if (!data) return;
                        const badge = document.getElementById('unread-item-messages-badge');
                        if (badge) {
                            if (data.count > 0) {
                                badge.textContent = data.count > 5 ? '5+' : data.count;
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
                        console.error('Error fetching item messages unread count:', error);
                    });
            }

            // エラーハンドリングを追加して実行
            try {
                updatePendingCount();
                updateUnreadMemberMessagesCount();
                updateUnreadItemMessagesCount();

                // 定期的に実行（5分ごと）
                setInterval(updatePendingCount, 300000);
                setInterval(updateUnreadMemberMessagesCount, 300000);
                setInterval(updateUnreadItemMessagesCount, 300000);
            } catch (error) {
                console.error('Error in update functions:', error);
            }
        });
    </script>

    @stack('scripts')

    <footer>
        <small>&copy cast manager</small>
    </footer>

</body>

</html>
