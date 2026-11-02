
<x-item-layout>


<x-slot name="header">
キャストアイコン
</x-slot>


<section id="icon_itemAddArea" class="registerFormArea">
    <div class="add">
        <h3>キャストアイコン登録</h3>
        <form method="post" action="{{ route('item.icon_items.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <input type="text" name="title" id="title" class="form-control" required placeholder="アイコン名">
            </div>
            <div class="form-group">
                <input type="file" name="filename" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-primary">登録</button>

        </form>
    </div>
</section>



<section>

    <table class="listTbl">
        <thead>
            <tr>
                <th>並替</th>
                <th>アイコン名</th>
                <th>アイコン画像</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($icon_items as $icon_item)
                <tr data-id="{{ $icon_item->id }}">
                    <td class="handle center">
                        <span class="sort-handle">
                        <span></span><span></span><span></span>
                        </span>
                    </td>
                    <td class="size_15">{{ $icon_item->title }}</td>
                    <td>@if ($icon_item->filename)
                            <img src="{{ asset('uploads/icon_item/' . $icon_item->filename) }}" alt="{{ $icon_item->title }}" class="w_100px">
                        @else
                            なし
                        @endif</td>
                    <td class="action">
                        <a href="{{ route('item.icon_items.edit', $icon_item) }}" class="af-btn af-btn-edit af-btn-sm">編集</a>
                        <form action="{{ route('item.icon_items.destroy', $icon_item) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="af-btn af-btn-delete af-btn-sm" onclick="return confirm('本当に削除しますか？')">削除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <script>
        $(function() {
            // テーブルの tbody を sortable に設定
            $('#sortable').sortable({
                handle: '.sort-handle',
                update: function(icon_items, ui) {
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
                        url: "{{ route('item.icon_items.sort') }}", // サーバー側のルート
                        data: {
                            order: order,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // alert('並び替えが保存されました');
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
