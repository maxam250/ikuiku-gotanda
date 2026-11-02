<x-item-layout>


<x-slot name="header">
設備カテゴリ編集
</x-slot>


<div class="back">
    <a href="{{ route('item.equipment_cates.index') }}" class="btn-primary">戻る</a>
</div>

<section class="contentArea">
    <form action="{{ route('item.equipment_cates.update', $equipmentCate->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <div class="form-group">
                <label for="active">表示</label>
                <select name="active" id="active">
                    <option value="0" {{ $equipmentCate->active == 0 ? 'selected' : '' }}>表示なし</option>
                    <option value="1" {{ $equipmentCate->active == 1 ? 'selected' : '' }}>表示</option>
                </select>
            </div>
            <div class="form-group">
                <label for="title">名称</label>
                <input type="text" name="title" class="form-control size_m" value="{{ $equipmentCate->title }}" required>
            </div>
            <div class="form-group">
                <label for="text">説明文</label>
                <textarea name="text" class="form-control">{{ $equipmentCate->text }}</textarea>
            </div>
            <div class="form-group">
                <label for="link">リンク先</label>
                <input type="text" name="link" class="form-control size_m" value="{{ $equipmentCate->link }}">
            </div>
            <div class="form-group">
                <label for="flag">フラグ</label>
                <div>
                <input type="checkbox" name="flag" id="flrag" class="form-control" value="1" {{ $equipmentCate->flag == 1 ? 'checked' : '' }}></div>
            </div>
        </div>
        <button type="submit" class="btn"> 更新する </button>


</section>



</x-item-layout>
