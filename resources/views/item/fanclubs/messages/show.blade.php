<x-item-layout>
    <x-slot name="header">メッセージスレッド</x-slot>

    <div class="mfc-msg-admin-wrap">
        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <a href="{{ route('item.fanclub.messages.index') }}" class="member-btn member-btn-sm">&larr; 一覧へ戻る</a>
            <span class="rounded-full bg-purple-100 px-3 py-1 text-sm text-purple-700">
                会員: {{ $fanclubMessage->member->nickname ?: ($fanclubMessage->member->name ?? '不明') }}
            </span>
        </div>

        @if(session('success'))
            <div class="mb-3 rounded-lg border border-green-200 bg-green-50 px-4 py-2 text-green-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-3 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-red-700">{{ session('error') }}</div>
        @endif

        {{-- スレッド表示 --}}
        @foreach($thread as $msg)
        <div class="mb-5 space-y-3 rounded-xl border border-purple-100 bg-white p-4 shadow-sm">

            {{-- 会員メッセージ（水色系） --}}
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                <div class="mb-1 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">
                        <i class="fas fa-user text-blue-400"></i> 会員
                    </span>
                    <span class="font-semibold text-gray-900">{{ $msg->member->nickname ?: ($msg->member->name ?? '会員') }}</span>
                    <span class="ml-auto text-xs text-gray-500">{{ $msg->created_at->format('Y/m/d H:i') }}</span>
                </div>
                <p class="whitespace-pre-wrap text-sm leading-relaxed text-gray-700">{{ $msg->message }}</p>
                @if($msg->image_path)
                    <img src="{{ asset('../storage/' . $msg->image_path) }}" alt="添付画像"
                         class="mt-3 h-24 w-24 cursor-pointer rounded-lg border border-blue-200 object-cover opacity-90 hover:opacity-100"
                         onclick="openImgModal(this.src)">
                @endif
            </div>

            {{-- 返信（キャスト側・紫系） --}}
            @foreach($msg->replies as $reply)
            <div class="ml-0 rounded-xl border border-purple-300 bg-purple-100 p-4 md:ml-8">
                <div class="mb-1 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 rounded-full bg-purple-200 px-2 py-0.5 text-xs font-semibold text-purple-800">
                        <i class="fas fa-reply text-purple-500"></i> あなた
                    </span>
                    <span class="ml-auto text-xs text-gray-500">{{ $reply->created_at->format('Y/m/d H:i') }}</span>
                </div>
                <p class="whitespace-pre-wrap text-sm leading-relaxed text-gray-800">{{ $reply->message }}</p>
                @if($reply->image_path)
                    <img src="{{ asset('../storage/' . $reply->image_path) }}" alt="添付画像"
                         class="mt-3 h-24 w-24 cursor-pointer rounded-lg border border-purple-300 object-cover opacity-90 hover:opacity-100"
                         onclick="openImgModal(this.src)">
                @endif
                <form method="POST" action="{{ route('item.fanclub.messages.destroy', $reply) }}" class="mt-2"
                      onsubmit="return confirm('この返信を削除しますか？');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="member-btn member-btn-sm">返信を削除</button>
                </form>
            </div>
            @endforeach
        </div>
        @endforeach

        {{-- 返信フォーム（最新のメッセージIDに対して返信） --}}
        @php $latestMsg = $thread->first(); @endphp
        @if($latestMsg)
        <form method="POST"
              action="{{ route('item.fanclub.messages.reply', $latestMsg) }}"
              enctype="multipart/form-data"
              class="mt-4 rounded-xl border border-purple-100 bg-white p-4 shadow-sm">
            @csrf
            <h4 class="mb-2 font-semibold text-gray-800">返信する</h4>
            <p class="mb-2 text-xs text-gray-500">同一会員への返信は1日1回までです。</p>
            <textarea name="message" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-purple-400 focus:outline-none"
                      rows="5" placeholder="返信を入力してください" maxlength="2000" required>{{ old('message') }}</textarea>
            @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                <label class="inline-flex cursor-pointer flex-col gap-1 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <span class="flex items-center gap-1">
                        <i class="las la-image"></i> 画像を添付
                    </span>
                    <input type="file" name="image" accept="image/*" class="text-xs">
                </label>
                <button type="submit" class="member-btn member-btn-primary">返信する</button>
            </div>
        </form>
        @endif
    </div>

    {{-- 画像モーダル --}}
    <div id="imgModal"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75"
         onclick="closeImgModal()">
        <div class="relative max-h-[90vh] max-w-[90vw]" onclick="event.stopPropagation()">
            <img id="imgModalImg" src="" alt="拡大画像"
                 class="max-h-[85vh] max-w-[90vw] rounded-lg object-contain shadow-2xl">
            <button onclick="closeImgModal()"
                    class="absolute -right-3 -top-3 flex h-8 w-8 items-center justify-center rounded-full bg-white shadow-md hover:bg-gray-100">
                <i class="fas fa-times text-gray-700"></i>
            </button>
        </div>
    </div>

    <script>
    function openImgModal(src) {
        document.getElementById('imgModalImg').src = src;
        const modal = document.getElementById('imgModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeImgModal() {
        const modal = document.getElementById('imgModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeImgModal();
    });
    </script>
</x-item-layout>
