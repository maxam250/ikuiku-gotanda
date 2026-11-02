<x-item-layout>


<x-slot name="header">
ランキングカテゴリ編集
</x-slot>

<div class="back">
    <a href="{{ route('item.rank_cates.index') }}" class="btn-primary">戻る</a>
</div>


<section>
    <form action="{{ route('item.rank_cates.update', $rankCate->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <div class="form-group">
                <label for="title">カテゴリ名</label>
                <input type="text" name="title" class="form-control size_m" value="{{ $rankCate->title }}" required>
            </div>
            <div class="form-group">
                <label for="body">説明文</label>
                <textarea name="body" class="form-control">{{ $rankCate->body }}</textarea>
            </div>
        </div>
        <button type="submit" class="btn">更新</button>

        <!-- 戻るボタン -->
        <a href="{{ route('item.rank_cates.index') }}" class="btn btn-secondary">
            戻る
        </a>

</section>



</x-item-layout>
