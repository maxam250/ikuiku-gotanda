
<x-item-layout>


<x-slot name="header">
日別出勤
</x-slot>


    <!-- 日付のリストを表示 -->
    <section class="jumpMenu">
        <form name="jumpForm">
            <select name="jumpMenu" onchange="jumpToPage(this)">
                @foreach ($dates as $date)
                    <option value="{{ route('item.schedules.index', ['date' => $date['date']]) }}"
                        {{ request('date') == $date['date'] ? 'selected' : '' }}>
                        {{ $date['date_jp'] }}
                    </option>
                @endforeach
            </select>
        </form>
    </section>

    <script>
        function jumpToPage(selectObject) {
            if (selectObject.value != "") {
                window.location.href = selectObject.value;
            }
        }
    </script>

    <section class="onScheduleArea">
    @foreach ($schedules as $date => $scheduleGroup)
    <h3 class="date">{{ $adjustedDate->isoFormat('YYYY年MM月DD日 (dddd)') }}</h3>

    <ul class="list sortable-list" data-date="{{ $date }}">
        @foreach ($scheduleGroup as $schedule)
        <li class="box" data-schedule-id="{{ $schedule->id }}" id="schedule-{{ $schedule->id }}">
            <div class="img">
                <img src="{{ $schedule->itemImage && $schedule->itemImage->filename
                ? asset('uploads/' . $schedule->itemImage->filename)
                : asset('images/dmy.jpg') }}"
                alt="キャスト画像">
            </div>
            <div class="name">{{ $schedule->item->item_name }}({{ $schedule->item->item_age}})</div>



            <!-- 開始時間 -->
            <div class="time">
                <select class="time-select" data-schedule-id="{{ $schedule->id }}" data-field="time_start">
                    @foreach (generateTimeOptions('09:00', '44:00') as $time => $label)
                        <option value="{{ $time }}" {{ $schedule->time_start == $time ? 'selected' : '' }}>
                            @if ($time > '24:00:00')
                            翌
                            @endif
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                〜
                <!-- 終了時間 -->
                <select class="time-select" data-schedule-id="{{ $schedule->id }}" data-field="time_end">
                    @foreach (generateTimeOptions('10:00', '44:00') as $time => $label)
                        <option value="{{ $time }}" {{ $schedule->time_end == $time ? 'selected' : '' }}>
                            @if ($time > '24:00:00')
                            翌
                            @endif
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- ステータス選択 -->
            <div class="status">
                <select class="status-select" data-schedule-id="{{ $schedule->id }}">
                    <option value="0" {{ $schedule->status == 0 ? 'selected' : '' }}>出勤</option>
                    <option value="1" {{ $schedule->status == 1 ? 'selected' : '' }}>リクエスト受付</option>
                    <option value="2" {{ $schedule->status == 2 ? 'selected' : '' }}>TEL確認</option>
                    <option value="3" {{ $schedule->status == 3 ? 'selected' : '' }}>受付終了</option>
                    <option value="4" {{ $schedule->status == 4 ? 'selected' : '' }}>当日休み</option>
                    <option value="5" {{ $schedule->status == 5 ? 'selected' : '' }}>予約完売</option>
                </select>
            </div>


            <!-- 削除ボタン -->
            <button class="btn delete-btn" data-schedule-id="{{ $schedule->id }}">削除</button>

        </li>
        @endforeach
    </ul>
    @endforeach
    </section>

    <section class="offScheduleArea">
    <h3>出勤していないキャスト</h3>
    <form action="{{ route('item.schedules.bulkUpdate') }}" method="POST">
        @csrf
    <ul class="list">
    @foreach ($non_schedules as $non_schedule)
    <li class="box">
        <label>
        <div class="img">
            <img src="{{ $non_schedule->thumbnail && $non_schedule->thumbnail->filename
            ? asset('uploads/' . $non_schedule->thumbnail->filename)
            : asset('images/dmy.jpg') }}"
            alt="キャスト画像">
        </div>
        <div class="name">{{ $non_schedule->item_name }}({{ $non_schedule->item_age}})</div>

        <!-- チェックボックス -->
        <div class="apply">
        <input type="checkbox" name="items[{{ $non_schedule->id }}][selected]" value="1"> 出勤に設定
        </div>

        <!-- 開始時間 -->
        <div class="time">
            <select name="items[{{ $non_schedule->id }}][time_start]" required>
                @foreach (generateTimeOptions('09:00', '44:00') as $time => $label)
                    <option value="{{ $time }}" {{ $non_schedule->default_sch_start && $non_schedule->default_sch_start == $time ? 'selected' : '' }}>
                        @if ($time > '24:00:00')
                        翌
                        @endif
                        {{ $label }}</option>
                @endforeach
            </select>
            〜
            <!-- 終了時間 -->
            <select name="items[{{ $non_schedule->id }}][time_end]" required>
                @foreach (generateTimeOptions('10:00', '44:00') as $time => $label)
                    <option value="{{ $time }}" {{ $non_schedule->default_sch_end && $non_schedule->default_sch_end == $time ? 'selected' : '' }}>
                        @if ($time > '24:00:00')
                        翌
                        @endif
                        {{ $label }}</option>
                @endforeach
            </select>
        </div>
    </li>
    @endforeach
    </ul>
