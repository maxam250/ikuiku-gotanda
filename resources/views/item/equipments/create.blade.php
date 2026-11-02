
<x-item-layout>


<x-slot name="header">
設備追加
</x-slot>


<div class="back">
    <a href="{{ route('item.equipments.index') }}" class="btn-primary">戻る</a>
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
    <h3>リスト登録</h3>
        <form method="post" action="{{ route('item.equipments.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">

                <label for="cate_id">カテゴリ</label>
                <select name="cate_id" id="cate_id" class="form-control" required>
                    <option value="" disabled selected>カテゴリを選択してください</option>
                    @foreach($equipmentCates as $cate)
                        <option value="{{ $cate->id }}">{{ $cate->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="title">名称</label>
                <input type="text" name="title" id="title" class="form-control size_m" required>
            </div>
            <div class="form-group">
                <label for="cate">項目1</label>
                <input type="text" name="text1" id="text1" class="form-control size_m">
            </div>
            <div class="form-group">
                <label for="text2">項目2</label>
                <input type="text" name="text2" id="text2" class="form-control size_m">
            </div>
            <div class="form-group">
                <label for="text3">項目3</label>
                <input type="text" name="text3" id="text3" class="form-control size_m">
            </div>
            <div class="form-group">
                <label for="text4">項目4</label>
                <input type="text" name="text4" id="text4" class="form-control size_m">
            </div>
            <div class="form-group">
                <label for="text5">項目5</label>
                <input type="text" name="text5" id="text5" class="form-control size_m">
            </div>
            <div class="form-group">
                <label for="flag1">フラグ1</label>
                <div class="unit">
                <input type="checkbox" name="flag1" id="flag1" class="form-control" value="1">
                </div>
            </div>
            <div class="form-group">
                <label for="flag2">フラグ2</label>
                <div class="unit">
                <input type="checkbox" name="flag2" id="flag2" class="form-control" value="1">
                </div>
            </div>
            <div class="form-group">
                <label for="flag3">フラグ3</label>
                <div class="unit">
                <input type="checkbox" name="flag3" id="flag3" class="form-control" value="1">
                </div>
            </div>
            <div class="form-group">
                <label for="flag4">フラグ4</label>
                <div class="unit">
                <input type="checkbox" name="flag4" id="flag4" class="form-control" value="1">
                </div>
            </div>
            <div class="form-group">
                <label for="flag5">フラグ5</label>
                <div class="unit">
                <input type="checkbox" name="flag5" id="flag5" class="form-control" value="1">
                </div>
            </div>
            <div class="form-group">
                <label for="filename">画像</label>
                <input type="file" name="filename" class="form-control-file">
            </div>
            <div class="form-group">
                <label for="link">リンク</label>
                <input type="text" name="link" id="link" class="form-control size_m">
            </div>
            <button type="submit" class="btn btn-primary">登録</button>

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
