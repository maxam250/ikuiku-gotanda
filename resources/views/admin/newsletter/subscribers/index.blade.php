<x-admin-layout>
    <x-slot name="header">
        メールマガジン購読者管理
    </x-slot>

    <x-newsletter-menu />

    @if(session('success'))
        <div class="alert alert-success" style="padding: 1rem; margin-bottom: 2rem; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
        <form method="GET" action="{{ route('admin.newsletter.subscribers.index') }}" style="display: contents;">
            <div>
                <label style="display: block;">検索（メールアドレス）</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control">
            </div>
            <div>
                <label style="display: block;">ステータス</label>
                <select name="status" class="form-control">
                    <option value="">すべて</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>確認済み</option>
                    <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>未確認</option>
                    <option value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>解除済み</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary">検索</button>
                <a href="{{ route('admin.newsletter.subscribers.index') }}" class="btn btn-secondary">リセット</a>
            </div>
        </form>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.newsletter.subscribers.create') }}" class="btn btn-primary">新規追加</a>
            <a href="{{ route('admin.newsletter.subscribers.export') }}" class="btn btn-success">CSVエクスポート</a>
        </div>
        <form method="POST" action="{{ route('admin.newsletter.process-bounced') }}" onsubmit="return confirm('バウンスメールを処理し、該当する購読者を削除しますか？');" style="display: contents;">
            @csrf
            <button type="submit" class="btn btn-warning">バウンスメール処理</button>
        </form>
    </div>

    <form id="bulkForm" method="POST" action="">
        @csrf
        <div style="margin-bottom: 1rem;">
            <button type="button" onclick="bulkAction('destroy')" class="btn btn-danger btn-sm">選択した項目を削除</button>
            <button type="button" onclick="bulkAction('unsubscribe')" class="btn btn-warning btn-sm">選択した項目を解除</button>
        </div>

        <table class="table">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                <th>ID</th>
                <th>メールアドレス</th>
                <th>ステータス</th>
                <th>登録日時</th>
                <th>確認日時</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subscribers as $subscriber)
            <tr>
                <td><input type="checkbox" name="ids[]" value="{{ $subscriber->id }}" class="subscriber-checkbox"></td>
                <td>{{ $subscriber->id }}</td>
                <td>
                    <a href="{{ route('admin.newsletter.subscribers.show', $subscriber->id) }}" class="text-indigo-600 hover:text-indigo-900">
                        {{ $subscriber->email }}
                    </a>
                </td>
                <td>
                    @if($subscriber->isUnsubscribed())
                        <span class="badge bg-secondary">解除済み</span>
                    @elseif($subscriber->isVerified())
                        <span class="badge bg-success">確認済み</span>
                    @else
                        <span class="badge bg-warning">未確認</span>
                    @endif
                </td>
                <td>{{ $subscriber->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $subscriber->verified_at ? $subscriber->verified_at->format('Y-m-d H:i') : '-' }}</td>
                <td>
                    <a href="{{ route('admin.newsletter.subscribers.edit', $subscriber->id) }}" class="btn btn-primary btn-sm">編集</a>
                    <form method="POST" action="{{ route('admin.newsletter.subscribers.destroy', $subscriber->id) }}" style="display: inline;" onsubmit="return confirm('本当に削除しますか？');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">削除</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </form>

    {{ $subscribers->links() }}

    <script>
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.subscriber-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        }

        function bulkAction(action) {
            const checkboxes = document.querySelectorAll('.subscriber-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('操作する項目を選択してください。');
                return;
            }

            const ids = Array.from(checkboxes).map(cb => cb.value);
            const form = document.getElementById('bulkForm');

            // チェックされていないチェックボックスを無効化
            const allCheckboxes = document.querySelectorAll('.subscriber-checkbox');
            allCheckboxes.forEach(checkbox => {
                if (!checkbox.checked) {
                    checkbox.disabled = true;
                }
            });

            // method属性を明示的にPOSTに設定
            form.method = 'POST';

            // 既存の_methodフィールドを削除（もしあれば）
            const existingMethod = form.querySelector('input[name="_method"]');
            if (existingMethod) {
                existingMethod.remove();
            }

            if (action === 'destroy') {
                if (!confirm('選択した' + ids.length + '件の購読者を削除しますか？')) {
                    allCheckboxes.forEach(checkbox => checkbox.disabled = false);
                    return;
                }
                form.action = '/kanrigamen/newsletter/subscribers/bulk-destroy';
            } else if (action === 'unsubscribe') {
                if (!confirm('選択した' + ids.length + '件の購読者を解除しますか？')) {
                    allCheckboxes.forEach(checkbox => checkbox.disabled = false);
                    return;
                }
                form.action = '/kanrigamen/newsletter/subscribers/bulk-unsubscribe';
            }

            form.submit();
        }
    </script>
</x-admin-layout>

