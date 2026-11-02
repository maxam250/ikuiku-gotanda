<x-item-layout>
    <x-slot name="header">
        ファンクラブ投稿管理
    </x-slot>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="af-btn-row">
        <a href="{{ route('item.fanclub.contents.create') }}" class="af-btn af-btn-primary">チャンネル開設</a>
        <a href="{{ route('item.fanclub.edit') }}" class="af-btn af-btn-cancel">ファンクラブ設定</a>
    </div>

    <table class="w-full border-collapse border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">タイトル</th>
                <th class="border p-2 text-left">カテゴリ</th>
                <th class="border p-2 text-left">ポスト数</th>
                <th class="border p-2 text-left">公開</th>
                <th class="border p-2 text-left">操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contents as $content)
                <tr>
                    <td class="border p-2">{{ $content->title }}</td>
                    <td class="border p-2">{{ $content->category->name ?? '-' }}</td>
                    <td class="border p-2">{{ $content->children_count ?? 0 }}</td>
                    <td class="border p-2">
                        @if($content->published_at)
                            公開中
                        @elseif($content->scheduled_at)
                            予約: {{ $content->scheduled_at->format('Y-m-d H:i') }}
                        @else
                            非公開
                        @endif
                    </td>
                    <td class="border p-2">
                        <div class="af-btn-row">
                            <a href="{{ route('item.fanclub.contents.children.index', $content) }}"
                               class="af-btn af-btn-cancel af-btn-sm">ポスト</a>
                            <a href="{{ route('item.fanclub.contents.edit', $content) }}"
                               class="af-btn af-btn-edit af-btn-sm">チャンネル編集</a>
                            <form action="{{ route('item.fanclub.contents.publish-now', $content) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="af-btn af-btn-primary af-btn-sm">今すぐ公開</button>
                            </form>
                            <form action="{{ route('item.fanclub.contents.destroy', $content) }}" method="POST" class="inline" onsubmit="return confirm('削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="af-btn af-btn-delete af-btn-sm">削除</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="border p-4 text-center text-gray-500">投稿がありません。</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $contents->links() }}</div>
</x-item-layout>

