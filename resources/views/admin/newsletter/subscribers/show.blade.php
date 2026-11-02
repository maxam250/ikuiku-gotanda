<x-admin-layout>
    <x-slot name="header">
        メールマガジン購読者詳細
    </x-slot>

    <x-newsletter-menu />

    <div class="bg-white shadow-lg rounded-lg mb-6">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">購読者詳細</h2>
                <div class="space-x-2">
                    <a href="{{ route('admin.newsletter.subscribers.edit', $subscriber->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        編集
                    </a>
                    <a href="{{ route('admin.newsletter.subscribers.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                        一覧に戻る
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">メールアドレス</label>
                    <div class="mt-1 text-sm text-gray-900">{{ $subscriber->email }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ステータス</label>
                    <div class="mt-1">
                        @if($subscriber->isUnsubscribed())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">解除済み</span>
                        @elseif($subscriber->isVerified())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">確認済み</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">未確認</span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">登録日時</label>
                    <div class="mt-1 text-sm text-gray-900">{{ $subscriber->created_at->format('Y年m月d日 H:i:s') }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">確認日時</label>
                    <div class="mt-1 text-sm text-gray-900">{{ $subscriber->verified_at ? $subscriber->verified_at->format('Y年m月d日 H:i:s') : '-' }}</div>
                </div>
                @if($subscriber->unsubscribed_at)
                <div>
                    <label class="block text-sm font-medium text-gray-700">解除日時</label>
                    <div class="mt-1 text-sm text-gray-900">{{ $subscriber->unsubscribed_at->format('Y年m月d日 H:i:s') }}</div>
                </div>
                @endif
                @if($subscriber->ip_address)
                <div>
                    <label class="block text-sm font-medium text-gray-700">登録時のIPアドレス</label>
                    <div class="mt-1 text-sm text-gray-900">{{ $subscriber->ip_address }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 配信履歴 -->
    @if($subscriber->logs->count() > 0)
    <div class="bg-white shadow-lg rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">配信履歴</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">配信ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">件名</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ステータス</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">送信日時</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subscriber->logs->sortByDesc('created_at') as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <a href="{{ route('admin.newsletter.campaigns.show', $log->campaign_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    #{{ $log->campaign_id }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ \Illuminate\Support\Str::limit($log->campaign->subject ?? '-', 50) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->status === 'sent')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">送信済み</span>
                                @elseif($log->status === 'bounced')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">バウンス</span>
                                @elseif($log->status === 'failed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">失敗</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->sent_at ? $log->sent_at->format('Y/m/d H:i') : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</x-admin-layout>

