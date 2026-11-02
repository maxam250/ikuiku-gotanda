<x-admin-layout>

    <x-slot name="header">
    マイコスチューム編集
    </x-slot>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>マイコスチューム編集</h1>
                <a href="{{ route('admin.mycostumes.index') }}" class="btn btn-secondary">
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

            <form action="{{ route('admin.mycostumes.update', $mycostume) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <!-- キャスト -->
                                <div class="mb-3">
                                    <label for="item_id" class="form-label">キャスト <span class="text-danger">*</span></label>
                                    <select class="form-control" id="item_id" name="item_id" required>
                                        <option value="">キャストを選択してください</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}" {{ old('item_id', $mycostume->item_id) == $item->id ? 'selected' : '' }}>
                                                {{ $item->item_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- カテゴリ -->
                                <div class="mb-3">
                                    <label for="mycostume_cate_id" class="form-label">カテゴリ <span class="text-danger">*</span></label>
                                    <select class="form-control" id="mycostume_cate_id" name="mycostume_cate_id" required>
                                        <option value="">カテゴリを選択してください</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('mycostume_cate_id', $mycostume->mycostume_cate_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- コス名 -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">コス名 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ old('name', $mycostume->name) }}" required>
                                </div>

                                <!-- 説明文 -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">説明文</label>
                                    <textarea class="form-control" id="description" name="description"
                                              rows="4">{{ old('description', $mycostume->body) }}</textarea>
                                    <div class="form-text">管理画面でのメモ用途です</div>
                                </div>
                            </div>
                        </div>

                        <!-- 画像アップロード -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">画像設定</h5>
                            </div>
                            <div class="card-body">
                                @if ($mycostume->filename)
                                    <div class="mb-3">
                                        <div class="current-image-label">現在の画像:</div>
                                        <img src="{{ Storage::url('mycostume/' . $mycostume->filename) }}"
                                             alt="現在の画像" class="img-thumbnail"
                                             style="max-width: 200px; height: auto; margin-top: 0.5rem;">
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label for="image" class="form-label">画像</label>
                                    <input type="file" class="form-control" id="image" name="image"
                                           accept="image/jpeg,image/jpg,image/png">
                                    <div class="form-text">JPG、JPEG、PNG形式、最大2MB（変更しない場合は空欄のまま）</div>
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
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="active"
                                               name="active" value="1" {{ old('active', $mycostume->active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="active">
                                            <strong>表示する</strong>
                                        </label>
                                    </div>
                                    <div class="form-text">チェックを外すと非表示になります</div>
                                </div>
                            </div>
                        </div>

                        <!-- 操作ボタン -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-save"></i> 更新する
                                </button>
                                <a href="{{ route('admin.mycostumes.index') }}" class="btn btn-secondary w-100">
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
