
<x-admin-layout>


<x-slot name="header">
ギャラリー
</x-slot>

<section>


    <div class="toPageArea">
        <a href="{{ route('admin.gallery.create') }}" class="create">新しいギャラリーを作成</a>
    </div>
    <div class="py-4">
    {{ $galleries->links() }}
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
                <th>サムネイル</th>
                <th>タイトル/内容</th>
                {{--<th>投稿者</th>--}}
                <th>キャスト</th>
                <th>公開範囲</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($galleries as $gallery)
                <tr data-id="{{ $gallery->id }}">
                    <td class="handle center">
                      <span class="sort-handle">
                        <span></span><span></span><span></span>
                      </span>
                    </td>
                    <td class="size_10{{ $gallery->active == 1 ? ' on' : ' off' }}">{{ $gallery->active == 1 ? '公開中' : '非公開' }}</td>
                    <td class="size_10">{{ $gallery->created_at }}<br>{{ $gallery->updated_at }}</td>

                    <td class="thumbnail-cell">
                        @if($gallery->images->count() > 0)
                            <img src="{{ Storage::url('gallery/' . $gallery->images->first()->filename) }}"
                                 alt="{{ $gallery->title }}"
                                 style="max-width: 80px; max-height: 80px; object-fit: cover; border-radius: 4px;">
                        @else
                            <div class="no-image">画像なし</div>
                        @endif
                    </td>

                    <td><p>{{ $gallery->title }}</p><p>{{ $gallery->description }}</p></td>
                    {{--<td class="size_10">
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
                    --}}
                    <td class="size_10">
                        @if ($gallery->item_id)
                            @foreach($items as $item)
                                @if ( $item->id == $gallery->item_id )
                                {{ $item->item_name }}
                                    @if ($item->thumbnail)
                                    <img src="{{ Storage::url($item->thumbnail->filename) }}" alt="{{ $item->item_name }}" style="max-width:100%; max-height:100px; object-fit: cover;">
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
                        <div class="admin-btn-group">
                            <a href="{{ route('admin.gallery.edit', $gallery) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <form action="{{ route('admin.gallery.destroy', $gallery) }}" method="POST" style="display:inline;">
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
    <div class="py-4">
    {{ $galleries->links() }}
    </div>
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
                    url: "{{ route('admin.gallery.sort') }}", // サーバー側のルート
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

<style>
    .thumbnail-cell {
        text-align: center;
        vertical-align: middle;
        width: 100px;
    }

    .thumbnail-cell img {
        border: 1px solid #ddd;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .no-image {
        color: #999;
        font-size: 12px;
        padding: 20px 10px;
        background: #f8f9fa;
        border: 1px dashed #ddd;
        border-radius: 4px;
    }
</style>




</x-admin-layout>
