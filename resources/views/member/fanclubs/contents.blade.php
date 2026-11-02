<x-member-layout>
    <x-slot name="header">
        ファンクラブコンテンツ
    </x-slot>

    <section class="member-section">
        <h2 class="member-section-title">入会中ファンクラブの最新投稿</h2>

        @if($contents->isEmpty())
            <p class="member-empty">表示可能なコンテンツがありません。入会中ファンクラブをご確認ください。</p>
        @else
            <div class="member-grid">
                @foreach($contents as $content)
                    <article class="member-card">
                        @if(!empty($content->cast) && $content->cast->thumbnail)
                            <div class="mfc-row-cast-icon">
                                <img src="{{ asset('../storage/' . $content->cast->thumbnail->filename) }}" alt="{{ $content->cast->name }}">
                            </div>
                        @endif
                        <h3 class="member-card-title">{{ $content->title }}</h3>
                        <p class="member-meta">キャスト: {{ $content->cast->name ?? '未設定' }}</p>
                        <p class="member-meta">カテゴリ: {{ $content->category->name ?? '未分類' }}</p>
                        <p class="member-meta">公開日: {{ optional($content->published_at)->format('Y-m-d H:i') }}</p>
                        <p class="member-meta">{{ $content->description }}</p>

                        @if(!empty($content->cast_id))
                            <div class="member-actions">
                                <a href="{{ route('member.fanclubs.index') }}" class="member-btn">ファンクラブを確認</a>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <div>{{ $contents->links() }}</div>
</x-member-layout>
