<x-admin-layout>

<x-slot name="header">
 {{ $item->item_name }} のマイコスチューム管理
</x-slot>

<section class="toPageArea">
    <a href="{{ route('admin.mycostumes.index') }}" class="lists">キャスト一覧に戻る</a>
    <a href="{{ route('admin.mycostumes.create', ['item_id' => $item->id]) }}" class="create">新規作成</a>
</section>

<section>
    <div class="cast-header">
        <h2>{{ $item->item_name }}</h2>
        <div class="cast-summary">
            <span class="badge badge-primary">総数: {{ $mycostumes->count() }}</span>
            @foreach ($categories as $category)
                @php
                    $categoryCount = $mycostumes->where('mycostume_cate_id', $category->id)->count();
                @endphp
                @if ($categoryCount > 0)
                    <span class="badge badge-secondary">{{ $category->name }}: {{ $categoryCount }}</span>
                @endif
            @endforeach
        </div>
    </div>

    @if ($mycostumes->count() > 0)
        <table class="listTbl">
            <thead>
                <tr>
                    <th>並替</th>
                    <th>カテゴリ</th>
                    <th>表示</th>
                    <th>コス名</th>
                    <th>画像</th>
                    <th>説明文</th>
                    <th>アクション</th>
                </tr>
            </thead>
            <tbody class="sortable" data-item-id="{{ $item->id }}">
                @foreach ($mycostumes as $mycostume)
                    <tr data-id="{{ $mycostume->id }}">
                        <td class="handle center">
                          <span class="sort-handle">
                            <span></span><span></span><span></span>
                          </span>
                        </td>
                        <td>
                            {{ $mycostume->category ? $mycostume->category->name : '未設定' }}
                        </td>
                        <td>
                           @switch($mycostume->active)
                                @case(0)
                                非表示
                                @break

                                @case(1)
                                表示
                                @break

                                @default
                                未設定
                            @endswitch
                        </td>
                        <td>{{ $mycostume->name }}</td>
                        <td>
                            @if ($mycostume->filename)
                                <img src="{{ Storage::url('mycostume/' . $mycostume->filename) }}" alt="" width="100">
                            @else
                                なし
                            @endif
                        </td>
                        <td>{{ Str::limit($mycostume->body, 50) }}</td>
                        <td class="action">
                            <a href="{{ route('admin.mycostumes.edit', $mycostume) }}" class="btn btn-warning">編集</a>
                            <form action="{{ route('admin.mycostumes.destroy', $mycostume) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <p class="text-muted">このキャストのマイコスチュームはまだ登録されていません。</p>
            <a href="{{ route('admin.mycostumes.create', ['item_id' => $item->id]) }}" class="btn btn-primary">新規作成</a>
        </div>
    @endif
</section>

<style>
.cast-header {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #dee2e6;
}

.cast-header h2 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 1.5em;
}

.cast-summary {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.9em;
}

.badge-primary {
    background-color: #007bff;
    color: white;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.sortable {
    cursor: move;
}

.sort-handle {
    display: inline-block;
    width: 20px;
    height: 20px;
    cursor: move;
}

.sort-handle span {
    display: block;
    width: 100%;
    height: 2px;
    background-color: #999;
    margin: 2px 0;
}

.empty-state {
    text-align: center;
    padding: 40px;
    background-color: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.empty-state p {
    margin-bottom: 20px;
    font-size: 1.1em;
}

.btn {
    display: inline-block;
    font-weight: 400;
    text-align: center;
    vertical-align: middle;
    user-select: none;
    border: 1px solid transparent;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    text-decoration: none;
    cursor: pointer;
    margin: 0 2px;
}

.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    color: #fff;
    background-color: #0069d9;
    border-color: #0062cc;
}

.btn-warning {
    color: #212529;
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-warning:hover {
    color: #212529;
    background-color: #e0a800;
    border-color: #d39e00;
}

.btn-danger {
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    color: #fff;
    background-color: #c82333;
    border-color: #bd2130;
}
</style>

<script>
    $(function() {
        // テーブルを sortable に設定
        $('.sortable').sortable({
            handle: '.sort-handle',
            update: function(event, ui) {
                let order = [];
                $(this).find('tr').each(function(index, element) {
                    order.push({
                        id: $(this).data('id'),
                        position: index + 1
                    });
                });

                // 並び替えた順序をサーバーに送信
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.mycostumes.sort') }}",
                    data: {
                        order: order,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('並び替えが保存されました');
                    },
                    error: function(xhr) {
                        alert('並び替えの保存に失敗しました');
                    }
                });
            }
        });
    });
</script>

</x-admin-layout>
