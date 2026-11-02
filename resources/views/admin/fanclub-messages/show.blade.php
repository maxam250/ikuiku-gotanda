<x-admin-layout>
    <x-slot name="header">
        <h2>メッセージ詳細</h2>
    </x-slot>

    <div class="py-6 px-4 max-w-5xl mx-auto">

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <div class="mb-4 flex flex-wrap gap-3 items-center justify-between">
            <a href="{{ route('admin.fanclub-messages.index') }}" class="af-btn af-btn-cancel af-btn-sm">&larr; 一覧へ</a>
            <div class="text-sm text-gray-600 rounded-lg border border-purple-100 bg-purple-50 px-3 py-2">
                <span class="font-medium text-gray-800">FC:</span> {{ $fanclubMessage->fanclub->title ?? '-' }}
                <span class="mx-2 text-gray-400">/</span>
                <span class="font-medium text-gray-800">会員:</span> {{ $fanclubMessage->member->name ?? '-' }}
                <span class="ml-2 text-xs text-gray-500">(HN: {{ $fanclubMessage->member->nickname ?? '-' }})</span>
            </div>
        </div>

        {{-- 管理アクション --}}
        <div class="flex flex-wrap gap-3 mb-6 p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- 警告 --}}
            <form method="POST" action="{{ route('admin.fanclub-messages.warn', $fanclubMessage) }}">
                @csrf @method('PATCH')
                <button type="submit"
                    class="af-btn af-btn-sm {{ $fanclubMessage->is_warned ? 'af-btn-reset' : 'af-btn-cancel' }}">
                    {{ $fanclubMessage->is_warned ? '⚠ 警告解除' : '⚠ 警告' }}
                </button>
            </form>

            {{-- 非表示 --}}
            <form method="POST" action="{{ route('admin.fanclub-messages.hide', $fanclubMessage) }}"
                  class="flex gap-2 items-center">
                @csrf @method('PATCH')
                <input type="text" name="admin_note" placeholder="管理メモ（任意）"
                       value="{{ $fanclubMessage->admin_note }}"
                       class="border rounded px-2 py-1 text-xs w-56">
                <button type="submit"
                    class="af-btn af-btn-sm {{ $fanclubMessage->is_hidden ? 'af-btn-delete' : 'af-btn-cancel' }}">
                    {{ $fanclubMessage->is_hidden ? '表示に戻す' : '非表示にする' }}
                </button>
            </form>

            {{-- 削除 --}}
            <form method="POST" action="{{ route('admin.fanclub-messages.destroy', $fanclubMessage) }}"
                  onsubmit="return confirm('このメッセージを削除しますか？');">
                @csrf
                @method('DELETE')
                <button type="submit" class="af-btn af-btn-delete">
                    削除
                </button>
            </form>

            {{-- 利用停止 --}}
            <details class="flex-1">
                <summary class="cursor-pointer px-3 py-1.5 text-sm bg-red-50 border border-red-300 rounded text-red-700 inline-block">
                    利用停止
                </summary>
                <form method="POST" action="{{ route('admin.fanclub-messages.suspend', $fanclubMessage) }}"
                      class="mt-2 p-3 bg-red-50 border border-red-200 rounded flex flex-wrap gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs mb-1">停止期限</label>
                        <input type="date" name="suspended_until" required
                               class="border rounded px-2 py-1 text-sm">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs mb-1">理由</label>
                        <input type="text" name="suspension_reason" required
                               class="border rounded px-2 py-1 text-sm w-full">
                    </div>
                    <button type="submit" class="af-btn af-btn-delete">
                        停止実行
                    </button>
                </form>
            </details>
        </div>

        {{-- メッセージスレッド --}}
        <div class="space-y-3 rounded-xl border border-purple-100 bg-white p-4 shadow-sm">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="mb-2 flex flex-wrap items-center gap-2">
                    <span class="font-semibold text-gray-900">{{ $fanclubMessage->member->name ?? '会員' }}</span>
                    <span class="text-xs text-gray-500">(HN: {{ $fanclubMessage->member->nickname ?? '-' }})</span>
                    <span class="text-xs text-gray-500">{{ $fanclubMessage->created_at->format('Y/m/d H:i') }}</span>
                    @if($fanclubMessage->is_warned)
                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs">警告</span>
                    @endif
                    @if($fanclubMessage->is_hidden)
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">非表示</span>
                    @endif
                </div>
                <p class="whitespace-pre-wrap text-sm leading-relaxed text-gray-700">{{ $fanclubMessage->message }}</p>
                @if($fanclubMessage->image_path)
                    <img src="{{ asset('../storage/' . $fanclubMessage->image_path) }}" alt="添付画像"
                         class="mt-3 w-24 h-24 object-cover rounded-lg border border-gray-200">
                @endif
                @if($fanclubMessage->admin_note)
                    <p class="mt-3 rounded bg-yellow-50 border border-yellow-200 px-3 py-2 text-sm text-yellow-800">管理メモ: {{ $fanclubMessage->admin_note }}</p>
                @endif
            </div>

            @foreach($fanclubMessage->replies as $reply)
            <div class="md:ml-8 rounded-xl border border-purple-200 bg-purple-50/50 p-4">
                <div class="mb-2 flex flex-wrap items-center gap-2">
                    <span class="font-semibold text-purple-800">{{ $fanclubMessage->fanclub->cast->item_name ?? 'キャスト' }}</span>
                    <span class="text-xs text-gray-500">{{ $reply->created_at->format('Y/m/d H:i') }}</span>
                </div>
                <p class="whitespace-pre-wrap text-sm leading-relaxed text-gray-700">{{ $reply->message }}</p>
                @if($reply->image_path)
                    <img src="{{ asset('../storage/' . $reply->image_path) }}" alt="添付画像"
                         class="mt-3 w-36 h-auto object-cover rounded-lg border border-gray-200">
                @endif
            </div>
            @endforeach
        </div>
    </div>
</x-admin-layout>
