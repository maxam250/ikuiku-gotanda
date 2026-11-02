<x-admin-layout>

<x-slot name="header">ジャーナル作成</x-slot>

<div class="af-page">
    <div class="af-page-header">
        <h1 class="af-page-title">ジャーナル作成</h1>
        <a href="{{ route('admin.blogs.index') }}" class="af-btn af-btn-cancel">
            <i class="fas fa-arrow-left"></i> 一覧に戻る
        </a>
    </div>

    @if ($errors->any())
        <div class="af-alert af-alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="af-layout">

            {{-- 左カラム --}}
            <div class="af-main">
                <div class="af-section">
                    <div class="af-section-header">
                        <h5 class="af-section-title">基本情報</h5>
                    </div>
                    <div class="af-section-body">

                        <div class="af-field">
                            <label class="af-label" for="title">タイトル<span class="af-required">*</span></label>
                            <input type="text" name="title" id="title" class="af-input" value="{{ old('title') }}" required>
                        </div>

                        <div class="af-field">
                            <label class="af-label" for="content">内容</label>
                            <textarea id="content" name="content" class="af-textarea tinymce">{{ old('content', '') }}</textarea>
                        </div>

                        <div class="af-field">
                            <label class="af-label" for="main_image">メイン画像</label>
                            <input type="file" name="main_image" id="main_image" class="af-file" accept="image/*">
                        </div>

                        <div class="af-field">
                            <label class="af-label">
                                <i class="fas fa-images" style="margin-right:0.2rem;"></i>
                                ファイルマネージャー（サブ画像）
                            </label>
                            <p class="af-hint">保存後に編集画面から画像URLを確認できます。本文に貼り付けてご利用ください。</p>
                            <input type="file" name="sub_images[]" multiple class="af-file" accept="image/*">
                        </div>

                    </div>
                </div>
            </div>

            {{-- 右カラム --}}
            <div class="af-sidebar">
                <div class="af-section">
                    <div class="af-section-header">
                        <h5 class="af-section-title">公開設定</h5>
                    </div>
                    <div class="af-section-body">
                        <label class="af-check-label">
                            <input type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                            公開する
                        </label>
                    </div>
                </div>

                <div class="af-section">
                    <div class="af-section-body">
                        <div class="af-btn-row" style="flex-direction:column;">
                            <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                <i class="fas fa-plus"></i> 記事を作成する
                            </button>
                            <a href="{{ route('admin.blogs.index') }}" class="af-btn af-btn-cancel af-btn-full">
                                <i class="fas fa-times"></i> キャンセル
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

@push('scripts')
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('js/admin-tinymce.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initAdminTinyMCE(
            '#content',
            '{{ asset("js/tinymce/skins/ui/" . ($siteSettings?->tinymce_skin ?? "oxide")) }}',
            '{{ asset("js/tinymce/skins/content/" . (($siteSettings?->tinymce_skin ?? "oxide") === "oxide-dark" ? "dark" : "default") . "/content.min.css") }}'
        );
    });
</script>
@endpush

</x-admin-layout>
