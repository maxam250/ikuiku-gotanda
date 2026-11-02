<x-admin-layout>
    <x-slot name="header">ファンクラブ管理</x-slot>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <div class="mb-4 flex justify-end">
        <a href="{{ route('admin.fanclubs.create') }}"
           class="px-4 py-2 bg-pink-500 text-white text-sm rounded hover:bg-pink-600">
            + 新規作成
        </a>
    </div>

    <div class="border bg-white overflow-x-auto rounded shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr class="border-b">
                    <th class="text-left px-4 py-3">ID</th>
                    <th class="text-left px-4 py-3">キャスト名</th>
                    <th class="text-left px-4 py-3">タイトル</th>
                    <th class="text-left px-4 py-3">価格</th>
                    <th class="text-left px-4 py-3">ステータス</th>
                    <th class="text-left px-4 py-3">全会員数</th>
                    <th class="text-left px-4 py-3">有効会員数</th>
                    <th class="text-left px-4 py-3">チャンネル数</th>
                    <th class="text-left px-4 py-3">作成日</th>
                    <th class="text-left px-4 py-3">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fanclubs as $fanclub)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ $fanclub->id }}</td>
                        <td class="px-4 py-3 font-medium">
                            {{ $fanclub->cast->item_name ?? '—' }}
                        </td>
                        <td class="px-4 py-3">{{ $fanclub->title }}</td>
                        <td class="px-4 py-3">¥{{ number_format($fanclub->price) }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusMap = ['active' => ['label' => '公開中', 'class' => 'bg-green-100 text-green-700'],
                                              'paused' => ['label' => '一時停止', 'class' => 'bg-yellow-100 text-yellow-700'],
                                              'canceled' => ['label' => 'キャンセル', 'class' => 'bg-red-100 text-red-700']];
                                $s = $statusMap[$fanclub->status] ?? ['label' => $fanclub->status, 'class' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $s['class'] }}">{{ $s['label'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">{{ $fanclub->memberships_count }}</td>
                        <td class="px-4 py-3 text-center font-semibold text-pink-600">{{ $fanclub->active_memberships_count }}</td>
                        <td class="px-4 py-3 text-center">{{ $fanclub->contents_count }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $fanclub->created_at->format('Y/m/d') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3 text-xs whitespace-nowrap">
                                <a class="text-blue-600 hover:underline" href="{{ route('admin.fanclubs.show', $fanclub) }}">詳細</a>
                                <a class="text-gray-600 hover:underline" href="{{ route('admin.fanclubs.edit', $fanclub) }}">編集</a>
                                <form class="contents" method="POST" action="{{ route('admin.fanclubs.destroy', $fanclub) }}"
                                      onsubmit="return confirm('「{{ $fanclub->title }}」を削除しますか？')">
                                    @csrf @method('DELETE')
                                    <button class="af-btn af-btn-delete af-btn-sm" type="submit">削除</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-400">ファンクラブが登録されていません</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $fanclubs->links() }}</div>
</x-admin-layout>
