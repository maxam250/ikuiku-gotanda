<x-admin-layout>

    <x-slot name="header">
    オプション(有料)編集
    </x-slot>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>オプション(有料)編集</h1>
                <a href="{{ route('admin.option_pays.index') }}" class="btn btn-secondary">
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

            <form action="{{ route('admin.option_pays.update', $option_pays->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <!-- カテゴリ -->
                                <div class="mb-3">
                                    <label for="cate" class="form-label">カテゴリ</label>
                                    <select class="form-control" id="cate" name="cate">
                                        <option value="">カテゴリを選択してください</option>
                                        @foreach($optionCates as $cate)
                                            <option value="{{ $cate->id }}" {{ old('cate', $option_pays->cate) == $cate->id ? 'selected' : '' }}>
                                                {{ $cate->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- オプション名 -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">オプション名 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                           value="{{ old('title', $option_pays->title) }}" required>
                                </div>

                                <!-- 説明文 -->
                                <div class="mb-3">
                                    <label for="body" class="form-label">説明文</label>
                                    <textarea class="form-control" id="body" name="body"
                                              rows="3">{{ old('body', $option_pays->body) }}</textarea>
                                    <div class="form-text">管理画面でのメモ用途です</div>
                                </div>

                                <!-- 金額 -->
                                <div class="mb-3">
                                    <label for="price" class="form-label">金額</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="price" name="price"
                                               value="{{ old('price', $option_pays->price) }}">
                                        <span class="input-group-text">円</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 画像アップロード -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">画像設定</h5>
                            </div>
                            <div class="card-body">
                                @if($option_pays->filename)
                                    <div class="mb-3">
                                        <div class="current-image-label">現在の画像:</div>
                                        <img src="{{ Storage::url('option_pays/' . $option_pays->filename) }}"
                                             alt="現在の画像" class="img-thumbnail"
                                             style="max-width: 200px; height: auto; margin-top: 0.5rem;">
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label for="filename" class="form-label">画像</label>
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
                                <a href="{{ route('admin.option_pays.index') }}" class="btn btn-secondary w-100">
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
