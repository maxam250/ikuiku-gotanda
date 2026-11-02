
<x-item-layout>


<x-slot name="header">
コース
</x-slot>

<section class="toPageArea">
    <a href="{{ route('item.courses.index') }}" class="cate">コースカテゴリの編集</a>
    <a href="{{ route('item.course_fees.create') }}" class="create">新規追加</a>
</section>

<section>

    <table class="listTbl">
        <thead>
            <tr>
                <th>並替</th>
                <th>カテゴリ</th>
                <th>コース/分</th>
                <th>料金</th>
                <th>説明文</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($courseFees as $fee)
                <tr data-id="{{ $fee->id }}">
                    <td class="handle center">
                      <span class="sort-handle">
                        <span></span><span></span><span></span>
                      </span>
                    </td>
                    <td class="size_10">
                    @foreach($courses as $course)
                        {{ $fee->course_id == $course->id ? $course->title : '' }}

                    @endforeach
                    </td>
                    <td class="size_10">{{ $fee->course }}/（{{ $fee->course_time }}分）</td>
                    <td class="size_10">{{ $fee->fee }} 円</td>
                    <td>{{ $fee->text }}</td>
                    <td class="action">
                        <a href="{{ route('item.course_fees.edit', $fee) }}" class="btn btn-warning">編集</a>
                        <form action="{{ route('item.course_fees.destroy', $fee) }}" method="POST" class="d-inline">
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
                        url: "{{ route('item.course_fees.sort') }}", // サーバー側のルート
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
