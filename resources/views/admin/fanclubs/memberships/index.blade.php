<x-admin-layout>
    <x-slot name="header">ファンクラブ会員管理</x-slot>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    {{-- フィルター --}}
    <form method="GET" class="mb-4 flex flex-wrap gap-2 items-end">
        <div>
            <label class="text-xs text-gray-500 block mb-0.5">ステータス</label>
            <select name="status" class="border px-2 py-1 text-sm rounded">
                <option value="">すべて</option>
                @foreach(['active','paused','canceled'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500 block mb-0.5">支払状況</label>
            <select name="payment_status" class="border px-2 py-1 text-sm rounded">
                <option value="">すべて</option>
                @foreach(['pending','paid','unpaid','refunded'] as $ps)
                    <option value="{{ $ps }}" @selected(request('payment_status') === $ps)>{{ $ps }}</option>
                @endforeach
            </select>
        </div>
        <button class="af-btn af-btn-search af-btn-sm">絞り込み</button>
        <a href="{{ route('admin.fanclubs.memberships.index') }}" class="px-3 py-1 bg-gray-200 text-sm rounded">リセット</a>
        <a href="{{ route('admin.fanclubs.payment_logs.index') }}" class="ml-auto px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">決済履歴一覧 →</a>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm bg-white border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2 text-left">ID</th>
                    <th class="border p-2 text-left">ファンクラブ</th>
                    <th class="border p-2 text-left">会員</th>
                    <th class="border p-2 text-left">ステータス</th>
                    <th class="border p-2 text-left">支払</th>
                    <th class="border p-2 text-left">次回請求</th>
                    <th class="border p-2 text-left">警告</th>
                    <th class="border p-2 text-left">操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse($memberships as $membership)
                <tr class="{{ $membership->payment_status === 'unpaid' ? 'bg-red-50' : '' }}">
                    <td class="border p-2 text-gray-400">{{ $membership->id }}</td>
                    <td class="border p-2">{{ $membership->fanclub->title ?? '—' }}</td>
                    <td class="border p-2">{{ $membership->member->name ?? '—' }}</td>
                    <td class="border p-2">
                        @php $sc = ['active'=>'bg-green-100 text-green-700','paused'=>'bg-yellow-100 text-yellow-700','canceled'=>'bg-red-100 text-red-600']; @endphp
                        <span class="px-1.5 py-0.5 rounded text-xs font-semibold {{ $sc[$membership->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $membership->status }}</span>
                        @if($membership->isSuspended())
                            <span class="block text-xs text-orange-600 mt-0.5">停止中</span>
                        @endif
                    </td>
                    <td class="border p-2">
                        @php $pc = ['paid'=>'text-green-600','unpaid'=>'text-red-600 font-bold','pending'=>'text-gray-500']; @endphp
                        <span class="{{ $pc[$membership->payment_status] ?? 'text-gray-500' }} text-xs">{{ $membership->payment_status }}</span>
                    </td>
                    <td class="border p-2 text-xs text-gray-500">{{ optional($membership->next_billing_at)->format('Y/m/d') ?? '—' }}</td>
                    <td class="border p-2 text-xs text-center">
                        {{ $membership->warning_count > 0 ? '⚠ ' . $membership->warning_count . '回' : '—' }}
                    </td>
                    <td class="border p-2">
                        <a href="{{ route('admin.fanclubs.memberships.show', $membership) }}" class="text-xs text-blue-600 hover:underline">詳細・制裁</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="border p-4 text-center text-gray-400">会員データがありません。</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $memberships->withQueryString()->links() }}</div>
</x-admin-layout>
