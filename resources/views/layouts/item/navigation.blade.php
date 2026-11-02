<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">


    <!-- Primary Navigation Menu -->
    <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">

                <!-- Navigation Links -->
                <div class="space-x-8 sm:-my-px sm:ms-0 sm:flex ">
                    <x-nav-link :href="route('item.dashboard')" :active="request()->routeIs('dashboard')">
                       <p>
                                <img src="{{ asset('images/common/header/logo.png') }}"
                                     alt="{{ $shops->shop_name }}"
                                     class="header-logo-img"
                                     width="100"
                                     loading="eager">
                         </p>
                        <p class="ml-3 text-xs">マイページ</p>
                    </x-nav-link>
                </div>

                <div class="link link-prof" style="margin-left:15px;">
                    <a href="{{ url('/items/'. Auth::guard('item')->user()->id) }}" target="_blank">プロフ&nbsp;&raquo;</a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::guard('item')->user()->item_name }} さん</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @php
                            $fcMsgUnreadCast = 0;
                            try {
                                $castUser = Auth::guard('item')->user();
                                $castFanclub = $castUser ? \App\Models\Fanclub::where('cast_id', $castUser->id)->first() : null;
                                if ($castFanclub) {
                                    $fcMsgUnreadCast = \App\Models\FanclubMessage::where('fanclub_id', $castFanclub->id)
                                        ->where('sender_type', 'member')
                                        ->where('is_read', false)
                                        ->count();
                                }
                            } catch (\Exception $e) {}
                        @endphp
                        <ul class="navi">
                        {{--
                            <li><a href="{{asset('item/dashboard')}}">ホーム</a></li>
                            <li><a href="{{asset('item/messages')}}">メッセージボックス</a></li>
                            <li><a href="{{asset('item/schedules_month')}}">【出勤】リクエスト</a></li>
                            <li><a href="{{asset('item/mycostume')}}">マイコスチューム</a></li>
                            <li><a href="{{asset('item/reservation-history')}}">予約履歴</a></li>
                            <li><a href="{{asset('item/myoption')}}">マイオプション</a></li>
                            <li>
                                <a href="{{ route('item.fanclub.messages.index') }}" style="position:relative;">
                                    ファンクラブメッセージ
                                    @if($fcMsgUnreadCast > 0)
                                        <span class="nav-badge">{{ $fcMsgUnreadCast }}</span>
                                    @endif
                                </a>
                            </li>
                            <li><a href="{{asset('item/items/edit')}}">プロフィール編集</a></li>
                            <li><a href="{{asset('item/items/coupons')}}">クーポン設定</a></li>
                            --}}
                            <li><a href="{{asset('item/schedules_month')}}">【出勤】リクエスト</a></li>
                            <li><a href="{{asset('item/items/account')}}">アカウント情報</a></li>
                            <li><!-- Authentication -->
                                <form method="POST" action="{{ route('item.logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('item.logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form></li>
                        </ul>

                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('item.dashboard')" :active="request()->routeIs('dashboard')">
             <ul class="navi">
                        <li><a href="{{asset('item/dashboard')}}">ホーム</a></li>
                        <li><a href="{{asset('item/schedules_month')}}">【出勤】リクエスト</a></li>
                        <li><a href="{{asset('item/items/edit')}}">プロフィール編集</a></li>
                        <li><a href="{{asset('item/items/account')}}">アカウント情報</a></li>
                        {{--

                        <li><a href="{{asset('item/messages')}}">メッセージボックス</a></li>
                        <li><a href="{{asset('item/reservation-history')}}">予約履歴</a></li>
                        <li><a href="{{asset('item/items/coupons')}}">クーポン設定</a></li>
                        <li><a href="{{ route('item.fanclub.edit') }}">ファンクラブ開設・更新</a></li>
                        <li><a href="{{asset('item/mycostume')}}">マイコスチューム</a></li>
                        <li><a href="{{asset('item/myoption')}}">マイオプション</a></li>
                        --}}
            </ul>
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">

            <div class="mt-3 space-y-1">


                <!-- Authentication -->
                <form method="POST" action="{{ route('item.logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('item.logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

