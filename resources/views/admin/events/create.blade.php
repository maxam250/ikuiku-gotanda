<x-admin-layout>

    <x-slot name="header">
    イベント登録
    </x-slot>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>イベント登録</h1>
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

            <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
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
                                        <option value="0">表示なし</option>
                                        <option value="1">TOP&イベント</option>
                                        <option value="2">TOPのみ</option>
                                        <option value="3">イベントのみ</option>
                                    </select>
                                </div>

                                <!-- タイトル -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">タイトル <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                                </div>

                                <!-- 内容 -->
                                <div class="mb-3">
                                    <label for="body" class="form-label">内容</label>
                                    <textarea name="body" id="editor" class="form-control tinymce">{{ old('body') }}</textarea>
                                </div>

                                <!-- 画像 -->
                                <div class="mb-3">
                                    <label for="filename" class="form-label">画像</label>
                                    <input type="file" name="filename" id="filename" class="form-control" accept="image/jpeg,image/png,image/gif">
                                    <div class="form-text">対応フォーマット: JPEG, PNG, GIF</div>
                                </div>

                                <!-- リンク -->
                                <div class="mb-3">
                                    <label for="link" class="form-label">リンク</label>
                                    <input type="text" name="link" id="link" class="form-control" value="{{ old('link') }}" placeholder="リンク先のURL">
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
                                        <input class="form-check-input" type="checkbox" id="timer_flag" name="timer_flag" value="1" {{ old('timer_flag') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="timer_flag">
                                            <strong>タイマーフラグ</strong>
                                        </label>
                                    </div>
                                    <div class="form-text">期間を指定して公開したい場合にチェックを入れて日時をセットしてください</div>
                                </div>

                                <!-- タイマーフラグがチェックされたら表示される日時フィールド -->
                                <div id="date_fields" style="display:none;">
                                    <div class="mb-3">
                                        <label for="date_start" class="form-label">掲載開始日時</label>
                                        <input type="datetime-local" name="date_start" id="date_start" class="form-control" value="{{ old('date_start') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="date_end" class="form-label">掲載終了日時</label>
                                        <input type="datetime-local" name="date_end" id="date_end" class="form-control" value="{{ old('date_end') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 操作ボタン -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-plus"></i> イベント作成
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
