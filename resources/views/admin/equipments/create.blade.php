<x-admin-layout>

    <x-slot name="header">
    ホテル新規作成
    </x-slot>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>ホテル新規作成</h1>
                <a href="{{ route('admin.equipments.index') }}" class="btn btn-secondary">
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

            <form action="{{ route('admin.equipments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <!-- カテゴリ -->
                                <div class="mb-3">
                                    <label for="cate_id" class="form-label">カテゴリ <span class="text-danger">*</span></label>
                                    <select class="form-control" id="cate_id" name="cate_id" required>
                                        <option value="" disabled selected>カテゴリを選択してください</option>
                                        @foreach($equipmentCates as $cate)
                                            <option value="{{ $cate->id }}" {{ old('cate_id') == $cate->id ? 'selected' : '' }}>
                                                {{ $cate->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 名称 -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">名称 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                           value="{{ old('title') }}" required>
                                </div>

                                <!-- 外部DB用ID -->
                                {{-- <div class="mb-3">
                                    <label for="external_id" class="form-label">外部DB用ID</label>
                                    <input type="text" class="form-control" id="external_id" name="external_id"
                                           value="{{ old('external_id') }}" placeholder="外部DBで使用するIDを入力">
                                </div> --}}

                                <!-- 概要 -->
                                <div class="mb-3">
                                    <label for="text1" class="form-label">概要</label>
                                    <input type="text" class="form-control" id="text1" name="text1"
                                           value="{{ old('text1') }}">
                                </div>

                                <!-- 項目2-5 -->
                                <div class="mb-3">
                                    <label for="text2" class="form-label">電話番号</label>
                                    <input type="text" class="form-control" id="text2" name="text2"
                                           value="{{ old('text2') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="text3" class="form-label">住所</label>
                                    <input type="text" class="form-control" id="text3" name="text3"
                                           value="{{ old('text3') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="text4" class="form-label">料金</label>
                                    <input type="text" class="form-control" id="text4" name="text4"
                                           value="{{ old('text4') }}">
                                </div>
                            {{--
                                <div class="mb-3">
                                    <label for="text5" class="form-label">項目5</label>
                                    <input type="text" class="form-control" id="text5" name="text5"
                                           value="{{ old('text5') }}">
                                </div>
                            --}}
                                <!-- リンク -->
                                <div class="mb-3">
                                    <label for="link" class="form-label">リンク</label>
                                    <input type="text" class="form-control" id="link" name="link"
                                           value="{{ old('link') }}">
                                </div>
                            </div>
                        </div>

                        <!-- 画像アップロード -->
                         {{--
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">画像設定</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="filename" class="form-label">メイン画像</label>
                                    <input type="file" class="form-control" id="filename" name="filename"
                                           accept="image/*">
                                    <div class="form-text">対応形式: JPEG, PNG, JPG, GIF (最大2MB)</div>
                                </div>
                            </div>
                        </div>
                        --}}
                    </div>

                    <div class="col-lg-4">
                        <!-- フラグ設定 -->
                        {{--
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">フラグ設定</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="flag1"
                                               name="flag1" value="1" {{ old('flag1') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flag1">
                                            フラグ1
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="flag2"
                                               name="flag2" value="1" {{ old('flag2') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flag2">
                                            フラグ2
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="flag3"
                                               name="flag3" value="1" {{ old('flag3') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flag3">
                                            フラグ3
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="flag4"
                                               name="flag4" value="1" {{ old('flag4') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flag4">
                                            フラグ4
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="flag5"
                                               name="flag5" value="1" {{ old('flag5') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="flag5">
                                            フラグ5
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        --}}
                        <!-- 操作ボタン -->
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-save"></i> 作成する
                                </button>
                                <a href="{{ route('admin.equipments.index') }}" class="btn btn-secondary w-100">
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
