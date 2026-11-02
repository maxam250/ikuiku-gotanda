<x-admin-layout>
    <x-slot name="header">ファンクラブ 編集</x-slot>

    <div class="mb-4 flex gap-3">
        <a href="{{ route('admin.fanclubs.index') }}" class="text-sm text-gray-500 hover:underline">← 一覧に戻る</a>
        <a href="{{ route('admin.fanclubs.show', $fanclub) }}" class="text-sm text-blue-600 hover:underline">詳細を見る</a>
    </div>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border rounded shadow-sm p-6 max-w-xl">
        <div class="mb-5 text-sm text-gray-500 bg-gray-50 rounded p-3">
            キャスト：<span class="font-semibold text-gray-800">{{ $fanclub->cast->item_name ?? '—' }}</span>
            <span class="ml-3 text-xs text-gray-400">（キャストの変更は不可）</span>
        </div>

        <form method="POST" action="{{ route('admin.fanclubs.update', $fanclub) }}"
              enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="space-y-4">
                {{-- タイトル --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">タイトル <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $fanclub->title) }}" required
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-pink-400">
                </div>

                {{-- 説明 --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">説明</label>
                    <textarea name="description" rows="4"
                              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-pink-400">{{ old('description', $fanclub->description) }}</textarea>
                </div>

                {{-- 価格 --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">入会費（円） <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', $fanclub->price) }}" required min="0"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-pink-400">
                </div>

                {{-- ステータス --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ステータス <span class="text-red-500">*</span></label>
                    <select name="status" required
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-pink-400">
                        <option value="active"   @selected(old('status', $fanclub->status) === 'active')>公開中</option>
                        <option value="paused"   @selected(old('status', $fanclub->status) === 'paused')>一時停止</option>
                        <option value="canceled" @selected(old('status', $fanclub->status) === 'canceled')>キャンセル</option>
                    </select>
                </div>

                {{-- メイン画像 --}}
                <div>
                    @include('admin.fanclubs.partials._main_image', ['fanclub' => $fanclub])
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="af-btn af-btn-primary">
                    更新する
                </button>
                <a href="{{ route('admin.fanclubs.show', $fanclub) }}"
                   class="px-5 py-2 bg-gray-100 text-gray-600 text-sm rounded hover:bg-gray-200">
                    キャンセル
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
