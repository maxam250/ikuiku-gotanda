<x-item-layout>


    <x-slot name="header">
     イベント編集
    </x-slot>

    <div class="back">
        <a href="{{ route('item.events.index') }}" class="btn-primary">戻る</a>
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
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    </section>

    <section>
    <form action="{{ route('item.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="position">表示位置</label>
            <select name="position" id="position">
                <option value="0" {{ $event->position == 0 ? 'selected' : '' }}>表示なし</option>
                <option value="1" {{ $event->position == 1 ? 'selected' : '' }}>TOP&amp;イベント</option>
                <option value="2" {{ $event->position == 2 ? 'selected' : '' }}>TOPのみ</option>
                <option value="3" {{ $event->position == 3 ? 'selected' : '' }}>イベントのみ</option>
            </select>
        </div>
        <div class="form-group">
            <label for="title">タイトル</label>
            <input type="text" name="title" class="form-control size_m" value="{{ $event->title }}" required>
        </div>
        <div class="form-group">
            <label for="body">内容</label>
            <textarea name="body" id="editor" class="form-control">{{ $event->body }}</textarea>
        </div>
        <div class="form-group flex-wrap">
            <label for="filename">画像</label>
            <input type="file" name="filename" class="form-control-file">
            @if($event->filename)
            <div class="ml-4">
                <img src="{{ Storage::url('events/' . $event->filename) }}" alt="イベント画像" width="200">
            </div>
            @endif
        <!-- 画像削除チェックボックス -->
        @if ($event->filename)
        <div class="ml-4">
            <input type="checkbox" name="delete_image" id="delete_image" value="1">
            <label for="delete_image">現在の画像を削除する</label>
        </div>
        @endif
        </div>
        <div class="form-group">
        @if($event->filename)
        <label for="fileurl">画像URL(コピペ用)</label>
        <input type="text" name="image_url" value="{{ Storage::url('events/' . $event->filename) }}" readonly>
        @endif
        </div>
        <div class="form-group">
            <label for="link">リンク</label>
            <input type="text" name="link" class="form-control size_m" value="{{ $event->link }}" placeholder="リンク先のURL">
        </div>
        <div class="form-group">
            <label for="timer_flag">タイマーフラグ</label>
            <input type="checkbox" name="timer_flag" id="timer_flag" value="1"
            {{ $event->timer_flag == 1 ? 'checked' : '' }}>

            &emsp;<span class="hint">※期間を指定して公開したい場合にチェックを入れて日時をセットしてください。</span>
        </div>

        <!-- タイマーフラグがチェックされたら表示される日時フィールド -->
        <div id="data_fields" class="{{ $event->timer_flag ? '' : 'd-none' }}">
            <div class="form-group">
                <label for="date_start">掲載開始日時</label>
                <input type="datetime-local" name="date_start" id="date_start" class="form-control" value="{{ $event->date_start }}">
            </div>
            <div class="form-group">
                <label for="date_end">掲載終了日時</label>
                <input type="datetime-local" name="date_end" id="date_end" class="form-control" value="{{ $event->date_end }}">
            </div>
        </div>
        <button type="submit" class="af-btn af-btn-primary size_m">更新</button>
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
