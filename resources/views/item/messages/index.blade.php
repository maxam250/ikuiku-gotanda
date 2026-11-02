<x-item-layout>
    <x-slot name="header">
        メッセージボックス
    </x-slot>

    <div class="py-1">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden sm:rounded-lg">
                <div class="text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- メッセージ表示エリア -->
                    <div class="message-chat-area rounded-sm p-2 mb-4">
                        <div class="message-list space-y-8">
                            @foreach($messages as $message)
                                <div class="message-item {{ $message->sender_type === 'item' ? 'flex justify-end' : 'flex justify-start' }}">
                                    <div class="message-content max-w-[90%] relative">
                                        <div class="rounded-lg px-4 py-3 {{ $message->sender_type === 'item' ? 'bg-green-500 text-white' : 'bg-blue-500 text-gray-900 text-white' }} relative">
                                            <!-- 三角形 -->
                                            <div class="message-triangle absolute w-3 h-3 {{ $message->sender_type === 'item' ? 'bg-green-500 right-0 transform translate-x-1/2 rotate-45' : 'bg-blue-500 left-0 transform -translate-x-1/2 rotate-45' }}"></div>

                                            <div class="message-body-wrap">
                                                <div class="text-sm font-semibold mb-1">
                                                    {{ $message->sender_type === 'item' ? 'あなた' : $shops->shop_name }}
                                                </div>
                                                @if($message->title)
                                                    <div class="text-sm font-medium mb-2 pb-2 border-b border-{{ $message->sender_type === 'item' ? 'green' : 'blue' }}-400">
                                                        {{ $message->title }}
                                                    </div>
                                                @endif
                                                <div class="text-sm mb-2 message-text-content">
                                                    {!! nl2br(e($message->message)) !!}
                                                </div>
                                                @if($message->image_path)
                                                    <div class="mt-2">
                                                        <img src="{{ Storage::url($message->image_path) }}"
                                                            alt="添付画像"
                                                            class="max-w-full h-auto rounded cursor-pointer hover:opacity-90"
                                                            onclick="openImageModal(this.src)">
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" class="msg-toggle-btn" onclick="toggleMessageBody(this)">もっと見る ▼</button>
                                        </div>
                                        <div class="message-time text-xs text-gray-500 mt-2 {{ $message->sender_type === 'item' ? 'text-right' : 'text-left' }}">
                                            {{ $message->created_at->format('Y年m月d日 H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 画像モーダル -->
                    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden flex items-center justify-center z-50" onclick="closeImageModal()">
                        <div class="max-w-4xl max-h-[90vh] p-2">
                            <img id="modalImage" src="" alt="拡大画像" class="max-w-full max-h-full">
                        </div>
                    </div>

                    <!-- メッセージ送信フォーム -->
                    <form action="{{ route('item.messages.store') }}" method="POST" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div class="mb-3">
                                <input type="text" name="title" value="{{ old('title') }}" placeholder="タイトル (任意)" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="mb-3">
                                <textarea name="message" rows="3" placeholder="メッセージを入力..." required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="image" class="block text-sm font-medium text-gray-700">画像ファイル（jpg, png, gif, webp）</label>
                                <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/gif,image/webp" class="mt-1 block w-full">
                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex justify-center">
                                <button type="submit" class="af-btn af-btn-primary">
                                    送信
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // ESCキーでモーダルを閉じる
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });

        function toggleMessageBody(btn) {
            const wrap = btn.previousElementSibling;
            if (!wrap || !wrap.classList.contains('message-body-wrap')) return;
            if (wrap.classList.contains('is-collapsed')) {
                wrap.classList.remove('is-collapsed');
                btn.textContent = '閉じる ▲';
            } else {
                wrap.classList.add('is-collapsed');
                btn.textContent = 'もっと見る ▼';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.message-body-wrap').forEach(function(wrap) {
                if (wrap.scrollHeight > 500) {
                    wrap.classList.add('is-collapsed');
                    const btn = wrap.nextElementSibling;
                    if (btn && btn.classList.contains('msg-toggle-btn')) {
                        btn.style.display = 'inline-block';
                    }
                }
            });
        });
    </script>

    <style>
    /* 管理画面メッセージ（.chat-container）と同じパターン背景 */
    .message-chat-area {
        background-color: #f8f9fa;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23000000' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .message-list {
        padding: 1rem;
    }
    .message-content {
        position: relative;
        display: inline-block;
    }
    .message-time {
        position: absolute;
        bottom: -25px;
        white-space: nowrap;
        width: 100%;
    }
    .message-body-wrap.is-collapsed {
        max-height: 500px;
        overflow: hidden;
    }
    .msg-toggle-btn {
        display: none;
        font-size: 12px;
        cursor: pointer;
        margin-top: 6px;
        color: inherit;
        background: none;
        border: 1px solid currentColor;
        border-radius: 4px;
        padding: 2px 8px;
        opacity: 0.85;
    }
    .msg-toggle-btn:hover {
        opacity: 1;
    }
    .message-text-content {
        word-break: break-word;
    }
    </style>
</x-item-layout> 