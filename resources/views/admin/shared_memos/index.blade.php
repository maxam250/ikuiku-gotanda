<x-admin-layout>
    <x-slot name="header">
        共有メモ管理
    </x-slot>

    <div class="admin-card">
        <div class="admin-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="admin-card-title mb-0">共有メモ一覧</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.shared_memo_categories.index') }}" class="admin-btn admin-btn-secondary">カテゴリ管理</a>
                <a href="{{ route('admin.shared_memos.create') }}" class="admin-btn admin-btn-success">＋ 新規登録</a>
            </div>
        </div>

        <div class="admin-card-body border-bottom pb-4 mb-4">
            <form method="GET" action="{{ route('admin.shared_memos.index') }}" class="af-search">
                <div class="af-search-row">
                    <div class="af-search-field" style="min-width:160px;">
                        <label>キーワード</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="af-input" placeholder="タイトル・メモで検索">
                    </div>
                    <div class="af-search-field" style="min-width:120px;">
                        <label>カテゴリ</label>
                        <select name="category_id" class="af-select">
                            <option value="">すべて</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="af-search-field" style="min-width:110px;">
                        <label>スタッフ</label>
                        <select name="staff_id" class="af-select">
                            <option value="">すべて</option>
                            @foreach($staffs as $staff)
                                <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="af-search-field" style="min-width:130px;">
                        <label>期間（開始）</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="af-input">
                    </div>
                    <div class="af-search-field" style="min-width:130px;">
                        <label>期間（終了）</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="af-input">
                    </div>
                </div>
                <div class="af-btn-row af-btn-row-center" style="margin-top:0.5rem;">
                    <button type="submit" class="af-btn af-btn-search">
                        <i class="fas fa-search"></i> 検索
                    </button>
                    <a href="{{ route('admin.shared_memos.index') }}" class="af-btn af-btn-reset">
                        <i class="fas fa-redo"></i> リセット
                    </a>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:100px;">日付</th>
                        <th style="width:120px;">カテゴリ</th>
                        <th style="width:100px;">スタッフ</th>
                        <th>タイトル</th>
                        <th>メモ</th>
                        <th style="width:90px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($memos as $memo)
                        <tr>
                            <td class="text-nowrap">{{ $memo->memo_date->format('Y/m/d') }}</td>
                            <td>
                                @if($memo->category)
                                    <span class="badge bg-secondary">{{ $memo->category->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $memo->staff->name ?? '-' }}</td>
                            <td>{{ $memo->title ?: '-' }}</td>
                            <td style="max-width:300px; white-space:pre-wrap; font-size:0.82rem;">{{ \Str::limit($memo->body, 80) }}
                                @if($memo->url)
                                    <br><a href="{{ $memo->url }}" target="_blank" class="text-primary small">{{ \Str::limit($memo->url, 40) }}</a>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.shared_memos.edit', $memo) }}" class="admin-btn admin-btn-info admin-btn-xs">編集</a>
                                <form action="{{ route('admin.shared_memos.destroy', $memo) }}" method="POST" class="d-inline" onsubmit="return confirm('削除しますか？')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="admin-btn admin-btn-danger admin-btn-xs">削除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">共有メモがありません</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $memos->withQueryString()->links() }}</div>
    </div>
</x-admin-layout>
