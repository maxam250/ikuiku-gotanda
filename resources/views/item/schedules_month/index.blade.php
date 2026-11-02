
<x-item-layout>


<x-slot name="header">
出勤リクエスト
</x-slot>

<!-- デフォルト出勤時間設定 -->
<section class="mb-8">
    <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-clock text-blue-500 mr-2"></i>
            デフォルト出勤時間設定
        </h3>

        <form action="{{ route('item.schedules_month.update-default-times') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                <div>
                    <label for="default_sch_start" class="block text-sm font-medium text-gray-700 mb-2">
                        開始時間
                    </label>
                    <select name="default_sch_start" id="default_sch_start" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">選択してください</option>
                        @foreach (generateTimeOptions('09:00', '44:00') as $time => $label)
                            <option value="{{ $time }}" {{ $item->default_sch_start && $item->default_sch_start == $time ? 'selected' : '' }}>
                                @if ($time > '24:00:00')
                                    翌
                                @endif
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="default_sch_end" class="block text-sm font-medium text-gray-700 mb-2">
                        終了時間
                    </label>
                    <select name="default_sch_end" id="default_sch_end" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">選択してください</option>
                        @foreach (generateTimeOptions('10:00', '44:00') as $time => $label)
                            <option value="{{ $time }}" {{ $item->default_sch_end && $item->default_sch_end == $time ? 'selected' : '' }}>
                                @if ($time > '24:00:00')
                                    翌
                                @endif
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="funny-btn funny-btn-save">
                    <i class="fas fa-save"></i>保存
                </button>
            </div>
        </form>
    </div>
</section>



