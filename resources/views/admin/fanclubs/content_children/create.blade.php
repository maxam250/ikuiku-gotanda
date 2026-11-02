<x-admin-layout>
    <x-slot name="header">ポストの新規作成</x-slot>

    <form action="{{ route('admin.fanclubs.contents.children.store', $content) }}" method="POST" enctype="multipart/form-data" class="space-y-4 max-w-3xl">
        @csrf
        @include('item.fanclub_content_children.partials.form', ['child' => null])
        <button type="submit" class="af-btn af-btn-primary">作成</button>
    </form>
</x-admin-layout>
