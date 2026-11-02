<x-item-layout>


    <x-slot name="header">
        設備管理画面
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

    <form action="{{ route('item.equipments.update', $equipment->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
@php
    $selectedCate = $equipment->cate_id;
@endphp
            <select name="cate_id" id="cate_id" class="form-control" required>
                <option value="" disabled {{ old('cate_id', $selectedCate ?? '') == '' ? 'selected' : '' }}>カテゴリを選択してください</option>
                @foreach($equipmentCates as $cate)
                    <option value="{{ $cate->id }}" {{ old('cate_id', $selectedCate ?? '') == $cate->id ? 'selected' : '' }}>
                        {{ $cate->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="title">名称</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $equipment->title }}" required>
        </div>
        <div class="form-group">
            <label for="text1">text1</label>
            <input type="text" name="text1" id="text1" class="form-control" value="{{ $equipment->text1 }}" >
        </div>
        <div class="form-group">
            <label for="text2">text2</label>
            <input type="text" name="text2" id="text2" class="form-control" value="{{ $equipment->text2 }}" >
        </div>
        <div class="form-group">
            <label for="text3">text3</label>
            <input type="text" name="text3" id="text3" class="form-control" value="{{ $equipment->text3 }}" >
        </div>
        <div class="form-group">
            <label for="text4">text4</label>
            <input type="text" name="text4" id="text4" class="form-control" value="{{ $equipment->text4 }}" >
        </div>
        <div class="form-group">
            <label for="text5">text5</label>
            <input type="text" name="text5" id="text5" class="form-control" value="{{ $equipment->text5 }}" >
        </div>
        <div class="form-group">
            <label for="flag">フラグ1
            <input type="checkbox" name="flag1" class="form-control" value="1" {{ $equipment->flag1 == 1 ? 'checked' : '' }}></label>
        </div>
        <div class="form-group">
            <label for="flag">フラグ2
            <input type="checkbox" name="flag2" class="form-control" value="1" {{ $equipment->flag2 == 1 ? 'checked' : '' }}></label>
        </div>
        <div class="form-group">
            <label for="flag">フラグ3
            <input type="checkbox" name="flag3" class="form-control" value="1" {{ $equipment->flag3 == 1 ? 'checked' : '' }}></label>
        </div>
        <div class="form-group">
            <label for="flag">フラグ4
            <input type="checkbox" name="flag4" class="form-control" value="1" {{ $equipment->flag4 == 1 ? 'checked' : '' }}></label>
        </div>
        <div class="form-group">
            <label for="flag">フラグ5
            <input type="checkbox" name="flag5" class="form-control" value="1" {{ $equipment->flag5 == 1 ? 'checked' : '' }}></label>
        </div>
        <div class="form-group">
            <label for="filename">画像</label>
            <input type="file" name="filename" class="form-control-file">
            @if($equipment->filename)
                <img src="{{ asset('uploads/equipment/' . $equipment->filename) }}" alt="" width="100">
            @endif
        </div>
        <!-- 画像削除チェックボックス -->
        @if ($equipment->filename)
        <div>
            <input type="checkbox" name="delete_image" id="delete_image" value="1">
            <label for="delete_image">現在の画像を削除する</label>
        </div>
        @endif
        <div class="form-group">
            <label for="link">link</label>
            <input type="text" name="link" id="link" class="form-control" value="{{ $equipment->link }}" >
        </div>

        <button type="submit" class="btn btn-primary"> 更新する </button>
    </form>

</section>



</x-item-layout>
