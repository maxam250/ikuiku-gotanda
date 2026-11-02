<x-admin-layout>

    <x-slot name="header">
    ランキング編集
    </x-slot>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>ランキング編集</h1>
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

            <div class="row">
                <div class="col-lg-8">
                    <!-- ランキングアイテム編集 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">ランキングアイテム編集</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.ranks.item_rank.update', $rank->id) }}" method="POST">
                                @csrf
                                @method('POST')
                                <ul class="list-unstyled" style="display:grid; grid-template-columns:repeat(5,1fr); gap:0.75rem;">
                                    @foreach($itemRanks as $itemRank)
                                    <li class="border p-2" style="min-width:0;">
                                        <p class="mb-1"><strong>No. {{ $itemRank->no }}</strong></p>
                                        <img src="" alt="サムネイル" id="thumbnail-{{ $itemRank->no }}" class="thumbnail mb-1 d-block" style="display:none !important; width:100%; max-height:80px; object-fit:cover;">
                                        <select name="item_rank[{{ $itemRank->no }}]" class="form-control form-control-sm item-select mb-1" data-rank-no="{{ $itemRank->no }}">
                                            <option value="">-- 選択 --</option>
                                            @foreach ($items as $item)
                                            <option value="{{ $item->id }}" data-thumbnail="{{ $item->th_flename() }}" {{ $itemRank->item_id == $item->id ? 'selected' : '' }}>{{ $item->item_name }}</option>
                                            @endforeach
                                        </select>
                                        <textarea name="text[{{ $itemRank->no }}]" class="form-control form-control-sm" rows="2" placeholder="コメント">{{ $itemRank->text }}</textarea>
                                    </li>
                                    @endforeach
                                </ul>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> ランキングアイテムを更新
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- ランキング情報編集 -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">ランキング情報編集</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.ranks.update', $rank->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- ランキング名 -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">ランキング名 <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $rank->title) }}" required>
                                </div>

                                <!-- カテゴリ -->
                                @php
                                    $selectedCate = $rank->cate_id;
                                @endphp
                                <div class="mb-3">
                                    <label for="cate_id" class="form-label">カテゴリ選択 <span class="text-danger">*</span></label>
                                    <select name="cate_id" id="cate_id" class="form-control" required>
                                        <option value="" disabled {{ old('cate_id', $selectedCate ?? '') == '' ? 'selected' : '' }}>カテゴリを選択してください</option>
                                        @foreach($rankCates as $cate)
                                            <option value="{{ $cate->id }}" {{ old('cate_id', $selectedCate ?? '') == $cate->id ? 'selected' : '' }}>
                                                {{ $cate->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 説明文 -->
                                <div class="mb-3">
                                    <label for="body" class="form-label">説明文</label>
                                    <textarea name="body" id="body" class="form-control" rows="5">{{ old('body', $rank->body) }}</textarea>
                                </div>

                                <!-- 画像タイトルファイル名 -->
                                <div class="mb-3">
                                    <label for="filename" class="form-label">画像タイトルファイル名</label>
                                    <input type="text" name="filename" id="filename" class="form-control" value="{{ old('filename', $rank->filename) }}" placeholder="xxxx.jpg / ●●●.png">
                                    <div class="form-text">画像タイトルファイル名を入力してください</div>
                                </div>

                                <!-- 送信ボタン -->
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> ランキング情報を更新
                                    </button>
                                </div>
                            </form>
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
                            <form action="{{ route('admin.ranks.update', $rank->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="title" value="{{ $rank->title }}">
                                <input type="hidden" name="cate_id" value="{{ $rank->cate_id }}">
                                <input type="hidden" name="body" value="{{ $rank->body }}">
                                <input type="hidden" name="filename" value="{{ $rank->filename }}">

                                <!-- 公開設定 -->
                                <div class="mb-3">
                                    <label for="active" class="form-label">公開設定</label>
                                    <select name="active" id="active" class="form-control">
                                        <option value="0" {{ old('active', $rank->active) == 0 ? 'selected' : '' }}>非公開</option>
                                        <option value="1" {{ old('active', $rank->active) == 1 ? 'selected' : '' }}>公開</option>
                                    </select>
                                </div>

                                <!-- 表示ポジション -->
                                <div class="mb-3">
                                    <label for="position" class="form-label">表示</label>
                                    <select name="position" id="position" class="form-control">
                                        <option value="0" {{ old('position', $rank->position) == 0 ? 'selected' : '' }}>全ポジション</option>
                                        <option value="1" {{ old('position', $rank->position) == 1 ? 'selected' : '' }}>ポジション①のみ</option>
                                        <option value="2" {{ old('position', $rank->position) == 2 ? 'selected' : '' }}>ポジション②のみ</option>
                                    </select>
                                </div>

                                <!-- 期間 -->
                                <div class="mb-3">
                                    <label for="term_start" class="form-label">期間 [開始]</label>
                                    <input type="date" name="term_start" id="term_start" class="form-control" value="{{ old('term_start', $rank->term_start ? $rank->term_start->format('Y-m-d') : '') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="term_end" class="form-label">期間 [終了]</label>
                                    <input type="date" name="term_end" id="term_end" class="form-control" value="{{ old('term_end', $rank->term_end ? $rank->term_end->format('Y-m-d') : '') }}">
                                </div>

                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-save"></i> 設定を更新
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // すべてのアイテムセレクトボックスに対してイベントリスナーを設定
    document.querySelectorAll('.item-select').forEach(function(select) {
        select.addEventListener('change', function() {
            // 現在選択されているオプションのdata-thumbnail属性を取得
            const selectedOption = this.options[this.selectedIndex];
            const thumbnailUrl = selectedOption.getAttribute('data-thumbnail');
            const rankNo = this.getAttribute('data-rank-no');
            const thumbnailImg = document.getElementById('thumbnail-' + rankNo);

            // サムネイルURLがある場合は画像を表示し、ない場合は非表示に
            if (thumbnailUrl) {
                thumbnailImg.src = thumbnailUrl;
                thumbnailImg.style.setProperty('display', 'block', 'important');
            } else {
                thumbnailImg.style.setProperty('display', 'none', 'important');
            }
        });

        // ページロード時にすでに選択されている値がある場合の処理
        const initialSelectedOption = select.options[select.selectedIndex];
        const initialThumbnailUrl = initialSelectedOption.getAttribute('data-thumbnail');
        const initialRankNo = select.getAttribute('data-rank-no');
        const initialThumbnailImg = document.getElementById('thumbnail-' + initialRankNo);

        if (initialThumbnailUrl) {
            initialThumbnailImg.src = initialThumbnailUrl;
            initialThumbnailImg.style.setProperty('display', 'block', 'important');
        }
    });

    // カテゴリ選択が変更されたときに、表示設定フォームのhiddenフィールドも更新
    const cateSelect = document.getElementById('cate_id');
    if (cateSelect) {
        cateSelect.addEventListener('change', function() {
            const selectedCateId = this.value;
            const displayFormCateInput = document.querySelector('form[action*="update"] input[name="cate_id"][type="hidden"]');
            if (displayFormCateInput) {
                displayFormCateInput.value = selectedCateId;
            }
        });
    }
});
</script>
@endpush

</x-admin-layout>
