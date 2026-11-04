
<x-admin-layout>


<x-slot name="header">
日別出勤
</x-slot>


    <!-- 日付のリストを表示 -->
    <section class="jumpMenu">
        <form name="jumpForm">
            <div class="select-wrap">
                <select name="jumpMenu" onchange="jumpToPage(this)">
                    @foreach ($dates as $date)
                        <option value="{{ route('admin.schedules.index', ['date' => $date['date']]) }}"
                            {{ request('date') == $date['date'] ? 'selected' : '' }}>
                            {{ $date['date_jp'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </section>

    <script>
        function jumpToPage(selectObject) {
            if (selectObject.value != "") {
                window.location.href = selectObject.value;
            }
        }

        // 名前検索・ページネーション操作後は未出勤エリアへスクロール
        document.addEventListener('DOMContentLoaded', function () {
            const params = new URLSearchParams(window.location.search);
            if (params.has('search') || params.has('page')) {
                const target = document.getElementById('offScheduleArea');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    </script>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 1rem; margin-bottom: 2rem; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="padding: 1rem; margin-bottom: 2rem; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;">
            {{ session('error') }}
        </div>
    @endif

    <section class="onScheduleArea">
    @foreach ($schedules as $date => $scheduleGroup)
    <h3 class="section-title active">{{ $adjustedDate->isoFormat('YYYY年MM月DD日 (dddd)') }}</h3>

    <ul class="list sortable-list" data-date="{{ $date }}">
        @foreach ($scheduleGroup as $schedule)
        @continue(!$schedule->item)
        <li class="box" data-schedule-id="{{ $schedule->id }}" id="schedule-{{ $schedule->id }}">
            <div class="img relative">
                <div class="absolute top-0 right-0 text-xs ">
                    @if($schedule->flag == 1 || $schedule->flag == 4)
                        <span class="p-1 bg-yellow-300 rounded">出勤申請中</span>
                    @elseif($schedule->flag == 2)
                        <span class="p-1 bg-red-300 rounded">削除申請中</span>
                    @elseif($schedule->flag == 3)
                        <span class="p-1 bg-green-300 rounded">変更申請中</span>
                    @endif
                </div>
                <img src="{{ $schedule->item->thumbnail && $schedule->item->thumbnail->filename
                ? Storage::url($schedule->item->thumbnail->filename)
                : asset('images/dmy.jpg') }}"
                alt="キャスト画像">
            </div>
            <div class="name">{{ $schedule->item->item_name }}({{ $schedule->item->item_age}})</div>

            <!-- デフォルト出勤時間表示 -->
            @if($schedule->item->default_sch_start && $schedule->item->default_sch_end)
            <div class="text-xs text-gray-500 mb-2">
                <i class="fas fa-clock text-blue-500 mr-1"></i>
                デフォルト: {{ substr($schedule->item->default_sch_start, 0, 5) }}〜{{ substr($schedule->item->default_sch_end, 0, 5) }}
            </div>
            @endif

            <!-- 開始時間 -->
            <div class="flex items-center time text-left mb-2">
                <select class="time-select text-sm pt-2 pb-2 pl-2 pr-8" data-schedule-id="{{ $schedule->id }}" data-field="time_start">
                    @foreach (generateTimeOptions('06:00', '44:00') as $time => $label)
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
                <select class="time-select text-sm pt-2 pb-2 pl-2 pr-8" data-schedule-id="{{ $schedule->id }}" data-field="time_end">
                    @foreach (generateTimeOptions('06:00', '44:00') as $time => $label)
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
            <div class="status mb-2">
                <select class="status-select text-sm w-full" data-schedule-id="{{ $schedule->id }}">
                    <option value="0" {{ $schedule->status == 0 ? 'selected' : '' }}>出勤</option>
                    <option value="1" {{ $schedule->status == 1 ? 'selected' : '' }}>リクエスト受付</option>
                    <option value="2" {{ $schedule->status == 2 ? 'selected' : '' }}>TEL確認</option>
                    <option value="3" {{ $schedule->status == 3 ? 'selected' : '' }}>受付終了</option>
                    <option value="4" {{ $schedule->status == 4 ? 'selected' : '' }}>当日休み</option>
                    <option value="5" {{ $schedule->status == 5 ? 'selected' : '' }}>予約完売</option>
                </select>
            </div>


            <!-- 削除ボタン -->
                                        <button class="admin-btn admin-btn-warning admin-btn-sm delete-btn" data-schedule-id="{{ $schedule->id }}">
                                <i class="fas fa-trash"></i>削除
                            </button>

        </li>
        @endforeach
    </ul>
    @endforeach
    </section>

    <section class="offScheduleArea" id="offScheduleArea">
        <h3 class="section-title">未出勤キャスト</h3>

        {{-- 名前検索 + ページネーション 横並び --}}
        <div class="schedule-search-pagination">
            <form method="GET" action="{{ route('admin.schedules.index', ['date' => $adjustedDate->format('Y-m-d')]) }}" class="items-search-form">
                <input type="text" name="search" value="{{ $search }}" placeholder="名前検索" class="items-search-input">
                @if($search)
                    <a href="{{ route('admin.schedules.index', ['date' => $adjustedDate->format('Y-m-d')]) }}" class="items-search-clear" title="クリア">✕</a>
                @endif
                <button type="submit" class="items-search-btn"><i class="fas fa-search"></i></button>
            </form>
            <div>{{ $non_schedules->links('vendor.pagination.admin-items') }}</div>
        </div>

        <form action="{{ route('admin.schedules.bulkUpdate') }}" method="POST">
            @csrf
            <div class="btnArea" style="text-align: center; margin-bottom: 2rem;">
                <button type="submit" class="af-btn af-btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    一斉登録
                </button>
            </div>
            <ul class="list">
                @foreach ($non_schedules as $non_schedule)
                <li class="box">
                    <label>
                        <div class="img">
                            <img src="{{ $non_schedule->thumbnail && $non_schedule->thumbnail->filename
                            ? Storage::url($non_schedule->thumbnail->filename)
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
                            <select name="items[{{ $non_schedule->id }}][time_start]" class="time-select text-sm pt-2 pb-2 pl-2 pr-8" required>
                                @foreach (generateTimeOptions('06:00', '44:00') as $time => $label)
                                    <option value="{{ $time }}" {{ $non_schedule->default_sch_start && $non_schedule->default_sch_start == $time ? 'selected' : '' }}>
                                        @if ($time > '24:00:00')
                                        翌
                                        @endif
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            〜
                            <!-- 終了時間 -->
                            <select name="items[{{ $non_schedule->id }}][time_end]" class="time-select text-sm pt-2 pb-2 pl-2 pr-8" required>
                                @foreach (generateTimeOptions('10:00', '44:00') as $time => $label)
                                    <option value="{{ $time }}" {{ $non_schedule->default_sch_end && $non_schedule->default_sch_end == $time ? 'selected' : '' }}>
                                        @if ($time > '24:00:00')
                                        翌
                                        @endif
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </label>
                </li>
                @endforeach
            </ul>
            <!-- selectするとtime_start、time_endが入らなくなる -->
            <input type="hidden" name="schedule_date" value="{{$adjustedDate}}">
            <div style="margin: 1.5rem 0;">
                {{ $non_schedules->links('vendor.pagination.admin-items') }}
            </div>
            <div class="btnArea" style="text-align: center; margin-top: 2rem;">
                <button type="submit" class="af-btn af-btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    一斉登録
                </button>
            </div>
        </form>
    </section>


<!-- jQueryとSortable用のスクリプト -->
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script src="{{ asset('js/Sortable.min.js') }}"></script>
<script src="{{ asset('js/admin/sortable-grid.js') }}?v={{ filemtime(public_path('js/admin/sortable-grid.js')) }}"></script>

<script>

// 並び替え

// ドラッグ並び替え（SortableJS：カードが滑らかに入れ替わる）
document.addEventListener('DOMContentLoaded', function () {
    initSortableGrid({
        selector: '.sortable-list',
        idAttr: 'schedule-id',
        paramName: 'scheduleOrder',
        url: "{{ route('admin.schedules.sort') }}",
        token: "{{ csrf_token() }}",
    });
});


// 出勤から削除
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const scheduleId = this.getAttribute('data-schedule-id');
            const scheduleElement = document.getElementById('schedule-' + scheduleId);

            if (confirm('本当に削除しますか？')) {
                fetch(`/kanrigamen/schedules/${scheduleId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    if (!response.ok) {
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            const text = await response.text();
                            const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                            try {
                                const err = JSON.parse(cleanedText);
                                throw new Error(err.message || 'サーバーエラー');
                            } catch (e) {
                                if (e instanceof SyntaxError) {
                                    throw new Error('サーバーエラー: レスポンスの解析に失敗しました');
                                }
                                throw e;
                            }
                        }
                        throw new Error('サーバーエラーが発生しました');
                    }
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const text = await response.text();
                        const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                        try {
                            return JSON.parse(cleanedText);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            return { success: true, message: '削除が完了しました' };
                        }
                    }
                    const text = await response.text();
                    const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                    try {
                        return JSON.parse(cleanedText);
                    } catch {
                        return { success: true, message: '削除が完了しました' };
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        // フェードアウトアニメーションを追加
                        scheduleElement.style.transition = 'opacity 0.3s';
                        scheduleElement.style.opacity = '0';
                        setTimeout(() => {
                            scheduleElement.remove();
                        }, 300);
                    } else {
                        alert(data ? (data.message || '削除に失敗しました') : 'エラーが発生しました: レスポンスが不正です');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'エラーが発生しました');
                });
            }
        });
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
            fetch(`/kanrigamen/schedules/${scheduleId}/status`, {
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
//             fetch(`/admin/schedules/${scheduleId}/update-time`, {
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
 $('.time-select[data-schedule-id]').on('change', function() {
    const scheduleId = $(this).data('schedule-id');
    const field = $(this).data('field');
    const value = $(this).val();

    $.ajax({
        url: '/kanrigamen/schedules/' + scheduleId + '/update-time',
        method: 'PUT',
        data: {
            _token: '{{ csrf_token() }}',
            field: field,
            value: value
        },
        success: function(response) {
            if (!response.success) {
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




</x-admin-layout>
