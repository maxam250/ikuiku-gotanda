<x-admin-layout>
    <x-slot name="header">ファンクラブ会員詳細</x-slot>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <a href="{{ route('admin.fanclubs.memberships.index') }}" class="text-sm text-gray-500 hover:underline">← 会員一覧に戻る</a>
    </div>

    {{-- 基本情報 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white border rounded shadow-sm p-5">
            <h2 class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-wide">会員情報</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">会員名</dt><dd>{{ $membership->member->name ?? '—' }}</dd></div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">メール</dt><dd>{{ $membership->member?->routeNotificationForMail() ?? '—' }}</dd></div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">ファンクラブ</dt><dd>{{ $membership->fanclub->title ?? '—' }}</dd></div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">ステータス</dt>
                    <dd>
                        @php $sc = ['active'=>'bg-green-100 text-green-700','paused'=>'bg-yellow-100 text-yellow-700','canceled'=>'bg-red-100 text-red-600']; @endphp
                        <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $sc[$membership->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $membership->status }}</span>
                        @if($membership->isSuspended())
                            <span class="ml-1 px-2 py-0.5 rounded text-xs bg-orange-100 text-orange-700">停止中〜{{ $membership->suspended_until->format('m/d') }}</span>
                        @endif
                    </dd>
                </div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">支払状況</dt><dd>{{ $membership->payment_status }}</dd></div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">支払方法</dt><dd>{{ $membership->payment_method }}</dd></div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">開始日</dt><dd>{{ optional($membership->started_at)->format('Y/m/d') ?? '—' }}</dd></div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">次回請求</dt><dd>{{ optional($membership->next_billing_at)->format('Y/m/d') ?? '—' }}</dd></div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">最終支払</dt><dd>{{ optional($membership->last_paid_at)->format('Y/m/d') ?? '—' }}</dd></div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">Square顧客ID</dt><dd class="text-xs text-gray-500 break-all">{{ $membership->square_customer_id ?? '—' }}</dd></div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">SubscriptionID</dt><dd class="text-xs text-gray-500 break-all">{{ $membership->square_subscription_id ?? '—' }}</dd></div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">警告回数</dt><dd>{{ $membership->warning_count }}</dd></div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">最終警告日</dt><dd>{{ optional($membership->warning_sent_at)->format('Y/m/d H:i') ?? '—' }}</dd></div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">停止期限</dt><dd>{{ optional($membership->suspended_until)->format('Y/m/d') ?? '—' }}</dd></div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">理由</dt><dd>{{ $membership->suspension_reason ?? '—' }}</dd></div>
                <div class="flex gap-2"><dt class="w-36 text-gray-500 flex-shrink-0">管理メモ</dt><dd>{{ $membership->admin_note ?? '—' }}</dd></div>
            </dl>
        </div>

        {{-- 制裁操作パネル --}}
        <div class="space-y-4">
            {{-- ステータス変更 --}}
            <div class="bg-white border rounded shadow-sm p-5">
                <h2 class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-wide">ステータス更新</h2>
                <form action="{{ route('admin.fanclubs.memberships.update', $membership) }}" method="POST" class="space-y-2">
                    @csrf @method('PATCH')
                    <div class="flex gap-2">
                        <select name="status" class="border px-2 py-1 text-sm flex-1">
                            @foreach(['active','paused','canceled'] as $s)
                                <option value="{{ $s }}" @selected($membership->status === $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                        <select name="payment_status" class="border px-2 py-1 text-sm flex-1">
                            @foreach(['pending','paid','unpaid','refunded'] as $ps)
                                <option value="{{ $ps }}" @selected($membership->payment_status === $ps)>{{ $ps }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="text" name="admin_note" value="{{ $membership->admin_note }}" class="border px-2 py-1 text-sm w-full" placeholder="管理メモ">
                    <button class="af-btn af-btn-primary af-btn-sm">更新</button>
                </form>
            </div>

            {{-- 1. 警告送信 --}}
            <div class="bg-white border border-yellow-300 rounded shadow-sm p-5">
                <h2 class="text-sm font-bold text-yellow-700 mb-3">① 警告送信</h2>
                <form action="{{ route('admin.fanclubs.memberships.warn', $membership) }}" method="POST" class="space-y-2">
                    @csrf
                    <textarea name="reason" rows="2" class="border px-2 py-1 text-sm w-full rounded" placeholder="警告理由（メールに記載されます）" required></textarea>
                    <button class="af-btn af-btn-reset af-btn-sm"
                        onclick="return confirm('会員に警告メールを送信します。よろしいですか？')">
                        警告を送信
                    </button>
                </form>
            </div>

            {{-- 2. 期間停止 --}}
            <div class="bg-white border border-orange-300 rounded shadow-sm p-5">
                <h2 class="text-sm font-bold text-orange-700 mb-3">② 期間停止</h2>
                <form action="{{ route('admin.fanclubs.memberships.suspend', $membership) }}" method="POST" class="space-y-2">
                    @csrf
                    <div class="flex gap-2 items-center">
                        <input type="number" name="days" value="3" min="1" max="365" class="border px-2 py-1 text-sm w-20 rounded">
                        <span class="text-sm">日間停止</span>
                    </div>
                    <textarea name="reason" rows="2" class="border px-2 py-1 text-sm w-full rounded" placeholder="停止理由" required></textarea>
                    <button class="af-btn af-btn-reset af-btn-sm"
                        onclick="return confirm('会員を指定期間停止します。よろしいですか？')">
                        停止を設定
                    </button>
                </form>
            </div>

            {{-- 3. 即時脱会 --}}
            <div class="bg-white border border-red-400 rounded shadow-sm p-5">
                <h2 class="text-sm font-bold text-red-700 mb-3">③ 即時脱会（Square解約）</h2>
                <form action="{{ route('admin.fanclubs.memberships.expel', $membership) }}" method="POST" class="space-y-2">
                    @csrf
                    <textarea name="reason" rows="2" class="border px-2 py-1 text-sm w-full rounded" placeholder="脱会理由（記録用）" required></textarea>
                    <button class="af-btn af-btn-delete af-btn-sm"
                        onclick="return confirm('Square サブスクを即時解約し、脱会処理を行います。この操作は取り消せません。よろしいですか？')">
                        即時脱会を実行
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 決済履歴 --}}
    <div class="bg-white border rounded shadow-sm p-5">
        <h2 class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-wide">決済履歴（直近20件）</h2>
        @if($membership->paymentLogs->isNotEmpty())
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500">
                <tr class="border-b">
                    <th class="text-left px-3 py-2">日時</th>
                    <th class="text-left px-3 py-2">ステータス</th>
                    <th class="text-right px-3 py-2">金額</th>
                    <th class="text-left px-3 py-2">Invoice ID</th>
                    <th class="text-left px-3 py-2">エラー</th>
                </tr>
            </thead>
            <tbody>
                @foreach($membership->paymentLogs as $log)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-3 py-2 text-xs text-gray-500">{{ $log->created_at->format('Y/m/d H:i') }}</td>
                    <td class="px-3 py-2">
                        @php $pc = ['paid'=>'bg-green-100 text-green-700','failed'=>'bg-red-100 text-red-700','refunded'=>'bg-blue-100 text-blue-700']; @endphp
                        <span class="px-1.5 py-0.5 rounded text-xs font-semibold {{ $pc[$log->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $log->status }}</span>
                    </td>
                    <td class="px-3 py-2 text-right">¥{{ number_format($log->amount) }}</td>
                    <td class="px-3 py-2 text-xs text-gray-500 truncate max-w-xs">{{ $log->square_invoice_id ?? '—' }}</td>
                    <td class="px-3 py-2 text-xs text-red-500">{{ $log->error_message ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p class="text-gray-400 text-sm text-center py-4">決済履歴はありません</p>
        @endif
    </div>
</x-admin-layout>
