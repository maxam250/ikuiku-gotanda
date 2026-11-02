
<x-admin-layout>


<x-slot name="header">
月間出勤
</x-slot>


<section id="itemList">
{{-- <ul>
    @foreach($items as $list)
    <li>
        <a href="{{ route('admin.schedules_month.index', ['itemId' => $list->id, 'year' => $currentYear, 'month' => $currentMonth]) }}">{{ $list->item_name }}</a>
    </li>
    @endforeach
</ul> --}}

    <!-- キャストのリストを表示 -->
    <section class="jumpMenu">
        <div class="jumpMenu-wrap">
            <input type="text" id="castNameSearch" placeholder="名前で絞り込み" class="cast-name-search">
            <form name="jumpForm">
                <div class="select-wrap">
                <select name="jumpMenu" id="jumpMenuSelect" onchange="jumpToPage(this)">
                    <option value="">編集するキャストを選択してください</option>
                    @foreach ($item_lists as $item_list)
                        <option value="{{ route('admin.schedules_month.index', ['itemId' => $item_list->id, 'year' => $currentYear, 'month' => $currentMonth]) }}"
                            {{ $item->id == $item_list->id ? 'selected' : '' }}
                            data-name="{{ $item_list->item_name }}">
                            {{ $item_list->item_name }}
                        </option>
                    @endforeach
                </select>
                </div>
            </form>
        </div>
    </section>

    <script>
        function jumpToPage(selectObject) {
            if (selectObject.value != "") {
                window.location.href = selectObject.value;
            }
        }
        document.getElementById('castNameSearch').addEventListener('input', function () {
            var keyword = this.value.trim().toLowerCase();
            var select  = document.getElementById('jumpMenuSelect');
            Array.from(select.options).forEach(function (opt) {
                if (opt.value === '') return;
                opt.hidden = keyword !== '' && !opt.dataset.name.toLowerCase().includes(keyword);
            });
        });
    </script>
</section>


<section id="calenderArea">


    <!-- 前月と翌月へのリンク -->
    <div class="navigation">
        <div>
        <a href="{{ route('admin.schedules_month.index', ['itemId' => $item->id, 'year' => $prevYear, 'month' => $prevMonth]) }}" class="btn pre ">前月</a>
        </div>

        <div class="flex">
        <div class="itemBox">
        <div class="img">
            <img src="{{ $item->thumbnail && $item->thumbnail->filename
                ? Storage::url($item->thumbnail->filename)
                : asset('images/dmy.jpg') }}"
                alt="キャスト画像">
        </div>
        <div class="name">{{ $item->item_name }}({{ $item->item_age}})</div>
        </div>

        <!-- デフォルト出勤時間表示 -->
        @if($item->default_sch_start && $item->default_sch_end)
        <div class="text-sm text-gray-600 mb-2">
            <i class="fas fa-clock text-blue-500 mr-1"></i>
            デフォルト出勤時間: {{ substr($item->default_sch_start, 0, 5) }}〜{{ substr($item->default_sch_end, 0, 5) }}
        </div>
        @endif

        <h3>{{ $currentYear }}年{{ $currentMonth }}月</h3>
        </div>

        <div>
        <a href="{{ route('admin.schedules_month.index', ['itemId' => $item->id, 'year' => $nextYear, 'month' => $nextMonth]) }}" class="btn next">翌月</a>
        </div>
    </div>


    <table>
    <thead>
    <tr>
        <th>日</th>
        <th>月</th>
        <th>火</th>
        <th>水</th>
        <th>木</th>
        <th>金</th>
        <th>土</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($calendar as $week)
        <tr>
            @foreach ($week as $day)
            @php
                $formattedDate = $currentYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
            @endphp
                <td data-date="{{$formattedDate}}" class="calendar-day">
                    @if ($day)
                        {{ $day }}

                        <!-- スケジュール表示 -->
                        @if (isset($schedules[$formattedDate]))
                        @foreach ($schedules[$formattedDate] as $sch)

                        @if($sch->flag == 1)
                            <div class="request add">出勤申請中</div>
                        @elseif($sch->flag == 2)
                            <div class="request delete">取消申請中</div>
                            <div style="display:none;">
                        @elseif($sch->flag == 3 || $sch->flag == 4)
                            <div class="request update">変更申請中</div>
                        @endif

                            <div id="schedule-{{ $sch->id }}" class="schedule-list">
                                <!-- 開始時間 -->
                                <div class="time">
                                    <select class="time-start" data-schedule-id="{{ $sch->id }}">
                                        @foreach (generateTimeOptions('06:00', '44:00') as $time => $label)
                                            <option value="{{ $time }}" {{ $sch->time_start == $time ? 'selected' : '' }}>
                                                @if ($time > '24:00:00')
                                                翌
                                                @endif
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    〜
                                    <!-- 終了時間 -->
                                    <select class="time-end" data-schedule-id="{{ $sch->id }}">
                                        @foreach (generateTimeOptions('06:00', '44:00') as $time => $label)
                                            <option value="{{ $time }}" {{ $sch->time_end == $time ? 'selected' : '' }}>
                                                @if ($time > '24:00:00')
                                                翌
                                                @endif
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- ステータス選択 -->
                                {{--
                                <div class="status">
                                    <select class="status-select" data-schedule-id="{{ $sch->id }}">
                                        <option value="0">出勤</option>
                                        <option value="1">リクエスト</option>
                                        <option value="2">TEL確認</option>
                                        <option value="3">受付終了</option>
                                        <option value="4">当日休み</option>
                                        <option value="5">予約完売</option>
                                    </select>
                                    <button class="btn-del delete-schedule" data-schedule-id="{{ $sch->id }}">×</button>
                                </div>
                                --}}
                            </div>
                        @endforeach

                    @else
                    <!-- 出勤ボタン -->
                    <button class="btn toggle-schedule" data-date="{{ $formattedDate }}">出勤</button>
                    @endif

                    <!-- 新規作成されたスケジュールを表示するエリア -->
                    <div class="schedule-info" id="schedule-{{ $formattedDate }}">
                        <!-- 出勤情報があればここに表示 -->
                    </div>
                    @endif
                </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
    </table>
