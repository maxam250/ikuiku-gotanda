<x-admin-layout>

    <x-slot name="header">
    リンク追加
    </x-slot>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>リンク追加</h1>
                <a href="{{ route('admin.links.index') }}" class="btn btn-secondary">
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

            <div class="alert alert-info">
                <p class="mb-0">フルコードを登録するとアップロードしたバナー画像は表示されず優先して表示されます。</p>
            </div>

            <form action="{{ route('admin.links.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <!-- サイト名 -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">サイト名 <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                                </div>

                                <!-- フルコード -->
                                <div class="mb-3">
                                    <label for="fullcode" class="form-label">フルコード（HTMLタグ）</label>
                                    <textarea onKeyDown="textareaResize(event)" name="fullcode" id="fullcode" class="form-control" rows="5">{{ old('fullcode') }}</textarea>
                                    <div class="form-text">フルコードを登録するとバナー画像より優先して表示されます。フルコードを使用する場合、リンクや画像を登録する必要はありません。</div>
                                </div>

                                <!-- リンク -->
                                <div class="mb-3">
                                    <label for="link" class="form-label">リンク</label>
                                    <input type="text" name="link" id="link" class="form-control" value="{{ old('link') }}" placeholder="https://example.com">
                                </div>

                                <!-- バナー画像 -->
                                <div class="mb-3">
                                    <label for="filename" class="form-label">バナー画像</label>
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
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ old('active', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="active">
                                            <strong>表示する</strong>
                                        </label>
                                    </div>
                                </div>

                                <!-- サイズ -->
                                <div class="mb-3">
                                    <label class="form-label">サイズ</label>
                                    <div class="form-check">
                                        <input type="radio" name="position" value="0" id="size_0" class="form-check-input" {{ old('position', 0) == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="size_0">フリー最上段</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="position" value="1" id="size_1" class="form-check-input" {{ old('position', 0) == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="size_1">468x60</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="position" value="2" id="size_2" class="form-check-input" {{ old('position', 0) == '2' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="size_2">200x40</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="position" value="3" id="size_3" class="form-check-input" {{ old('position', 0) == '3' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="size_3">88x31</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="position" value="4" id="size_4" class="form-check-input" {{ old('position', 0) == '4' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="size_4">テキスト</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="position" value="5" id="size_5" class="form-check-input" {{ old('position', 0) == '5' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="size_5">フリー最下段</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 操作ボタン -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-plus"></i> リンク登録
                                </button>
                                <a href="{{ route('admin.links.index') }}" class="btn btn-secondary w-100">
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
