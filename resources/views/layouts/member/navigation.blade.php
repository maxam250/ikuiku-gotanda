@php
    $member = Auth::guard('member')->user();
    $fcMsgUnread = 0;
    if ($member) {
        try {
            $memberMsgIds = \App\Models\FanclubMessage::where('member_id', $member->id)
                ->whereNull('parent_id')->pluck('id');
            $fcMsgUnread = \App\Models\FanclubMessage::whereIn('parent_id', $memberMsgIds)
                ->where('sender_type', 'cast')->where('is_read', false)->count();
        } catch (\Exception $e) {}
    }
    $navItems = [
        ['label' => 'ホーム', 'route' => 'member.dashboard', 'match' => 'member.dashboard'],
        ['label' => 'メッセージ', 'route' => 'member.messages.index', 'match' => 'member.messages.*'],
        ['label' => 'ご利用履歴', 'route' => 'member.history', 'match' => 'member.history'],
        ['label' => '入会可能ファンクラブ', 'route' => 'member.fanclubs.index', 'match' => 'member.fanclubs.index'],
        ['label' => '入会中ファンクラブ', 'route' => 'member.fanclubs.joined', 'match' => 'member.fanclubs.joined'],
        ['label' => 'ファンクラブコンテンツ', 'route' => 'member.fanclubs.contents', 'match' => 'member.fanclubs.contents'],
        ['label' => 'アカウント', 'route' => 'member.account', 'match' => 'member.account'],
        ['label' => 'パスワード', 'route' => 'member.account.password', 'match' => 'member.account.password'],
    ];
@endphp

<nav x-data="{ open: false }" class="border-b border-slate-200 bg-gradient-to-r from-sky-50 via-blue-50 to-sky-100">
    <div class="member-topnav-inner">
        <div class="member-topnav-brand-area">
            <a href="{{ route('member.dashboard') }}" class="member-topnav-brand">
                {{ $shops->shop_name }} <span class="member-topnav-brand-sub">Member Page</span>
            </a>

            <div class="member-topnav-links">
                @foreach($navItems as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="member-topnav-link {{ request()->routeIs($item['match']) ? 'is-active' : '' }}"
                        style="position:relative;">
                        {{ $item['label'] }}
                        @if($item['route'] === 'member.fanclubs.joined' && $fcMsgUnread > 0)
                            <span class="nav-badge">{{ $fcMsgUnread }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <div class="member-topnav-actions">
            <a href="{{ url('/') }}" target="_blank" class="member-topnav-site" rel="noopener noreferrer">サイトを見る</a>
            <span class="member-topnav-user">{{ $member?->name ?? '' }} さん</span>
            <form method="POST" action="{{ route('member.logout') }}">
                @csrf
                <button type="submit" class="member-topnav-logout">
                    ログアウト
                </button>
            </form>
        </div>

        <button
            @click="open = !open"
            class="member-topnav-toggle"
            aria-label="メニューを開閉">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div x-cloak :class="{'block': open, 'hidden': !open}" class="member-topnav-mobile">
        <div class="member-topnav-mobile-links">
            <div class="member-drawer-head">
                <p class="member-drawer-user">{{ $member?->name ?? '' }} さん</p>
                <div class="member-drawer-quick">
                    <a href="{{ route('member.fanclubs.joined') }}" class="member-topnav-mobile-link">入会中ファンクラブ</a>
                    <a href="{{ route('member.fanclubs.contents') }}" class="member-topnav-mobile-link">ファンクラブコンテンツ</a>
                    <a href="{{ route('member.messages.index') }}" class="member-topnav-mobile-link">メッセージ</a>
                </div>
            </div>
            @foreach($navItems as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="member-topnav-mobile-link {{ request()->routeIs($item['match']) ? 'is-active' : '' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
            <a href="{{ url('/') }}" target="_blank" class="member-topnav-mobile-link" rel="noopener noreferrer">サイトを見る</a>
            <form method="POST" action="{{ route('member.logout') }}">
                @csrf
                <button type="submit" class="member-topnav-mobile-logout">
                    ログアウト
                </button>
            </form>
        </div>
    </div>
</nav>

