<x-member-layout>

<x-slot name="header">
ギャラリー登録
</x-slot>

<div class="back">
    <a href="{{ route('member.gallery.index') }}" class="member-btn member-btn-secondary">
        <i class="fas fa-arrow-left"></i>戻る
    </a>
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

    <form action="{{ route('member.gallery.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="flex flex-col md:flex-row md:items-center gap-4 p-2">
            <label for="active" class="font-medium text-gray-700 min-w-[120px] bg-gray-200 px-3 py-2 rounded-lg text-center">公開設定</label>
            <select name="active" id="active">
                <option value="0">非公開</option>
                <option value="1">公開</option>
            </select>
        </div>

        <div class="flex flex-col md:flex-row md:items-center gap-4 p-2">
            <label for="flag" class="font-medium text-gray-700 min-w-[120px] bg-gray-200 px-3 py-2 rounded-lg text-center">公開範囲</label>
            <select name="flag" id="flag">
                <option value="0">全員に公開</option>
                <option value="1">ログインユーザーのみ</option>
                <option value="2">限定公開</option>
            </select>
        </div>

        <div class="flex flex-col md:flex-row md:items-center gap-4 p-2">
            <label for="title" class="font-medium text-gray-700 min-w-[120px] bg-gray-200 px-3 py-2 rounded-lg text-center">タイトル</label>
            <input type="text" name="title" id="title" class="form-control size_m" value="{{ old('title') }}" required>
        </div>

        <div class="flex flex-col md:flex-row md:items-center gap-4 p-2">
            <label for="description" class="font-medium text-gray-700 min-w-[120px] bg-gray-200 px-3 py-2 rounded-lg text-center">説明</label>
            <textarea name="description" id="description" class="form-control" rows="5">{{ old('description') }}</textarea>
        </div>

        <div class="flex flex-col md:flex-row md:items-center gap-4 p-2">
            <label for="item_id" class="font-medium text-gray-700 min-w-[120px] bg-gray-200 px-3 py-2 rounded-lg text-center">キャスト</label>
            <div class="text-gray-900">
            <select name="item_id" id="item_id" class="min-w-48">
                <option value="">選択してください</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}"{{ old('item_id') == $item->id ? ' selected' : '' }}>
                        {{ $item->item_name }}
                    </option>
                @endforeach
            </select>
            </div>
        </div>

        <div class="member-btn-group">
            <button type="submit" class="member-btn member-btn-primary">
                <i class="fas fa-plus"></i>登録する
            </button>
        </div>
    </form>
</section>

</x-member-layout>
