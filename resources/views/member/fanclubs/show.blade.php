<x-member-layout>
    <x-slot name="header">
        {{ $fanclub->title }}
    </x-slot>

    @if(session('success'))
        <div class="mfc-alert">{{ session('success') }}</div>
    @endif

    {{-- ファンクラブ情報カード --}}
    <section class="mfc-info-card">
        <div class="mfc-info-top">
            @if($fanclub->cast && $fanclub->cast->thumbnail)
                <div class="mfc-row-cast-icon">
                    <img src="{{ asset('../storage/' . $fanclub->cast->thumbnail->filename) }}" alt="{{ $fanclub->cast->name }}">
                </div>
            @endif
            <h2 class="mfc-info-title">{{ $fanclub->title }}</h2>
            <div class="mfc-info-badges">
                <span class="mfc-price-badge">
                    <i class="las la-coins"></i>入会費 {{ number_format($fanclub->price) }}円
                </span>
                @if($membership)
                    <x-fanclub.status-label :value="$membership->status" />
                @else
                    <span class="mfc-status-unregistered">未入会</span>
                @endif
            </div>
        </div>

        @if($fanclub->description)
            <p class="mfc-info-desc">{{ $fanclub->description }}</p>
        @endif

        @if(!$membership)
            <div class="mfc-subscribe-wrap">
                <form method="POST" action="{{ route('member.fanclubs.subscribe', $fanclub) }}">
                    @csrf
                    <button class="member-btn member-btn-primary">ファンクラブに参加する</button>
                </form>
            </div>
        @endif
    </section>

    {{-- ファンクラブメッセージ（入会中のみ） --}}
    @if($membership && $membership->status === 'active')
    <section class="mfc-msg-entry-section">
        <div class="mfc-msg-entry-card">
            <div class="mfc-msg-entry-info">
                <span class="mfc-msg-entry-icon"><i class="fa-solid fa-comment"></i></span>
                <div>
                    <p class="mfc-msg-entry-label">キャストへメッセージ</p>
                    <p class="mfc-msg-entry-desc">1日1回、キャストに直接メッセージを送れます。</p>
                </div>
                @if($fcMsgUnread > 0)
                    <span class="mfc-msg-entry-badge">{{ $fcMsgUnread }}件の返信</span>
                @endif
            </div>
            <a href="{{ route('member.fanclubs.messages.index', $fanclub) }}" class="member-btn member-btn-sm">
                メッセージを開く
            </a>
        </div>
    </section>
    @endif

    {{-- コンテンツ一覧 --}}
    <section class="member-section member-section--fanclub-posts">
        <h2 class="member-section-title">コンテンツ</h2>

        @if($channels->isEmpty() && !($hasUncategorized ?? false))
            <p class="member-empty">チャンネルがまだありません。</p>
        @else
            @if($channels->isNotEmpty() && !($viewingUncategorized ?? false))
                <div class="member-fanclub-channels" aria-label="チャンネル">
                    @foreach($channels as $ch)
                        @php $isActive = $selectedChannel && $selectedChannel->id === $ch->id; @endphp
                        <span class="member-fanclub-channel-tab {{ $isActive ? 'is-active' : '' }}">{{ $ch->name }}</span>
                    @endforeach
                </div>
            @endif

            <div class="member-fanclub-post-stack">
                @if($contents->isNotEmpty())
                    @php $content = $contents->first(); @endphp
                    <article class="member-card member-fanclub-post">

                        <div class="mfc-post-header">
                            <h3 class="member-card-title">{{ $content->title }}</h3>
                            <div class="mfc-post-meta">
                                @if($content->published_at)
                                    <span>{{ $content->published_at->format('Y/m/d') }}</span>
                                @endif
                                <span class="mfc-post-type {{ $content->is_paid ? 'is-paid' : 'is-free' }}">
                                    {{ $content->is_paid ? '有料' : '無料' }}
                                </span>
                                @if(optional($content->category)->name)
                                    <span class="mfc-post-channel">{{ $content->category->name }}</span>
                                @endif
                            </div>
                            @if($content->description)
                                <p class="mfc-post-desc">{{ $content->description }}</p>
                            @endif
                        </div>

                        <div class="member-fanclub-post__body">
                            @if($childPaginator && $childPaginator->isNotEmpty())
                                <x-fanclub.child-block :child="$childPaginator->first()" />
                            @elseif($childPaginator && $childPaginator->total() === 0)
                                <p class="member-empty">公開中のポストはまだありません。</p>
                            @endif
                        </div>

                        @if($childPaginator && $childPaginator->hasPages())
                            <div class="member-fanclub-child-pager">
                                {{ $childPaginator->links('pagination::member-child', ['prevTitle' => $childPrevTitle, 'nextTitle' => $childNextTitle]) }}
                            </div>
                        @endif

                    </article>
                @else
                    <p class="member-empty">このチャンネルに表示できる投稿はまだありません。</p>
                @endif
            </div>

            @if($contents->hasPages())
                <div class="member-fanclub-post__pager">
                    {{ $contents->links('pagination::member') }}
                </div>
            @endif
        @endif
    </section>

    <script>
        document.querySelectorAll('.fanclub-timer').forEach(function (el) {
            var target = el.dataset.timerTarget;
            var textEl = el.querySelector('.js-timer-text');
            if (!target || !textEl) return;
            var update = function () {
                var diff = new Date(target).getTime() - Date.now();
                if (diff <= 0) { textEl.textContent = '終了しました'; return; }
                var day  = Math.floor(diff / 86400000);
                var hour = Math.floor((diff % 86400000) / 3600000);
                var min  = Math.floor((diff % 3600000) / 60000);
                var sec  = Math.floor((diff % 60000) / 1000);
                textEl.textContent = day + '日 ' + hour + '時間 ' + min + '分 ' + sec + '秒';
            };
            update();
            setInterval(update, 1000);
        });
    </script>
</x-member-layout>
