<x-admin-layout>

    <x-slot name="header">
    オプションカテゴリ編集
    </x-slot>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>オプションカテゴリ編集</h1>
                <a href="{{ route('admin.option_cates.index') }}" class="btn btn-secondary">
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

            <form action="{{ route('admin.option_cates.update', $optionCate->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <!-- カテゴリ名 -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">カテゴリ名 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                           value="{{ old('title', $optionCate->title) }}" required>
                                </div>

                                <!-- 説明文 -->
                                <div class="mb-3">
                                    <label for="body" class="form-label">説明文</label>
                                    <textarea class="form-control" id="body" name="body"
                                              rows="3">{{ old('body', $optionCate->body) }}</textarea>
                                    <div class="form-text">管理画面でのメモ用途です</div>
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
                                <a href="{{ route('admin.option_cates.index') }}" class="btn btn-secondary w-100">
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
