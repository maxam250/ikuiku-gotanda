
<x-admin-layout>


<x-slot name="header">
ランキングカテゴリ
</x-slot>


<section class="toPageArea">
    <a href="{{ route('admin.ranks.index') }}" class="lists">ランキング管理</a>
</section>


<section class="contentArea">

    <section id="rankAddArea" class="registerFormArea">
        <div class="add">
        <h3>ランキングカテゴリ登録</h3>
        <div class="hint">ランキングはカテゴリの登録後に編集できます</div>
        <form method="post" action="{{ route('admin.rank_cates.store') }}">
            @csrf
            <div>
            <input type="text" name="title" id="title" class="form-control" required placeholder="カテゴリ名">
            <button type="submit" class="btn btn-primary">登録</button>
        </div>
        </form>
        </div>
    </section>



    <section class="listTableArea">

        <table class="listTbl">
            <thead>
                <tr>
                    <th>並替</th>
                    <th>公開</th>
                    <th>タイトル</th>
                    <th>説明文</th>
                    <th>アクション</th>
                </tr>
            </thead>
            <tbody id="sortable">
                @foreach ($rankCates as $cate)
                    <tr data-id="{{ $cate->id }}">
                        <td class="handle center">
                          <span class="sort-handle">
                            <span></span><span></span><span></span>
                          </span>
                        </td>
                        <td class="center">
                            @if($cate->active)
                                <span class="badge badge-success">公開</span>
                            @else
                                <span class="badge badge-secondary">非公開</span>
                            @endif
                        </td>
                        <td>{{ $cate->title }}</td>
                        <td>{!! nl2br(e($cate->body)) !!}</td>
                        <td class="action">
                            <a href="{{ route('admin.rank_cates.edit', $cate) }}" class="btn btn-warning">編集</a>
                            <form action="{{ route('admin.rank_cates.destroy', $cate) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </section>

</section>



<script>
    $(function() {
        // テーブルの tbody を sortable に設定
        $('#sortable').sortable({
            handle: '.sort-handle',
            update: function(rank_cates, ui) {
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
                    url: "{{ route('admin.rank_cates.sort') }}", // サーバー側のルート
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
