<x-admin-layout>
    <x-slot name="header">
        メールマガジンテンプレート作成
    </x-slot>

    <x-newsletter-menu />

    <div class="bg-white shadow-lg rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">新規テンプレート作成</h2>

            <form action="{{ route('admin.newsletter.templates.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">テンプレート名 <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="subject" class="block text-sm font-medium text-gray-700">件名 <span class="text-red-500">*</span></label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    @error('subject')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="body_text" class="block text-sm font-medium text-gray-700">プレーンテキスト本文</label>
                    <textarea name="body_text" id="body_text" rows="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('body_text') }}</textarea>
                    @error('body_text')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="body_html" class="block text-sm font-medium text-gray-700">HTML本文</label>
                    <textarea name="body_html" id="body_html" class="tinymce mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('body_html') }}</textarea>
                    @error('body_html')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.newsletter.templates.index') }}" class="af-btn af-btn-cancel">
                        キャンセル
                    </a>
                    <button type="submit" class="af-btn af-btn-primary">
                        保存
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('js/admin-tinymce.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initAdminTinyMCE(
                '#body_html',
                '{{ asset("js/tinymce/skins/ui/" . ($siteSettings?->tinymce_skin ?? "oxide")) }}',
                '{{ asset("js/tinymce/skins/content/" . (($siteSettings?->tinymce_skin ?? "oxide") === "oxide-dark" ? "dark" : "default") . "/content.min.css") }}'
            );
        });
    </script>
    @endpush
</x-admin-layout>

