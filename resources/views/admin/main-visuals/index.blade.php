<x-admin-layout>

    <x-slot name="header">
    メインビジュアル管理
    </x-slot>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>メインビジュアル管理</h1>
                <a href="{{ route('admin.main-visuals.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> 新規作成
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($mainVisuals->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">メインビジュアル一覧</h5>
                    <small class="text-muted">ドラッグ&ドロップで並び順を変更できます</small>
                </div>
                <div class="card-body p-0">
                    <div id="sortable-list" class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">順序</th>
                                    <th width="100">PC画像</th>
                                    <th width="100">スマホ画像</th>
                                    <th>タイトル</th>
                                    <th width="100">表示設定</th>
                                    <th width="120">表示期間</th>
                                    <th width="100">状態</th>
                                    <th width="150">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mainVisuals as $visual)
                                <tr data-id="{{ $visual->id }}" class="sortable-row">
                                    <td class="text-center">
                                        <i class="fas fa-grip-vertical text-muted drag-handle"></i>
                                        <span class="sort-order">{{ $visual->sort_order }}</span>
                                    </td>
                                    <td>
                                        @if($visual->pc_image_path)
                                            <img src="{{ Storage::url($visual->pc_image_path) }}"
                                                 alt="PC画像" class="img-thumbnail" style="width: 80px; height: 45px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                 style="width: 80px; height: 45px; border-radius: 4px;">
                                                <small class="text-muted">なし</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($visual->mobile_image_path)
                                            <img src="{{ Storage::url($visual->mobile_image_path) }}"
                                                 alt="スマホ画像" class="img-thumbnail" style="width: 80px; height: 45px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                 style="width: 80px; height: 45px; border-radius: 4px;">
                                                <small class="text-muted">なし</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $visual->title }}</strong>
                                        @if($visual->description)
                                            <br><small class="text-muted">{{ Str::limit($visual->description, 50) }}</small>
                                        @endif
                                        @if($visual->link_url)
                                            <br><small class="text-info"><i class="fas fa-link"></i> リンク設定済み</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            @if($visual->show_on_pc)
                                                <span class="badge bg-primary">PC</span>
                                            @endif
                                            @if($visual->show_on_mobile)
                                                <span class="badge bg-info">スマホ</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($visual->start_date || $visual->end_date)
                                            <small>
                                                @if($visual->start_date)
                                                    {{ $visual->start_date->format('m/d') }}〜
                                                @endif
                                                @if($visual->end_date)
                                                    {{ $visual->end_date->format('m/d') }}
                                                @endif
                                            </small>
                                        @else
                                            <small class="text-muted">常時表示</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($visual->is_active)
                                            <span class="badge bg-success">表示中</span>
                                        @else
                                            <span class="badge bg-secondary">非表示</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="admin-btn-group">
                                            <a href="{{ route('admin.main-visuals.edit', $visual) }}"
                                               class="admin-btn admin-btn-secondary admin-btn-sm">
                                                <i class="fas fa-edit"></i>編集
                                            </a>
                                            <form action="{{ route('admin.main-visuals.destroy', $visual) }}"
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="admin-btn admin-btn-warning admin-btn-sm"
                                                        onclick="return confirm('削除してもよろしいですか？')">
                                                    <i class="fas fa-trash"></i>削除
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-image fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">メインビジュアルが登録されていません</h5>
                    <p class="text-muted">新規作成ボタンから最初のメインビジュアルを作成してください。</p>
                    <a href="{{ route('admin.main-visuals.create') }}" class="admin-btn admin-btn-submit">
                        <i class="fas fa-plus"></i>新規作成
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.querySelector('#sortable-list tbody');
    if (tbody) {
        const sortable = new Sortable(tbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = [];
                const rows = tbody.querySelectorAll('.sortable-row');

                rows.forEach((row, index) => {
                    items.push({
                        id: row.dataset.id,
                        position: index
                    });

                    // 表示順序を更新
                    row.querySelector('.sort-order').textContent = index;
                });

                // サーバーに並び順を送信
                fetch('{{ route("admin.main-visuals.sort") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ items: items })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('並び順が更新されました');
                    }
                })
                .catch(error => {
                    console.error('エラー:', error);
                });
            }
        });
    }
});
</script>
@endpush
</x-admin-layout>
