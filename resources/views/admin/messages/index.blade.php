<x-admin-layout>

<x-slot name="header">メッセージ管理</x-slot>

<div class="af-page">
    <div class="af-page-header">
        <h1 class="af-page-title">メッセージ管理（会員）</h1>
    </div>

    {{-- 検索フォーム --}}
    <form method="GET" action="{{ route('admin.messages.index') }}" class="af-search">
        <div class="af-search-row">
            <div class="af-search-field" style="min-width:60px; max-width:80px;">
                <label for="member_id">ID</label>
                <input type="text" class="af-input" id="member_id" name="member_id"
                       value="{{ request('member_id') }}" placeholder="ID">
            </div>
            <div class="af-search-field" style="min-width:80px; max-width:110px;">
                <label for="member_no">会員番号</label>
                <input type="text" class="af-input" id="member_no" name="member_no"
                       value="{{ request('member_no') }}" placeholder="会員番号">
            </div>
            <div class="af-search-field">
                <label for="name">名前</label>
                <input type="text" class="af-input" id="name" name="name"
                       value="{{ request('name') }}" placeholder="名前">
            </div>
            <div class="af-search-field">
                <label for="email">メール</label>
                <input type="email" class="af-input" id="email" name="email"
                       value="{{ request('email') }}" placeholder="メール">
            </div>
            <div class="af-search-field">
                <label for="tel">電話番号</label>
                <input type="tel" class="af-input" id="tel" name="tel"
                       value="{{ request('tel') }}" placeholder="電話番号">
            </div>
        </div>
        <div class="af-btn-row af-btn-row-center" style="margin-top:0.5rem;">
            <button type="submit" class="af-btn af-btn-search">
                <i class="fas fa-search"></i> 検索
            </button>
            <a href="{{ route('admin.messages.index') }}" class="af-btn af-btn-reset">
                <i class="fas fa-redo"></i> リセット
            </a>
        </div>
    </form>

    @if(request('member_id') || request('member_no') || request('name') || request('email') || request('tel'))
    <div class="af-alert-info" style="margin-top:0.5rem; display:flex; align-items:center; gap:0.4rem; flex-wrap:wrap;">
        <i class="fas fa-search"></i>
        検索結果: {{ $members->count() }}件
        @if(request('member_id'))
            <span class="af-badge af-badge-on">ID: {{ request('member_id') }}</span>
        @endif
        @if(request('member_no'))
            <span class="af-badge af-badge-on">会員番号: {{ request('member_no') }}</span>
        @endif
        @if(request('name'))
            <span class="af-badge af-badge-on">名前: {{ request('name') }}</span>
        @endif
        @if(request('email'))
            <span class="af-badge af-badge-on">メール: {{ request('email') }}</span>
        @endif
        @if(request('tel'))
            <span class="af-badge af-badge-on">電話: {{ request('tel') }}</span>
        @endif
        <a href="{{ route('admin.messages.index') }}" class="af-btn af-btn-cancel af-btn-sm">
            <i class="fas fa-times"></i> クリア
        </a>
    </div>
    @endif

    {{-- 会員リスト --}}
    <div class="af-section" style="margin-top:0.75rem;">
        <div class="af-section-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h5 class="af-section-title">会員一覧</h5>
            <span id="unread-badge" class="af-badge af-badge-on" style="display:none;"></span>
        </div>
        <div class="af-section-body" style="padding:0;">
            <div class="af-member-list" id="member-list">
                @foreach($members as $member)
                    <a href="{{ route('admin.messages.show', $member->id) }}"
                       class="af-member-item {{ request()->route('member_id') == $member->id ? 'is-active' : '' }}">
                        <div>
                            <div class="af-member-id">
                                [ {{ $member->id }} ]
                                <strong>{{ $member->name }}</strong>
                            </div>
                            @if($member->last_message_at)
                                <div class="af-member-meta">
                                    最終メッセージ: {{ $member->last_message_at->format('Y/m/d H:i') }}
                                </div>
                            @endif
                        </div>
                        @if($member->unread_count > 0)
                            <span class="af-badge af-badge-on">{{ $member->unread_count }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const unreadBadge = document.getElementById('unread-badge');

    function updateUnreadCount() {
        fetch('/kanrigamen/messages/unread-count')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    unreadBadge.textContent = data.count;
                    unreadBadge.style.display = 'inline-flex';
                } else {
                    unreadBadge.style.display = 'none';
                }
            })
            .catch(() => {});
    }

    updateUnreadCount();
    setInterval(updateUnreadCount, 30000);
});
</script>

</x-admin-layout>
