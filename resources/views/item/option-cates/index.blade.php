
<x-item-layout>


<x-slot name="header">
オプションカテゴリ
</x-slot>




<section class="toPageArea">
    <a href="{{ route('item.options.index') }}">オプション</a>
    <a href="{{ route('item.option_pays.index') }}">オプション２</a>
</section>


    <section id="optionAddArea" class="registerFormArea">
        <div class="add">
        <h3>オプションカテゴリ登録</h3>
        <div class="hint">オプションはカテゴリの登録後に編集できます</div>
        <form method="post" action="{{ route('item.option_cates.store') }}">
            @csrf
            <div>
            <input type="text" name="title" id="title" class="form-control" required placeholder="カテゴリ名">
            <button type="submit" class="btn btn-primary">登録</button>
        </div>
        </form>
        </div>
    </section>


    <section class="contentArea">


        <table class="listTbl">
        <thead>
            <tr>
                <th>並替</th>
                <th>タイトル</th>
                <th>説明文</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($optionCates as $cate)
                <tr data-id="{{ $cate->id }}">
                    <td class="handle center">
                      <span class="sort-handle">
                        <span></span><span></span><span></span>
                      </span>
                    </td>
                    <td>{{ $cate->title }}</td>
                    <td>{!! nl2br(e($cate->body)) !!}</td>
                    <td class="action">
                        <a href="{{ route('item.option_cates.edit', $cate) }}" class="btn btn-warning">編集</a>
                        <form action="{{ route('item.option_cates.destroy', $cate) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="af-btn af-btn-delete af-btn-sm" onclick="return confirm('本当に削除しますか？')">削除</button>
                        </form>
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
            update: function(option_cates, ui) {
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
                    url: "{{ route('item.option_cates.sort') }}", // サーバー側のルート
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


</x-item-layout>
