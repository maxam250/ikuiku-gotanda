<x-admin-layout>
    <x-slot name="header">
        メールマガジン送信管理
    </x-slot>

    <x-newsletter-menu />

    @if(session('success'))
        <div style="padding:1rem; margin-bottom:1.5rem; background:#d4edda; color:#155724; border:1px solid #c3e6cb; border-radius:4px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="padding:1rem; margin-bottom:1.5rem; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; border-radius:4px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- 使い方ガイド --}}
    <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:1.25rem; margin-bottom:1.5rem; font-size:0.875rem; color:#1e40af;">
        <strong>メールマガジンの送り方</strong>
        <ol style="margin:0.5rem 0 0 1.25rem; line-height:2;">
            <li>「新規メールを作る」ボタンで件名・本文を入力して保存する</li>
            <li>一覧の「詳細・送信」を開き、「テストメール」で自分のアドレスに試し送りして内容を確認する</li>
            <li>問題なければ「今すぐ全員に送信」ボタンを押す</li>
            <li>ステータスが「送信完了」になれば完了です</li>
        </ol>
        <p style="margin:0.75rem 0 0; font-size:0.8rem; color:#1d4ed8;">
            ※ メール送信にはサーバーのメール設定（SMTP等）が必要です。設定が完了するまでは送信内容がログに記録されます。
        </p>
    </div>

    {{-- 新規作成 + 予約処理 --}}
    <div style="background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.1); padding:1.25rem; margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
        <a href="{{ route('admin.newsletter.campaigns.create') }}" class="btn btn-primary">＋ 新規メールを作る</a>
        <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
            <form method="POST" action="{{ route('admin.newsletter.campaigns.process-scheduled') }}" onsubmit="return confirm('配信予約時刻を過ぎたメールを今すぐ送信しますか？');">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm">予約時刻が過ぎたメールを手動送信する</button>
            </form>
            <span style="font-size:0.75rem; color:#9ca3af;">（通常は自動処理されます）</span>
        </div>
    </div>

    {{-- 一覧 --}}
    <div style="background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.1); padding:1.25rem;">
        <h2 style="font-size:1rem; font-weight:600; margin-bottom:1rem;">送信済み・予定メール一覧</h2>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>件名</th>
                        <th>状態</th>
                        <th>送信数 / 対象</th>
                        <th>配信予定日時</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                    <tr>
                        <td>{{ $campaign->id }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($campaign->subject, 50) }}</td>
                        <td>
                            @if($campaign->status === 'draft')
                                <span style="padding:2px 8px; background:#f3f4f6; color:#374151; border-radius:999px; font-size:0.75rem; font-weight:600;">下書き</span>
                            @elseif($campaign->status === 'scheduled')
                                <span style="padding:2px 8px; background:#dbeafe; color:#1e40af; border-radius:999px; font-size:0.75rem; font-weight:600;">送信予約済み</span>
                            @elseif($campaign->status === 'sending')
                                <span style="padding:2px 8px; background:#fef9c3; color:#854d0e; border-radius:999px; font-size:0.75rem; font-weight:600;">送信中…</span>
                            @elseif($campaign->status === 'completed')
                                <span style="padding:2px 8px; background:#dcfce7; color:#166534; border-radius:999px; font-size:0.75rem; font-weight:600;">送信完了</span>
                            @elseif($campaign->status === 'failed')
                                <span style="padding:2px 8px; background:#fee2e2; color:#991b1b; border-radius:999px; font-size:0.75rem; font-weight:600;">送信失敗</span>
                            @elseif($campaign->status === 'cancelled')
                                <span style="padding:2px 8px; background:#f3f4f6; color:#6b7280; border-radius:999px; font-size:0.75rem; font-weight:600;">キャンセル済み</span>
                            @endif
                        </td>
                        <td>{{ $campaign->sent_count }} / {{ $campaign->total_recipients }} 件</td>
                        <td>{{ $campaign->scheduled_at ? $campaign->scheduled_at->format('Y/m/d H:i') : '－' }}</td>
                        <td>
                            <a href="{{ route('admin.newsletter.campaigns.show', $campaign->id) }}" class="btn btn-primary btn-sm">詳細・送信</a>
                            @if($campaign->status === 'draft' || $campaign->status === 'scheduled')
                                <a href="{{ route('admin.newsletter.campaigns.edit', $campaign->id) }}" class="btn btn-secondary btn-sm">編集</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color:#9ca3af; padding:2rem;">まだメールがありません。「新規メールを作る」から始めてください。</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">
            {{ $campaigns->links() }}
        </div>
    </div>
</x-admin-layout>
