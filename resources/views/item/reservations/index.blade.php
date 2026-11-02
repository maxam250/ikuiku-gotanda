
<x-item-layout>


<x-slot name="header">
予約一覧
</x-slot>

<section class="toPageArea">
    <a href="{{ route('item.reservations.create') }}" class="create">新しい予約を作成</a>
</section>


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
                <th>メモ</th>
                <th>メモ共有</th>
                <th>場所</th>
                <th>料金</th>
                <th>フラグ</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservations as $reservation)
                <tr>
                    <td class="center">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#resModal{{ $reservation->id }}" class="border-0 bg-transparent p-0">
                            <i class="las la-comment-dots"></i>
                        </button>

@include('item.reservations.detail')

                    </td>
                    <td class="center"><p>[ {{ $reservation->id }} ]</p>{{ $reservation->kind == 1 ? '通常' : 'リクエスト' }}</td>
                    <td>
                        <p>{{ $reservation->date->format('Y/m/d').'('. $reservation->date->isoFormat('ddd'). ')' }}
                         </p>
                        <p>
                        {{ $reservation->time_start ? $reservation->time_start->format('H:i') : ''}} -
                        {{ $reservation->time_end ? $reservation->time_end->format('H:i') : '' }}
                        </p>
                    </td>
                    <td class="center"><p>[ {{ $reservation->member_id }} ]</p>
                        <p>{{ $reservation->name }}</p>
                    </td>
                    <td>
                        @foreach($courses as $course)
                            {{ $course->id == $reservation->course_id ? $course->title : '' }}
                        @endforeach


                        @foreach($course_fees as $course_fee)
                            {{ $course_fee->id == $reservation->course_time ? $course_fee->course : '' }}
                        @endforeach
                    </td>
                    <td>
                        {{ $reservation->item_name }}
                    </td>
                    <td class="size_15">
                        {!! nl2br(e($reservation->memo)) !!}
                    </td>
                    <td class="size_15">
                        {!! nl2br(e($reservation->memo_share)) !!}
                    </td>
                    <td>
                        {{ $reservation->area }} / {{ $reservation->place }} / {{ $reservation->address }}
                    </td>
                    <td>
                        {{ number_format($reservation->fee_total) }}円
                    </td>
                    <td class="size_6">
                        <select class="flag-select" data-reservation-id="{{ $reservation->id }}" id="colorSelect" onChange="changeColor(this)">
                            <option value="0" {{ $reservation->flag == 0 ? 'selected' : '' }}>未設定</option>
                            <option value="1" {{ $reservation->flag == 1 ? 'selected' : '' }}>確認中</option>
                            <option value="2" {{ $reservation->flag == 2 ? 'selected' : '' }}>確定</option>
                            <option value="3" {{ $reservation->flag == 3 ? 'selected' : '' }}>保留</option>
                        </select>
                    </td>
                    <td class="action">
                        <a href="{{ route('item.reservations.edit', $reservation) }}" class="btn btn-warning">編集</a>
                        <form action="{{ route('item.reservations.destroy', $reservation) }}" method="POST" class="d-inline">
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
        var reservationId = $(this).data('reservation-id');
        var newFlag = $(this).val();

        $.ajax({
            url: '/item/reservation/update-flag',
            method: 'POST',
            data: {
                reservation_id: reservationId,
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
