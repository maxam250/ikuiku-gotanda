<x-member-layout>
    <x-slot name="header">
        {{ $fanclub->title }} - メッセージ
    </x-slot>

    <section class="mfc-msg-section">

        <div class="mfc-msg-page-nav">
            <a href="{{ route('member.fanclubs.show', $fanclub) }}" class="member-btn member-btn-sm">&larr; ファンクラブへ戻る</a>
        </div>

        <h2 class="member-section-title">キャストへメッセージ</h2>

        @if(session('success'))
            <div class="mfc-alert mfc-alert--success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mfc-alert mfc-alert--error">{{ session('error') }}</div>
        @endif

        {{-- 送信フォーム --}}
        @if($canSendMessageToday)
        <form method="POST"
              action="{{ route('member.fanclubs.messages.store', $fanclub) }}"
              enctype="multipart/form-data"
              class="mfc-msg-form">
            @csrf
            <textarea name="message"
                      class="mfc-msg-textarea"
                      placeholder="メッセージを入力してください（1日1回まで、最大2000文字）"
                      maxlength="2000"
                      required>{{ old('message') }}</textarea>
            @error('message')<p class="mfc-msg-error">{{ $message }}</p>@enderror
            <div class="mfc-msg-form-footer">
                <label class="mfc-msg-file-label">
                    <i class="las la-image"></i> 画像を添付（1枚まで）
                    <input type="file" name="image" accept="image/*" class="mfc-msg-file-input">
                </label>
                <button type="submit" class="member-btn member-btn-primary">送信する</button>
            </div>
        </form>
        @else
            <p class="mfc-msg-sent-today">
                <i class="las la-check-circle"></i> 本日のメッセージは送信済みです。明日またお送りください。
            </p>
        @endif

        {{-- メッセージ履歴 --}}
        @forelse($fanclubMessages as $msg)
        <div class="mfc-msg-thread">
            {{-- 自分のメッセージ --}}
            <div class="mfc-msg-bubble mfc-msg-bubble--member">
                <div class="mfc-msg-bubble-header">
                    <span class="mfc-msg-sender">あなた</span>
                    <span class="mfc-msg-date">{{ $msg->created_at->format('Y/m/d H:i') }}</span>
                </div>
                <p class="mfc-msg-body">{{ $msg->message }}</p>
                @if($msg->image_path)
                    <img src="{{ asset('../storage/' . $msg->image_path) }}" alt="添付画像" class="mfc-msg-image">
                @endif
                {{-- 削除ボタン（送信日数に関わらず削除可能・ソフトデリート） --}}
                <div class="mfc-msg-delete-wrap">
                    <form method="POST"
                          action="{{ route('member.fanclubs.messages.destroy', [$fanclub, $msg]) }}"
                          onsubmit="return confirm('このメッセージを削除しますか？')">
                        @csrf @method('DELETE')
                        <button type="submit" class="mfc-msg-delete-btn af-btn af-btn-delete af-btn-sm" title="削除">
                            <i class="fa-solid fa-trash text-gray-500"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- キャストからの返信 --}}
            @foreach($msg->replies as $reply)
            <div class="mfc-msg-bubble mfc-msg-bubble--cast">
                <div class="mfc-msg-bubble-header">
                    <span class="mfc-msg-sender">{{ $fanclub->cast->item_name ?? 'キャスト' }}</span>
                    <span class="mfc-msg-date">{{ $reply->created_at->format('Y/m/d H:i') }}</span>
                </div>
                <p class="mfc-msg-body">{{ $reply->message }}</p>
                @if($reply->image_path)
                    <img src="{{ asset('../storage/' . $reply->image_path) }}" alt="添付画像" class="mfc-msg-image">
                @endif
            </div>
            @endforeach
        </div>
        @empty
            <p class="member-empty">まだメッセージはありません。</p>
        @endforelse

        @if($fanclubMessages->hasPages())
            <div class="member-pagination">{{ $fanclubMessages->links() }}</div>
        @endif

    </section>
</x-member-layout>
