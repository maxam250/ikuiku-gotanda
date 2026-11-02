<x-admin-layout>
    <x-slot name="header">
        ポイント管理 - {{ $member->name }}
    </x-slot>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-user me-2"></i>
            {{ $member->name }}様のポイント管理
        </h2>
    </div>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3 pt-5">
                        <h4>[{{ $member->id }}] 会員名：{{ $member->name }}　/　会員番号：{{ $member->member_no }}　[ <a href="{{ route('admin.members.edit', $member->id) }}">会員情報編集ページ</a> ]</h4>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">現在のポイント状況</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3>現在のポイント: <span class="text-4xl">{{ number_format($member->point) }}pt</span></h3>
                                </div>
                                <div class="col-md-6 text-end">
                                    <a href="{{ route('admin.member_points.index') }}" class="btn btn-secondary">
                                        会員一覧に戻る
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">ポイント操作</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.member_points.store', $member->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="points">ポイント数</label>
                                            <input type="number" class="form-control" id="points" name="points" required min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">追加/減算</label>
                                            <select class="form-control" id="status" name="status" required>
                                                <option value="add">追加</option>
                                                <option value="del">減算</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="reason">理由</label>
                                            <input type="text" class="form-control" id="reason" name="reason">
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">ポイントを更新</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">ポイント履歴</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>日付</th>
                                        <th>追加/減産</th>
                                        <th>ポイント数</th>
                                        <th>メモ</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pointHistories as $history)
                                        <tr>
                                            <td>{{ $history->created_at->format('Y/m/d H:i') }}</td>
                                            <td>
                                                @if($history->status === 'add')
                                                    <span style="color: #007bff; font-weight: bold;">＋追加</span>
                                                @else
                                                    <span style="color: #d9534f; font-weight: bold;">－減算</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($history->status === 'add')
                                                    <span style="color: #007bff;">+{{ number_format($history->point) }}</span>
                                                @else
                                                    <span style="color: #d9534f;">-{{ number_format($history->point) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.member_points.history.update', $history->id) }}" method="POST" class="d-inline-flex align-items-center">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="text" name="memo" value="{{ $history->memo }}" >
                                                    <button type="submit" class="btn-compact whitespace-nowrap">保存</button>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.member_points.history.destroy', $history->id) }}" method="POST" onsubmit="return confirm('この履歴を削除し、ポイントを元に戻します。よろしいですか？');" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-compact">削除</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center">履歴がありません</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mt-3">
                        {{ $pointHistories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
        .table-bordered th, .table-bordered td {
            padding: 0.5rem;
            vertical-align: middle;
            font-size: 0.9rem;
        }
    .btn-compact {
        display: inline-block;
        padding: 0.55rem 0.75rem;
        margin-left: 0.5rem;
        font-size: 0.95rem;
        line-height: 1.2;
        border-radius: 0.25rem;
        background: #007bff;
        color: #fff;
        border: none;
        transition: background 0.2s;
        display: inline-block;
    }
    .btn-compact:hover, .btn-compact:focus {
        background: #0056b3;
        color: #fff;
        text-decoration: none;
    }
    </style>
</x-admin-layout>
