<x-admin-layout>
    <x-slot name="header">ポスト：{{ $content->title }}</x-slot>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap gap-2 mb-4">
        <a href="{{ route('admin.fanclubs.contents.children.create', $content) }}" class="px-4 py-2 bg-black text-white">ポストを追加</a>
        <a href="{{ route('admin.fanclubs.contents.show', $content) }}" class="px-4 py-2 border">チャンネルを閲覧</a>
        <a href="{{ route('admin.fanclubs.contents.index') }}" class="px-4 py-2 border">チャンネル一覧</a>
    </div>

    <div class="border bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-2">ID</th>
                    <th class="text-left p-2">タイトル</th>
                    <th class="text-left p-2">公開</th>
                    <th class="text-left p-2">ティザー</th>
                    <th class="text-left p-2">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($children as $ch)
                    <tr class="border-b">
                        <td class="p-2">{{ $ch->id }}</td>
                        <td class="p-2">{{ $ch->title ?: '（無題）' }}</td>
                        <td class="p-2">{{ $ch->published_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td class="p-2">{{ $ch->teaser_enabled ? $ch->teaser_style : '—' }}</td>
                        <td class="p-2">
                            <a class="underline" href="{{ route('admin.fanclubs.contents.children.edit', [$content, $ch]) }}">編集</a>
                            <form class="inline" method="POST" action="{{ route('admin.fanclubs.contents.children.destroy', [$content, $ch]) }}" onsubmit="return confirm('削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="af-btn af-btn-delete af-btn-sm ml-2">削除</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-4 text-gray-500">ポストがありません。</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
