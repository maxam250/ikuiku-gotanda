<x-admin-layout>

    <x-slot name="header">
    ランキングカテゴリ編集
    </x-slot>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>ランキングカテゴリ編集</h1>
                <a href="{{ route('admin.rank_cates.index') }}" class="btn btn-secondary">
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

            <form action="{{ route('admin.rank_cates.update', $rankCate->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="title" class="form-label">カテゴリ名 <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $rankCate->title) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="body" class="form-label">説明文</label>
                                    <textarea name="body" id="body" class="form-control" rows="5">{{ old('body', $rankCate->body) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">表示設定</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="active" class="form-label">公開設定</label>
                                    <select name="active" id="active" class="form-control">
                                        <option value="1" {{ old('active', $rankCate->active) == 1 ? 'selected' : '' }}>公開</option>
                                        <option value="0" {{ old('active', $rankCate->active) == 0 ? 'selected' : '' }}>非公開</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-save"></i> 更新
                                </button>
                                <a href="{{ route('admin.rank_cates.index') }}" class="btn btn-secondary w-100">
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
