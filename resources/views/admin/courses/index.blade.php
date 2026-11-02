
<x-admin-layout>


<x-slot name="header">
コースカテゴリ
</x-slot>

<section class="toPageArea">
    <a href="{{ route('admin.course_fees.index') }}" class="lists">コースの編集</a>
</section>


<section id="courseAddArea" class="modern-register-form">
    <div class="form-container">
        <h3>コースカテゴリ登録</h3>
        <form method="post" action="{{ route('admin.courses.store') }}" class="inline-form">
            @csrf
            <div class="form-row">
                <select name="active" id="active" class="modern-select">
                    <option value="0">表示なし</option>
                    <option value="1">表示</option>
                </select>
                <input type="text" name="title" id="title" class="modern-input" required placeholder="コース名">
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
                    <div class="admin-btn-group">
                        <a href="{{ route('admin.courses.edit', $course) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                            <i class="fas fa-edit"></i>編集
                        </a>
                        <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" style="display:inline;">
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
                    url: "{{ route('admin.courses.sort') }}", // サーバー側のルート
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
