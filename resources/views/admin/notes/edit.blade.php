<x-admin-layout>

<x-slot name="header">
注意事項 編集
</x-slot>

<section class="toPageArea">
    <a href="{{ route('admin.notes.index') }}" class="lists">← 一覧に戻る</a>
</section>

<div class="container">
    <form method="POST" action="{{ route('admin.notes.update', $note) }}">
        @csrf
        @method('PUT')

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="modern-register-form">
            <div class="form-container">
                <div class="form-group">
                    <label>タイトル（h4） <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="modern-input" required value="{{ old('title', $note->title) }}">
                </div>
                <div class="form-group">
                    <label>サブタイトル（h5）</label>
                    <input type="text" name="subtitle" class="modern-input" value="{{ old('subtitle', $note->subtitle) }}">
                </div>
                <div class="form-group">
                    <label>コンテンツ</label>
                    <textarea id="note-editor" name="content" rows="15" class="modern-input">{!! old('content', $note->content) !!}</textarea>
                </div>
                <div class="form-row" style="justify-content:flex-end; gap:1rem;">
                    <a href="{{ route('admin.notes.index') }}" class="admin-btn admin-btn-secondary">キャンセル</a>
                    <button type="submit" class="admin-btn admin-btn-submit">
                        <i class="fas fa-save"></i> 更新
                    </button>
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
        '#note-editor',
        '{{ asset("js/tinymce/skins/ui/" . ($siteSettings?->tinymce_skin ?? "oxide")) }}',
        '{{ asset("js/tinymce/skins/content/" . (($siteSettings?->tinymce_skin ?? "oxide") === "oxide-dark" ? "dark" : "default") . "/content.min.css") }}'
    );
});
</script>
@endpush

</x-admin-layout>