<section id="calenderArea">


        <div class="itemBox">
            <div class="img">
                <img src="{{ $item->thumbnail && $item->thumbnail->filename
                    ? Storage::url($item->thumbnail->filename)
                    : asset('images/dmy.jpg') }}"
                    alt="キャスト画像">
            </div>
            <div class="name">{{ $item->item_name }}({{ $item->item_age}})</div>
        </div>
    <!-- 前月と翌月へのリンク -->
    <div class="navigation">
        <div>
        <a href="{{ route('item.schedules_month.index', ['year' => $prevYear, 'month' => $prevMonth]) }}" class="funny-btn funny-btn-nav">
            <i class="fas fa-chevron-left"></i>前月
        </a>
        </div>

        <div class="flex">
        <h3>{{ $currentYear }}年{{ $currentMonth }}月</h3>
        </div>

        <div>
        <a href="{{ route('item.schedules_month.index', ['year' => $nextYear, 'month' => $nextMonth]) }}" class="funny-btn funny-btn-nav">
            翌月<i class="fas fa-chevron-right"></i>
        </a>
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
                $weekDayLabel = ['日', '月', '火', '水', '木', '金', '土'];
                $carbonDate = Carbon\Carbon::createFromFormat('Y-m-d', $formattedDate);
                $weekDay = $carbonDate->dayOfWeek; // 0〜6（0=日, 6=土）

            @endphp
                <td data-date="{{$formattedDate}}" class="calendar-day{{ $day ?? ' noframe' }} bg-red">
                    @if ($day)
                        {{ $day }}
                        <span class="weekday">（{{ $weekDayLabel[$weekDay] }}）</span>
                        <!-- スケジュール表示 -->
                        @if (isset($schedules[$formattedDate]))
                        @foreach ($schedules[$formattedDate] as $sch)


                        @if($sch->flag == 1 || $sch->flag == 4)
                            <div class="request add">出勤申請中</div>
                        @elseif($sch->flag == 2)
                            <div class="request delete">取消申請中</div>
                            <div class="d-none">
                        @elseif($sch->flag == 3)
                            <div class="request update">変更申請中</div>
                        @endif
                            <div id="schedule-{{ $sch->id }}" class="schedule-list">
                                <!-- 開始時間 -->
                                <div class="sp-flex">
                                    <div class="time">
                                        <select class="time-start" data-schedule-id="{{ $sch->id }}">
                                            @foreach (generateTimeOptions('09:00', '44:00') as $time => $label)
                                                <option value="{{ $time }}"
                                                @php
                                                    $baseTime = $sch->request_time_start ?? $sch->time_start;
                                                    $selected = $baseTime == $time;
                                                @endphp
                                                {{  $selected ? 'selected' : '' }}>
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
                                            @foreach (generateTimeOptions('10:00', '44:00') as $time => $label)
                                                <option value="{{ $time }}"
                                                @php
                                                    $baseTime = $sch->request_time_end ?? $sch->time_end;
                                                    $selected = $baseTime == $time;
                                                @endphp
                                                {{  $selected ? 'selected' : '' }}>
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
                                            <option value="0" {{ $sch->status == 0 ? 'selected' : '' }}>出勤</option>
                                            <option value="1" {{ $sch->status == 1 ? 'selected' : '' }}>リクエスト</option>
                                            <!--<option value="2" {{ $sch->status == 2 ? 'selected' : '' }}>TEL確認</option>
                                            <option value="3" {{ $sch->status == 3 ? 'selected' : '' }}>受付終了</option>
                                            <option value="4" {{ $sch->status == 4 ? 'selected' : '' }}>当日休み</option>
                                            <option value="5" {{ $sch->status == 5 ? 'selected' : '' }}>予約完売</option>-->
                                        </select>

                                    </div>
                                    --}}
                                </div><!-- sp-flex -->

                                        @if($sch->flag == 0)
                                        <button class="funny-btn funny-btn-cancel delete-schedule" data-schedule-id="{{ $sch->id }}">
                                            <i class="fas fa-times"></i>出勤取消
                                        </button>
                                        @endif
                            </div>
                            @if($sch->flag == 2)
                            </div>
                            @endif
                        @endforeach

                        @if (isset($schedules[$formattedDate]))
                            @php
                                $hasRequest = collect($schedules[$formattedDate])->contains(function ($sch) {
                                    return in_array($sch->flag, [1, 2, 3, 4]);
                                });
                            @endphp

                            @if ($hasRequest)
                                <button class="cancel cancel-request" data-date="{{ $formattedDate }}">申請を取り消す</button>
                            @endif
                        @endif
                    @else
                    <!-- 出勤ボタン -->
                    <button class="funny-btn funny-btn-work toggle-schedule" data-date="{{ $formattedDate }}">
                        <i class="fas fa-calendar-check"></i>出勤
                    </button>

                    @endif
                    <div class="schedule-info" id="schedule-{{ $formattedDate }}">
                        <!-- 出勤情報があればここに表示 -->
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
                url: '/item/schedules_month/toggle',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    date: date,
                    item_id: itemId,
                },
                success: function(response) {
                    if (response.status === 'created') {
                        // フォームパーツを表示
                        const scheduleInfoDiv = document.getElementById('schedule-' + date);
                        const scheduleId = response.sch_id;

                        scheduleInfoDiv.innerHTML = createScheduleForm(scheduleId, date);

                        // alert(scheduleId + '出勤登録されました');
                    }
                },
                error: function(xhr, status, error) {
                    console.log(error);
                    alert('エラーが発生しました');
                }
            });

        });
    });


    $('.cancel-request').on('click', function () {
        const date = $(this).data('date');

        if (!confirm(date + ' の申請を取り消しますか？')) return;

        $.ajax({
            url: '/item/schedules_month/cancel-request',
            method: 'POST',
            data: {
                date: date,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                alert('申請を取り消しました');
                location.reload(); // または該当セルだけ再描画
            },
            error: function () {
                alert('申請の取り消しに失敗しました');
            }
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
    @foreach (generateTimeOptions('09:00', '44:00') as $time => $label)
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


    const timeOptions = generateTimeOptions('09:00', '44:00');
    const timeEndOptions = generateTimeOptions('09:00', '44:00');

    return `
        <div id="schedule-${scheduleId}" class="schedule-list">

            <!-- ステータス選択 -->
            <div class="status text-center">
            <p>
            出勤申請しました
            </p>
                <button class="cancel cancel-request" onclick="location.reload();">リロード</button>
            </div>
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
        url: '/item/schedules_month/update',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            schedule_id: scheduleId,
            field: field,
            value: value,
        },
        success: function(response) {
            // alert('更新されました');
        },
        error: function(xhr, status, error) {
            console.log(error);
            alert('エラーが発生しました');
        }
    });
});



// スケジュールの削除
$(document).on('click', '.delete-schedule', function() {
    const scheduleId = $(this).data('schedule-id');

    $.ajax({
        url: '/item/schedules_month/' + scheduleId,
        method: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        success: function(response) {
            $('#schedule-' + scheduleId).fadeOut(500, function() {
                $(this).remove();
            });
            // alert('削除されました');
        },
        error: function(xhr, status, error) {
            console.log(error);
            alert('削除に失敗しました');
        }
    });
});
</script>

<style>
/* Funny Button Styles */
.funny-btn {
    padding: 8px 12px;
    font-size: 0.85rem;
    text-decoration: none;
    border-radius: 6px;
    text-align: center;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 36px;
}

.funny-btn-save {
    background: #6c757d;
    color: #fff;
}

.funny-btn-save:hover {
    background: #5a6268;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(108, 117, 125, 0.3);
}

.funny-btn-nav {
    background: #6c757d;
    color: #fff;
}

.funny-btn-nav:hover {
    background: #5a6268;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(108, 117, 125, 0.3);
}

.funny-btn-cancel {
    background: #6c757d;
    color: #fff;
    font-size: 0.75rem;
    padding: 6px 10px;
}

.funny-btn-cancel:hover {
    background: #dc3545;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
}

.funny-btn-work {
    background: #6c757d;
    color: #fff;
    display: block;
    margin: 0 auto;
    width: fit-content;
}

.funny-btn-work:hover {
    background: #5a6268;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(108, 117, 125, 0.3);
}

.funny-btn i {
    margin-right: 4px;
    font-size: 0.75rem;
}

.funny-btn-nav i:last-child {
    margin-right: 0;
    margin-left: 4px;
}

/* スマホサイズでのレイアウト調整 */
@media (max-width: 768px) {
    .sp-flex {
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px;
    }

    .time, .status {
        flex: 0 0 auto;
    }

    .funny-btn-cancel {
        margin-top: 8px;
        text-align: center;
        display: block;
        width: fit-content;
        margin-left: auto;
        margin-right: auto;
    }
}
</style>

</x-item-layout>
