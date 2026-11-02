
<x-admin-layout>


<x-slot name="header">
外部リンク
</x-slot>

<section>

    <div class="toPageArea">
        <a href="{{ route('admin.links.create') }}"  class="create">新しいリンクを追加</a>
    </div>

    @php
        $now = date('Y-m-d H:i:s');
    @endphp
    <table class="listTbl">
        <thead>
            <tr>
                <th>並替</th>
                <th>画像</th>
                <th>サイト名</th>
                <th>サイズ</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($links as $link)
                <tr data-id="{{ $link->id }}">
                    <td class="handle center">
                      <span class="sort-handle">
                        <span></span><span></span><span></span>
                      </span>
                    </td>
                    <td class="size_10">
                        @if ($link->fullcode)
                            <div>{!! $link->fullcode !!}</div>

                        @elseif ($link->filename)
                            <img src="{{ Storage::url('link/' . $link->filename) }}" alt="links Image" width="100">
                        @else
                            なし
                        @endif
                    </td>
                    <td>{{ $link->title }}</td>
                    <td class="size_8">
                        @if (  $link->position == 0 )
                        フリー最上段
                        @elseif ( $link->position == 1 )
                        468x60
                        @elseif ( $link->position == 2 )
                        200x40
                        @elseif ( $link->position == 3 )
                        88x31
                        @elseif ( $link->position == 4 )
                        テキスト
                        @elseif ( $link->position == 5 )
                        フリー最下段
                        @else
                        未設定
                        @endif
                    </td>
                    <td class="action">
                        <div class="admin-btn-group">
                            <a href="{{ route('admin.links.edit', $link) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <form action="{{ route('admin.links.destroy', $link) }}" method="POST" style="display:inline;">
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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
    $(function() {
        // テーブルの tbody を sortable に設定
        $('#sortable').sortable({
            handle: '.sort-handle',
            update: function(event, ui) {
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
                    url: "{{ route('admin.links.sort') }}", // サーバー側のルート
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
