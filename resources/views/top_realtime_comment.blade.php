@if($realtimeComments && $realtimeComments->count() > 0)
<section class="realtime-comment-section">
    <div class="realtime-comment-header">
        <h2 class="section-title realtime-comment-title">
            <span class="section-title-en">REALTIME</span>
            <span class="section-title-ja">リアルタイムコメント</span>
        </h2>
    </div>

    <div class="realtime-comment-container">
        <div class="realtime-comment-list">
            @foreach($realtimeComments as $comment)
            <div class="realtime-comment-item">
                <div class="realtime-comment-content-row">
                    {{-- プロフィール画像 --}}
                    <div class="realtime-comment-avatar-wrap">
                        <a href="{{ route('items.show', $comment->id) }}" class="realtime-comment-avatar-link">
                            <div class="realtime-comment-avatar">
                                <img src="{{ $comment->th_flename() }}" alt="{{ $comment->item_name }}" class="realtime-comment-avatar-image">
                            </div>
                        </a>
                    </div>

                    {{-- コメント内容 --}}
                    <div class="realtime-comment-main">
                        <div class="realtime-comment-meta-row">
                            <a href="{{ route('items.show', $comment->id) }}" class="realtime-comment-name">
                                {{ $comment->item_name }}
                            </a>
                            <!-- <span class="realtime-comment-time-short">
                                @if($comment->item_p_comment_date)
                                    @if($comment->item_p_comment_date->diffInHours() < 24)
                                        {{ $comment->item_p_comment_date->diffForHumans() }}
                                    @else
                                        {{ $comment->item_p_comment_date->format('m月d日 H:i') }}
                                    @endif
                                @endif
                            </span> -->
                        </div>

                        <div class="realtime-comment-body">
                            <a href="{{ route('items.show', $comment->id) }}#realtime-comment" class="realtime-comment-read-link">コメントを読む</a>
                        </div>

                        {{-- インタラクションボタン --}}
                        <div class="realtime-comment-actions">
                            <a href="{{ route('items.show', $comment->id) }}" class="realtime-comment-profile-link">
                                <i class="fa-solid fa-circle-user"></i>
                                <span>プロフィール</span>
                            </a>
                            <span class="realtime-comment-time-full">
                                <i class="fas fa-clock realtime-comment-time-icon"></i>
                                {{ $comment->item_p_comment_date ? $comment->item_p_comment_date->format('Y/m/d H:i') : '' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- フッター --}}
        <div class="realtime-comment-footer">
            <p class="realtime-comment-footer-text">
                <i class="fas fa-info-circle realtime-comment-footer-icon"></i>
                過去14日間のコメントを表示しています
            </p>
        </div>
    </div>
</section>
@endif
