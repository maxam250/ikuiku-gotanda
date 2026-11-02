<x-admin-layout>
    <x-slot name="header">
        <h2>ファンクラブメッセージ監視</h2>
    </x-slot>

    <div class="py-6 px-4 max-w-full mx-auto">

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        {{-- フィルタ --}}
        <form method="GET" class="mb-6 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">ファンクラブID</label>
                <input type="number" name="fanclub_id" value="{{ request('fanclub_id') }}"
                       class="border rounded px-2 py-1 text-sm w-28">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">会員ID</label>
                <input type="number" name="member_id" value="{{ request('member_id') }}"
                       class="border rounded px-2 py-1 text-sm w-28">
            </div>
            <div class="flex gap-2 items-center">
                <label class="flex items-center gap-1 text-sm">
                    <input type="checkbox" name="warned" value="1" {{ request('warned') ? 'checked' : '' }}>
                    警告あり
                </label>
                <label class="flex items-center gap-1 text-sm">
                    <input type="checkbox" name="hidden" value="1" {{ request('hidden') ? 'checked' : '' }}>
                    非表示
                </label>
                <label class="flex items-center gap-1 text-sm">
                    <input type="checkbox" name="reply_only" value="1" {{ request('reply_only') ? 'checked' : '' }}>
                    返信メッセージのみ
                </label>
            </div>
            <button type="submit" class="af-btn af-btn-search">絞り込む</button>
            <a href="{{ route('admin.fanclub-messages.index') }}" class="af-btn af-btn-reset">リセット</a>
            <a href="{{ route('admin.fanclub-banned-words.index') }}" class="ml-auto af-btn af-btn-delete">禁止ワード管理</a>
        </form>

        <table class="w-full border-collapse text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="border px-3 py-2">投稿日</th>
                    <th class="border px-3 py-2">会員名</th>
                    <th class="border px-3 py-2">メッセージ</th>
                    <th class="border px-3 py-2">キャスト返信</th>
                    <th class="border px-3 py-2">警告ステータス</th>
                    <th class="border px-3 py-2">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $msg)
                @php
                    $memberName = $msg->member->name ?? '-';
                    $memberNickname = $msg->member->nickname ?? '-';
                    $suspendKey = $msg->fanclub_id . '_' . $msg->member_id;
                    $isSuspended = (bool)($suspendedMap[$suspendKey] ?? false);
                @endphp
                <tr class="{{ $msg->trashed() ? 'bg-purple-50 text-gray-500' : 'bg-white' }}">
                    <td class="border px-3 py-2 text-xs whitespace-nowrap">
                        {{ $msg->created_at->format('Y/m/d H:i') }}
                        @if($msg->trashed())
                            <div class="text-[11px] text-gray-500 mt-1">削除済み</div>
                        @endif
                    </td>
                    <td class="border px-3 py-2">
                        <div>{{ $memberName }}</div>
                        <div><span class="text-gray-500 text-xs">HN:</span> {{ $memberNickname }}</div>
                    </td>
                    <td class="border px-3 py-2">
                        <div class="flex items-start gap-3">
                            @if($msg->image_path)
                                <img src="{{ asset('../storage/' . $msg->image_path) }}" alt="添付画像"
                                     class="w-12 h-12 object-cover rounded border border-gray-200">
                            @endif
                            <div>
                                <div class="text-xs text-gray-500 mb-1">FC: {{ $msg->fanclub->title ?? '-' }}</div>
                                <p class="leading-relaxed">{{ \Str::limit($msg->message, 140) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="border px-3 py-2 text-xs">
                        @if(($msg->replies_count ?? 0) > 0)
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">返信あり</span>
                            <div class="mt-1">件数: {{ $msg->replies_count }}</div>
                            @if(!empty($msg->replies_max_created_at))
                                <div class="text-gray-500 mt-1">最終: {{ \Carbon\Carbon::parse($msg->replies_max_created_at)->format('Y/m/d H:i') }}</div>
                            @endif
                        @else
                            <span class="text-gray-400">未返信</span>
                        @endif
                    </td>
                    <td class="border px-3 py-2">
                        <form method="POST" action="{{ route('admin.fanclub-messages.status', $msg) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="border rounded px-2 py-1 text-xs" @disabled($msg->trashed())>
                                <option value="normal" @selected(!$isSuspended && !$msg->is_warned)>通常</option>
                                <option value="warn" @selected(!$isSuspended && $msg->is_warned)>警告</option>
                                <option value="suspend" @selected($isSuspended)>停止</option>
                            </select>
                            <button type="submit" class="af-btn af-btn-primary af-btn-sm" @disabled($msg->trashed())>更新</button>
                        </form>
                    </td>
                    <td class="border px-3 py-2 whitespace-nowrap">
                        <a href="{{ route('admin.fanclub-messages.show', $msg) }}"
                           class="af-btn af-btn-search af-btn-sm">詳細</a>
                        @if(!$msg->trashed())
                        <form method="POST" action="{{ route('admin.fanclub-messages.destroy', $msg) }}" class="inline"
                              onsubmit="return confirm('このメッセージを削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="af-btn af-btn-delete af-btn-sm">削除</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="border px-3 py-4 text-center text-gray-400">メッセージがありません</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">{{ $messages->links() }}</div>
    </div>
</x-admin-layout>
