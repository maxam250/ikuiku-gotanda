<x-item-layout>
    <x-slot name="header">ポストの編集 #{{ $child->id }}</x-slot>

    <form action="{{ route('item.fanclub.contents.children.update', [$content, $child]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')
        @include('item.fanclub_content_children.partials.form', ['child' => $child])
        <button type="submit" class="af-btn af-btn-primary">更新</button>
    </form>
</x-item-layout>
