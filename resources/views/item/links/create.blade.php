
<x-item-layout>


<x-slot name="header">
リンク追加
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
            <p>フルコードを登録するとアップロードしたバナー画像は表示されずコードのバナーが優先して表示されます。</p>
        </div>
    </section>

    <section>
        <form action="{{ route('item.links.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="active">表示</label>
                <input type="checkbox" name="active" value="1" {{ old('active', $links->active ?? 1) == 0 ? '' : 'checked' }}>
            </div>

            <div class="form-group form_parts">
            <label for="position">サイズ</label>
                <div class="content">
                <input type="radio" name="position" value="0" {{ old('position', $link->position ?? '') == '0' ? 'checked' : '' }} id="size_0">&nbsp;<label class="radio" for="size_0">フリー最上段</label>&nbsp;
                <input type="radio" name="position" value="1" {{ old('position', $link->position ?? '') == '1' ? 'checked' : '' }} id="size_1">&nbsp;<label class="radio" for="size_1">468x60</label>&nbsp;
                <input type="radio" name="position" value="2" {{ old('position', $link->position ?? '') == '2' ? 'checked' : '' }} id="size_2">&nbsp;<label class="radio" for="size_2">200x40</label>&nbsp;
                <input type="radio" name="position" value="3" {{ old('position', $link->position ?? '') == '3' ? 'checked' : '' }} id="size_3">&nbsp;<label class="radio" for="size_3">88x31</label>&nbsp;
                <input type="radio" name="position" value="4" {{ old('position', $link->position ?? '') == '4' ? 'checked' : '' }} id="size_4">&nbsp;<label class="radio" for="size_4">テキスト</label>&nbsp;
                <input type="radio" name="position" value="5" {{ old('position', $link->position ?? '') == '5' ? 'checked' : '' }} id="size_5">&nbsp;<label class="radio" for="size_5">フリー最下段</label>
                </div>
            </div>
            <div class="form-group">
                <label for="title">サイト名</label>
                <input type="text" name="title" class="form-control size_m" required value="{{ old('link', $links->link ?? '') }}">
            </div>
            <div class="form-group">
                <label for="editor">フルコード</label>
                <textarea onKeyDown="textareaResize(event)"  name="fullcode" class="form-control">{{ old('fullcode', $links->fullcode ?? '') }}</textarea>
            </div>
            <div class="form-group">
                <label for="link">リンク</label>
                <input type="text" name="link" class="form-control size_m" value="{{ old('link', $links->link ?? '') }}">
            </div>
            <div class="form-group">
                <label for="filename">バナー画像</label>
                <input type="file" name="filename" class="form-control-file">
            </div>

            <button type="submit" class="btn btn-primary size_m">リンク登録</button>
        </form>
    </section>

</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var timerFlagCheckbox = document.getElementById('timer_flag');
        var dateFields = document.getElementById('date_fields');

        // チェックボックスの状態に応じて表示・非表示を切り替え
        timerFlagCheckbox.addEventListener('change', function () {
            if (timerFlagCheckbox.checked) {
                dateFields.style.display = 'block';
            } else {
                dateFields.style.display = 'none';
            }
        });
    });
</script>


</x-item-layout>
