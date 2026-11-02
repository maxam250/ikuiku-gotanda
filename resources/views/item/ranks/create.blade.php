
<x-item-layout>


<x-slot name="header">
ランキング登録
</x-slot>


    <div class="back">
        <a href="{{ route('item.ranks.index') }}" class="btn-primary">戻る</a>
    </div>

    @if($errors->any())
    <div class="MessageArea">
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

<section class="contentArea">
    
    <section class="formArea">
        <h3>ランキング登録</h3>
        <form method="post" action="{{ route('item.ranks.store') }}">
            @csrf

            <div class="form-group">
                <label for="num">ランキングの表示数</label>
                <select name="num" id="num">
                @for($i=1; $i <= 10; $i++ )
                    <option value="{{ $i }}">{{ $i }}位</option>
                @endfor
                </select>まで
            </div>

            @if (count($rankCates) > 0)
            <div class="form-group">
                <label for="title">カテゴリ</label>
                <select name="cate_id" id="cate_id" class="form-control" required>
                    <option value="" disabled selected>カテゴリを選択してください</option>
                    @foreach($rankCates as $cate)
                        <option value="{{ $cate->id }}">{{ $cate->title }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="form-group">
                <label for="title">ランキング名</label>
                <input type="text" name="title" id="title" class="form-control size_m" required>
            </div>
            <div class="form-group">
                <label for="body">説明文</label>
                <textarea name="body" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label for="position">表示</label>
                <select name="position" id="position">
                    <option value="0">全ポジション</option>
                    <option value="1">ポジション①のみ</option>
                    <option value="2">ポジション②のみ</option>
                </select>
            </div>

            <div id="date_fields">
                <div class="form-group">
                    <label for="term_start">期間 [開始]</label>
                    <input type="date" name="term_start" id="term_start" class="form-control">
                </div>
                <div class="form-group">
                    <label for="term_end">期間 [終了]</label>
                    <input type="date" name="term_end" id="term_end" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="filename">画像タイトルファイル名(xxxx.jpg / ●●●.png)</label>
                <input type="text" name="filename" id="filename" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">登録</button>

        </form>
    </section>

</section>
</x-item-layout>
