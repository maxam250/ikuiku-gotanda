<x-admin-layout>
    <x-slot name="header">チャンネル・投稿管理</x-slot>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">

        <a href="{{ route('admin.fanclubs.contents.create') }}"
           class="inline-flex items-center px-3 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700">
            チャンネル新規作成
        </a>
    </div>

    <div class="border bg-white p-4 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">ID</th>
                    <th class="text-left py-2">キャスト</th>
                    <th class="text-left py-2">タイトル</th>
                    <th class="text-left py-2">ポスト数</th>
                    <th class="text-left py-2">公開日時</th>
                    <th class="text-left py-2">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contents as $content)
                    <tr class="border-b">
                        <td class="py-2">{{ $content->id }}</td>
                        <td class="py-2">{{ $content->cast->item_name ?? '未設定' }}</td>
                        <td class="py-2">{{ $content->title }}</td>
                        <td class="py-2">{{ $content->children_count ?? $content->children->count() }}</td>
                        <td class="py-2">{{ optional($content->published_at)->format('Y-m-d H:i') ?? '-' }}</td>
                        <td class="py-2">
                            <div class="flex items-center gap-3 whitespace-nowrap text-sm">
                                <a class="admin-fc-action-post underline" href="{{ route('admin.fanclubs.contents.children.index', $content) }}">ポスト</a>
                                <a class="underline" href="{{ route('admin.fanclubs.contents.show', $content) }}">閲覧</a>
                                <a class="underline" href="{{ route('admin.fanclubs.contents.edit', $content) }}">編集</a>
                                <form class="contents" method="POST" action="{{ route('admin.fanclubs.contents.destroy', $content) }}" onsubmit="return confirm('削除しますか？')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="af-btn af-btn-delete af-btn-sm" type="submit">削除</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $contents->links() }}</div>

    @push('styles')
    <style>
        .admin-fc-action-post {
            display: inline-block;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            background-color: #fef3c7;
            color: #1e293b;
            text-decoration: none !important;
        }
        .admin-fc-action-post:hover {
            background-color: #fde68a;
            color: #0f172a;
        }
    </style>
    @endpush
</x-admin-layout>
