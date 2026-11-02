<x-admin-layout>
    <x-slot name="header">
        共有メモ カテゴリ管理
    </x-slot>

    <div class="admin-card">
        <div class="admin-card-header d-flex justify-content-between align-items-center">
            <h2 class="admin-card-title mb-0">カテゴリ一覧</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.shared_memos.index') }}" class="admin-btn admin-btn-secondary">← 共有メモ一覧</a>
                <a href="{{ route('admin.shared_memo_categories.create') }}" class="admin-btn admin-btn-success">＋ 新規作成</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success m-3">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:80px;">並び順</th>
                        <th>カテゴリ名</th>
                        <th>説明</th>
                        <th style="width:100px;">作成日</th>
                        <th style="width:90px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="text-center">{{ $category->sort }}</td>
                            <td class="fw-bold">{{ $category->name }}</td>
                            <td class="text-muted small">{{ \Str::limit($category->description, 60) ?: '-' }}</td>
                            <td class="text-nowrap">{{ $category->created_at->format('Y/m/d') }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.shared_memo_categories.edit', $category) }}" class="admin-btn admin-btn-info admin-btn-xs">編集</a>
                                <form action="{{ route('admin.shared_memo_categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('削除しますか？')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="admin-btn admin-btn-danger admin-btn-xs">削除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">カテゴリがありません</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
