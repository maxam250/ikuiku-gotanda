<x-item-layout>
    <x-slot name="header">ポストの新規作成</x-slot>

    <form action="{{ route('item.fanclub.contents.children.store', $content) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @include('item.fanclub_content_children.partials.form', ['child' => null])
        <button type="submit" class="af-btn af-btn-primary">登録</button>
    </form>
</x-item-layout>
