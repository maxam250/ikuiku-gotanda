<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>キャストマイページ{{ config('app.name', 'shop') }}</title>
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <!-- css -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/item/common.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/form.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/tab.css') }}">
@php
        //$pageid = Request::path();
        $pageid = Route::currentRouteName();
@endphp

    @if(str_contains($pageid, 'item.index'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/item/index.css') }}">
    @elseif(str_contains($pageid, 'item.items'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/item/items.css') }}">
    @elseif(str_contains($pageid, 'item.schedules_month'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/item/schedules_month.css') }}">
    @elseif(str_contains($pageid, 'item.schedules'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/item/schedules.css') }}">
    @elseif(str_contains($pageid, 'item.pickups'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/item/pickup.css') }}">
    @elseif(str_contains($pageid, 'item.events'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/item/event.css') }}">
    @elseif(str_contains($pageid, 'item.ranks'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/item/rank.css') }}">
    @elseif(str_contains($pageid, 'item.gallery'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/item/gallery.css') }}">
    @elseif(str_contains($pageid, 'item.links'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/item/link.css') }}">
    @elseif(str_contains($pageid, 'item.reservations') || str_contains($pageid, 'item.histories') || str_contains($pageid, 'item.members'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/item/reservation.css') }}">
    @elseif(str_contains($pageid, 'item.members'))
    <link rel="stylesheet" type="text/css" href="{{ asset('css/frontend/item/member.css') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


        <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/dashboard/item.css', 'resources/js/app.js'])

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script src="{{ asset('js/main.js') }}"></script>

    </head>
    <body class="font-sans antialiased">

        <div style="margin:0 0 3rem">



            @include('layouts.item.navigation')


                <div class="name w-36 p-2 block sm:hidden">{{ Auth::guard('item')->user()->item_name }} さん</div>

            <div class="flex">

                <nav id="side">

                    <ul class="navi">
                        <li><a href="{{asset('item/dashboard')}}">ホーム</a></li>
                        <li><a href="{{asset('item/schedules_month')}}">【出勤】リクエスト</a></li>
                        {{--
                            <li><a href="{{ route('item.fanclub.edit') }}">ファンクラブ開設・更新</a></li>
                            <li><a href="{{asset('item/reservation-history')}}">予約履歴</a></li>
                            <li><a href="{{asset('item/mycostume')}}">マイコスチューム</a></li>
                            <li><a href="{{asset('item/myoption')}}">マイオプション</a></li>
                            <li><a href="{{asset('item/messages')}}">メッセージボックス</a></li>
                            <li><a href="{{asset('item/items/coupons')}}">クーポン設定</a></li>
                            <li><a href="{{asset('item/items/edit')}}">プロフィール編集</a></li>

                        --}}
                        <li><a href="{{asset('item/items/account')}}">アカウント情報</a></li>
                        {{-- <li><a href="{{asset('item/events')}}">--【イベント】リクエスト</a></li> --}}

                    </ul>



                    <ul class="navi setting">
                    <li class="logout"><!-- Authentication -->
                        <form method="POST" action="{{ route('item.logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('item.logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form></li>
                    </ul>

                </nav>

                <!-- Page Content -->
                <main>
                <!-- Page Heading -->
                    @isset($header)
                        <header class="item-page-header mb-4">
                            <div class="titleArea">
                                <h2 class="p-2"> {{ $header }}</h2>
                            </div>
                        </header>
                    @endisset
                    {{ $slot }}
                </main>

            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                function updatePendingCount() {
                    fetch("{{ route('item.reservations.pendingCount') }}")
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
            </script>

     <footer>
       <small>&copy {{ $shops->shop_name }}</small>
     </footer>

    </body>
</html>
