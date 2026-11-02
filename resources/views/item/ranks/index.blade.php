
<x-item-layout>


<x-slot name="header">
ランキング一覧
</x-slot>

<section class="toPageArea">
    <a href="{{ route('item.rank_cates.index') }}" class="cate">カテゴリの編集</a>
    <a href="{{ route('item.ranks.create') }}" class="create">新規作成</a>
    <a href="{{ route('item.ranks.index') }}" class="lists">ランキング一覧</a>
</section>

<section class="listTableArea">

    {{-- <a href="{{ route('item.ranks.create') }}">新しいランキングを作成</a> --}}


    <table class="listTbl">
        <thead>
            <tr>
                <th>並替</th>
                <th>タイトル / ランキング</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($ranks as $rank)
                <tr data-id="{{ $rank->id }}">
                    <td class="handle center">
                      <span class="sort-handle">
                        <span></span><span></span><span></span>
                      </span>
                    </td>
                    <td>
                        <div class="title">
                        <strong>{{ $rank->title }}</strong><span class="term">{{ $rank->term_start ? "期間：".$rank->term_start->format('Y/m/d') : '' }}{{ $rank->term_end ? $rank->term_end->format('～Y/m/d') : '' }}</span>
                        <p>{!! nl2br(e($rank->body)) !!}</p>
                        </div>

                        <ul class="list flex">
                            @foreach($rank->itemRanks as $itemRank)
                                <li>
                                    <p class="rank">No. {{ $itemRank->no }}</p>

                                    <!-- アイテム情報を表示 -->
                                    @if ($itemRank->items)
                                    <p class="img">
                                        @if ($itemRank->items->thumbnail)
                                            {{-- <img src="{{ asset('uploads/' . $itemRank->items->thumbnail->filename) }}" alt="" class="w_100px ml-2"> --}}

                                            <img src="{{ $itemRank->items->thumbnail && $itemRank->items->thumbnail->filename
                                            ? asset('uploads/' .$itemRank->items->thumbnail->filename)
                                            : asset('images/dmy.jpg') }}"
                                            alt="キャスト画像">
                                        @else
                                            <img src="{{ asset('images/dmy.jpg') }}" alt="">
                                        @endif
                                    </p>
                                    <p class="name">{{ $itemRank->items->item_name }}</p>
                                    @else
                                        --
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="action">
                        <a href="{{ route('item.ranks.edit', $rank) }}" class="btn btn-warning">編集</a>
                        <form action="{{ route('item.ranks.destroy', $rank) }}" method="POST" class="d-inline">
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
                update: function(ranks, ui) {
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
                        url: "{{ route('item.ranks.sort') }}", // サーバー側のルート
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
