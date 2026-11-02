<x-admin-layout>


<x-slot name="header">
 コスカテゴリ編集
</x-slot>


<div class="back">
    <a href="{{ route('admin.costume_cates.index') }}" class="btn-primary">戻る</a>
</div>

<section class="MessageArea">
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
</section>

<section>
    <form action="{{ route('admin.costume_cates.update', $costumeCate->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <div class="form-group">
                <label for="active">表示</label>
                <select name="active" id="active">
                    <option value="0" {{ $costumeCate->active == 0 ? 'selected' : '' }}>表示なし</option>
                    <option value="1" {{ $costumeCate->active == 1 ? 'selected' : '' }}>表示</option>
                </select>
            </div>
            <div class="form-group">
                <label for="title">名称</label>
                <input type="text" name="title" class="form-control" value="{{ $costumeCate->title }}" required>
            </div>
            <div class="form-group">
                <label for="text">説明文</label>
                <textarea name="text" class="form-control">{{ $costumeCate->text }}</textarea>
            </div>
            <div class="form-group">
                <label for="price">料金</label>
                <input type="text" name="price" class="form-control size_s" value="{{ $costumeCate->price }}">円
            </div>
            <div class="form-group">
                <label for="flag">フラグ</label>
                <input type="checkbox" name="flag" value="1" {{ $costumeCate->flag == 1 ? 'checked' : '' }}>
            </div>
        </div>
        <button type="submit" class="btn">更新</button>

        <!-- 戻るボタン -->
        <a href="{{ route('admin.costume_cates.index') }}" class="btn btn-secondary">
            戻る
        </a>

</section>



</x-admin-layout>
