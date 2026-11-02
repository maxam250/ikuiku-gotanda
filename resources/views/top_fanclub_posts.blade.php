@if($fanclubPosts && $fanclubPosts->count() > 0)
<section class="top-fanclub-posts-section">
    <div class="fanclub-header">
        <h2 class="section-title fanclub-title">
            <span class="section-title-en">FANCLUB</span>
            <span class="section-title-ja">ファンクラブ最新投稿</span>
        </h2>
    </div>

    <div class="top-fanclub-posts-list">
        @foreach($fanclubPosts as $post)
        @php $fanclub = $post->cast?->fanclub ?? null; @endphp
        <div class="top-fanclub-post-item">
            {{-- アバター --}}
            <div class="fanclub-avatar-wrap">
                @if($fanclub)
                <a href="{{ route('fanclub.show', $fanclub) }}" class="fanclub-avatar-link">
                @else
                <span class="fanclub-avatar-link">
                @endif
                    <div class="fanclub-avatar">
                        <img src="{{ $post->cast?->th_flename() ?? asset('images/dmy.jpg') }}"
                             alt="{{ $post->cast?->item_name }}"
                             class="fanclub-avatar-image">
                    </div>
                @if($fanclub)
                </a>
                @else
                </span>
                @endif
            </div>

            {{-- 内容 --}}
            <div class="fanclub-main">
                <div class="fanclub-meta-row">
                    <span class="fanclub-name">
                        {{ $post->cast?->item_name }}
                    </span>
                    <span class="fanclub-time-short">
                        {{ $post->published_at?->format('m月d日 H:i') }}
                    </span>
                </div>

                <div class="top-fanclub-post-title">
                    @if($fanclub)
                    <a href="{{ route('fanclub.show', $fanclub) }}" class="top-fanclub-post-link">
                        {{ $post->title }}
                        @if($post->is_paid)
                        <span class="top-fanclub-post-badge top-fanclub-post-badge--paid">有料</span>
                        @else
                        <span class="top-fanclub-post-badge top-fanclub-post-badge--free">無料</span>
                        @endif
                    </a>
                    @else
                        {{ $post->title }}
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="fanclub-footer">
        <a href="{{ route('fanclub') }}" class="top-fanclub-posts-more">
            ファンクラブ一覧へ <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</section>
@endif
