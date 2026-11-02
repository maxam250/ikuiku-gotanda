<x-admin-layout>
    <x-slot name="header">
        メールマガジン詳細・送信
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

    {{-- メール内容 --}}
    <div style="background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.1); padding:1.5rem; margin-bottom:1.5rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.75rem;">
            <h2 style="font-size:1rem; font-weight:600;">メール内容</h2>
            <div style="display:flex; gap:0.5rem;">
                @if($campaign->status === 'draft' || $campaign->status === 'scheduled')
                    <a href="{{ route('admin.newsletter.campaigns.edit', $campaign->id) }}" class="btn btn-secondary btn-sm">編集</a>
                @endif
                <a href="{{ route('admin.newsletter.campaigns.index') }}" class="btn btn-secondary btn-sm">一覧に戻る</a>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem;">
            <div>
                <div style="font-size:0.8rem; color:#6b7280; margin-bottom:0.25rem;">状態</div>
                @if($campaign->status === 'draft')
                    <span style="padding:3px 10px; background:#f3f4f6; color:#374151; border-radius:999px; font-size:0.8rem; font-weight:600;">下書き（まだ送っていません）</span>
                @elseif($campaign->status === 'scheduled')
                    <span style="padding:3px 10px; background:#dbeafe; color:#1e40af; border-radius:999px; font-size:0.8rem; font-weight:600;">送信予約済み</span>
                @elseif($campaign->status === 'sending')
                    <span style="padding:3px 10px; background:#fef9c3; color:#854d0e; border-radius:999px; font-size:0.8rem; font-weight:600;">送信中…</span>
                @elseif($campaign->status === 'completed')
                    <span style="padding:3px 10px; background:#dcfce7; color:#166534; border-radius:999px; font-size:0.8rem; font-weight:600;">送信完了</span>
                @elseif($campaign->status === 'failed')
                    <span style="padding:3px 10px; background:#fee2e2; color:#991b1b; border-radius:999px; font-size:0.8rem; font-weight:600;">送信失敗</span>
                @elseif($campaign->status === 'cancelled')
                    <span style="padding:3px 10px; background:#f3f4f6; color:#6b7280; border-radius:999px; font-size:0.8rem; font-weight:600;">キャンセル済み</span>
                @endif
            </div>
            <div>
                <div style="font-size:0.8rem; color:#6b7280; margin-bottom:0.25rem;">送信数 / 送信対象</div>
                <div style="font-size:0.9rem;">{{ $campaign->sent_count }} 件 / {{ $campaign->total_recipients }} 件</div>
            </div>
            <div>
                <div style="font-size:0.8rem; color:#6b7280; margin-bottom:0.25rem;">配信予定日時</div>
                <div style="font-size:0.9rem;">{{ $campaign->scheduled_at ? $campaign->scheduled_at->format('Y年m月d日 H:i') : '未設定' }}</div>
            </div>
            <div>
                <div style="font-size:0.8rem; color:#6b7280; margin-bottom:0.25rem;">送信完了日時</div>
                <div style="font-size:0.9rem;">{{ $campaign->sent_at ? $campaign->sent_at->format('Y年m月d日 H:i') : '－' }}</div>
            </div>
        </div>

        <div style="margin-bottom:1rem;">
            <div style="font-size:0.8rem; color:#6b7280; margin-bottom:0.25rem;">件名</div>
            <div style="font-size:0.95rem; font-weight:500;">{{ $campaign->subject }}</div>
        </div>

        <div style="margin-bottom:1rem;">
            <div style="font-size:0.8rem; color:#6b7280; margin-bottom:0.25rem;">プレーンテキスト本文</div>
            <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:4px; padding:1rem; font-size:0.85rem; white-space:pre-wrap; max-height:200px; overflow-y:auto;">{{ $campaign->body_text ?: '－' }}</div>
        </div>

        <div>
            <div style="font-size:0.8rem; color:#6b7280; margin-bottom:0.25rem;">HTML本文</div>
            <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:4px; padding:1rem; font-size:0.85rem; max-height:200px; overflow-y:auto;">{!! $campaign->body_html ?: '－' !!}</div>
        </div>
    </div>

    {{-- ① 送信アクション（枠外・大きめ） --}}
    @if(in_array($campaign->status, ['draft', 'scheduled']))
    <div style="background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.1); padding:1.5rem; margin-bottom:1.5rem; border-left:4px solid #2563eb;">
        <h3 style="font-size:1rem; font-weight:600; margin-bottom:0.4rem;">全員に送信する</h3>
        <p style="font-size:0.85rem; color:#6b7280; margin-bottom:1.25rem;">
            内容を確認したら下のボタンで登録済みの全購読者にメールを送信します。<br>
            送信前に必ず下の「テストメールを送る」で内容を確認してください。
        </p>
        <div style="display:flex; gap:1rem; flex-wrap:wrap; align-items:center;">
            <form method="POST" action="{{ route('admin.newsletter.campaigns.send', $campaign->id) }}">
                @csrf
                <button type="submit"
                        onclick="return confirm('登録済みの全購読者（{{ $campaign->total_recipients }}件）にメールを送信します。\nよろしいですか？')"
                        style="padding:0.8rem 2rem; font-size:1rem; font-weight:700; background:#2563eb; color:#fff; border:none; border-radius:6px; cursor:pointer; white-space:nowrap;">
                    今すぐ全員に送信する（{{ $campaign->total_recipients }}件）
                </button>
            </form>
            @if($campaign->status === 'scheduled')
                <form method="POST" action="{{ route('admin.newsletter.campaigns.cancel', $campaign->id) }}">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('送信予約をキャンセルします。よろしいですか？')"
                            style="padding:0.8rem 1.5rem; font-size:1rem; font-weight:700; background:#dc2626; color:#fff; border:none; border-radius:6px; cursor:pointer; white-space:nowrap;">
                        送信予約をキャンセルする
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endif

    {{-- ① 送信中リセット（詰まった場合） --}}
    @if($campaign->status === 'sending')
    <div style="background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.1); padding:1.5rem; margin-bottom:1.5rem; border-left:4px solid #f59e0b;">
        <h3 style="font-size:0.95rem; font-weight:600; margin-bottom:0.4rem; color:#92400e;">送信中のまま止まっていますか？</h3>
        <p style="font-size:0.85rem; color:#6b7280; margin-bottom:1rem;">
            送信処理が途中で止まった場合は、下のボタンで「下書き」に戻してから再送信できます。
        </p>
        <form method="POST" action="{{ route('admin.newsletter.campaigns.reset-sending', $campaign->id) }}">
            @csrf
            <button type="submit"
                    onclick="return confirm('下書きにリセットして再送信できる状態に戻します。よろしいですか？')"
                    style="padding:0.6rem 1.5rem; font-size:0.9rem; font-weight:600; background:#f59e0b; color:#fff; border:none; border-radius:6px; cursor:pointer;">
                下書きにリセットして再送信できるようにする
            </button>
        </form>
    </div>
    @endif

    {{-- ② テストメール送信 --}}
    <div style="background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.1); padding:1.5rem; margin-bottom:1.5rem;">
        <h3 style="font-size:0.95rem; font-weight:600; margin-bottom:0.4rem;">テストメールを送る</h3>
        <p style="font-size:0.85rem; color:#6b7280; margin-bottom:1rem;">
            本番送信の前に、自分のメールアドレス宛にテスト送信して内容を確認できます。
        </p>
        <form method="POST" action="{{ route('admin.newsletter.campaigns.test-send', $campaign->id) }}">
            @csrf
            <div style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">
                <input type="email" name="test_email" placeholder="送り先メールアドレス（例: you@example.com）"
                       required class="form-control" style="max-width:320px;">
                <button type="submit" class="btn btn-secondary">テストメールを送る</button>
            </div>
        </form>
    </div>

    {{-- ③ 配信ログ --}}
    @if($campaign->logs->count() > 0)
    <div style="background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,0.1); padding:1.5rem;">
        <h3 style="font-size:0.95rem; font-weight:600; margin-bottom:1rem;">送信ログ（最新50件）</h3>
        <div style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>メールアドレス</th>
                        <th>結果</th>
                        <th>送信日時</th>
                        <th>エラー内容</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campaign->logs->take(50) as $log)
                    <tr>
                        <td>{{ $log->subscriber->email }}</td>
                        <td>
                            @if($log->status === 'sent')
                                <span style="padding:2px 8px; background:#dcfce7; color:#166534; border-radius:999px; font-size:0.75rem; font-weight:600;">送信済み</span>
                            @elseif($log->status === 'bounced')
                                <span style="padding:2px 8px; background:#fee2e2; color:#991b1b; border-radius:999px; font-size:0.75rem; font-weight:600;">届かなかった</span>
                            @elseif($log->status === 'failed')
                                <span style="padding:2px 8px; background:#fee2e2; color:#991b1b; border-radius:999px; font-size:0.75rem; font-weight:600;">送信失敗</span>
                            @endif
                        </td>
                        <td>{{ $log->sent_at ? $log->sent_at->format('Y/m/d H:i') : '－' }}</td>
                        <td style="font-size:0.8rem; color:#9ca3af;">{{ $log->error_message ?: '－' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</x-admin-layout>
