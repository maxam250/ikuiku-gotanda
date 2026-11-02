
<x-admin-layout>


<x-slot name="header">
        オプションカテゴリ一覧
</x-slot>

<section>

    {{-- <a href="{{ route('admin.option_cates.create') }}">新しいカテゴリを作成</a> --}}

    <section id="optionAddArea">
        <h3>オプションカテゴリ登録</h3>
        <form method="post" action="{{ route('admin.option_cates.store') }}">
            @csrf
            <div class="form-group">
                <label for="title">カテゴリ名</label>
                <input type="text" name="title" id="title" class="form-control" required>
                <button type="submit" class="btn btn-primary">登録</button>
            </div>

        </form>
    </section>



    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>タイトル</th>
                <th>説明文</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($optionCates as $cate)
                <tr data-id="{{ $cate->id }}">
                    <td>{{ $cate->id }}</td>
                    <td>{{ $cate->title }}</td>
                    <td>{!! nl2br(e($cate->body)) !!}</td>
                    <td>
                        <a href="{{ route('admin.option_cates.edit', $cate) }}" class="btn btn-warning">編集</a>
                        <form action="{{ route('admin.option_cates.destroy', $cate) }}" method="POST" style="display:inline;">
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



<script>
    $(function() {
        // テーブルの tbody を sortable に設定
        $('#sortable').sortable({
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
                    url: "{{ route('admin.option_cates.sort') }}", // サーバー側のルート
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
