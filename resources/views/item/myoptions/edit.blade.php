<x-item-layout>

<x-slot name="header">マイオプション編集</x-slot>

<div class="back">
    <a href="{{ route('item.myoptions.index') }}" class="af-btn af-btn-cancel af-btn-sm">戻る</a>
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
    <form action="{{ route('item.myoptions.update', $myoption->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <table class="form-table">
            <tr>
                <th>表示設定</th>
                <td>
                    <select name="active">
                        <option value="1" @selected($myoption->active == 1)>表示</option>
                        <option value="0" @selected($myoption->active == 0)>非表示</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th>カテゴリ</th>
                <td>
                    @foreach($myoptionCates as $cate)
                        <label class="radio-label">
                            <input type="radio" name="myoption_cate_id" value="{{ $cate->id }}"
                                   @checked($myoption->myoption_cate_id == $cate->id)>
                            {{ $cate->name }}
                        </label>
                    @endforeach
                </td>
            </tr>

            <tr>
                <th>マイオプ名 <span class="required">※</span></th>
                <td>
                    <input type="text" name="name" value="{{ old('name', $myoption->name) }}" class="input-m" required>
                </td>
            </tr>

            <tr>
                <th>説明文</th>
                <td>
                    <textarea name="body" class="textarea-m" rows="5">{{ old('body', $myoption->body) }}</textarea>
                </td>
            </tr>

            <tr>
                <th>料金</th>
                <td>
                    <input type="text" name="price" value="{{ old('price', $myoption->price) }}" class="input-s size_s"> 円
                </td>
            </tr>

            <tr>
                <th>画像</th>
                <td>
                    @if($myoption->filename)
                        <div class="current-image">
                            <img src="{{ Storage::url('myoption/' . $myoption->filename) }}" alt="" class="thumb">
                            <label class="delete-check">
                                <input type="checkbox" name="delete_image" value="1">
                                現在の画像を削除する
                            </label>
                        </div>
                    @endif
                    <input type="file" name="filename" accept="image/*">
                </td>
            </tr>

            <tr>
                <td colspan="2" class="form-action">
                    <button type="submit" class="af-btn af-btn-primary">
                        <i class="fas fa-save"></i> 更新
                    </button>
                </td>
            </tr>
        </table>
    </form>
</section>

<style>
.form-table { width: 100%; border-collapse: collapse; }
.form-table th,
.form-table td { padding: 10px 12px; border: 1px solid #ddd; vertical-align: middle; }
.form-table th { background: #f5f5f5; width: 140px; font-weight: normal; white-space: nowrap; }
.form-table .form-action { border: none; padding-top: 14px; }
.input-m { width: 280px; max-width: 100%; padding: 6px 8px; border: 1px solid #ccc; border-radius: 4px; }
.input-s { width: 100px; padding: 6px 8px; border: 1px solid #ccc; border-radius: 4px; }
.textarea-m { width: 100%; max-width: 480px; padding: 6px 8px; border: 1px solid #ccc; border-radius: 4px; resize: vertical; }
.radio-label { margin-right: 16px; cursor: pointer; }
.required { color: #e53e3e; }
.current-image { display: flex; align-items: center; gap: 14px; margin-bottom: 8px; }
.current-image .thumb { height: 80px; width: auto; border: 1px solid #ddd; border-radius: 4px; object-fit: cover; }
.delete-check { font-size: 0.8rem; color: #c00; cursor: pointer; }
.item-btn { display: inline-flex; align-items: center; gap: 4px; padding: 8px 20px; border-radius: 4px; border: none; cursor: pointer; font-size: 0.875rem; font-weight: 500; transition: opacity 0.2s; }
.item-btn:hover { opacity: 0.85; }
.item-btn-primary { background: #555; color: #fff; }
</style>

</x-item-layout>
