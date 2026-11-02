<x-admin-layout>
    <x-slot name="header">ファンクラブ 新規作成</x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.fanclubs.index') }}" class="text-sm text-gray-500 hover:underline">← 一覧に戻る</a>
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
        <form method="POST" action="{{ route('admin.fanclubs.store') }}">
            @csrf

            <div class="space-y-4">
                {{-- キャスト --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">キャスト <span class="text-red-500">*</span></label>
                    @if($casts->isEmpty())
                        <p class="text-sm text-yellow-600 bg-yellow-50 p-2 rounded">
                            ファンクラブ未登録のアクティブなキャストがいません。
                        </p>
                    @else
                        <select name="cast_id" required
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-pink-400">
                            <option value="">-- 選択してください --</option>
                            @foreach($casts as $cast)
                                <option value="{{ $cast->id }}" @selected(old('cast_id') == $cast->id)>
                                    {{ $cast->item_name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                {{-- タイトル --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">タイトル <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-pink-400"
                           placeholder="例）○○ちゃんの応援ファンクラブ">
                </div>

                {{-- 説明 --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">説明</label>
                    <textarea name="description" rows="4"
                              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-pink-400"
                              placeholder="ファンクラブの説明文">{{ old('description') }}</textarea>
                </div>

                {{-- 価格 --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">入会費（円） <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', 0) }}" required min="0"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-pink-400">
                </div>

                {{-- ステータス --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ステータス <span class="text-red-500">*</span></label>
                    <select name="status" required
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-pink-400">
                        <option value="active"   @selected(old('status', 'active') === 'active')>公開中</option>
                        <option value="paused"   @selected(old('status') === 'paused')>一時停止</option>
                        <option value="canceled" @selected(old('status') === 'canceled')>キャンセル</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="px-5 py-2 bg-pink-500 text-sm rounded hover:bg-pink-600"
                        @if($casts->isEmpty()) disabled @endif>
                    作成する
                </button>
                <a href="{{ route('admin.fanclubs.index') }}"
                   class="px-5 py-2 bg-gray-100 text-gray-600 text-sm rounded hover:bg-gray-200">
                    キャンセル
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
