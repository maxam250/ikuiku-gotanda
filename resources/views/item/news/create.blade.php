
<x-item-layout>


<x-slot name="header">
ニュース登録
</x-slot>


<div class="back">
    <a href="{{ route('item.news.index') }}" class="btn-primary">戻る</a>
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

    <form action="{{ route('item.news.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="active">表示</label>
            <select name="active" id="active">
                <option value="0">表示なし</option>
                <option value="1">表示</option>
            </select>
        </div>
        <div class="form-group">
            <label for="title">タイトル</label>
            <input type="text" name="title" class="form-control size_m" required>
        </div>
        <div class="form-group">
                <label for="editor">内容</label>
                <textarea onKeyDown="textareaResize(event)" id="editor"  name="body" class="form-control">{{ old('body', $news->body ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label for="filename">画像</label>
            <input type="file" name="filename" class="form-control-file">
        </div>
        <div class="form-group">
            <label for="link">リンク</label>
            <input type="text" name="link" class="form-control size_m" placeholder="リンク先のURL">
        </div>
        <div class="form-group">
            <label for="timer_flag">タイマーフラグ</label>
            <input type="checkbox" name="timer_flag" id="timer_flag"  value="1">
            &emsp;<span class="hint">※期間を指定して公開したい場合にチェックを入れて日時をセットしてください。</span>
        </div>

        <!-- タイマーフラグがチェックされたら表示される日時フィールド -->
        <div id="date_fields" class="d-none">
            <div class="form-group">
                <label for="date_start">掲載開始日時</label>
                <input type="datetime-local" name="date_start" id="date_start" class="form-control">
            </div>
            <div class="form-group">
                <label for="date_end">掲載終了日時</label>
                <input type="datetime-local" name="date_end" id="date_end" class="form-control">
            </div>
        </div>

        <button type="submit" class="af-btn af-btn-primary size_m">ニュース記事作成</button>
    </form>
</section>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        var timerFlagCheckbox = document.getElementById('timer_flag');
        var dateFields = document.getElementById('date_fields');

        // チェックボックスの状態に応じて表示・非表示を切り替え
        timerFlagCheckbox.addEventListener('change', function () {
            if (timerFlagCheckbox.checked) {
                dateFields.classList.remove('d-none');
            } else {
                dateFields.classList.add('d-none');
            }
        });
    });
</script>


</x-item-layout>
