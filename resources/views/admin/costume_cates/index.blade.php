
<x-admin-layout>


    <x-slot name="header">
    コスカテゴリ
    </x-slot>


    <section class="toPageArea">
        <a href="{{ route('admin.costumes.index') }}" class="lists">コスチューム</a>
    </section>


    <section class="contentArea">

        <section id="costumeAddArea" class="registerFormArea">
            <div class="add">
            <h3>コスチュームカテゴリ登録</h3>
            <div class="hint">コスチュームはカテゴリの登録後に編集できます</div>
            <form method="post" action="{{ route('admin.costume_cates.store') }}">
                @csrf
                <div>
                <input type="text" name="title" id="title" class="form-control" required placeholder="カテゴリ名">
                <button type="submit" class="btn btn-primary">登録</button>
            </div>
            </form>
            </div>
        </section>



        <section class="listTableArea">

            <table class="listTbl">
                <thead>
                    <tr>
                        <th>並替</th>
                        <th>タイトル</th>
                        <th>説明文</th>
                        <th>料金</th>
                        <th>アクション</th>
                    </tr>
                </thead>
                <tbody id="sortable">
                    @foreach ($costumeCates as $cate)
                        <tr data-id="{{ $cate->id }}">
                            <td class="handle center">
                              <span class="sort-handle">
                                <span></span><span></span><span></span>
                              </span>
                            </td>
                            <td class="size_10">{{ $cate->title }}</td>
                            <td>{!! nl2br(e($cate->body)) !!}</td>
                            <td class="size_10">{{ $cate->price ? number_format($cate->price).'円' : '' }}</td>
                            <td class="action">
                                <a href="{{ route('admin.costume_cates.edit', $cate) }}" class="btn btn-warning">編集</a>
                                <form action="{{ route('admin.costume_cates.destroy', $cate) }}" method="POST" style="display:inline;">
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

    </section>



    <script>
        $(function() {
            // テーブルの tbody を sortable に設定
            $('#sortable').sortable({
                handle: '.sort-handle',
                update: function(costume_cates, ui) {
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
                        url: "{{ route('admin.costume_cates.sort') }}", // サーバー側のルート
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
