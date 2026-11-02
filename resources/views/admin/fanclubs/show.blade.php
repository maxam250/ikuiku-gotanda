<x-admin-layout>
    <x-slot name="header">ファンクラブ詳細</x-slot>

    <div class="mb-4 flex gap-3">
        <a href="{{ route('admin.fanclubs.index') }}" class="text-sm text-gray-500 hover:underline">← 一覧に戻る</a>
        <a href="{{ route('admin.fanclubs.edit', $fanclub) }}" class="text-sm text-blue-600 hover:underline">編集</a>
    </div>

    {{-- 基本情報 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white border rounded shadow-sm p-5">
            <h2 class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-wide">基本情報</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex gap-2">
                    <dt class="w-24 text-gray-500 flex-shrink-0">ID</dt>
                    <dd class="text-gray-800">{{ $fanclub->id }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="w-24 text-gray-500 flex-shrink-0">キャスト</dt>
                    <dd class="font-semibold">{{ $fanclub->cast->item_name ?? '—' }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="w-24 text-gray-500 flex-shrink-0">タイトル</dt>
                    <dd>{{ $fanclub->title }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="w-24 text-gray-500 flex-shrink-0">説明</dt>
                    <dd class="text-gray-700">{{ $fanclub->description ?: '—' }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="w-24 text-gray-500 flex-shrink-0">価格</dt>
                    <dd class="font-semibold">¥{{ number_format($fanclub->price) }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="w-24 text-gray-500 flex-shrink-0">ステータス</dt>
                    <dd>
                        @php
                            $statusMap = ['active' => ['label' => '公開中', 'class' => 'bg-green-100 text-green-700'],
                                          'paused' => ['label' => '一時停止', 'class' => 'bg-yellow-100 text-yellow-700'],
                                          'canceled' => ['label' => 'キャンセル', 'class' => 'bg-red-100 text-red-700']];
                            $s = $statusMap[$fanclub->status] ?? ['label' => $fanclub->status, 'class' => 'bg-gray-100 text-gray-600'];
                        @endphp
                        <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $s['class'] }}">{{ $s['label'] }}</span>
                    </dd>
                </div>
                <div class="flex gap-2">
                    <dt class="w-24 text-gray-500 flex-shrink-0">作成日</dt>
                    <dd>{{ $fanclub->created_at->format('Y/m/d H:i') }}</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="w-24 text-gray-500 flex-shrink-0">更新日</dt>
                    <dd>{{ $fanclub->updated_at->format('Y/m/d H:i') }}</dd>
                </div>
            </dl>
        </div>

        {{-- 会員数サマリー --}}
        <div class="bg-white border rounded shadow-sm p-5">
            <h2 class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-wide">会員数</h2>
            <div class="grid grid-cols-2 gap-4 text-center">
                <div class="bg-pink-50 rounded p-4">
                    <div class="text-3xl font-bold text-pink-600">{{ $activeCount }}</div>
                    <div class="text-xs text-gray-500 mt-1">有効会員数</div>
                </div>
                <div class="bg-gray-50 rounded p-4">
                    <div class="text-3xl font-bold text-gray-700">{{ $fanclub->memberships->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">全会員数</div>
                </div>
                <div class="bg-yellow-50 rounded p-4">
                    <div class="text-2xl font-bold text-yellow-600">{{ $fanclub->memberships->where('status', 'paused')->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">一時停止</div>
                </div>
                <div class="bg-red-50 rounded p-4">
                    <div class="text-2xl font-bold text-red-500">{{ $fanclub->memberships->where('status', 'canceled')->count() }}</div>
                    <div class="text-xs text-gray-500 mt-1">キャンセル</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 最近のコンテンツ --}}
    <div class="bg-white border rounded shadow-sm p-5 mb-6">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide">最近のチャンネル（最大10件）</h2>
            <a href="{{ route('admin.fanclubs.contents.index') }}" class="text-xs text-blue-600 hover:underline">チャンネル一覧 →</a>
        </div>
        @if($fanclub->contents->isNotEmpty())
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500">
                <tr class="border-b">
                    <th class="text-left px-3 py-2">ID</th>
                    <th class="text-left px-3 py-2">タイトル</th>
                    <th class="text-left px-3 py-2">有料</th>
                    <th class="text-left px-3 py-2">ポスト数</th>
                    <th class="text-left px-3 py-2">公開日時</th>
                    <th class="text-left px-3 py-2">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fanclub->contents as $content)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-3 py-2 text-gray-400">{{ $content->id }}</td>
                    <td class="px-3 py-2">{{ $content->title }}</td>
                    <td class="px-3 py-2">
                        @if($content->is_paid)
                            <span class="px-1.5 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs">有料</span>
                        @else
                            <span class="text-gray-400 text-xs">無料</span>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-center">{{ $content->children_count ?? $content->children->count() }}</td>
                    <td class="px-3 py-2 text-xs text-gray-500">{{ optional($content->published_at)->format('Y/m/d H:i') ?? '未公開' }}</td>
                    <td class="px-3 py-2">
                        <div class="flex gap-2 text-xs">
                            <a class="text-blue-600 hover:underline" href="{{ route('admin.fanclubs.contents.show', $content) }}">詳細</a>
                            <a class="text-gray-500 hover:underline" href="{{ route('admin.fanclubs.contents.edit', $content) }}">編集</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p class="text-gray-400 text-sm text-center py-6">チャンネルがありません</p>
        @endif
    </div>

    {{-- 会員一覧 --}}
    <div class="bg-white border rounded shadow-sm p-5">
        <h2 class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-wide">会員一覧</h2>
        @if($fanclub->memberships->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500">
                    <tr class="border-b">
                        <th class="text-left px-3 py-2">会員名</th>
                        <th class="text-left px-3 py-2">ステータス</th>
                        <th class="text-left px-3 py-2">支払方法</th>
                        <th class="text-left px-3 py-2">支払状況</th>
                        <th class="text-left px-3 py-2">開始日</th>
                        <th class="text-left px-3 py-2">次回請求</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fanclub->memberships as $membership)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-3 py-2 font-medium">{{ $membership->member->name ?? '—' }}</td>
                        <td class="px-3 py-2">
                            @php
                                $ms = ['active' => 'bg-green-100 text-green-700', 'paused' => 'bg-yellow-100 text-yellow-700', 'canceled' => 'bg-red-100 text-red-600'];
                            @endphp
                            <span class="px-1.5 py-0.5 rounded text-xs font-semibold {{ $ms[$membership->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $membership->status }}</span>
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-600">{{ $membership->payment_method }}</td>
                        <td class="px-3 py-2 text-xs text-gray-600">{{ $membership->payment_status }}</td>
                        <td class="px-3 py-2 text-xs text-gray-500">{{ optional($membership->started_at)->format('Y/m/d') ?? '—' }}</td>
                        <td class="px-3 py-2 text-xs text-gray-500">{{ optional($membership->next_billing_at)->format('Y/m/d') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p class="text-gray-400 text-sm text-center py-6">会員がいません</p>
        @endif
    </div>
</x-admin-layout>
