<x-admin-layout>


<x-slot name="header">
     オプションカテゴリ編集
</x-slot>



<section>
    <form action="{{ route('admin.option_cates.update', $optionCate->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <div class="form-group">
                <label for="title">カテゴリ名</label>
                <input type="text" name="title" class="form-control" value="{{ $optionCate->title }}" required>
            </div>
            <div class="form-group">
                <label for="body">説明文</label>
                <textarea name="body" class="form-control">{{ $optionCate->body }}</textarea>
            </div>
        </div>
        <button type="submit">更新</button>

        <!-- 戻るボタン -->
        <a href="{{ route('admin.option_cates.index') }}" class="btn btn-secondary">
            戻る
        </a>

</section>



</x-admin-layout>
