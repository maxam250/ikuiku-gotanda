
<x-admin-layout>


<x-slot name="header">
オプションカテゴリ
</x-slot>




<section class="toPageArea">
    <a href="{{ route('admin.options.index') }}" class="lists">オプション</a>
    <a href="{{ route('admin.option_pays.index') }}" class="lists">オプション(有料)</a>
</section>


    <section id="optionAddArea" class="modern-register-form">
        <div class="form-container">
            <h3>オプションカテゴリ登録</h3>
            <p class="form-hint">オプションはカテゴリの登録後に編集できます</p>
            <form method="post" action="{{ route('admin.option_cates.store') }}" class="inline-form">
                @csrf
                <div class="form-row">
                    <input type="text" name="title" id="title" class="modern-input" required placeholder="カテゴリ名">
                    <button type="submit" class="admin-btn admin-btn-submit">
                        <i class="fas fa-plus"></i>登録
                    </button>
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
                        <div class="admin-btn-group">
                            <a href="{{ route('admin.option_cates.edit', $cate) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <form action="{{ route('admin.option_cates.destroy', $cate) }}" method="POST" style="display:inline;">
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
