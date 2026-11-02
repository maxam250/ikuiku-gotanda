
<x-admin-layout>


<x-slot name="header">
イベント
</x-slot>

<section>
    <div class="toPageArea">
    <a href="{{ route('admin.events.create') }}" class="create">新しいイベントを作成</a>
    </div>
    @php
        $now = date('Y-m-d H:i:s');
    @endphp
    <table class="listTbl">
        <thead>
            <tr>
                <th>並替</th>
                <th>表示場所</th>
                <th>画像</th>
                <th>タイトル</th>
                <th>タイマーセット</th>
                <th>開始日</th>
                <th>終了日</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($events as $event)
                <tr data-id="{{ $event->id }}" class="{{ $event->position == 0 ? 'row-inactive' : '' }}">
                    <td class="handle center">
                      <span class="sort-handle">
                        <span></span><span></span><span></span>
                      </span>
                    </td>
                    <td class="size_10 center">
                   @switch($event->position)
                        @case(0)
                        非表示
                        @break

                        @case(1)
                        TOP＆イベント
                        @break

                        @case(2)
                        TOPのみ
                        @break

                        @case(3)
                        イベントのみ
                        @break

                        @default
                        未設定
                    @endswitch

                    </td>
                    <td class="size_20">
                        @if ($event->filename)
                            <div class="img">
                                <img src="{{ Storage::url('events/'.$event->filename) }}" alt="" width="100">
                            </div>
                        @else
                            なし
                        @endif
                    </td>
                    <td>{{ $event->title }}</td>
                    <td class="size_8 center">
                        @if (($event->date_start && $event->date_start->greaterThan($now)) ||
                             ($event->date_end && $event->date_end->greaterThan($now)))
                            {{ $event->timer_flag ? 'セット中' : '' }}
                        @endif
                    </td>
                    <td class="size_10">
                        {{ $event->date_start ? $event->date_start->format('Y/m/d　H:i') : '未設定' }}</td>
                    <td class="size_10">{{ $event->date_end ? $event->date_end->format('Y/m/d　H:i') : '未設定' }}</td>
                    <td class="action center">
                        <div class="admin-btn-group">
                            <a href="{{ route('admin.events.edit', $event) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" style="display:inline;">
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


<style>
    .row-inactive { opacity: 0.4; }
</style>

<script>
    $(function() {
        // テーブルの tbody を sortable に設定
        $('#sortable').sortable({
            handle: '.sort-handle',
            delay: 0,
            distance: 1,
            scrollSpeed: 25,
            scrollSensitivity: 50,
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
                    url: "{{ route('admin.events.sort') }}", // サーバー側のルート
                    data: {
                        order: order,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // 保存完了（アラートなし）
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