</section>

<script>

document.addEventListener('DOMContentLoaded', function () {
    // すべての出勤ボタンにクリックイベントを設定
    document.querySelectorAll('.toggle-schedule').forEach(button => {
        button.addEventListener('click', function () {
            const date = this.dataset.date;
            const itemId = {{ $item->id }};
            // Ajaxリクエストでスケジュールを作成
            $.ajax({
                url: '/kanrigamen/schedules_month/toggle',
                method: 'POST',
                dataType: 'json',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    date: date,
                    item_id: itemId,
                },
                success: function(response) {
                    console.log('Success response:', response);
                    if (response && response.status === 'created') {
                        // フォームパーツを表示
                        const scheduleInfoDiv = document.getElementById('schedule-' + date);
                        const scheduleId = response.sch_id;
                        const button = document.querySelector(`.toggle-schedule[data-date="${date}"]`);

                        if (scheduleInfoDiv && scheduleId) {
                            scheduleInfoDiv.innerHTML = createScheduleForm(scheduleId, date);
                            // ボタンを非表示にする
                            if (button) {
                                button.style.display = 'none';
                            }
                            console.log('Schedule form created:', scheduleId);
                        } else {
                            console.error('ScheduleInfoDiv or scheduleId not found:', scheduleInfoDiv, scheduleId);
                        }
                    } else {
                        console.log('Response status is not created:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Ajax error:', {status: xhr.status, statusText: xhr.statusText, error: error, responseText: xhr.responseText});
                    // レスポンスが成功しているが、JSONパースに失敗した場合
                    if (xhr.status === 200) {
                        try {
                            let responseText = xhr.responseText || '';
                            // BOMを除去
                            responseText = responseText.replace(/^\uFEFF+/, '').trim();
                            const response = typeof responseText === 'string' && responseText ? JSON.parse(responseText) : xhr.responseJSON;
                            console.log('Parsed response from error handler:', response);
                            if (response && response.status === 'created') {
                                const scheduleInfoDiv = document.getElementById('schedule-' + date);
                                const scheduleId = response.sch_id;
                                const button = document.querySelector(`.toggle-schedule[data-date="${date}"]`);

                                if (scheduleInfoDiv && scheduleId) {
                                    scheduleInfoDiv.innerHTML = createScheduleForm(scheduleId, date);
                                    // ボタンを非表示にする
                                    if (button) {
                                        button.style.display = 'none';
                                    }
                                    console.log('Schedule form created from error handler:', scheduleId);
                                } else {
                                    console.error('ScheduleInfoDiv or scheduleId not found:', scheduleInfoDiv, scheduleId);
                                }
                                return;
                            }
                        } catch (e) {
                            // JSONパースに失敗したが、処理は成功している可能性がある
                            console.log('処理は完了しましたが、レスポンスの解析に失敗しました:', e, 'ResponseText:', xhr.responseText?.substring(0, 100));
                            return;
                        }
                    }
                    // 実際のエラーの場合のみアラートを表示
                    if (xhr.status >= 400) {
                        console.error('Error:', error, 'Status:', xhr.status);
                        const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'エラーが発生しました';
                        alert(errorMessage);
                    }
                }
            });
        });
    });
});



