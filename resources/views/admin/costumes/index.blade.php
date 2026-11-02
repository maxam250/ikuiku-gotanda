
<x-admin-layout>


<x-slot name="header">
 コスチューム管理
</x-slot>

<section class="toPageArea">
    <a href="{{ route('admin.costume_cates.index') }}" class="cate">カテゴリの編集</a>
    <a href="{{ route('admin.costumes.create') }}" class="create">新規作成</a>
    <a href="{{ route('admin.costumes.index') }}" class="lists">コスチューム一覧</a>
</section>

<section>

    @php
        $now = date('Y-m-d H:i:s');
    @endphp
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
        <tbody id="sortable">
            @foreach ($costumes as $costume)
                <tr data-id="{{ $costume->id }}">
                    <td class="handle center">
                      <span class="sort-handle">
                        <span></span><span></span><span></span>
                      </span>
                    </td>
                    <td>
                        @switch($costume->cate)
                             @case(0)
                             未設定
                             @break

                             @case(1)
                             私服
                             @break

                             @case(2)
                             水着
                             @break

                             @case(3)
                             その他
                             @break

                             @default
                             未設定
                         @endswitch</td>
                    <td>
                   @switch($costume->active)
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
                    <td>{{ $costume->name }}</td>
                    <td>
                        @if ($costume->filename)
                            <img src="{{ Storage::url('costume/' . $costume->filename) }}" alt="" width="100">
                        @else
                            なし
                        @endif
                    </td>
                    <td>{{ $costume->body }}</td>
                    <td class="action">
                        <div class="admin-btn-group">
                            <a href="{{ route('admin.costumes.edit', $costume) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <form action="{{ route('admin.costumes.destroy', $costume) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="admin-btn admin-btn-warning admin-btn-sm" onclick="return confirm('本当に削除しますか？')">
                                    <i class="fas fa-trash"></i>削除
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


</section>


<script>
    $(function() {
        // テーブルの tbody を sortable に設定
        $('#sortable').sortable({
            handle: '.sort-handle',
            update: function(costume, ui) {
                // 並び替えが完了した時に呼び出される
                let order = [];
                $('#sortable tr').each(function(index, element) {
                    order.push({
                        id: $(this).data('id'),
                        position: index + 1
                    });
                });

                // 並び替えた順序をサーバーに送信
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.costumes.sort') }}", // サーバー側のルート
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
