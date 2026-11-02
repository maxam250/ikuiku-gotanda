
<x-item-layout>


<x-slot name="header">
プロフィール
</x-slot>


<section id="profileAddArea" class="registerFormArea">
    <div class="add">
        <h3>プロフィール登録</h3>
        <form method="post" action="{{ route('item.profiles.store') }}">
            @csrf
            <div class="form-group">
                <input type="text" name="title" id="title" class="form-control" required placeholder="項目名">
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
                <th>タイトル</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($profiles as $profile)
                <tr data-id="{{ $profile->id }}">
                    <td class="handle center">
                        <span class="sort-handle">
                        <span></span><span></span><span></span>
                        </span>
                    </td>
                    <td>{{ $profile->title }}</td>
                    <td class="action">
                        <a href="{{ route('item.profiles.edit', $profile) }}" class="btn btn-warning">編集</a>
                        <form action="{{ route('item.profiles.destroy', $profile) }}" method="POST" class="d-inline">
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
                update: function(profiles, ui) {
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
                        url: "{{ route('item.profiles.sort') }}", // サーバー側のルート
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
