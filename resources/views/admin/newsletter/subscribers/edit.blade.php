<x-admin-layout>
    <x-slot name="header">
        メールマガジン購読者編集
    </x-slot>

    <x-newsletter-menu />

    <div class="bg-white shadow-lg rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">購読者編集</h2>

            <form action="{{ route('admin.newsletter.subscribers.update', $subscriber->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $subscriber->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="verified" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ old('verified', $subscriber->isVerified()) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-600">確認済み</span>
                    </label>
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="unsubscribed" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ old('unsubscribed', $subscriber->isUnsubscribed()) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-600">解除済み</span>
                    </label>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-500">
                        登録日時: {{ $subscriber->created_at->format('Y年m月d日 H:i') }}<br>
                        @if($subscriber->verified_at)
                        確認日時: {{ $subscriber->verified_at->format('Y年m月d日 H:i') }}<br>
                        @endif
                        @if($subscriber->unsubscribed_at)
                        解除日時: {{ $subscriber->unsubscribed_at->format('Y年m月d日 H:i') }}<br>
                        @endif
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.newsletter.subscribers.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        キャンセル
                    </a>
                    <button type="submit" class="af-btn af-btn-primary">
                        更新
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

