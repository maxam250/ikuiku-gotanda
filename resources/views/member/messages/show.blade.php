<x-member-layout>
    <x-slot name="header">
        メッセージ詳細
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('member.messages.index') }}" class="af-btn af-btn-cancel af-btn-sm">
                            ← メッセージ一覧に戻る
                        </a>
                    </div>

                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ $message->title }}
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                {{ $message->created_at->format('Y/m/d H:i') }} に
                                {{ $message->is_admin ? '管理者' : 'あなた' }} から送信
                            </p>
                        </div>
                        <div class="border-t border-gray-200">
                            <dl>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        メッセージ内容
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {!! nl2br(e($message->content)) !!}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($message->image_path)
                        <div class="mt-2">
                            <img src="{{ Storage::url($message->image_path) }}"
                                alt="添付画像"
                                class="max-w-full h-auto rounded cursor-pointer hover:opacity-90"
                                onclick="openImageModal(this.src)"
                                style="max-height: 200px;">
                        </div>
                    @endif

                    @if(!$message->is_admin)
                        <div class="mt-4">
                            <form action="{{ route('member.messages.destroy', $message) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="af-btn af-btn-delete" onclick="return confirm('本当に削除しますか？')">
                                    削除
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 画像モーダル -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden flex items-center justify-center z-50" onclick="closeImageModal()">
        <div class="max-w-4xl max-h-[90vh] p-2">
            <img id="modalImage" src="" alt="拡大画像" class="max-w-full max-h-full">
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
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</x-member-layout>
