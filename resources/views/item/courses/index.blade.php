
<x-item-layout>


<x-slot name="header">
コースカテゴリ
</x-slot>

<section class="toPageArea">
    <a href="{{ route('item.course_fees.index') }}" class="lists">コースの編集</a>
</section>


<section id="courseAddArea" class="registerFormArea">
    <div class="add">
    <h3>コースカテゴリ登録</h3>
    <form method="post" action="{{ route('item.courses.store') }}">
        @csrf
        <div class="form-group">
            <select name="active" id="active">
                <option value="0">表示なし</option>
                <option value="1">表示</option>
            </select>
        </div>
        <div class="form-group">
            <input type="text" name="title" id="title" class="form-control" required placeholder="コース名">
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
                <th>コース名</th>
                <th>コメント</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($courses as $course)
            <tr data-id="{{ $course->id }}">
                <td class="handle center">
                <span class="sort-handle">
                    <span></span><span></span><span></span>
                </span>
                </td>
                <td class="size_15">{{ $course->title }}</td>
                <td>{!! nl2br(e($course->body)) !!}</td>
                <td class="action">
                    <a href="{{ route('item.courses.edit', $course) }}" class="btn btn-warning">編集</a>
                    <form action="{{ route('item.courses.destroy', $course) }}" method="POST" class="d-inline">
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
            update: function(courses, ui) {
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
                    url: "{{ route('item.courses.sort') }}", // サーバー側のルート
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
