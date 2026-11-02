<x-item-layout>
    <x-slot name="header">
        ファンクラブ設定
    </x-slot>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 text-sm rounded">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('item.fanclub.update') }}" method="POST" class="space-y-4"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm mb-1">タイトル</label>
            <input type="text" name="title" value="{{ old('title', $fanclub->title) }}" class="w-full border px-3 py-2">
        </div>

        <div>
            <label class="block text-sm mb-1">説明</label>
            <textarea name="description" rows="4" class="w-full border px-3 py-2">{{ old('description', $fanclub->description) }}</textarea>
        </div>

        {{-- メイン画像 --}}
        <div>
            @include('admin.fanclubs.partials._main_image', ['fanclub' => $fanclub])
        </div>

        <div>
            <label class="block text-sm mb-1">入会費</label>
            <input type="number" min="0" name="price" value="{{ old('price', $fanclub->price) }}" class="border px-3 py-2 cursor-pointer" style="width: 120px;"> 円
        </div>

        <div>
            <label class="block text-sm mb-1">ステータス</label>
            <select name="status" class="border px-3 py-2 w_120px cursor-pointer">
                @foreach(['active' => '公開中', 'paused' => '一時停止', 'canceled' => '停止'] as $key => $label)
                    <option value="{{ $key }}" @selected(old('status', $fanclub->status) === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="af-btn-row">
            <button type="submit" class="af-btn af-btn-primary">保存</button>
            <a href="{{ route('item.fanclub.contents.index') }}" class="af-btn af-btn-cancel">チャンネル管理へ</a>
            <a href="{{ route('item.fanclub.members') }}" class="af-btn af-btn-cancel">ファン一覧へ</a>
        </div>
    </form>
</x-item-layout>

