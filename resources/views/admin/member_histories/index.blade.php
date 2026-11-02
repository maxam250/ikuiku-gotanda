<x-admin-layout>
    <x-slot name="header">
    会員編集
    </x-slot>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-user me-2"></i>
            {{ $member->name }}様の利用履歴
        </h2>
    </div>

    <!-- 統計情報 -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-red-100 border-none text-black mb-4">
                <div class="card-body">
                    <h4>来店回数</h4>
                    <p>{{ number_format($stats['total_visits']) }}回</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-green-100 border-none text-black mb-4">
                <div class="card-body">
                    <h4>累計金額</h4>
                    <p>¥{{ number_format($stats['total_amount']) }}</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-blue-100 border-none text-black mb-4">
                <div class="card-body">
                    <h4>平均利用金額</h4>
                    <p>¥{{ number_format($stats['avg_amount']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- フィルターフォーム -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            検索フィルター
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">期間（開始）</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">期間（終了）</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ステータス&nbsp;</label>
                    <select name="status" class="form-selectx">
                        <option value="">全て</option>
                        <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>予約済み</option>
                        <option value="visited" {{ request('status') === 'visited' ? 'selected' : '' }}>来店済み</option>
                        <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>キャンセル</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">並び順&nbsp;</label>
                    <select name="sort" class="form-selectx">
                        <option value="reserved_at" {{ request('sort', 'reserved_at') === 'reserved_at' ? 'selected' : '' }}>予約日時</option>
                        <option value="total_amount" {{ request('sort') === 'total_amount' ? 'selected' : '' }}>金額</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> 検索
                    </button>
                    <a href="{{ route('admin.member_histories.index', $member) }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i> リセット
                    </a>
                    <a href="{{ route('admin.member_histories.export', $member) }}" class="btn btn-success float-end">
                        <i class="fas fa-file-export me-1"></i> CSVエクスポート
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- 履歴一覧 -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-history me-1"></i>
            利用履歴一覧
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>予約日時</th>
                            <th>担当キャスト</th>
                            <th>コース</th>
                            <th>金額</th>
                            <th>ステータス</th>
                            <th>メモ</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($histories as $history)
                        <tr>
                            <td>{{ $history->reserved_at->format('Y年m月d日 H:i') }}</td>
                            <td>{{ optional($history->cast)->name ?? '未定' }}</td>
                            <td>{{ optional($history->course)->name ?? '-' }}</td>
                            <td>¥{{ number_format($history->total_amount ?? 0) }}</td>
                            <td>
                                @switch($history->status)
                                    @case('reserved')
                                        <span class="badge bg-primary">予約済み</span>
                                        @break
                                    @case('visited')
                                        <span class="badge bg-success">来店済み</span>
                                        @break
                                    @case('canceled')
                                        <span class="badge bg-danger">キャンセル</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $history->status }}</span>
                                @endswitch
                            </td>
                            <td>{{ Str::limit($history->memo, 30) }}</td>
                            <td>
                                <a href="{{ route('admin.member_histories.edit', ['member' => $member, 'history' => $history]) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> 編集
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $histories->links() }}
            </div>
        </div>
    </div>

    <div class="mb-4">
        <a href="{{ route('admin.members.edit', $member) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> 会員情報に戻る
        </a>
        <a href="{{ route('admin.member_histories.create', $member) }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> 新規予約
        </a>
    </div>
</div>

</x-admin-layout>
