<x-admin-layout>

    <x-slot name="header">
    コスチューム追加
    </x-slot>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>コスチューム追加</h1>
                <a href="{{ route('admin.costumes.index') }}" class="btn btn-secondary">
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

            <form action="{{ route('admin.costumes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <!-- コス名 -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">コス名 <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                                </div>

                                <!-- 説明文 -->
                                <div class="mb-3">
                                    <label for="body" class="form-label">説明文</label>
                                    <textarea name="body" id="body" class="form-control" rows="5">{{ old('body') }}</textarea>
                                </div>

                                <!-- 画像 -->
                                <div class="mb-3">
                                    <label for="filename" class="form-label">画像</label>
                                    <input type="file" name="filename" id="filename" class="form-control" accept="image/*">
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
                                    <label for="active" class="form-label">表示設定</label>
                                    <select name="active" id="active" class="form-control">
                                        <option value="0">非表示</option>
                                        <option value="1" {{ old('active', 1) == 1 ? 'selected' : '' }}>表示</option>
                                    </select>
                                </div>

                                <!-- カテゴリ -->
                                @if (count($costumeCates) > 0)
                                <div class="mb-3">
                                    <label class="form-label">カテゴリ</label>
                                    @foreach($costumeCates as $cate)
                                    <div class="form-check">
                                        <input type="radio" name="cate" id="cate{{ $cate->id }}" class="form-check-input" value="{{ $cate->id }}" {{ old('cate', $loop->first ? $cate->id : '') == $cate->id ? 'checked' : '' }}>
                                        <label for="cate{{ $cate->id }}" class="form-check-label">{{ $cate->title }}</label>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- 操作ボタン -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-plus"></i> コスチューム追加
                                </button>
                                <a href="{{ route('admin.costumes.index') }}" class="btn btn-secondary w-100">
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

</x-admin-layout>
