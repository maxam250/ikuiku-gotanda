
<x-item-layout>


<x-slot name="header">
設備
</x-slot>

<section class="toPageArea">
    <a href="{{ route('item.equipment_cates.index') }}" class="cate">カテゴリの編集</a>
    <a href="{{ route('item.equipments.create') }}" class="create">新規追加</a>
</section>

<section>

    <table class="listTbl">
        <thead>
            <tr>
                <th>並替</th>
                <th>カテゴリ</th>
                <th>title</th>
                <th>画像</th>
                <th>text1</th>
                <th>text2</th>
                <th>text3</th>
                <th>text4</th>
                <th>text5</th>
                <th>flag1</th>
                <th>flag2</th>
                <th>flag3</th>
                <th>flag4</th>
                <th>flag5</th>
                <th>link</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach($equipments as $equipment)
                <tr data-id="{{ $equipment->id }}">
                    <td class="handle center">
                      <span class="sort-handle">
                        <span></span><span></span><span></span>
                      </span>
                    </td>
                    <td>
                    @foreach ($equipmentCates as $cate)
                        {{ $cate->id == $equipment->cate_id ? $cate->title : '' }}

                    @endforeach
                    </td>
                    <td>{{ $equipment->title }} </td>
                    <td class="size_10">
                        @if ($equipment->filename)
                            <img src="{{ asset('uploads/equipment/' . $equipment->filename) }}" alt="equipment Image">
                        @else
                            なし
                        @endif
                    </td>
                    <td>{{ $equipment->text1 }} </td>
                    <td>{{ $equipment->text2 }}</td>
                    <td>{{ $equipment->text3 }}</td>
                    <td>{{ $equipment->text4 }}</td>
                    <td>{{ $equipment->text5 }}</td>
                    <td>{{ $equipment->flag1 == 1 ? '○': '×' }}</td>
                    <td>{{ $equipment->flag2 == 1 ? '○': '×' }}</td>
                    <td>{{ $equipment->flag3 == 1 ? '○': '×' }}</td>
                    <td>{{ $equipment->flag4 == 1 ? '○': '×' }}</td>
                    <td>{{ $equipment->fla5 == 1 ? '○': '×' }}</td>
                    <td>{!! $equipment->link ? '<a href="' . $equipment->link . '" target="_blank"><i class="las la-external-link-alt"></i></a>' : '' !!}
                    </td>
                    <td class="action">
                        <a href="{{ route('item.equipments.edit', $equipment->id) }}" class="btn btn-warning">編集</a>
                        <form action="{{ route('item.equipments.destroy',  $equipment->id) }}" method="POST" class="d-inline">
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
                update: function(equipment, ui) {
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
                        url: "{{ route('item.equipments.sort') }}", // サーバー側のルート
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
