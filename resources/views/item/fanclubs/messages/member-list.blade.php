<x-item-layout>
    <x-slot name="header">会員メッセージ一覧</x-slot>

    <div class="mfc-msg-admin-wrap">
        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <a href="{{ route('item.dashboard') }}" class="member-btn member-btn-sm">&larr; ダッシュボードへ戻る</a>
            <a href="{{ route('item.fanclub.messages.index') }}" class="member-btn member-btn-sm">スレッド一覧へ</a>
        </div>

        @if($fanclub)
            <div class="mb-4 rounded-xl border border-purple-100 bg-purple-50/60 px-4 py-3">
                <p class="text-sm text-gray-600">ファンクラブ</p>
                <p class="font-semibold text-gray-800">{{ $fanclub->title }}</p>
            </div>
        @endif

        @if(session('success'))
            <div class="mfc-alert mfc-alert--success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mfc-alert mfc-alert--error">{{ session('error') }}</div>
        @endif

        @if($messages->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-gray-500">
                会員メッセージはまだありません。
            </div>
        @else
            <div class="rounded-xl border border-purple-100 bg-white shadow-sm overflow-hidden">
                <table class="fc-member-list-table w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-purple-50">
                            <th class="border px-3 py-2 text-left" style="width:15%;">日付</th>
                            <th class="border px-3 py-2 text-left" style="width:15%;">会員</th>
                            <th class="border px-3 py-2 text-left" style="width:12%;">返信状況</th>
                            <th class="border px-3 py-2 text-left">メッセージ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $message)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-3 py-2 whitespace-nowrap text-gray-600">{{ $message->created_at->format('Y/m/d H:i') }}</td>
                                <td class="border px-3 py-2">
                                    <span class="font-medium text-gray-900">{{ $message->member->nickname ?: ($message->member->name ?? '-') }}</span>
                                </td>
                                <td class="border px-3 py-2">
                                    @if(($message->replies_count ?? 0) > 0)
                                        <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs text-green-700">
                                            返信済み（{{ $message->replies_count }}）
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs text-amber-700">
                                            未返信
                                        </span>
                                    @endif
                                </td>
                                <td class="border px-3 py-2">
                                    <a href="{{ route('item.fanclub.messages.show', $message) }}" class="block text-gray-700 hover:text-blue-700">
                                        <p class="line-clamp-2 leading-relaxed">{{ \Str::limit($message->message, 120) }}</p>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="member-pagination mt-4">{{ $messages->links() }}</div>
        @endif
    </div>

    <style>
        @media screen and (max-width: 767px) {
            .fc-member-list-table th,
            .fc-member-list-table td {
                display: table-cell !important;
            }
        }
    </style>
</x-item-layout>
