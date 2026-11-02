<x-item-layout>
    <x-slot name="header">
        チャンネル編集
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('item.fanclub.contents.children.index', $content) }}" class="af-btn af-btn-cancel af-btn-sm">ポストの管理</a>
        <a href="{{ route('item.fanclub.contents.index') }}" class="af-btn af-btn-cancel af-btn-sm">一覧</a>
    </div>

    <form action="{{ route('item.fanclub.contents.update', $content) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        @include('item.fanclub_contents.partials.parent_form', ['content' => $content, 'categories' => $categories])
        <button type="submit" class="af-btn af-btn-primary">チャンネルを更新</button>
    </form>
</x-item-layout>
