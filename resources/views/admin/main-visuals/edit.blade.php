<x-admin-layout>

    <x-slot name="header">
    メインビジュアル編集
    </x-slot>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>メインビジュアル編集</h1>
                <a href="{{ route('admin.main-visuals.index') }}" class="btn btn-secondary">
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

            <form action="{{ route('admin.main-visuals.update', $mainVisual) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <!-- タイトル -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">タイトル <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                           value="{{ old('title', $mainVisual->title) }}" required>
                                    <div class="form-text">画像のalt属性にも使用されます</div>
                                </div>

                                <!-- 説明文 -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">説明文</label>
                                    <textarea class="form-control" id="description" name="description"
                                              rows="3">{{ old('description', $mainVisual->description) }}</textarea>
                                    <div class="form-text">管理画面でのメモ用途です</div>
                                </div>

                                <!-- リンクURL -->
                                <div class="mb-3">
                                    <label for="link_url" class="form-label">リンクURL</label>
                                    <input type="url" class="form-control" id="link_url" name="link_url"
                                           value="{{ old('link_url', $mainVisual->link_url) }}" placeholder="https://example.com/">
                                    <div class="form-text">画像クリック時の遷移先URL（省略可）</div>
                                </div>

                                <!-- 並び順 -->
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">並び順</label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order"
                                           value="{{ old('sort_order', $mainVisual->sort_order) }}" min="0">
                                    <div class="form-text">小さい数字ほど上に表示されます</div>
                                </div>
                            </div>
                        </div>

                        <!-- 画像アップロード -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">画像設定</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- PC用画像 -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="pc_image" class="form-label">PC用画像</label>

                                            @if($mainVisual->pc_image_path)
                                            <div class="mb-2">
                                                <div class="current-image-label">現在の画像:</div>
                                                <img src="{{ Storage::url($mainVisual->pc_image_path) }}"
                                                     alt="現在のPC画像" class="img-thumbnail"
                                                     style="max-width: 100%; height: auto;">
                                            </div>
                                            @endif

                                            <input type="file" class="form-control" id="pc_image" name="pc_image"
                                                   accept="image/*" onchange="previewImage(this, 'pc-preview')">
                                            <div class="form-text">推奨サイズ: 1920x1080px（16:9）</div>

                                            <div class="mt-2">
                                                <div id="pc-preview" class="preview-container" style="display: none;">
                                                    <div class="preview-label">新しい画像プレビュー:</div>
                                                    <img class="preview-image" style="max-width: 100%; height: auto; border-radius: 4px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- スマホ用画像 -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mobile_image" class="form-label">スマホ用画像</label>

                                            @if($mainVisual->mobile_image_path)
                                            <div class="mb-2">
                                                <div class="current-image-label">現在の画像:</div>
                                                <img src="{{ Storage::url($mainVisual->mobile_image_path) }}"
                                                     alt="現在のスマホ画像" class="img-thumbnail"
                                                     style="max-width: 100%; height: auto;">
                                            </div>
                                            @endif

                                            <input type="file" class="form-control" id="mobile_image" name="mobile_image"
                                                   accept="image/*" onchange="previewImage(this, 'mobile-preview')">
                                            <div class="form-text">推奨サイズ: 750x1334px（9:16）</div>

                                            <div class="mt-2">
                                                <div id="mobile-preview" class="preview-container" style="display: none;">
                                                    <div class="preview-label">新しい画像プレビュー:</div>
                                                    <img class="preview-image" style="max-width: 100%; height: auto; border-radius: 4px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                                <!-- 表示フラグ -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active"
                                               name="is_active" value="1"
                                               {{ old('is_active', $mainVisual->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <strong>表示する</strong>
                                        </label>
                                    </div>
                                    <div class="form-text">チェックを外すと非表示になります</div>
                                </div>

                                <!-- デバイス別表示設定 -->
                                <div class="mb-3">
                                    <label class="form-label">表示デバイス</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="show_on_pc"
                                               name="show_on_pc" value="1"
                                               {{ old('show_on_pc', $mainVisual->show_on_pc) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_on_pc">
                                            PC・タブレット
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="show_on_mobile"
                                               name="show_on_mobile" value="1"
                                               {{ old('show_on_mobile', $mainVisual->show_on_mobile) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_on_mobile">
                                            スマートフォン
                                        </label>
                                    </div>
                                </div>

                                <hr>

                                <!-- 表示期間 -->
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">表示開始日時</label>
                                    <input type="datetime-local" class="form-control" id="start_date"
                                           name="start_date"
                                           value="{{ old('start_date', $mainVisual->start_date?->format('Y-m-d\TH:i')) }}">
                                    <div class="form-text">空欄の場合は即座に表示開始</div>
                                </div>

                                <div class="mb-3">
                                    <label for="end_date" class="form-label">表示終了日時</label>
                                    <input type="datetime-local" class="form-control" id="end_date"
                                           name="end_date"
                                           value="{{ old('end_date', $mainVisual->end_date?->format('Y-m-d\TH:i')) }}">
                                    <div class="form-text">空欄の場合は無期限表示</div>
                                </div>
                            </div>
                        </div>

                        <!-- 操作ボタン -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-save"></i> 更新する
                                </button>
                                <a href="{{ route('admin.main-visuals.index') }}" class="btn btn-secondary w-100">
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
.current-image-label,
.preview-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const img = preview.querySelector('.preview-image');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endpush
</x-admin-layout>
