<x-admin-layout>
    <x-slot name="header">チャンネル閲覧 #{{ $content->id }}</x-slot>

    <div class="mb-4 flex gap-3">
        <a href="{{ route('admin.fanclubs.contents.children.index', $content) }}" class="underline font-medium">ポスト管理</a>
        <a href="{{ route('admin.fanclubs.contents.edit', $content) }}" class="underline">編集</a>
        <a href="{{ route('admin.fanclubs.contents.index') }}" class="underline text-gray-600">一覧へ</a>
    </div>

    <article class="border bg-white p-4">
        <h2 class="font-bold text-lg">{{ $content->title }}</h2>
        <p class="text-xs text-gray-500 mt-1">
            {{ optional($content->published_at)->format('Y-m-d H:i') }} /
            {{ $content->is_paid ? '有料' : '無料' }} /
            {{ $content->category->name ?? '未分類' }}
        </p>
        <p class="mt-3">{!! nl2br(e($content->description)) !!}</p>

        <h3 class="font-semibold mt-6 mb-2">ポスト（プレビュー）</h3>
        <p class="text-xs text-gray-500 mb-3">管理画面では本編をそのまま表示します（ティザーぼかしは適用しません）。</p>
        <div class="mt-2 space-y-6">
            @foreach($content->children as $child)
                <div class="border rounded-lg overflow-hidden bg-gray-50">
                    <p class="text-xs text-gray-600 px-3 py-2 bg-gray-100 border-b">
                        #{{ $child->id }} {{ $child->title ?: '（無題）' }}
                        / 公開 {{ $child->published_at?->format('Y-m-d H:i') ?? '—' }}
                        / ティザー {{ $child->teaser_enabled ? 'ON' : 'OFF' }}
                    </p>
                    <div class="p-3">
                        <x-fanclub.child-block :child="$child" :adminPreview="true" />
                    </div>
                </div>
            @endforeach
        </div>
    </article>

    @push('scripts')
    <script>
        document.querySelectorAll('.fanclub-timer').forEach(function (el) {
            var target = el.dataset.timerTarget;
            var textEl = el.querySelector('.js-timer-text');
            if (!target || !textEl) return;

            var update = function () {
                var diff = new Date(target).getTime() - Date.now();
                if (diff <= 0) {
                    textEl.textContent = '終了しました';
                    return;
                }

                var day = Math.floor(diff / 86400000);
                var hour = Math.floor((diff % 86400000) / 3600000);
                var min = Math.floor((diff % 3600000) / 60000);
                var sec = Math.floor((diff % 60000) / 1000);
                textEl.textContent = day + '日 ' + hour + '時間 ' + min + '分 ' + sec + '秒';
            };

            update();
            setInterval(update, 1000);
        });
    </script>
    @endpush
</x-admin-layout>
