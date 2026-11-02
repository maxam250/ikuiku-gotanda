
<x-admin-layout>


<x-slot name="header">
オプション
</x-slot>

<section class="toPageArea">
    <a href="{{ route('admin.option_cates.index') }}" class="cate">カテゴリの編集</a>
    <a href="{{ route('admin.options.index') }}" class="lists">オプション</a>
    <a href="{{ route('admin.option_pays.index') }}" class="lists">オプション(有料)</a>
</section>

<section class="modern-register-form">
    <div class="form-container">
        <h3>オプション登録</h3>
        <form method="post" action="{{ route('admin.options.store') }}" class="inline-form" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <select name="cate" id="cate" class="modern-select">
                    <option value="" selected>カテゴリを選択してください</option>
                    @foreach($optionCates as $cate)
                        <option value="{{ $cate->id }}">{{ $cate->title }}</option>
                    @endforeach
                </select>
                <input type="text" name="title" id="title" class="modern-input" required placeholder="オプション名">
                <textarea name="body" class="modern-textarea" placeholder="説明文"></textarea>
                <input type="file" name="filename" class="modern-file-input" accept="image/*" style="grid-column: 1 / -1;">
                <small class="form-text text-muted" style="grid-column: 1 / -1;">対応形式: JPEG, PNG, JPG, GIF (最大2MB)</small>
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
                    <td class="size_20">
                        {{ $option->title }}
                        @if($option->filename)
                            <br><img src="{{ Storage::url('options/' . $option->filename) }}" alt="{{ $option->title }}" style="max-width: 100px; max-height: 60px; margin-top: 5px;">
                        @endif
                    </td>
                    <td>{!! nl2br(e($option->body)) !!}</td>
                    <td class="action">
                        <div class="admin-btn-group">
                            <a href="{{ route('admin.options.edit', $option) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <form action="{{ route('admin.options.destroy', $option) }}" method="POST" style="display:inline;">
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
                        url: "{{ route('admin.options.sort') }}", // サーバー側のルート
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
