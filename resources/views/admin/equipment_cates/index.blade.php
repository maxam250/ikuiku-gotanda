
<x-admin-layout>


<x-slot name="header">
スタジオカテゴリ一覧
</x-slot>


<section class="toPageArea">
    <a href="{{ route('admin.equipments.index') }}" class="lists">スタジオの一覧</a>
</section>

<section class="contentArea">

    <section id="equipmentAddArea" class="modern-register-form">
        <div class="form-container">
            <h3>カテゴリ登録（エリアなど）</h3>
            <form method="post" action="{{ route('admin.equipment_cates.store') }}" class="inline-form">
                @csrf
                <div class="form-row">
                    <select name="active" id="active" class="modern-select">
                        <option value="0">表示なし</option>
                        <option value="1" selected>表示</option>
                    </select>
                    <input type="text" name="title" id="title" class="modern-input" required placeholder="名称">
                    <button type="submit" class="admin-btn admin-btn-submit">
                        <i class="fas fa-plus"></i>登録
                    </button>
                </div>
            </form>
        </div>
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
                        <div class="admin-btn-group">
                            <a href="{{ route('admin.equipment_cates.edit', $equipmentCate) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <form action="{{ route('admin.equipment_cates.destroy', $equipmentCate) }}" method="POST" style="display:inline;">
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
                    url: "{{ route('admin.equipment_cates.sort') }}", // サーバー側のルート
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
