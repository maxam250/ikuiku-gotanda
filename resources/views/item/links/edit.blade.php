<x-item-layout>


<x-slot name="header">
リンク編集
</x-slot>


<div class="back">
    <a href="{{ route('item.links.index') }}" class="btn-primary">戻る</a>
</div>

<section class="contentArea">

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

    <div class="hintArea">
        <p>フルコードを登録するとアップロードしたバナー画像は表示されず優先して表示されます。</p>
    </div>
    </section>


    <section>

        <form action="{{ route('item.links.update', $link->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>表示画像</label>
                @if($link->fullcode)
                    {!! $link->fullcode ? $link->fullcode : '' !!}
                @elseif($link->filename)
                    <a href="{{ $link->link }}" target="_blank"><img src="{{ asset('uploads/link/' . $link->filename) }}" alt="{{ $link->name }}"></a>
                @endif
            </div>

            <div class="form-group">
                <label for="active">表示</label>
                <input type="checkbox" name="active" value="1" {{ old('active', $link->active ?? 1) == 0 ? '' : 'checked' }}>
            </div>

            <div class="form-group form_parts">
                <label for="position">サイズ</label>

                <input type="radio" name="position" value="0" {{ old('position', $link->position ?? '') == '0' ? 'checked' : '' }} id="size_0">&nbsp;<label class="radio" for="size_0">フリー最上段</label>&nbsp;
                <input type="radio" name="position" value="1" {{ old('position', $link->position ?? '') == '1' ? 'checked' : '' }} id="size_1">&nbsp;<label class="radio" for="size_1">468x60</label>&nbsp;
                <input type="radio" name="position" value="2" {{ old('position', $link->position ?? '') == '2' ? 'checked' : '' }} id="size_2">&nbsp;<label class="radio" for="size_2">200x40</label>&nbsp;
                <input type="radio" name="position" value="3" {{ old('position', $link->position ?? '') == '3' ? 'checked' : '' }} id="size_3">&nbsp;<label class="radio" for="size_3">88x31</label>&nbsp;
                <input type="radio" name="position" value="4" {{ old('position', $link->position ?? '') == '4' ? 'checked' : '' }} id="size_4">&nbsp;<label class="radio" for="size_4">テキスト</label>&nbsp;
                <input type="radio" name="position" value="5" {{ old('position', $link->position ?? '') == '5' ? 'checked' : '' }} id="size_5">&nbsp;<label class="radio" for="size_5">フリー最下段</label>
            </div>
            <div class="form-group">
                <label for="title">タイトル</label>
                <input type="text" name="title" class="form-control size_m" value="{{ $link->title }}" required>
            </div>
            <div class="form-group">
                <label for="editor">フルコード</label>
                <textarea onKeyDown="textareaResize(event)" name="fullcode" class="form-control">{{ old('fullcode', $link->fullcode ?? '') }}</textarea>
            </div>

            <div class="form-group flex-wrap">
                <label for="filename">画像</label>
                <input type="file" name="filename" class="form-control-file">
                @if($link->filename)
                <div class="ml-4">
                    <img src="{{ asset('uploads/link/' . $link->filename) }}" alt="" width="200">
                </div>
                @endif
            <!-- 画像削除チェックボックス -->
            @if ($link->filename)
            <div class="ml-4">
                <input type="checkbox" name="delete_image" id="delete_image" value="1">
                <label for="delete_image">現在の画像を削除する</label>
            </div>
            @endif
            </div>
            <div class="form-group">
                <label for="link">リンク</label>
                <input type="text" name="link" class="form-control size_m" value="{{ $link->link }}">
            </div>
            <button type="submit" class="af-btn af-btn-primary">更新</button>
        </form>

    </section>

</section>

</x-item-layout>
