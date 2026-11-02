<x-item-layout>


<x-slot name="header">
ギャラリー編集
</x-slot>

<div class="back">
    <a href="{{ route('item.gallery.index') }}" class="btn-primary">戻る</a>
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
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
</section>

{{-- 画像一覧・編集 --}}
<section id="uupdateArea">
    <h3>画像一覧・操作</h3>
    <ul id="sortable-images" class="list">
        @foreach ($gallery->images as $image)
            <li data-id="{{ $image->id }}" class="box">
                <div class="img">
                <img src="{{ asset($image->filename) }}" width="120">
                </div>
                <form action="{{ route('item.gallery.image.delete', $image->id) }}" method="POST">
                    <div class="button">
                    @csrf @method('DELETE')
                    <button class="btn">削除</button>
                </div>
                </form>
                <form action="{{ route('item.gallery.image.update', $image->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="caption">
                    <input type="text" name="caption" value="{{ old('caption', $image->caption) }}" placeholder="キャプション">
                    </div>
                    <div class="button">
                        <button class="btn">キャプション保存</button>
                    </div>
                </form>
            </li>
        @endforeach
    </ul>
</section>

{{-- 画像アップロード --}}
<section id="uploadArea">
    <h3>画像アップロード</h3>
    <form action="{{ route('item.gallery.image.upload', $gallery->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="images[]" multiple>
        <button class="btn">アップロード</button>
    </form>
</section>

{{-- ギャラリー情報編集 --}}
<section id="contentArea">
    <h3>ギャラリー情報編集</h3>
    <form action="{{ route('item.gallery.update', $gallery->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>表示</label>
            <select name="active">
                <option value="0" @selected($gallery->active == 0)>非表示</option>
                <option value="1" @selected($gallery->active == 1)>表示</option>
            </select>
        </div>


        <div class="form-group">
            <label>投稿者</label>
            <select name="member_id">
                <option value="0">会員選択</option>
            @foreach($members as $member)
                <option value="{{ $member->id}}" @selected($gallery->member_id == $member->id)>{{ $member->name }}</option>
            @endforeach
            </select>
        </div>


        <div class="form-group">
            <label>キャスト</label>
            <select name="item_id">
                <option value="0">キャスト選択</option>
            @foreach($items as $item)
                <option value="{{ $item->id}}" @selected($gallery->item_id == $item->id)>{{ $item->item_name }}</option>
            @endforeach
            </select>
        </div>


        <div class="form-group">
        <label>タイトル</label>
        <input type="text" name="title" value="{{ old('title', $gallery->title) }}" class="size_m">
        </div>

        <div class="form-group">
        <label>コメント</label>
        <textarea name="description">{{ old('description', $gallery->description) }}</textarea>
        </div>


        <div class="form-group">
        <label>公開範囲</label>
        <select name="flag">
            <option value="0" @selected($gallery->flag == 0)>全員</option>
            <option value="1" @selected($gallery->flag == 1)>ログインユーザー</option>
            <option value="2" @selected($gallery->flag == 2)>限定公開</option>
        </select>
        </div>
        <button class="btn">更新</button>
    </form>
</section>

{{-- 並び替えJS --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    new Sortable(document.getElementById('sortable-images'), {
        animation: 150,
        onEnd: function () {
            const order = Array.from(document.querySelectorAll('#sortable-images li')).map((el, index) => ({
                id: el.dataset.id,
                sort: index + 1
            }));
            fetch('{{ route("item.gallery.image.sort", $gallery->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order })
            });
        }
    });
</script>
</x-item-layout>
