<x-admin-layout>
    <x-slot name="header">
        アクセスログ
    </x-slot>

    <section class="content" style="padding:0 8px;">

        {{-- 期間フィルタ --}}
        <section class="mb-4 p-6 bg-white rounded-xl">
            <form method="GET" action="{{ route('admin.access-logs.index') }}"
                  style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;">
                <div>
                    <label style="display:block; font-size:12px; color:#666;">開始日</label>
                    <input type="date" name="from" value="{{ $from->toDateString() }}"
                           class="py-1 px-2 rounded border border-gray-300">
                </div>
                <div>
                    <label style="display:block; font-size:12px; color:#666;">終了日</label>
                    <input type="date" name="to" value="{{ $to->toDateString() }}"
                           class="py-1 px-2 rounded border border-gray-300">
                </div>
                <div>
                    <button type="submit" class="af-btn af-btn-primary af-btn-sm">表示</button>
                </div>
                <div style="display:flex; gap:6px;">
                    <a href="{{ route('admin.access-logs.index', ['from' => now()->subDays(6)->toDateString(), 'to' => now()->toDateString()]) }}" class="af-btn af-btn-cancel af-btn-sm">直近7日</a>
                    <a href="{{ route('admin.access-logs.index', ['from' => now()->subDays(29)->toDateString(), 'to' => now()->toDateString()]) }}" class="af-btn af-btn-cancel af-btn-sm">直近30日</a>
                    <a href="{{ route('admin.access-logs.index', ['from' => now()->subDays(89)->toDateString(), 'to' => now()->toDateString()]) }}" class="af-btn af-btn-cancel af-btn-sm">直近90日</a>
                </div>
            </form>
            <p style="margin-top:8px; font-size:12px; color:#888;">
                対象期間: {{ $from->format('Y/m/d') }} 〜 {{ $to->format('Y/m/d') }}
                （最大約3ヶ月／{{ $retentionDays }}日を過ぎたログは自動削除されます）
            </p>
        </section>

        {{-- サマリー --}}
        <section style="display:flex; flex-wrap:wrap; gap:16px; margin-bottom:16px;">
            <div class="p-6 bg-white rounded-xl" style="flex:1; min-width:180px;">
                <p style="font-size:13px; color:#888;">合計 PV</p>
                <p style="font-size:28px; font-weight:700;">{{ number_format($totalPv) }}</p>
            </div>
            <div class="p-6 bg-white rounded-xl" style="flex:1; min-width:180px;">
                <p style="font-size:13px; color:#888;">合計 UU</p>
                <p style="font-size:28px; font-weight:700;">{{ number_format($totalUu) }}</p>
            </div>
            <div class="p-6 bg-white rounded-xl" style="flex:1; min-width:180px;">
                <p style="font-size:13px; color:#888;">直接流入(リファラ無)PV</p>
                <p style="font-size:28px; font-weight:700;">{{ number_format($directPv) }}</p>
            </div>
            <div class="p-6 bg-white rounded-xl" style="flex:1; min-width:180px;">
                <p style="font-size:13px; color:#888;">保存中の総ログ行数</p>
                <p style="font-size:28px; font-weight:700;">{{ number_format($totalRows) }}</p>
            </div>
        </section>

        {{-- 日別グラフ --}}
        <section class="mb-4 p-6 bg-white rounded-xl">
            <h3 style="font-size:16px; font-weight:700; margin-bottom:12px;">日別 PV / UU</h3>
            <div style="position:relative; height:320px;">
                <canvas id="accessChart"></canvas>
            </div>
        </section>

        <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-start;">

            {{-- リファラ集計 --}}
            <section class="mb-4 p-6 bg-white rounded-xl" style="flex:1; min-width:320px;">
                <h3 style="font-size:16px; font-weight:700; margin-bottom:12px;">リファラ（流入元）ランキング</h3>
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr style="border-bottom:2px solid #eee; text-align:left;">
                            <th style="padding:6px;">流入元ホスト</th>
                            <th style="padding:6px; text-align:right;">PV</th>
                            <th style="padding:6px; text-align:right;">UU</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($referers as $ref)
                            <tr style="border-bottom:1px solid #f0f0f0;">
                                <td style="padding:6px; word-break:break-all;">{{ $ref->referer_host }}</td>
                                <td style="padding:6px; text-align:right;">{{ number_format($ref->pv) }}</td>
                                <td style="padding:6px; text-align:right;">{{ number_format($ref->uu) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="padding:12px; color:#999;">データがありません</td></tr>
                        @endforelse
                        @if ($directPv > 0)
                            <tr style="border-top:2px solid #eee; color:#666;">
                                <td style="padding:6px;">（直接／リファラ無し）</td>
                                <td style="padding:6px; text-align:right;">{{ number_format($directPv) }}</td>
                                <td style="padding:6px; text-align:right;">-</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </section>

            {{-- 日別テーブル --}}
            <section class="mb-4 p-6 bg-white rounded-xl" style="flex:1; min-width:320px;">
                <h3 style="font-size:16px; font-weight:700; margin-bottom:12px;">日別内訳</h3>
                <div style="max-height:420px; overflow:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:13px;">
                        <thead>
                            <tr style="border-bottom:2px solid #eee; text-align:left;">
                                <th style="padding:6px;">日付</th>
                                <th style="padding:6px; text-align:right;">PV</th>
                                <th style="padding:6px; text-align:right;">UU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse (array_reverse($labels, true) as $i => $label)
                                <tr style="border-bottom:1px solid #f0f0f0;">
                                    <td style="padding:6px;">{{ $label }}</td>
                                    <td style="padding:6px; text-align:right;">{{ number_format($pvSeries[$i]) }}</td>
                                    <td style="padding:6px; text-align:right;">{{ number_format($uuSeries[$i]) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="padding:12px; color:#999;">データがありません</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- 生ログ（最新100件） --}}
        <section class="mb-4 p-6 bg-white rounded-xl">
            <h3 style="font-size:16px; font-weight:700; margin-bottom:12px;">生ログ（最新100件）</h3>
            <div style="max-height:480px; overflow:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:12px;">
                    <thead>
                        <tr style="border-bottom:2px solid #eee; text-align:left;">
                            <th style="padding:6px; white-space:nowrap;">日時</th>
                            <th style="padding:6px;">パス</th>
                            <th style="padding:6px;">リファラ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rawLogs as $log)
                            <tr style="border-bottom:1px solid #f0f0f0;">
                                <td style="padding:6px; white-space:nowrap;">{{ \Carbon\Carbon::parse($log->created_at)->format('m/d H:i:s') }}</td>
                                <td style="padding:6px; word-break:break-all;">/{{ $log->path }}</td>
                                <td style="padding:6px; word-break:break-all; color:#666;">{{ $log->referer ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="padding:12px; color:#999;">データがありません</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </section>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            var ctx = document.getElementById('accessChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [
                        {
                            label: 'PV',
                            data: @json($pvSeries),
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59,130,246,0.1)',
                            tension: 0.2,
                            fill: true,
                        },
                        {
                            label: 'UU',
                            data: @json($uuSeries),
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239,68,68,0.1)',
                            tension: 0.2,
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        })();
    </script>
    @endpush
</x-admin-layout>
