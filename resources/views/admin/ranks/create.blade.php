<x-admin-layout>

    <x-slot name="header">
    ランキング登録
    </x-slot>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>ランキング登録</h1>
                <a href="{{ route('admin.ranks.index') }}" class="btn btn-secondary">
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

            <form method="post" action="{{ route('admin.ranks.store') }}">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <!-- ランキング名 -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">ランキング名 <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                                </div>

                                <!-- カテゴリ -->
                                @if (count($rankCates) > 0)
                                <div class="mb-3">
                                    <label for="cate_id" class="form-label">カテゴリ <span class="text-danger">*</span></label>
                                    <select name="cate_id" id="cate_id" class="form-control" required>
                                        <option value="" disabled selected>カテゴリを選択してください</option>
                                        @foreach($rankCates as $cate)
                                            <option value="{{ $cate->id }}" {{ old('cate_id') == $cate->id ? 'selected' : '' }}>{{ $cate->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                <!-- 説明文 -->
                                <div class="mb-3">
                                    <label for="body" class="form-label">説明文</label>
                                    <textarea name="body" id="body" class="form-control" rows="5">{{ old('body') }}</textarea>
                                </div>

                                <!-- 画像タイトルファイル名 -->
                                <div class="mb-3">
                                    <label for="filename" class="form-label">画像タイトルファイル名</label>
                                    <input type="text" name="filename" id="filename" class="form-control" value="{{ old('filename') }}" placeholder="xxxx.jpg / ●●●.png">
                                    <div class="form-text">画像タイトルファイル名を入力してください</div>
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
                                <!-- ランキングの表示数 -->
                                <div class="mb-3">
                                    <label for="num" class="form-label">ランキングの表示数</label>
                                    <select name="num" id="num" class="form-control">
                                        @for($i=1; $i <= 10; $i++ )
                                            <option value="{{ $i }}" {{ old('num', 10) == $i ? 'selected' : '' }}>{{ $i }}位</option>
                                        @endfor
                                    </select>
                                    <div class="form-text">まで表示</div>
                                </div>

                                <!-- 公開設定 -->
                                <div class="mb-3">
                                    <label for="active" class="form-label">公開設定</label>
                                    <select name="active" id="active" class="form-control">
                                        <option value="1" {{ old('active', 1) == 1 ? 'selected' : '' }}>公開</option>
                                        <option value="0" {{ old('active', 1) == 0 ? 'selected' : '' }}>非公開</option>
                                    </select>
                                </div>

                                <!-- 表示 -->
                                <div class="mb-3">
                                    <label for="position" class="form-label">表示</label>
                                    <select name="position" id="position" class="form-control">
                                        <option value="0" {{ old('position', 0) == 0 ? 'selected' : '' }}>全ポジション</option>
                                        <option value="1" {{ old('position', 0) == 1 ? 'selected' : '' }}>ポジション①のみ</option>
                                        <option value="2" {{ old('position', 0) == 2 ? 'selected' : '' }}>ポジション②のみ</option>
                                    </select>
                                </div>

                                <!-- 期間 -->
                                <div class="mb-3">
                                    <label for="term_start" class="form-label">期間 [開始]</label>
                                    <input type="date" name="term_start" id="term_start" class="form-control" value="{{ old('term_start') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="term_end" class="form-label">期間 [終了]</label>
                                    <input type="date" name="term_end" id="term_end" class="form-control" value="{{ old('term_end') }}">
                                </div>
                            </div>
                        </div>

                        <!-- 操作ボタン -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-plus"></i> 登録
                                </button>
                                <a href="{{ route('admin.ranks.index') }}" class="btn btn-secondary w-100">
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
