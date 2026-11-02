
<x-item-layout>


<x-slot name="header">
ギャラリー登録
</x-slot>


<div class="back">
    <a href="{{ route('item.gallery.index') }}" class="btn-primary">戻る</a>
</div>

<section>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('item.gallery.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="active">表示</label>
            <select name="active" id="active">
                <option value="0">表示なし</option>
                <option value="1">表示</option>
            </select>
        </div>
        <div class="form-group">
            <label for="item">投稿者</label>
            <select name="item_id" id="item_id" class="form-control size_s">
                <option value="0" selected>なし</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}"{{ old('member_id' ?? '') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="title">タイトル</label>
            <input type="text" name="title" class="form-control size_m" required>
        </div>
        <div class="form-group">
            <label for="item">キャスト</label>
            <select name="item_id" id="item_id" class="form-control size_s">
                <option value="0" selected>なし</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}"{{ old('item_id' ?? '') == $item->id ? 'selected' : '' }}>{{ $item->item_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
                <label for="editor">コメント</label>
                <textarea onKeyDown="textareaResize(event)" id="editor"  name="descriptioin" class="form-control">{{ old('descriptioin', $gallery->descriptioin ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label for="flag">公開範囲</label>
            <select name="flag" id="active">
                <option value="0">全員</option>
                <option value="1">ログインユーザー</option>
                <option value="2">限定公開</option>
            </select>
        </div>


        <button type="submit" class="btn size_m">ギャラリー作成</button>
    </form>
</section>



</x-item-layout>
