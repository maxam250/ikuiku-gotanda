
<x-item-layout>


<x-slot name="header">
設備カテゴリ一覧
</x-slot>


<section class="toPageArea">
    <a href="{{ route('item.equipments.index') }}" class="lists">設備の一覧</a>
</section>

<section class="contentArea">

    <section id="equipmentAddArea" class="registerFormArea">
        <div class="add">
        <h3>カテゴリ登録（エリアなど）</h3>
        <form method="post" action="{{ route('item.equipment_cates.store') }}">
            @csrf
            <div class="form-group">
                <select name="active" id="active">
                    <option value="0">表示なし</option>
                    <option value="1" selected>表示</option>
                </select>
            </div>
            <div class="form-group">
                <input type="text" name="title" id="title" class="form-control size_l" required placeholder="名称">
            </div>
            <button type="submit" class="btn btn-primary">登録</button>
        </div>
        </form>
    </section>


    <section class="listTableArea">
    <table class="listTbl">
        <thead>
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>説明文</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($equipmentCates as $equipmentCate)
                <tr data-id="{{ $equipmentCate->id }}">
                    <td class="handle center">
                      <span class="sort-handle">
                        <span></span><span></span><span></span>
                      </span>
                    </td>
                    <td class="size_10">{{ $equipmentCate->title }}</td>
                    <td>{!! nl2br(e($equipmentCate->text)) !!}</td>
                    <td class="action">
                        <a href="{{ route('item.equipment_cates.edit', $equipmentCate) }}" class="btn btn-warning">編集</a>
                        <form action="{{ route('item.equipment_cates.destroy', $equipmentCate) }}" method="POST" class="d-inline">
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

</section>



<script>
    $(function() {
        // テーブルの tbody を sortable に設定
        $('#sortable').sortable({
            handle: '.sort-handle',
            update: function(equipments, ui) {
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
                    url: "{{ route('item.equipment_cates.sort') }}", // サーバー側のルート
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
