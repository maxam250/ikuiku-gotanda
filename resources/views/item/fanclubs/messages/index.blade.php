<x-item-layout>
    <x-slot name="header">ファンクラブメッセージ</x-slot>

    <div class="mfc-msg-admin-wrap">
        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h2 class="mfc-msg-admin-title !mb-0 !bg-transparent !text-gray-800 !p-0 flex items-center gap-2">
                受信メッセージ一覧
                @if(($unreadCount ?? 0) > 0)
                    <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-700">
                        NEW {{ $unreadCount }}
                    </span>
                @endif
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('item.dashboard') }}" class="member-btn member-btn-sm">&larr; ダッシュボードへ</a>
                <a href="{{ route('item.fanclub.messages.member-list') }}" class="member-btn member-btn-sm">会員メッセージ一覧</a>
            </div>
        </div>

        @if($fanclub)
            <div class="mb-4 rounded-xl border border-purple-100 bg-purple-50/60 px-4 py-3">
                <p class="text-sm text-gray-600">ファンクラブ</p>
                <p class="font-semibold text-gray-800">{{ $fanclub->title }}</p>
            </div>
        @endif

        @if($threads->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-gray-500">
                メッセージはまだありません。
            </div>
        @else
            <div class="space-y-2">
                @foreach($threads as $msg)
                <a href="{{ route('item.fanclub.messages.show', $msg) }}"
                   class="block rounded-xl border border-purple-100 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-purple-200">
                    <div class="mb-2 flex flex-wrap items-center gap-2">
                        <span class="font-semibold text-gray-900">{{ $msg->member->nickname ?: ($msg->member->name ?? '不明') }}</span>
                        <span class="text-xs text-gray-500">{{ $msg->created_at->format('Y/m/d H:i') }}</span>
                        @if(!$msg->is_read)
                            <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs text-red-700">NEW</span>
                        @endif
                        @if($msg->replies->count() > 0)
                            <span class="inline-flex rounded-full bg-purple-100 px-2 py-0.5 text-xs text-purple-700">
                                返信 {{ $msg->replies->count() }}件
                            </span>
                        @endif
                    </div>
                    <p class="text-sm leading-relaxed text-gray-700">{{ \Str::limit($msg->message, 100) }}</p>
                </a>
                @endforeach
            </div>
            <div class="member-pagination mt-4">{{ $threads->links() }}</div>
        @endif
    </div>
</x-item-layout>
