<x-admin-layout>

    <x-slot name="header">
    スタジオカテゴリ編集
    </x-slot>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>スタジオカテゴリ編集</h1>
                <a href="{{ route('admin.equipment_cates.index') }}" class="btn btn-secondary">
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

            <form action="{{ route('admin.equipment_cates.update', $equipmentCate->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <!-- 名称 -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">名称 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                           value="{{ old('title', $equipmentCate->title) }}" required>
                                </div>

                                <!-- 説明文 -->
                                <div class="mb-3">
                                    <label for="text" class="form-label">説明文</label>
                                    <textarea class="form-control" id="text" name="text"
                                              rows="3">{{ old('text', $equipmentCate->text) }}</textarea>
                                    <div class="form-text">管理画面でのメモ用途です</div>
                                </div>

                                <!-- リンク先 -->
                                <div class="mb-3">
                                    <label for="link" class="form-label">リンク先</label>
                                    <input type="text" class="form-control" id="link" name="link"
                                           value="{{ old('link', $equipmentCate->link) }}">
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
                                    <label for="active" class="form-label">表示</label>
                                    <select class="form-control" id="active" name="active">
                                        <option value="0" {{ old('active', $equipmentCate->active) == 0 ? 'selected' : '' }}>表示なし</option>
                                        <option value="1" {{ old('active', $equipmentCate->active) == 1 ? 'selected' : '' }}>表示</option>
                                    </select>
                                    <div class="form-text">表示/非表示を選択してください</div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="flag"
                                               name="flag" value="1" {{ old('flag', $equipmentCate->flag) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flag">
                                            <strong>フラグ</strong>
                                        </label>
                                    </div>
                                    <div class="form-text">フラグを設定します</div>
                                </div>
                            </div>
                        </div>

                        <!-- 操作ボタン -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-save"></i> 更新する
                                </button>
                                <a href="{{ route('admin.equipment_cates.index') }}" class="btn btn-secondary w-100">
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
