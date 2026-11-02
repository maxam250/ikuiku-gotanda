<nav x-data="{ open: false }" class="admin-nav" style="position: fixed; top: 0; left: 0; right: 0; z-index: 100;">

    <!-- Primary Navigation Menu -->
    <div class="admin-nav__container">
        <div class="admin-nav__bar">
            <div class="admin-nav__left">
                <!-- Logo -->
                <div id="logoArea">
                    <a href="{{ route('admin.dashboard') }}">
                        <x-application-logo />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="admin-nav__links">
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('dashboard')">
                       <p class="admin-nav__brand-title">{{ __('Cast Manager') }}</p>
                        <p class="admin-nav__brand-sub">管理画面</p>
                    </x-nav-link>
                </div>

                <div class="link" style="margin-left:45px;">
                    <a href="{{ url('/') }}" target="_blank">WEBサイトを見る&nbsp;&raquo;</a>
                </div>
            </div>

            <!-- スタッフ選択 -->
            <div class="admin-nav__staff" id="staff-select-wrap">
                <div class="admin-nav__staff-inner">
                    <label for="header-staff-select" class="admin-nav__staff-label">担当:</label>
                    <select id="header-staff-select" class="admin-nav__staff-select">
                        <option value="">-- 選択 --</option>
                    </select>
                    <span id="header-staff-indicator" class="admin-nav__staff-indicator hidden"></span>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="admin-nav__dropdown-wrap">
                <x-dropdown  width="48">
                    <x-slot name="trigger">
                        <button class="admin-nav__dropdown-btn">
                            <div>{{ $shops->shop_name }}</div>

                            <div class="admin-nav__dropdown-arrow">
                                <svg class="admin-nav__dropdown-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <ul class="navi">

                            <li><a href="{{asset('kanrigamen/shops')}}" style="background:rgb(227, 234, 255)">ショップ基本情報</a></li>
                    <li><a href="{{ route('admin.shop-groups.index') }}">系列店</a></li>
                            <li><a href="{{ route('admin.site-settings.edit') }}">サイト全般設定</a></li>
                            <li><!-- Authentication -->
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('admin.logout')"
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
            <div class="admin-nav__hamburger">
                <button @click="open = ! open" class="admin-nav__hamburger-btn">
                    <svg class="admin-nav__hamburger-svg" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="admin-nav__mobile hidden">
        <div class="admin-nav__mobile-links">
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="admin-nav__mobile-settings">

            <div class="admin-nav__mobile-settings-inner">


                <!-- Authentication -->
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('admin.logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

