
<x-item-layout>


<x-slot name="header">
        リンク管理
</x-slot>

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

    <form action="{{ route('item.courses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="active">表示</label>
            <input type="checkbox" name="active" value="1" {{ old('active', $links->active ?? 1) == 0 ? '' : 'checked' }}>
        </div>

        <div class="form-group">
        <label for="position">サイズ</label>

        <label><input type="radio" name="position" value="0" {{ old('position', $links->position ?? 0) == 0 ? 'checked' : '' }}>&nbsp;フリー最上段</label>&emsp;
        <label><input type="radio" name="position" value="1" {{ old('position', $links->position ?? 1) == 0 ? 'checked' : '' }}>&nbsp;468x60</label>&emsp;
        <label><input type="radio" name="position" value="2" {{ old('position', $links->position ?? 2) == 0 ? 'checked' : '' }}>&nbsp;200x40</label>&emsp;
        <label> <input type="radio" name="position" value="3" {{ old('position', $links->position ?? 3) == 0 ? 'checked' : '' }}>&nbsp;88x31</label>&emsp;
        <label><input type="radio" name="position" value="4" {{ old('position', $links->position ?? 4) == 0 ? 'checked' : '' }}>&nbsp;テキスト</label>&emsp;
        <label> <input type="radio" name="position" value="5" {{ old('position', $links->position ?? 5) == 0 ? 'checked' : '' }}>&nbsp;フリー最下段</label>

        </div>
        <div class="form-group">
            <label for="title">サイト名</label>
            <input type="text" name="title" class="form-control" required value="{{ old('link', $links->link ?? '') }}">
        </div>
        <div class="form-group">
                <label for="editor">フルコード</label>
                <textarea onKeyDown="textareaResize(event)"  name="fullcode" class="form-control">{{ old('fullcode', $links->fullcode ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label for="link">リンク</label>
            <input type="text" name="link" class="form-control" value="{{ old('link', $links->link ?? '') }}">
        </div>
        <div class="form-group">
            <label for="filename">バナー画像</label>
            <input type="file" name="filename" class="form-control-file">
        </div>

        <button type="submit" class="btn btn-primary">リンク登録</button>
    </form>
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
