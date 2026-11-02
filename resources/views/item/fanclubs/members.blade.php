<x-item-layout>
    <x-slot name="header">
        ファン一覧
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('item.fanclub.edit') }}" class="af-btn af-btn-cancel af-btn-sm">ファンクラブ設定へ戻る</a>
    </div>

    <div class="rounded-xl border border-purple-100 bg-white shadow-sm overflow-hidden">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr class="bg-purple-50">
                    <th class="border px-3 py-2 text-left">会員ID</th>
                    <th class="border px-3 py-2 text-left">表示名</th>
                    <th class="border px-3 py-2 text-left">ステータス</th>
                    <th class="border px-3 py-2 text-left">入会日</th>
                </tr>
            </thead>
            <tbody>
                @forelse($memberships as $membership)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-3 py-2">{{ $membership->member_id }}</td>
                        <td class="border px-3 py-2 font-medium text-gray-900">
                            {{ $membership->member->nickname ?: ($membership->member->name ?? '-') }}
                        </td>
                        <td class="border px-3 py-2">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs bg-gray-100 text-gray-700">
                                {{ $membership->status }}
                            </span>
                        </td>
                        <td class="border px-3 py-2 text-gray-600">{{ optional($membership->started_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="border p-6 text-center text-gray-500">まだ会員はいません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $memberships->links() }}
    </div>
</x-item-layout>

