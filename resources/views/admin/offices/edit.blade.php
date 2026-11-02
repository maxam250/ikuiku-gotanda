<x-admin-layout>
    <x-slot name="header">事務所 編集</x-slot>

    <div class="w-full py-8 px-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">事務所 編集</h1>
            <a href="{{ route('admin.offices.index') }}" class="admin-btn admin-btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> 一覧に戻る
            </a>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <div class="font-bold mb-2">入力にエラーがあります：</div>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <form action="{{ route('admin.offices.update', $office) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        名前 <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name"
                           class="w-full border-gray-300 rounded-md shadow-sm @error('name') border-red-500 @enderror"
                           value="{{ old('name', $office->name) }}" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="memo" class="block text-sm font-medium text-gray-700 mb-1">メモ</label>
                    <textarea name="memo" id="memo" rows="3"
                              class="w-full border-gray-300 rounded-md shadow-sm @error('memo') border-red-500 @enderror">{{ old('memo', $office->memo) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">並び順</label>
                        <input type="number" name="sort" id="sort"
                               class="w-full border-gray-300 rounded-md shadow-sm"
                               value="{{ old('sort', $office->sort) }}" min="0">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $office->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600">
                            <span class="text-sm text-gray-700">有効</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.offices.index') }}" class="admin-btn admin-btn-secondary">キャンセル</a>
                    <button type="submit" class="admin-btn admin-btn-primary">更新する</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
