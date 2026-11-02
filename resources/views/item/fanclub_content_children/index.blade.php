<x-item-layout>
    <x-slot name="header">
        ポスト：{{ $content->title }}
    </x-slot>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="af-btn-row">
        <a href="{{ route('item.fanclub.contents.children.create', $content) }}" class="af-btn af-btn-primary">ポストを追加</a>
        <a href="{{ route('item.fanclub.contents.edit', $content) }}" class="af-btn af-btn-edit">チャンネルを編集</a>
        <a href="{{ route('item.fanclub.contents.index') }}" class="af-btn af-btn-cancel">一覧へ</a>
    </div>

    <div class="border bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left p-2">#</th>
                    <th class="text-left p-2">タイトル</th>
                    <th class="text-left p-2">公開</th>
                    <th class="text-left p-2">ティーザー</th>
                    <th class="text-left p-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($children as $ch)
                    <tr class="border-b">
                        <td class="p-2">{{ $ch->id }}</td>
                        <td class="p-2">{{ $ch->title ?: '（無題）' }}</td>
                        <td class="p-2">
                            @if($ch->published_at)
                                公開中
                            @elseif($ch->scheduled_at)
                                予約: {{ $ch->scheduled_at->format('Y-m-d H:i') }}
                            @else
                                非公開
                            @endif
                        </td>
                        <td class="p-2">
                            @if($ch->teaser_enabled)
                                {{ $ch->teaser_style === 'custom_image' ? '専用画像' : 'ぼかし' }}
                                @if($ch->teaser_reveal_at)
                                    <span class="text-gray-400 text-xs">→{{ $ch->teaser_reveal_at->format('m/d H:i') }}</span>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="p-2 whitespace-nowrap">
                            <div class="af-btn-row">
                                <a href="{{ route('item.fanclub.contents.children.edit', [$content, $ch]) }}"
                                   class="af-btn af-btn-edit af-btn-sm">編集</a>
                                <form action="{{ route('item.fanclub.contents.children.publish-now', [$content, $ch]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="af-btn af-btn-primary af-btn-sm">即時公開</button>
                                </form>
                                <form action="{{ route('item.fanclub.contents.children.destroy', [$content, $ch]) }}" method="POST" class="inline" onsubmit="return confirm('削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="af-btn af-btn-delete af-btn-sm">削除</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-4 text-gray-500">ポストがありません。「ポストを追加」から作成してください。</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-item-layout>
