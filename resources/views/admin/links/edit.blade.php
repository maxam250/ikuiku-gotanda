<x-admin-layout>

    <x-slot name="header">
    リンク編集
    </x-slot>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>リンク編集</h1>
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

            <form action="{{ route('admin.links.update', $link->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <!-- 表示画像プレビュー -->
                                @if($link->fullcode || $link->filename)
                                    <x-links-grouped-list
                                        :links="collect([$link])"
                                        title="現在の表示プレビュー"
                                        :show-empty-groups="false"
                                    />
                                @endif

                                <!-- タイトル -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">タイトル <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $link->title) }}" required>
                                </div>

                                <!-- フルコード -->
                                <div class="mb-3">
                                    <label for="fullcode" class="form-label">フルコード（HTMLタグ）</label>
                                    <textarea onKeyDown="textareaResize(event)" name="fullcode" id="fullcode" class="form-control" rows="5">{{ old('fullcode', $link->fullcode ?? '') }}</textarea>
                                    <div class="form-text">フルコードを登録するとバナー画像より優先して表示されます。フルコードを使用する場合、リンクや画像を登録する必要はありません。</div>
                                </div>

                                <!-- リンク -->
                                <div class="mb-3">
                                    <label for="link" class="form-label">リンク</label>
                                    <input type="text" name="link" id="link" class="form-control" value="{{ old('link', $link->link) }}" placeholder="https://example.com">
                                </div>

                                <!-- バナー画像 -->
                                <div class="mb-3">
                                    <label for="filename" class="form-label">バナー画像</label>
                                    @if($link->filename)
                                    <div class="mb-2">
                                        <div class="current-image-label">現在の画像:</div>
                                        <img src="{{ asset('storage/link/' . $link->filename) }}" alt="現在の画像" class="img-thumbnail" style="width:auto;max-width: 100%; height: auto;">
                                    </div>
                                    @endif
                                    <input type="file" name="filename" id="filename" class="form-control" accept="image/*">
                                    @if ($link->filename)
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="delete_image" id="delete_image" value="1">
                                        <label class="form-check-label" for="delete_image">現在の画像を削除する</label>
                                    </div>
                                    @endif
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
                                        <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ old('active', $link->active ?? 1) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="active">
                                            <strong>表示する</strong>
                                        </label>
                                    </div>
                                </div>

                                <!-- サイズ -->
                                <div class="mb-3">
                                    <label class="form-label">サイズ</label>
                                    <div class="form-check">
                                        <input type="radio" name="position" value="0" id="size_0" class="form-check-input" {{ old('position', $link->position ?? '') == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="size_0">フリー最上段</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="position" value="1" id="size_1" class="form-check-input" {{ old('position', $link->position ?? '') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="size_1">468x60</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="position" value="2" id="size_2" class="form-check-input" {{ old('position', $link->position ?? '') == '2' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="size_2">200x40</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="position" value="3" id="size_3" class="form-check-input" {{ old('position', $link->position ?? '') == '3' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="size_3">88x31</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="position" value="4" id="size_4" class="form-check-input" {{ old('position', $link->position ?? '') == '4' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="size_4">テキスト</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="position" value="5" id="size_5" class="form-check-input" {{ old('position', $link->position ?? '') == '5' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="size_5">フリー最下段</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 操作ボタン -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-save"></i> 更新する
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
