
<x-item-layout>


<x-slot name="header">
オプション
</x-slot>

<section class="toPageArea">
    <a href="{{ route('item.option_cates.index') }}">カテゴリの編集</a>
    <a href="{{ route('item.options.index') }}">オプション</a>
    <a href="{{ route('item.option_pays.index') }}">オプション２</a>
</section>

<section class="registerFormArea">
    <div class="add">

        <h3>オプション登録</h3>
        <form method="post" action="{{ route('item.options.store') }}">
            @csrf
            <div class="form-group">

                <select name="cate" id="cate" class="form-control" required>
                    <option value="" disabled selected>カテゴリを選択してください</option>
                    @foreach($optionCates as $cate)
                        <option value="{{ $cate->id }}">{{ $cate->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <input type="text" name="title" id="title" class="form-control size_m" required placeholder="オプション名">
            </div>
            <div class="form-group">
                <textarea name="body" class="form-control h_3rem" placeholder="説明文"></textarea>
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
                <th>カテゴリ</th>
                <th>タイトル</th>
                <th>説明文</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($options as $option)
                <tr data-id="{{ $option->id }}">
                    <td class="handle center">
                      <span class="sort-handle">
                        <span></span><span></span><span></span>
                      </span>
                    </td>
                    <td class="size_15">
                        @foreach($optionCates as $cate)
                            {{ $option->cate == $cate->id ? $cate->title : '' }}
                        @endforeach
                    </td>
                    <td class="size_20">{{ $option->title }}</td>
                    <td>{!! nl2br(e($option->body)) !!}</td>
                    <td class="action">
                        <a href="{{ route('item.options.edit', $option) }}" class="btn btn-warning">編集</a>
                        <form action="{{ route('item.options.destroy', $option) }}" method="POST" class="d-inline">
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
                update: function(options, ui) {
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
                        url: "{{ route('item.options.sort') }}", // サーバー側のルート
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
