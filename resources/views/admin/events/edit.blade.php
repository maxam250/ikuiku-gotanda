<x-admin-layout>

    <x-slot name="header">
    イベント編集
    </x-slot>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>イベント編集</h1>
                <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> 一覧に戻る
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data" id="eventForm">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <!-- 表示位置 -->
                                <div class="mb-3">
                                    <label for="position" class="form-label">表示位置</label>
                                    <select name="position" id="position" class="form-control">
                                        <option value="0" {{ $event->position == 0 ? 'selected' : '' }}>表示なし</option>
                                        <option value="1" {{ $event->position == 1 ? 'selected' : '' }}>TOP&イベント</option>
                                        <option value="2" {{ $event->position == 2 ? 'selected' : '' }}>TOPのみ</option>
                                        <option value="3" {{ $event->position == 3 ? 'selected' : '' }}>イベントのみ</option>
                                    </select>
                                </div>

                                <!-- タイトル -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">タイトル <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $event->title) }}" required>
                                </div>

                                <!-- 内容 -->
                                <div class="mb-3">
                                    <label for="body" class="form-label">内容</label>
                                    <textarea name="body" id="editor" class="form-control tinymce">{{ old('body', $event->body) }}</textarea>
                                </div>

                                <!-- 画像 -->
                                <div class="mb-3">
                                    <label for="filename" class="form-label">画像</label>
                                    @if($event->filename)
                                    <div class="mb-2">
                                        <div class="current-image-label">現在の画像:</div>
                                        <img src="{{ Storage::url('events/' . $event->filename) }}" alt="現在の画像" class="img-thumbnail" style="max-width: 100%; height: auto;">
                                    </div>
                                    @endif
                                    <input type="file" name="filename" id="filename" class="form-control" accept="image/jpeg,image/png,image/gif">
                                    <div class="form-text">対応フォーマット: JPEG, PNG, GIF</div>
                                    @if ($event->filename)
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="delete_image" id="delete_image" value="1">
                                        <label class="form-check-label" for="delete_image">現在の画像を削除する</label>
                                    </div>
                                    @endif
                                    @if($event->filename)
                                    <div class="mt-2">
                                        <label for="fileurl" class="form-label">画像URL(コピペ用)</label>
                                        <input type="text" readonly value="{{ Storage::url('events/' . $event->filename) }}" class="form-control" id="fileurl">
                                    </div>
                                    @endif
                                </div>

                                <!-- リンク -->
                                <div class="mb-3">
                                    <label for="link" class="form-label">リンク</label>
                                    <input type="text" name="link" id="link" class="form-control" value="{{ old('link', $event->link) }}" placeholder="リンク先のURL">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- 表示設定 -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">表示設定</h5>
                            </div>
                            <div class="card-body">
                                <!-- タイマーフラグ -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="timer_flag" name="timer_flag" value="1" {{ old('timer_flag', $event->timer_flag) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="timer_flag">
                                            <strong>タイマーフラグ</strong>
                                        </label>
                                    </div>
                                    <div class="form-text">期間を指定して公開したい場合にチェックを入れて日時をセットしてください</div>
                                </div>

                                <!-- タイマーフラグがチェックされたら表示される日時フィールド -->
                                <div id="date_fields" style="{{ old('timer_flag', $event->timer_flag) ? '' : 'display:none;' }}">
                                    <div class="mb-3">
                                        <label for="date_start" class="form-label">掲載開始日時</label>
                                        <input type="datetime-local" name="date_start" id="date_start" class="form-control" value="{{ old('date_start', $event->date_start) }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="date_end" class="form-label">掲載終了日時</label>
                                        <input type="datetime-local" name="date_end" id="date_end" class="form-control" value="{{ old('date_end', $event->date_end) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 操作ボタン -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-save"></i> 更新する
                                </button>
                                <a href="{{ route('admin.events.index') }}" class="btn btn-secondary w-100">
                                    <i class="fas fa-times"></i> キャンセル
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.current-image-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('js/admin-tinymce.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // TinyMCE初期化
        initAdminTinyMCE(
            '#editor',
            '{{ asset("js/tinymce/skins/ui/" . ($siteSettings?->tinymce_skin ?? "oxide")) }}',
            '{{ asset("js/tinymce/skins/content/" . (($siteSettings?->tinymce_skin ?? "oxide") === "oxide-dark" ? "dark" : "default") . "/content.min.css") }}'
        );

        var timerFlagCheckbox = document.getElementById('timer_flag');
        var dateFields = document.getElementById('date_fields');

        // チェックボックスの状態に応じて表示・非表示を切り替え
        timerFlagCheckbox.addEventListener('change', function () {
            if (timerFlagCheckbox.checked) {
                dateFields.style.display = 'block';
            } else {
                dateFields.style.display = 'none';
            }
        });
    });
</script>
@endpush

</x-admin-layout>
