<x-item-layout>

<x-slot name="header">マイコスチューム追加</x-slot>

<div class="back">
    <a href="{{ route('item.mycostumes.index') }}" class="af-btn af-btn-cancel af-btn-sm">戻る</a>
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
    <form action="{{ route('item.mycostumes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <table class="form-table">
            <tr>
                <th>表示設定</th>
                <td>
                    <select name="active">
                        <option value="1">表示</option>
                        <option value="0">非表示</option>
                    </select>
                </td>
            </tr>

            @if(count($mycostumeCates) > 0)
            <tr>
                <th>カテゴリ</th>
                <td>
                    @foreach($mycostumeCates as $cate)
                        <label class="radio-label">
                            <input type="radio" name="mycostume_cate_id" value="{{ $cate->id }}" checked>
                            {{ $cate->name }}
                        </label>
                    @endforeach
                </td>
            </tr>
            @endif

            <tr>
                <th>マイコス名 <span class="required">※</span></th>
                <td>
                    <input type="text" name="name" value="{{ old('name') }}" class="input-m" required>
                </td>
            </tr>

            <tr>
                <th>説明文</th>
                <td>
                    <textarea name="body" class="textarea-m" rows="5">{{ old('body') }}</textarea>
                </td>
            </tr>

            <tr>
                <th>画像</th>
                <td>
                    <input type="file" name="filename" accept="image/*">
                </td>
            </tr>

            <tr>
                <td colspan="2" class="form-action">
                    <button type="submit" class="af-btn af-btn-primary">登録</button>
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
.textarea-m { width: 100%; max-width: 480px; padding: 6px 8px; border: 1px solid #ccc; border-radius: 4px; resize: vertical; }
.radio-label { margin-right: 16px; cursor: pointer; }
.required { color: #e53e3e; }
.item-btn { display: inline-flex; align-items: center; gap: 4px; padding: 8px 20px; border-radius: 4px; border: none; cursor: pointer; font-size: 0.875rem; font-weight: 500; transition: opacity 0.2s; }
.item-btn:hover { opacity: 0.85; }
.item-btn-primary { background: #555; color: #fff; }
</style>

</x-item-layout>
