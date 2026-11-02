<x-item-layout>


    <x-slot name="header">
     ランキング編集
    </x-slot>

    
    <div class="back">
        <a href="{{ route('item.ranks.index') }}" class="btn-primary">戻る</a>
    </div>



    <section class="list">
        <form action="{{ route('item.ranks.item_rank.update', $rank->id) }}" method="POST">
        @csrf
        @method('POST')
        <ul>
        @foreach($itemRanks as $itemRank)
        <li>
           <p class="rank">No. {{ $itemRank->no }}</p>
           <p>
            <select name="item_rank[{{ $itemRank->no }}]" class="item-select" data-rank-no="{{ $itemRank->no }}">
                <option value="">-- 選択 --</option>
                @foreach ($items as $item)
                <option value="{{ $item->id }}" data-thumbnail="{{ $item->th_flename() }}"  {{ $itemRank->item_id == $item->id ? 'selected' : '' }}>{{ $item->item_name }}</option>
                @endforeach
            </select>
            </p>
            <p class="img">
            <img src="" alt="サムネイル" id="thumbnail-{{ $itemRank->no }}" class="thumbnail d-none w_100px ml-2">
            </p>
            <p class="comment">
            <textarea name="text[{{ $itemRank->no }}]">{{ $itemRank->text }}</textarea>
            </p>
    
        </li>
        @endforeach
        </ul>
        <button type="submit" class="btn btn-primary center size_m">更新</button>
        </form>
    
    </section>


    <section class="contentArea">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="formArea">
    <form action="{{ route('item.ranks.update', $rank->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="active">表示</label>
            <select name="active" id="active">
                <option value="0" {{ $rank->active == 0 ? 'selected' : '' }}>非公開</option>
                <option value="1" {{ $rank->active == 1 ? 'selected' : '' }}>公開</option>
            </select>
        </div>

        <div class="form-group">
        @php
            $selectedCate = $rank->cate_id;
        @endphp
            <label for="cate_id">カテゴリ選択</label>
            <select name="cate_id" id="cate_id" class="form-control" required>
                <option value="" disabled {{ old('cate_id', $selectedCate ?? '') == '' ? 'selected' : '' }}>カテゴリを選択してください</option>
                @foreach($rankCates as $cate)
                    <option value="{{ $cate->id }}" {{ old('cate_id', $selectedCate ?? '') == $cate->id ? 'selected' : '' }}>
                        {{ $cate->title }}
                    </option>
                @endforeach
            </select>

        </div>
        <div class="form-group">
            <label for="title">ランキング名</label>
            <input type="text" name="title" id="title" class="form-control size_m" value="{{ $rank->title }}"  required>
        </div>
        <div class="form-group">
            <label for="body">説明文</label>
            <textarea name="body" class="form-control">{{ $rank->body }}</textarea>
        </div>
        <div class="form-group">
            <label for="position">表示</label>
            <select name="position" id="position">
                <option value="0" {{ $rank->position == 0 ? 'selected' : ''}}>全ポジション</option>
                <option value="1" {{ $rank->position == 1 ? 'selected' : ''}}>ポジション①のみ</option>
                <option value="2" {{ $rank->position == 2 ? 'selected' : ''}}>ポジション②のみ</option>
            </select>
        </div>

        <div id="date_fields">
            <div class="form-group">
                <label for="term_start">期間 [開始]</label>
                <input type="date" name="term_start" id="term_start" class="form-control" value="{{ $rank->term_start ? $rank->term_start->format('Y-m-d') : '' }}">
            </div>
            <div class="form-group">
                <label for="term_end">期間 [終了]</label>
                <input type="date" name="term_end" id="term_end" class="form-control" value="{{ $rank->term_end ? $rank->term_end->format('Y-m-d') : '' }}">
            </div>
        </div>

        <div class="form-group">
            <label for="filename">画像タイトルファイル名(xxxx.jpg / ●●●.png)</label>
            <input type="text" name="filename" id="filename" class="form-control" value="{{ $rank->filename }}">
        </div>

        <button type="submit" class="btn btn-primary"> 更新する </button>
    </form>
    </section>


</section>


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
                thumbnailImg.style.display = 'inline'; // 画像を表示
            } else {
                thumbnailImg.style.display = 'none'; // サムネイルを非表示
            }
        });

        // ページロード時にすでに選択されている値がある場合の処理
        const initialSelectedOption = select.options[select.selectedIndex];
        const initialThumbnailUrl = initialSelectedOption.getAttribute('data-thumbnail');
        const initialRankNo = select.getAttribute('data-rank-no');
        const initialThumbnailImg = document.getElementById('thumbnail-' + initialRankNo);

        if (initialThumbnailUrl) {
            initialThumbnailImg.src = initialThumbnailUrl;
            initialThumbnailImg.style.display = 'inline';
        }
    });
});


</script>


</x-item-layout>
