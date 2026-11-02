<section id="ListArea">
    @php
        $startHour = config('custom.timeline.start_hour', 10);
        $endHour   = config('custom.timeline.end_hour', 44);
    @endphp
        <h3 class="date">{{ $adjustedDate->isoFormat('YYYY年MM月DD日 (dd)') }}</h3>

    @foreach ($schedules as $date => $scheduleGroup)
        <ul class="list">
            @foreach ($scheduleGroup as $schedule)
            <li class="box">
                <div class="item">
                    <div class="img">
                        <a href="{{ route('items.show', ['id' => $schedule->item->id]) }}">
                            <img src="{{ $schedule->item->thumbnail && $schedule->item->thumbnail->filename ? asset('uploads/'.$schedule->item->thumbnail->filename) : asset('images/dmy.jpg') }}" alt="キャスト画像">
                        </a>
                    </div>
                    <div class="name">
                    <a href="{{ route('items.show', ['id' => $schedule->item->id]) }}">{{ $schedule->item->item_name }}</a>@if($schedule->item->item_cate != 3)({{ $schedule->item->item_age}})@endif </div>
                    @if($schedule->item->item_cate != 3)
                    <div class="size">T{{ $schedule->item->item_height }} B{{ $schedule->item->item_b }}({{ $schedule->item->item_cup}}) W{{ $schedule->item->item_w }} H{{ $schedule->item->item_h }}</div>
                    @endif
                    <div class="time">
                        @if( $schedule->status == 1 )リクエスト受付
                        @elseif( $schedule->status == 2 )TEL確認
                        @elseif( $schedule->status == 3 )受付終了
                        @elseif( $schedule->status == 4 )当日休み
                        @elseif( $schedule->status == 5 )予約完売
                        @else
                        {{ formatScheduleTime($schedule->time_start) }}～{{ formatScheduleTime($schedule->time_end) }}
                        @endif
                    </div>
                </div>
                <div class="status">

                    <div class="timeline-container">
                        <div class="timeline-background">
                            @for ($hour = $startHour; $hour <= $endHour; $hour++)
                                <div class="timeline-hour">
                                    {{ $hour >= 24 ? '翌'.sprintf('%02d', $hour - 24) : sprintf('%02d', $hour) }}時
                                </div>
                            @endfor
                        </div>

                        <div class="timeline-bars">
                            @if (!empty($schedule->reservation))
                                @php
                                    $resStart = scheduleTimeToFloat($schedule->reservation->time_start);
                                    $resEnd   = scheduleTimeToFloat($schedule->reservation->time_end);
                                    $totalHours = $endHour - $startHour + 1;
                                    $resLeftPercent  = (($resStart - $startHour) / $totalHours) * 100;
                                    $resWidthPercent = (($resEnd - $resStart) / $totalHours) * 100;
                                @endphp
                                <div class="reservation-bar" style="left: {{ $resLeftPercent }}%; width: {{ $resWidthPercent }}%;">
                                    {{ formatScheduleTime($schedule->reservation->time_start) }} - {{ formatScheduleTime($schedule->reservation->time_end) }}
                                </div>
                            @endif

                            @php
                                $workStart = scheduleTimeToFloat($schedule->time_start);
                                $workEnd   = scheduleTimeToFloat($schedule->time_end);
                                $totalHours = $endHour - $startHour + 1;
                                $workLeftPercent  = (($workStart - $startHour) / $totalHours) * 100;
                                $workWidthPercent = (($workEnd - $workStart) / $totalHours) * 100;
                            @endphp

                            <div class="schedule-bar" style="left: {{ $workLeftPercent }}%; width: {{ $workWidthPercent }}%;">
                                @if (!empty($schedule->reservation))
                                    @if( $schedule->status == 0 )リクエスト受付
                                    @elseif( $schedule->status == 2 )TEL確認
                                    @elseif( $schedule->status == 3 )受付終了
                                    @elseif( $schedule->status == 4 )当日休み
                                    @elseif( $schedule->status == 5 )予約完売
                                    @else
                                    {{ formatScheduleTime($schedule->time_start) }}～{{ formatScheduleTime($schedule->time_end) }}
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </li>
            @endforeach
        </ul>
     @endforeach
