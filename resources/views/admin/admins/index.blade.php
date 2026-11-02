<x-admin-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">管理者一覧</h3>
                        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> 新しい管理者を追加
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>名前</th>
                                        <th>メールアドレス</th>
                                        <th>ログイン名</th>
                                        <th>ロール</th>
                                        <th>ステータス</th>
                                        <th>作成日</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($admins as $admin)
                                        <tr>
                                            <td>{{ $admin->id }}</td>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->email }}</td>
                                            <td>{{ $admin->login_name }}</td>
                                            <td>
                                                <span class="badge {{ $admin->role === 'super_admin' ? 'bg-danger' : 'bg-primary' }}">
                                                    {{ $admin->role === 'super_admin' ? 'スーパー管理者' : '管理者' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $admin->status ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $admin->status ? '有効' : '無効' }}
                                                </span>
                                            </td>
                                            <td>{{ $admin->created_at->format('Y/m/d H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> 編集
                                                    </a>
                                                    @if($admin->id !== auth()->guard('admin')->id())
                                                        <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" 
                                                              onsubmit="return confirm('この管理者を削除してもよろしいですか？')" 
                                                              style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-trash"></i> 削除
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">管理者が見つかりませんでした。</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($admins->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $admins->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
