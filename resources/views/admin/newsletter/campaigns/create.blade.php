<x-admin-layout>
    <x-slot name="header">
        メールマガジン配信作成
    </x-slot>

    <x-newsletter-menu />

    <div class="bg-white shadow-lg rounded-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">新規配信作成</h2>

            <form action="{{ route('admin.newsletter.campaigns.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="template_id" class="block text-sm font-medium text-gray-700">テンプレート（オプション）</label>
                    <select name="template_id" id="template_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">テンプレートを選択しない</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" {{ old('template_id', $templateId) == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                        @endforeach
                    </select>
                    @error('template_id')
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

                <div class="mb-4">
                    <label for="filter_status" class="block text-sm font-medium text-gray-700">配信先フィルター <span class="text-red-500">*</span></label>
                    <select name="filter_status" id="filter_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="verified" {{ old('filter_status', 'verified') == 'verified' ? 'selected' : '' }}>確認済みのみ</option>
                        <option value="unverified" {{ old('filter_status') == 'unverified' ? 'selected' : '' }}>未確認のみ</option>
                        <option value="all" {{ old('filter_status') == 'all' ? 'selected' : '' }}>すべて</option>
                    </select>
                    @error('filter_status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="scheduled_at" class="block text-sm font-medium text-gray-700">配信予定日時（空欄の場合は下書きとして保存）</label>
                    <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">指定しない場合は下書きとして保存され、後で配信できます。</p>
                    @error('scheduled_at')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.newsletter.campaigns.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
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

            // テンプレート選択時に内容を自動入力
            document.getElementById('template_id').addEventListener('change', function() {
                const templateId = this.value;
                if (templateId) {
                    fetch(`/kanrigamen/newsletter/templates/${templateId}/data`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.subject) document.getElementById('subject').value = data.subject;
                            if (data.body_text) document.getElementById('body_text').value = data.body_text;
                            if (data.body_html && tinymce.get('body_html')) {
                                tinymce.get('body_html').setContent(data.body_html);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>

