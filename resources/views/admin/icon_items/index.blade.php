
<x-admin-layout>


<x-slot name="header">
キャストアイコン
</x-slot>


<section id="icon_itemAddArea" class="modern-register-form">
    <div class="form-container">
        <h3>キャストアイコン登録</h3>
        <form method="post" action="{{ route('admin.icon_items.store') }}" enctype="multipart/form-data" class="inline-form">
            @csrf
            <div class="form-row">
                <input type="text" name="title" id="title" class="modern-input" required placeholder="アイコン名">
                <input type="file" name="filename" class="modern-file-input">
                <button type="submit" class="admin-btn admin-btn-submit">
                    <i class="fas fa-plus"></i>登録
                </button>
            </div>
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
                            <img src="{{ Storage::url('icon_item/' . $icon_item->filename) }}" alt="{{ $icon_item->title }}" style="max-width:100px;">
                        @else
                            なし
                        @endif</td>
                    <td class="action">
                        <div class="admin-btn-group">
                            <a href="{{ route('admin.icon_items.edit', $icon_item) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <form action="{{ route('admin.icon_items.destroy', $icon_item) }}" method="POST" style="display:inline;">
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
                        url: "{{ route('admin.icon_items.sort') }}", // サーバー側のルート
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

</x-admin-layout>
