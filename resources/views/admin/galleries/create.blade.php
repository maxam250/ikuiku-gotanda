<x-admin-layout>

    <x-slot name="header">
    ギャラリー登録
    </x-slot>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>ギャラリー登録</h1>
                <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">
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

            <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
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
                                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                                </div>

                                <!-- 投稿者 -->
                                {{--
                                <div class="mb-3">
                                    <label for="member_id" class="form-label">投稿者</label>
                                    <select name="member_id" id="member_id" class="form-control">
                                        <option value="0" selected>なし</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                --}}
                                <!-- キャスト -->
                                <div class="mb-3">
                                    <label for="item_id" class="form-label">キャスト</label>
                                    <select name="item_id" id="item_id" class="form-control">
                                        <option value="0" selected>なし</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->item_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- コメント -->
                                <div class="mb-3">
                                    <label for="editor" class="form-label">コメント</label>
                                    <textarea onKeyDown="textareaResize(event)" id="editor" name="descriptioin" class="form-control" rows="5">{{ old('descriptioin', '') }}</textarea>
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
                                    <label for="active" class="form-label">表示</label>
                                    <select name="active" id="active" class="form-control">
                                        <option value="0">表示なし</option>
                                        <option value="1" {{ old('active', 1) == 1 ? 'selected' : '' }}>表示</option>
                                    </select>
                                </div>

                                <!-- 公開範囲 -->
                                <div class="mb-3">
                                    <label for="flag" class="form-label">公開範囲</label>
                                    <select name="flag" id="flag" class="form-control">
                                        <option value="0" {{ old('flag', 0) == 0 ? 'selected' : '' }}>全員</option>
                                        <option value="1" {{ old('flag', 0) == 1 ? 'selected' : '' }}>ログインユーザー</option>
                                        <option value="2" {{ old('flag', 0) == 2 ? 'selected' : '' }}>限定公開</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 操作ボタン -->
                        <div class="card mt-4">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-plus"></i> ギャラリー作成
                                </button>
                                <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary w-100">
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
