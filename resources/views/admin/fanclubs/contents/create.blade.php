<x-admin-layout>
    <x-slot name="header">チャンネル新規作成</x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.fanclubs.contents.index') }}" class="text-blue-600 underline text-sm">← 一覧に戻る</a>
    </div>

    <div class="border bg-white p-6 max-w-lg">
        <p class="text-sm text-gray-600 mb-4">チャンネルは <code class="text-xs bg-gray-100 px-1 rounded">fanclub_contents</code> に登録されます（ポストは別途追加）。</p>

        <form method="POST" action="{{ route('admin.fanclubs.contents.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">キャスト（ファンクラブ）</label>
                <select name="cast_id" required class="w-full border rounded px-3 py-2 text-sm @error('cast_id') border-red-500 @enderror">
                    <option value="">選択してください</option>
                    @foreach($casts as $cast)
                        <option value="{{ $cast->id }}" @selected(old('cast_id') == $cast->id)>{{ $cast->item_name }}</option>
                    @endforeach
                </select>
                @error('cast_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">タイトル（チャンネル名）</label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                       class="w-full border rounded px-3 py-2 text-sm @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">説明（任意）</label>
                <textarea name="description" rows="3" class="w-full border rounded px-3 py-2 text-sm @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_paid" value="0">
                <input type="checkbox" name="is_paid" id="is_paid" value="1" class="rounded border-gray-300" @checked(old('is_paid'))>
                <label for="is_paid" class="text-sm text-gray-700">有料（会員限定）</label>
            </div>

            <div class="pt-2">
                <button type="submit" class="af-btn af-btn-primary">
                    登録する
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
