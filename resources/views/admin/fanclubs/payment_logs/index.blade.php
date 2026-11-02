<x-admin-layout>
    <x-slot name="header">決済履歴一覧</x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.fanclubs.memberships.index') }}" class="text-sm text-gray-500 hover:underline">← 会員管理に戻る</a>
    </div>

    {{-- フィルター --}}
    <form method="GET" class="mb-4 flex flex-wrap gap-2 items-end">
        <div>
            <label class="text-xs text-gray-500 block mb-0.5">ステータス</label>
            <select name="status" class="border px-2 py-1 text-sm rounded">
                <option value="">すべて</option>
                @foreach(['paid','failed','refunded'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <button class="af-btn af-btn-search af-btn-sm">絞り込み</button>
        <a href="{{ route('admin.fanclubs.payment_logs.index') }}" class="px-3 py-1 bg-gray-200 text-sm rounded">リセット</a>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm bg-white border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2 text-left">日時</th>
                    <th class="border p-2 text-left">会員</th>
                    <th class="border p-2 text-left">ファンクラブ</th>
                    <th class="border p-2 text-left">ステータス</th>
                    <th class="border p-2 text-right">金額</th>
                    <th class="border p-2 text-left">Invoice ID</th>
                    <th class="border p-2 text-left">エラー</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="{{ $log->status === 'failed' ? 'bg-red-50' : '' }}">
                    <td class="border p-2 text-xs text-gray-500">{{ $log->created_at->format('Y/m/d H:i') }}</td>
                    <td class="border p-2">{{ $log->member->name ?? '—' }}</td>
                    <td class="border p-2">{{ $log->fanclub->title ?? '—' }}</td>
                    <td class="border p-2">
                        @php $pc = ['paid'=>'bg-green-100 text-green-700','failed'=>'bg-red-100 text-red-700','refunded'=>'bg-blue-100 text-blue-700']; @endphp
                        <span class="px-1.5 py-0.5 rounded text-xs font-semibold {{ $pc[$log->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $log->status }}</span>
                    </td>
                    <td class="border p-2 text-right">¥{{ number_format($log->amount) }}</td>
                    <td class="border p-2 text-xs text-gray-500 truncate max-w-xs">{{ $log->square_invoice_id ?? '—' }}</td>
                    <td class="border p-2 text-xs text-red-500">{{ $log->error_message ?? '' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="border p-4 text-center text-gray-400">決済履歴がありません。</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->withQueryString()->links() }}</div>
</x-admin-layout>
