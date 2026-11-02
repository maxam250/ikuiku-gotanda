<x-admin-layout>

    <x-slot name="header">
    キャストアイコン編集
    </x-slot>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>キャストアイコン編集</h1>
                <a href="{{ route('admin.icon_items.index') }}" class="btn btn-secondary">
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

            <form action="{{ route('admin.icon_items.update', $icon_item->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <!-- アイコン名 -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">アイコン名 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                           value="{{ old('title', $icon_item->title) }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- 画像アップロード -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">画像設定</h5>
                            </div>
                            <div class="card-body">
                                @if($icon_item->filename)
                                    <div class="mb-3">
                                        <div class="current-image-label">現在の画像:</div>
                                        <img src="{{ Storage::url('icon_item/' . $icon_item->filename) }}"
                                             alt="現在の画像" class="img-thumbnail"
                                             style="max-width: 200px; height: auto; margin-top: 0.5rem;">
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label for="filename" class="form-label">アイコン画像</label>
                                    <input type="file" class="form-control" id="filename" name="filename"
                                           accept="image/*">
                                    <div class="form-text">対応形式: JPEG, PNG, JPG, GIF (最大2MB)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- 操作ボタン -->
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-save"></i> 更新する
                                </button>
                                <a href="{{ route('admin.icon_items.index') }}" class="btn btn-secondary w-100">
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
</x-admin-layout>