// 時間オプションをJavaScriptで生成する関数
function generateTimeOptions(start, end) {

    // console.log('createScheduleForm called with:', start, end);

    let options = '';
    // ここで時間オプションを生成するロジックを追加
    // 例: options += `<option value="${time}">${label}</option>`;

    // helperカスタム関数
    @foreach (generateTimeOptions('06:00', '44:00') as $time => $label)
        @php
            if( substr($time, 0,2) > 24 ){
                $yoku = "翌";
            }else{
                $yoku = "";
            }
        @endphp


        options += '<option value="{{ $time }}">{{ $yoku }}{{ $label }}</option>';
    @endforeach

    return options;
}





// スケジュールフォームパーツを生成する関数
function createScheduleForm(scheduleId, date) {


    const timeOptions = generateTimeOptions('06:00', '44:00');
    const timeEndOptions = generateTimeOptions('06:00', '44:00');

    return `
        <div id="schedule-${scheduleId}" class="schedule-list">
            <!-- 開始時間 -->
            <div class="time">
                <select class="time-start" data-schedule-id="${scheduleId}">
                    ${timeOptions}
                </select>
                〜
                <!-- 終了時間 -->
                <select class="time-end" data-schedule-id="${scheduleId}">
                    ${timeEndOptions}
                </select>
            </div>
            <!-- ステータス選択 -->
            <!--
            <div class="status">
                <select class="status-select" data-schedule-id="${scheduleId}">
                    <option value="0">出勤</option>
                    <option value="1">リクエスト受付</option>
                    <option value="2">TEL確認</option>
                    <option value="3">受付終了</option>
                    <option value="4">当日休み</option>
                    <option value="5">予約完売</option>
                </select>
                <button class="btn-del delete-schedule" data-schedule-id="${scheduleId}">×</button>
            </div>
            -->
        </div>
    `;
}



// 時間、ステータスの更新
$(document).on('change', '.time-start, .time-end, .status-select', function() {
    const scheduleId = $(this).data('schedule-id');
    const field = $(this).hasClass('time-start') ? 'time_start' :
                  $(this).hasClass('time-end') ? 'time_end' : 'status';
    const value = $(this).val();

    $.ajax({
        url: '/kanrigamen/schedules_month/update',
        method: 'POST',
        dataType: 'json',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            schedule_id: scheduleId,
            field: field,
            value: value,
        },
        success: function(response) {
            // 更新成功
        },
        error: function(xhr, status, error) {
            // レスポンスが成功しているが、JSONパースに失敗した場合
            if (xhr.status === 200) {
                return; // 処理は成功している
            }
            // 実際のエラーの場合のみアラートを表示
            if (xhr.status >= 400) {
                console.error('Error:', error, 'Status:', xhr.status);
                const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : '更新に失敗しました';
                alert(errorMessage);
            }
        }
    });
});



// スケジュールの削除
$(document).on('click', '.delete-schedule', function() {
    const scheduleId = $(this).data('schedule-id');

    $.ajax({
        url: '/kanrigamen/schedules_month/' + scheduleId,
        method: 'DELETE',
        dataType: 'json',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        success: function(response) {
            $('#schedule-' + scheduleId).fadeOut(500, function() {
                $(this).remove();
            });
        },
        error: function(xhr, status, error) {
            // レスポンスが成功しているが、JSONパースに失敗した場合
            if (xhr.status === 200) {
                $('#schedule-' + scheduleId).fadeOut(500, function() {
                    $(this).remove();
                });
                return; // 処理は成功している
            }
            // 実際のエラーの場合のみアラートを表示
            if (xhr.status >= 400) {
                console.error('Error:', error, 'Status:', xhr.status);
                const errorMessage = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : '削除に失敗しました';
                alert(errorMessage);
            }
        }
    });
});



</script>





</x-admin-layout>
