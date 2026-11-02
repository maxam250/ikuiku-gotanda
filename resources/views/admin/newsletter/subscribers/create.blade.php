<x-admin-layout>
    <x-slot name="header">
        メールマガジン購読者追加
    </x-slot>

    <x-newsletter-menu />

    <div class="bg-white shadow-lg rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">新規購読者追加</h2>

            <form action="{{ route('admin.newsletter.subscribers.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="verified" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ old('verified') ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-600">確認済みとして登録する</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.newsletter.subscribers.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        キャンセル
                    </a>
                    <button type="submit" class="af-btn af-btn-primary">
                        保存
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

