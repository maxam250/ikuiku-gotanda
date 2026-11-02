<x-member-layout>
    <x-slot name="header">
        入会可能ファンクラブ
    </x-slot>

    <section class="member-section">
        <h2 class="member-section-title">ファンクラブ一覧</h2>

        @forelse($fanclubs as $fanclub)
            @php $membership = $fanclub->memberships->first(); @endphp
            <article class="mfc-row-card">
                @if($fanclub->cast && $fanclub->cast->thumbnail)
                    <div class="mfc-row-cast-icon">
                        <img src="{{ asset('../storage/' . $fanclub->cast->thumbnail->filename) }}" alt="{{ $fanclub->cast->name }}">
                    </div>
                @endif
                <div class="mfc-row-main">
                    <h3 class="mfc-row-title">{{ $fanclub->title }}</h3>
                    @if($fanclub->description)
                        <p class="mfc-row-desc">{{ $fanclub->description }}</p>
                    @endif
                    <div class="mfc-row-meta">
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
                <div class="mfc-row-actions">

                    <a href="{{ route('member.fanclubs.show', $fanclub) }}" class="member-btn member-btn-sm">コンテンツを見る</a>
                </div>
            </article>
        @empty
            <p class="member-empty">ファンクラブがまだありません。</p>
        @endforelse
    </section>

    @if($fanclubs->hasPages())
        <div class="member-pagination">{{ $fanclubs->links() }}</div>
    @endif
</x-member-layout>
