<x-item-layout>
    <x-slot name="header">
        チャンネル作成
    </x-slot>

    <form action="{{ route('item.fanclub.contents.store') }}" method="POST" class="space-y-4">
        @csrf
        @include('item.fanclub_contents.partials.parent_form', ['content' => null, 'categories' => $categories])
        <button type="submit" class="af-btn af-btn-primary">チャンネルを作成</button>
    </form>
    <p class="mt-4 text-sm text-gray-600">作成後、一覧から「ポスト」で本文・画像を追加してください。</p>
</x-item-layout>
