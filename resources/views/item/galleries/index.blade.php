
<x-item-layout>


<x-slot name="header">
ギャラリー
</x-slot>

<section>


    <div class="toPageArea">
        <a href="{{ route('item.gallery.create') }}" class="create">新しいギャラリーを作成</a>
    </div>
    @php
        $now = date('Y-m-d H:i:s');
    @endphp
    <table class="listTbl">
        <thead>
            <tr>
                <th>並替</th>
                <th>公開/非公開</th>
                <th>登録日/ 更新日</th>
                <th>タイトル/内容</th>
                <th>投稿者</th>
                <th>モデル</th>
                <th>公開範囲</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($gallaries as $gallery)
                <tr data-id="{{ $gallery->id }}">
                    <td class="handle center">
                      <span class="sort-handle">
                        <span></span><span></span><span></span>
                      </span>
                    </td>
                    <td class="size_10{{ $gallery->active == 1 ? ' on' : ' off' }}">{{ $gallery->active == 1 ? '公開中' : '非公開' }}</td>
                    <td class="size_10">{{ $gallery->created_at }}<br>{{ $gallery->updated_at }}</td>

                    <td><p>{{ $gallery->title }}</p><p>{{ $gallery->description }}</p></td>
                    <td class="size_10">
                        @if ($gallery->member_id)
                            @foreach($members as $member)
                                @if ( $member->id == $gallery->member_id )
                                {{ $member->name }}
                                @endif
                            @endforeach
                        @else
                            なし
                        @endif
                    </td>

                    <td class="size_10">
                        @if ($gallery->item_id)
                            @foreach($items as $item)
                                @if ( $item->id == $gallery->item_id )
                                {{ $item->item_name }}
                                    @if ($item->thumbnail)
                                    <img src="{{ asset('uploads/' . $item->thumbnail->filename)}}" alt="{{ $item->item_name }}">
                                    @else
                                    <img src="{{ asset('images/dmy.jpg') }}" alt="{{ $item->item_name }}">
                                    @endif
                                @endif
                            @endforeach
                        @else
                            なし
                        @endif
                    </td>

                    <td class="size_10{{ $gallery->flag == 0 ? '' : $gallery->flag }}">
                        @switch($gallery->flag)
                            @case(0)
                                全員
                                @break

                            @case(1)
                                ログインユーザー
                                @break

                            @case(2)
                                限定公開
                                @break

                            @default
                                全員
                        @endswitch
                    </td>
                    <td class="action">
                        <a href="{{ route('item.gallery.edit', $gallery) }}" class="btn btn-warning">編集</a>
                        <form action="{{ route('item.gallery.destroy', $gallery) }}" method="POST" class="d-inline">
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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
    $(function() {
        // テーブルの tbody を sortable に設定
        $('#sortable').sortable({
            handle: '.sort-handle', // ドラッグハンドルを .sort-handle クラスの要素に限定
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
                    url: "{{ route('item.gallery.sort') }}", // サーバー側のルート
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
