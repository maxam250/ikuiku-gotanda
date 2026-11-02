
<x-admin-layout>


<x-slot name="header">
店舗情報
</x-slot>

<section class="toPageArea">
    <a href="{{ route('admin.shops.create') }}" class="create">店舗情報追加</a>
</section>

<section>

    @php
        $now = date('Y-m-d H:i:s');
    @endphp
    <table class="listTbl">
        <thead>
            <tr>
                <th>ID</th>
                <th>ショップ名</th>
                <th>ドメイン</th>
                <th>エリア</th>
                <th>営業時間</th>
                <th>電話番号</th>
                <th>アクセス解析</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($shops2 as $shop2)
                <tr data-id="{{ $shop2->id }}">
                    <td>{{ $shop2->id }}</td>

                    <td>{{ $shop2->shop_name }}</td>
                    <td>{{ $shop2->shop_domain }}</td>
                    <td>{{ $shop2->shop_area }}</td>
                    <td>{{ $shop2->shop_open }}</td>
                    <td>{{ $shop2->shop_tel }}</td>
                    <td>@if( $shop2->shop_analytics)
                            ON
                        @endif
                    </td>
                    <td class="action">
                        <div class="admin-btn-group">
                            <a href="{{ route('admin.shops.edit', $shop2) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <form action="{{ route('admin.shops.destroy', $shop2) }}" method="POST" style="display:inline;">
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






</x-admin-layout>
