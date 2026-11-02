<x-admin-layout>

    <x-slot name="header">
    マイオプション新規作成
    </x-slot>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>マイオプション新規作成</h1>
                @if($selectedItemId)
                    <a href="{{ route('admin.myoptions.cast', $selectedItemId) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> 一覧に戻る
                    </a>
                @else
                    <a href="{{ route('admin.myoptions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> 一覧に戻る
                    </a>
                @endif
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

            <form action="{{ route('admin.myoptions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
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
                                            <option value="{{ $item->id }}" {{ old('item_id', $selectedItemId) == $item->id ? 'selected' : '' }}>
                                                {{ $item->item_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- カテゴリ -->
                                <div class="mb-3">
                                    <label for="myoption_cate_id" class="form-label">カテゴリ <span class="text-danger">*</span></label>
                                    <select class="form-control" id="myoption_cate_id" name="myoption_cate_id" required>
                                        <option value="">カテゴリを選択してください</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('myoption_cate_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- オプション名 -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">オプション名 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ old('name') }}" required>
                                </div>

                                <!-- 説明文 -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">説明文</label>
                                    <textarea class="form-control" id="description" name="description"
                                              rows="4">{{ old('description') }}</textarea>
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
                                <div class="mb-3">
                                    <label for="image" class="form-label">画像 <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="image" name="image"
                                           accept="image/jpeg,image/jpg,image/png" required>
                                    <div class="form-text">JPG、JPEG、PNG形式、最大2MB</div>
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
                                               name="active" value="1" {{ old('active', 1) ? 'checked' : '' }}>
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
                                    <i class="fas fa-save"></i> 作成する
                                </button>
                                @if($selectedItemId)
                                    <a href="{{ route('admin.myoptions.cast', $selectedItemId) }}" class="btn btn-secondary w-100">
                                        <i class="fas fa-times"></i> キャンセル
                                    </a>
                                @else
                                    <a href="{{ route('admin.myoptions.index') }}" class="btn btn-secondary w-100">
                                        <i class="fas fa-times"></i> キャンセル
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</x-admin-layout>
