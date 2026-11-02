<x-admin-layout>
    <x-slot name="header">チャンネル編集 #{{ $content->id }}</x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.fanclubs.contents.children.index', $content) }}" class="underline">ポスト管理</a>
    </div>

    <form action="{{ route('admin.fanclubs.contents.update', $content) }}" method="POST" class="space-y-4 max-w-3xl">
        @csrf
        @method('PUT')
        @include('item.fanclub_contents.partials.parent_form', ['content' => $content, 'categories' => $categories, 'hideNewCategory' => true])
        <button type="submit" class="af-btn af-btn-primary">更新</button>
    </form>
</x-admin-layout>
