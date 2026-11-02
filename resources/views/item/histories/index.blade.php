
<x-item-layout>


<x-slot name="header">
予約履歴一覧
</x-slot>


<section>

    @php
        $now = date('Y-m-d H:i:s');
    @endphp
    <table class="listTbl">
        <thead>
            <tr>
                <th>詳細</th>
                <th>種別</th>
                <th>日時</th>
                <th>予約名</th>
                <th>コース</th>
                <th>キャスト</th>
                <th>予約完了メモ</th>
                <th>メモ</th>
                <th>メモ共有</th>
                <th>場所</th>
                <th>料金</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($histories as $history)
                <tr>
                    <td class="center">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#resModal{{ $history->id }}" class="border-0 bg-transparent p-0">
                            <i class="las la-comment-dots"></i>
                        </button>

@include('item.histories.detail')

                    </td>
                    <td class="center"><p>[ {{ $history->id }} ]</p>{{ $history->kind == 1 ? '通常' : 'リクエスト' }}</td>
                    <td>
                        <p>{{ $history->date->format('Y/m/d').'('. $history->date->isoFormat('ddd'). ')' }}
                         </p>
                        <p>
                        {{ $history->time_start ? $history->time_start->format('H:i') : ''}} -
                        {{ $history->time_end ? $history->time_end->format('H:i') : '' }}
                        </p>
                    </td>
                    <td class="center"><p>[ {{ $history->member_id }} ]</p>
                        <p>{{ $history->name }}</p>
                    </td>
                    <td>
                        @foreach($courses as $course)
                            {{ $course->id == $history->course_id ? $course->title : '' }}
                        @endforeach


                        @foreach($course_fees as $course_fee)
                            {{ $course_fee->id == $history->course_time ? $course_fee->course : '' }}
                        @endforeach
                    </td>
                    <td>
                        {{ $history->item_name }}
                    </td>
                    <td class="size_15">
                        {!! nl2br(e($history->memo_result)) !!}
                    </td>
                    <td class="size_15">
                        {!! nl2br(e($history->memo)) !!}
                    </td>
                    <td class="size_15">
                        {!! nl2br(e($history->memo_share)) !!}
                    </td>
                    <td>
                        {{ $history->area }} / {{ $history->place }} / {{ $history->address }}
                    </td>
                    <td>
                        {{ number_format($history->fee_total) }}円
                    </td>
                    <td class="action">
                        <a href="{{ route('item.histories.edit', $history) }}" class="btn btn-warning">編集</a>
                        <form action="{{ route('item.histories.destroy', $history) }}" method="POST" class="d-inline">
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




<script>


function changeColor(selectObj) {
    const color = "";
    switch(selectObj.value) {
        case "0":
            color = "#f0f0f0"; // 
            break;
        case "1":
            color = "#ff0000"; // Red
            break;
        case "2":
            color = "#00ff00"; // Green
            break;
        case "3":
            color = "#0000ff"; // Blue
            break;
        default:
            color = "#ffffff"; // Default to white
    }
    selectObj.style.backgroundColor = color;
}


$(document).ready(function() {
    $('.flag-select').change(function() {
        var historyId = $(this).data('history-id');
        var newFlag = $(this).val();

        $.ajax({
            url: '/item/history/update-flag',
            method: 'POST',
            data: {
                history_id: historyId,
                flag: newFlag,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    alert('ステータスが更新されました');
                } else {
                    alert('ステータスの更新に失敗しました');
                }
            },
            error: function() {
                alert('エラーが発生しました');
            }
        });
    });
});


</script>
    <!-- Bootstrap JSをインクルード -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</x-item-layout>