<!-- selectするとtime_start、time_endが入らなくなる -->

    <input type="hidden" name="schedule_date" value="{{$adjustedDate}}">
    <button type="submit" class="btn size_m">一斉登録</button>
    </form>
    </section>


<!-- jQueryとSortable用のスクリプト -->
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>

// 並び替え

$(function() {
    // 並び替え可能にする
    $(".sortable-list").sortable({
        update: function(event, ui) {
            let scheduleOrder = [];
            $(this).find('li').each(function(index) {
                let scheduleId = $(this).data('schedule-id');
                scheduleOrder.push({
                    id: scheduleId,
                    sort: index + 1 // 並び順を1からスタートさせる
                });
            });

            // Ajaxで並び順をサーバーに送信
            $.ajax({
                url: "{{ route('item.schedules.sort') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    scheduleOrder: scheduleOrder
                },
                success: function(response) {
                    if(response.success) {
                        alert('並び替えが完了しました');
                    } else {
                        alert('並び替えに失敗しました');
                    }
                },
                error: function() {
                    alert('エラーが発生しました');
                }
            });
        }
    });
});


// 出勤から削除
$(document).ready(function() {
    // 削除ボタンがクリックされたらAjaxで削除リクエストを送信
    $('.delete-btn').on('click', function() {
        const scheduleId = $(this).data('schedule-id');
        const scheduleElement = $('#schedule-' + scheduleId);

        if (confirm('本当に削除しますか？')) {
            $.ajax({
                url: '/item/schedules/' + scheduleId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // 削除されたリスト項目をフェードアウトで消す
                        scheduleElement.fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert('削除に失敗しました');
                    }
                },
                error: function() {
                    alert('エラーが発生しました');
                }
            });
        }
    });
});


//　ステータスの変更
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.status-select');

    statusSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            const scheduleId = this.getAttribute('data-schedule-id');
            const status = this.value;

            // Ajaxリクエストを送信
            fetch(`/item/schedules/${scheduleId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // alert(data.message);  // ステータス更新成功時にメッセージを表示
                } else {
                    alert('ステータスの更新に失敗しました');
                }
            })
            .catch(error => {
                console.error('エラー:', error);
            });
        });
    });
});


//時間の変更
// document.addEventListener('DOMContentLoaded', function() {
//     const timeSelects = document.querySelectorAll('.time-select');

//     timeSelects.forEach(function(select) {
//         select.addEventListener('change', function() {
//             const scheduleId = this.getAttribute('data-schedule-id');
//             const field = this.getAttribute('data-field');
//             const value = this.value;

//             // Ajaxリクエストを送信
//             fetch(`/item/schedules/${scheduleId}/update-time`, {
//                 method: 'PUT',
//                 headers: {
//                     'Content-Type': 'application/json',
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//                 },
//                 body: JSON.stringify({
//                     field: field,
//                     value: value
//                 })
//             })
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     alert('更新されました');
//                 } else {
//                     alert('更新に失敗しました');
//                 }
//             })
//             .catch(error => {
//                 console.error('エラー:', error);
//             });
//         });
//     });
// });

</script>




<script>
 $('.time-select').on('change', function() {
    const scheduleId = $(this).data('schedule-id');
    const field = $(this).data('field');
    let value = $(this).val();

    // Adjust times beyond 24:00 to a valid format for the database
    if (value >= "24:00:00") {
        const parts = value.split(':');
        const hours = parseInt(parts[0]);
        const adjustedHours = hours - 24;
        value = adjustedHours.toString().padStart(2, '0') + ':' + parts[1] + ':' + parts[2];
    }

    $.ajax({
        url: '/item/schedules/' + scheduleId + '/update-time',
        method: 'PUT',
        data: {
            _token: '{{ csrf_token() }}',
            field: field,
            value: value
        },
        success: function(response) {
            if (response.success) {
                alert('スケジュールが更新されました');
            } else {
                alert('更新に失敗しました');
            }
        },
        error: function(xhr, status, error) {
            console.error(error);
            alert('エラーが発生しました');
        }
    });
});


// 未出勤者の時間を変更すると出勤チェックが自動的に入る
document.addEventListener("DOMContentLoaded", function () {
    // すべてのtime_startセレクトを取得
    document.querySelectorAll('select[name^="items"][name$="[time_start]"]').forEach(function (selectEl) {
        selectEl.addEventListener("change", function () {
            // name属性からキャストIDを抽出（例: items[123][time_start] → 123）
            const match = this.name.match(/^items\[(\d+)\]\[time_start\]$/);
            if (match) {
                const castId = match[1];
                const checkbox = document.querySelector(`input[name="items[${castId}][selected]"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            }
        });
    });
});

</script>




</x-item-layout>
