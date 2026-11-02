@php use Illuminate\Support\Facades\Storage; @endphp

<x-admin-layout>

<x-slot name="header">ジャーナル編集</x-slot>

<div class="af-page">
    <div class="af-page-header">
        <h1 class="af-page-title">ジャーナル編集</h1>
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

    <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
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
                            <input type="text" name="title" id="title" class="af-input" value="{{ old('title', $blog->title) }}" required>
                        </div>

                        <div class="af-field">
                            <label class="af-label" for="content">内容</label>
                            <textarea id="content" name="content" class="af-textarea tinymce">{{ old('content', $blog->content) }}</textarea>
                        </div>

                        <div class="af-field">
                            <label class="af-label">メイン画像</label>
                            @if ($blog->main_image)
                                <div class="af-current-img">
                                    <p>現在の画像</p>
                                    <img src="{{ Storage::url($blog->main_image) }}" alt="現在のメイン画像">
                                </div>
                                <button type="submit" form="delete-main-image-form" class="af-btn af-btn-delete af-btn-sm mb-2">
                                    <i class="fas fa-trash"></i> メイン画像を削除
                                </button>
                            @endif
                            <input type="file" name="main_image" id="main_image" class="af-file" accept="image/*">
                        </div>

                        <div class="af-field">
                            <label class="af-label">
                                <i class="fas fa-images" style="margin-right:0.2rem;"></i>
                                ファイルマネージャー（サブ画像）
                            </label>
                            <p class="af-hint">画像URLをコピーして本文エディタに貼り付けてください。</p>

                            @if($blog->sub_images && count($blog->sub_images) > 0)
                                <div class="af-imglist">
                                    @foreach($blog->sub_images as $index => $img)
                                        <div class="af-imgitem">
                                            <img src="{{ Storage::url($img) }}" alt="サブ画像">
                                            <input type="text" readonly
                                                   value="{{ url(Storage::url($img)) }}"
                                                   onclick="this.select()"
                                                   title="クリックで選択">
                                            <button type="submit" form="delete-sub-image-form-{{ $index }}"
                                                    class="af-btn af-btn-delete">
                                                <i class="fas fa-trash"></i> 削除
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="af-imglist-empty">画像がありません</div>
                            @endif

                            <label class="af-label" style="margin-top:0.4rem;">画像を追加アップロード</label>
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
                            <input type="checkbox" name="is_published" id="is_published" value="1"
                                   {{ old('is_published', $blog->is_published) ? 'checked' : '' }}>
                            公開する
                        </label>
                    </div>
                </div>

                <div class="af-section">
                    <div class="af-section-body">
                        <div class="af-btn-row" style="flex-direction:column;">
                            <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                <i class="fas fa-save"></i> 記事を更新する
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

    {{-- 画像削除フォーム（メインフォームの外） --}}
    @if ($blog->main_image)
    <form id="delete-main-image-form" action="{{ route('admin.blogs.deleteMainImage', $blog) }}" method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>
    @endif

    @if($blog->sub_images)
        @foreach($blog->sub_images as $index => $img)
        <form id="delete-sub-image-form-{{ $index }}" action="{{ route('admin.blogs.deleteSubImage', [$blog, $index]) }}" method="POST" style="display:none;">
            @csrf @method('DELETE')
        </form>
        @endforeach
    @endif
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
