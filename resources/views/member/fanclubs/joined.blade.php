<x-member-layout>
    <x-slot name="header">
        入会中ファンクラブ
    </x-slot>

    <section class="member-section">
        <h2 class="member-section-title">入会中のファンクラブ</h2>

        @if($memberships->isEmpty())
            <p class="member-empty">現在、入会中のファンクラブはありません。</p>
        @else
            @foreach($memberships as $membership)
                @php $fanclub = $membership->fanclub; @endphp
                @if($fanclub)
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
                                <x-fanclub.status-label :value="$membership->status" />
                            </div>
                        </div>
                        <div class="mfc-row-actions">
                            <a href="{{ route('member.fanclubs.show', $fanclub) }}" class="member-btn member-btn-sm">コンテンツを見る</a>
                        </div>
                    </article>
                @endif
            @endforeach
        @endif
    </section>

    @if($memberships->hasPages())
        <div class="member-pagination">{{ $memberships->links() }}</div>
    @endif
</x-member-layout>
