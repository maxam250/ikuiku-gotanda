@php
    use Illuminate\Support\Facades\Storage;
    $now = date('Y-m-d H:i:s');
@endphp

<x-admin-layout>


<x-slot name="header">
ニュース
</x-slot>

<section>


    <div class="toPageArea">
        <a href="{{ route('admin.news.create') }}" class="create">新しいニュースを作成</a>
    </div>
    <table class="listTbl">
        <thead>
            <tr>
                <th>並替</th>
                <th>登録日/ 更新日</th>
                <th>タイトル</th>
                <th>画像</th>
                <th>タイマーセット</th>
                <th>開始日</th>
                <th>終了日</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($news as $news2)
                <tr data-id="{{ $news2->id }}">
                    <td class="handle center">
                      <span class="sort-handle">
                        <span></span><span></span><span></span>
                      </span>
                    </td>
                    <td class="size_10">{{ $news2->created_at }}<br>{{ $news2->updated_at }}</td>

                    <td>{{ $news2->title }}</td>
                    <td class="size_10">
                        @if ($news2->filename)
                            <img src="{{ Storage::url('news/' . $news2->filename) }}" alt="news Image">
                        @else
                            なし
                        @endif
                    </td>
                    <td class="size_8 center">
                        @if (($news2->date_start && $news2->date_start->greaterThan($now)) ||
                        ($news2->date_end && $news2->date_end->greaterThan($now)))
                       {{ $news2->timer_flag ? 'セット中' : '' }}
                   @endif
                    </td>
                    <td class="size_10">
                        {{ $news2->date_start ? $news2->date_start->format('Y/m/d　H:i') : '未設定' }}</td>
                    <td class="size_10">{{ $news2->date_end ? $news2->date_end->format('Y/m/d　H:i') : '未設定' }}</td>
                    <td class="action">
                        <div class="admin-btn-group">
                            <a href="{{ route('admin.news.edit', $news2) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <form action="{{ route('admin.news.destroy', $news2) }}" method="POST" style="display:inline;">
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
                    url: "{{ route('admin.news.sort') }}", // サーバー側のルート
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
