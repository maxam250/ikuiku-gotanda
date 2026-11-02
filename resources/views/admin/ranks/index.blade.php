
<x-admin-layout>

<x-slot name="header">
ランキング一覧
</x-slot>

<section class="toPageArea">
    <a href="{{ route('admin.rank_cates.index') }}" class="cate">カテゴリの編集</a>
    <a href="{{ route('admin.ranks.create') }}" class="create">新規作成</a>
    <a href="{{ route('admin.ranks.index') }}" class="lists">ランキング一覧</a>
</section>

<section class="listTableArea">

    @foreach ($rankCates as $cateId => $cate)
        @if (isset($ranks[$cateId]) && $ranks[$cateId]->isNotEmpty())
        <div class="rank-cate-group">
            <h3 class="rank-cate-heading">{{ $cate->title }}</h3>
            <table class="listTbl listTbl--compact">
                <thead>
                    <tr>
                        <th style="width:36px;">並替</th>
                        <th>タイトル</th>
                        <th>ランキング</th>
                        <th style="width:100px;">アクション</th>
                    </tr>
                </thead>
                <tbody class="sortable" data-cate="{{ $cateId }}">
                    @foreach ($ranks[$cateId] as $rank)
                    <tr data-id="{{ $rank->id }}">
                        <td class="handle center">
                            <span class="sort-handle"><span></span><span></span><span></span></span>
                        </td>
                        <td>
                            <strong>{{ $rank->title }}</strong>
                            @if ($rank->term_start || $rank->term_end)
                            <span class="term" style="font-size:0.8em;color:#888;margin-left:6px;">
                                {{ $rank->term_start ? $rank->term_start->format('Y/m/d') : '' }}{{ $rank->term_end ? '〜'.$rank->term_end->format('Y/m/d') : '' }}
                            </span>
                            @endif
                        </td>
                        <td>
                            <ol class="rank-inline-list">
                                @foreach ($rank->itemRanks as $itemRank)
                                <li>
                                    @if ($itemRank->items)
                                        <span class="rank-no">{{ $itemRank->no }}位</span>
                                        <span class="rank-name">{{ $itemRank->items->item_name }}</span>
                                    @else
                                        <span class="rank-no">{{ $itemRank->no }}位</span><span class="rank-name">--</span>
                                    @endif
                                </li>
                                @endforeach
                            </ol>
                        </td>
                        <td class="action">
                            <div class="admin-btn-group">
                                <a href="{{ route('admin.ranks.edit', $rank) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                    <i class="fas fa-edit"></i>編集
                                </a>
                                <form action="{{ route('admin.ranks.destroy', $rank) }}" method="POST" style="display:inline;">
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
        </div>
        @endif
    @endforeach

    {{-- カテゴリなしのランク --}}
    @if (isset($ranks[null]) && $ranks[null]->isNotEmpty())
    <div class="rank-cate-group">
        <h3 class="rank-cate-heading">カテゴリなし</h3>
        <table class="listTbl listTbl--compact">
            <thead>
                <tr>
                    <th style="width:36px;">並替</th>
                    <th>タイトル</th>
                    <th>ランキング</th>
                    <th style="width:100px;">アクション</th>
                </tr>
            </thead>
            <tbody class="sortable" data-cate="0">
                @foreach ($ranks[null] as $rank)
                <tr data-id="{{ $rank->id }}">
                    <td class="handle center">
                        <span class="sort-handle"><span></span><span></span><span></span></span>
                    </td>
                    <td>
                        <strong>{{ $rank->title }}</strong>
                        @if ($rank->term_start || $rank->term_end)
                        <span class="term" style="font-size:0.8em;color:#888;margin-left:6px;">
                            {{ $rank->term_start ? $rank->term_start->format('Y/m/d') : '' }}{{ $rank->term_end ? '〜'.$rank->term_end->format('Y/m/d') : '' }}
                        </span>
                        @endif
                    </td>
                    <td>
                        <ol class="rank-inline-list">
                            @foreach ($rank->itemRanks as $itemRank)
                            <li>
                                @if ($itemRank->items)
                                    <span class="rank-no">{{ $itemRank->no }}位</span>
                                    <span class="rank-name">{{ $itemRank->items->item_name }}</span>
                                @else
                                    <span class="rank-no">{{ $itemRank->no }}位</span><span class="rank-name">--</span>
                                @endif
                            </li>
                            @endforeach
                        </ol>
                    </td>
                    <td class="action">
                        <div class="admin-btn-group">
                            <a href="{{ route('admin.ranks.edit', $rank) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <form action="{{ route('admin.ranks.destroy', $rank) }}" method="POST" style="display:inline;">
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
    </div>
    @endif

</section>

<style>
.rank-cate-group {
    margin-bottom: 2rem;
}
.rank-cate-heading {
    font-size: 1rem;
    font-weight: bold;
    padding: 6px 12px;
    background: #f0f4f8;
    border-left: 4px solid #4a90d9;
    margin-bottom: 4px;
    border-radius: 2px;
}
.listTbl--compact td,
.listTbl--compact th {
    padding: 4px 8px;
    font-size: 0.88rem;
    vertical-align: middle;
}
.rank-inline-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 4px 10px;
}
.rank-inline-list li {
    display: flex;
    align-items: center;
    gap: 3px;
    white-space: nowrap;
}
.rank-no {
    font-size: 0.75em;
    color: #888;
    font-weight: bold;
}
.rank-name {
    font-size: 0.88em;
}
</style>

<script>
$(function() {
    $('.sortable').each(function() {
        $(this).sortable({
            handle: '.sort-handle',
            update: function(event, ui) {
                let order = [];
                $(this).find('tr').each(function(index) {
                    order.push({ id: $(this).data('id'), position: index + 1 });
                });
                $.ajax({
                    type: 'POST',
                    url: "{{ route('admin.ranks.sort') }}",
                    data: { order: order, _token: '{{ csrf_token() }}' },
                    success: function() { /* 保存完了 */ },
                    error: function() { alert('並び替えの保存に失敗しました'); }
                });
            }
        });
    });
});
</script>

</x-admin-layout>
