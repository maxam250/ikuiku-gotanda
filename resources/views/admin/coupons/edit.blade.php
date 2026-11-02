<x-admin-layout>

    <x-slot name="header">
    クーポン編集
    </x-slot>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>クーポン編集</h1>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
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

            <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" enctype="multipart/form-data">
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
                                           value="{{ old('title', $coupon->title) }}" required>
                                </div>

                                <!-- 説明 -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">説明</label>
                                    <textarea class="form-control" id="description" name="description"
                                              rows="4">{{ old('description', $coupon->description) }}</textarea>
                                </div>
                                <!-- クーポンコード -->
                                <div class="mb-3">
                                    <label for="code" class="form-label">クーポンコード</label>
                                    <input type="text" class="form-control" id="code" name="code"
                                           value="{{ old('code', $coupon->code) }}" placeholder="空欄の場合は自動生成されます">
                                    <div class="form-text">空欄の場合は自動で生成されます</div>
                                </div>

                            </div>
                        </div>

                        <!-- 画像アップロード -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">画像設定</h5>
                            </div>
                            <div class="card-body">
                                @if($coupon->main_image)
                                    <div class="mb-3">
                                        <div class="current-image-label">現在の画像:</div>
                                        <img src="{{ Storage::url($coupon->main_image) }}"
                                             alt="現在の画像" class="img-thumbnail"
                                             style="max-width: 200px; height: auto; margin-top: 0.5rem;">
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <label for="main_image" class="form-label">メイン画像</label>
                                    <input type="file" class="form-control" id="main_image" name="main_image"
                                           accept="image/*">
                                    <div class="form-text">jpeg、png、jpg、gif形式、2MB以下</div>
                                </div>
                            </div>
                        </div>

                        <!-- 適用キャスト -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">適用キャスト</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($items as $item)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="item_{{ $item->id }}"
                                                       name="items[]" value="{{ $item->id }}" {{ in_array($item->id, old('items', $selectedItems)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="item_{{ $item->id }}">
                                                    {{ $item->item_name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-text">複数選択可能</div>
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
                                        <input class="form-check-input" type="checkbox" id="is_active"
                                               name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <strong>有効にする</strong>
                                        </label>
                                    </div>
                                    <div class="form-text">チェックを外すと無効になります</div>
                                </div>

                                <hr>

                                <!-- 有効期限 -->
                                <div class="mb-3">
                                    <label for="expires_at" class="form-label">有効期限</label>
                                    <input type="date" class="form-control" id="expires_at" name="expires_at"
                                           value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '') }}">
                                    <div class="form-text">空欄の場合は無期限</div>
                                </div>
                            </div>
                        </div>

                        <!-- 操作ボタン -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-save"></i> 更新する
                                </button>
                                <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary w-100">
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
