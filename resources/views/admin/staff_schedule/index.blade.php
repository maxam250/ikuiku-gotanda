<x-admin-layout>
    <x-slot name="header">
        スタッフスケジュール管理
    </x-slot>

    @push('styles')
    <style>
        .ss-calendar { border-collapse: collapse; width: 100%; table-layout: fixed; }
        .ss-calendar th {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
            text-align: center;
        }
        .ss-calendar td {
            border: 1px solid #e5e7eb;
            vertical-align: top;
            padding: 6px;
            min-height: 140px;
            background: #fff;
        }
        .ss-day-num { font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 4px; }
        .ss-day-num.is-today { color: #2563eb; }
        .ss-day-num.is-other-month { color: #d1d5db; }
        .ss-cate-block {
            margin-bottom: 6px;
            border-radius: 5px;
            padding: 4px 6px;
            font-size: 0.72rem;
        }
        .ss-cate-label {
            font-weight: 700;
            font-size: 0.68rem;
            margin-bottom: 2px;
        }
        .ss-staff-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255,255,255,0.75);
            border-radius: 4px;
            padding: 2px 5px;
            margin-bottom: 2px;
            font-size: 0.72rem;
            border: 1px dashed rgba(0,0,0,0.1);
        }
        .ss-staff-name { font-weight: 600; color: #1f2937; }
        .ss-edit-btn {
            font-size: 0.65rem;
            padding: 1px 6px;
            border-radius: 3px;
            color: #fff;
            cursor: pointer;
            border: none;
            margin-left: 4px;
        }
        .ss-add-btn {
            width: 100%;
            margin-top: 4px;
            font-size: 0.7rem;
            padding: 2px;
            background: #e5e7eb;
            color: #374151;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .ss-add-btn:hover { background: #d1d5db; }
        .ss-empty { font-size: 0.68rem; color: #9ca3af; }
        /* モーダル */
        #ss-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 9999; align-items: center; justify-content: center; }
        #ss-modal.open { display: flex; }
        #ss-modal-box {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            width: 380px;
            max-width: 95vw;
            box-shadow: 0 20px 40px rgba(0,0,0,.15);
        }
        #ss-modal-box h3 { font-size: 1rem; font-weight: 700; margin-bottom: 16px; color: #111827; }
        .ss-form-row { margin-bottom: 12px; }
        .ss-form-row label { display: block; font-size: 0.78rem; font-weight: 600; color: #374151; margin-bottom: 4px; }
        .ss-form-row select, .ss-form-row textarea {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 6px 8px;
            font-size: 0.85rem;
            color: #111827;
        }
        .ss-form-row select:focus, .ss-form-row textarea:focus { outline: none; border-color: #60a5fa; }
        .ss-delete-check { display: flex; align-items: center; gap: 6px; font-size: 0.8rem; color: #dc2626; margin-bottom: 12px; }
        .ss-modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; }
        .ss-btn { padding: 7px 18px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; border: none; cursor: pointer; }
        .ss-btn-cancel { background: #f3f4f6; color: #374151; }
        .ss-btn-cancel:hover { background: #e5e7eb; }
        .ss-btn-save { background: #2563eb; color: #fff; }
        .ss-btn-save:hover { background: #1d4ed8; }
        /* 一括設定モーダル */
        #ss-bulk-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 9999; align-items: center; justify-content: center; }
        #ss-bulk-modal.open { display: flex; }
        #ss-bulk-box {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            width: 680px;
            max-width: 96vw;
            max-height: 88vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 40px rgba(0,0,0,.2);
        }
        #ss-bulk-box h3 { font-size: 1rem; font-weight: 700; margin-bottom: 16px; color: #111827; flex-shrink: 0; }
        .sb-date-row { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; flex-shrink: 0; }
        .sb-date-row label { font-size: 0.82rem; font-weight: 600; color: #374151; white-space: nowrap; }
        .sb-date-row input[type="date"] {
            border: 1px solid #d1d5db; border-radius: 6px; padding: 5px 8px; font-size: 0.85rem; color: #111827;
        }
        .sb-select-all-row { display: flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 600; color: #374151; margin-bottom: 10px; flex-shrink: 0; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb; }
        .sb-list { overflow-y: auto; flex: 1; }
        .sb-row {
            display: grid;
            grid-template-columns: 28px 1fr 150px;
            align-items: center;
            gap: 8px;
            padding: 6px 4px;
            border-bottom: 1px solid #f3f4f6;
        }
        .sb-row:hover { background: #f9fafb; }
        .sb-name { font-size: 0.85rem; font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 6px; }
        .sb-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .sb-row select, .sb-row input[type="text"] {
            width: 100%; border: 1px solid #d1d5db; border-radius: 5px; padding: 4px 6px; font-size: 0.78rem; color: #111827;
        }
        .sb-row select:focus, .sb-row input[type="text"]:focus { outline: none; border-color: #60a5fa; }
        .ss-btn-bulk { background: #059669; color: #fff; }
        .ss-btn-bulk:hover { background: #047857; }
    </style>
    @endpush

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- 月ナビ --}}
    <div class="flex items-center gap-4 mb-5">
        <a href="{{ route('admin.staff_schedule.index', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}"
           class="admin-btn admin-btn-info text-sm px-3 py-1.5">
            <i class="fas fa-chevron-left"></i> 前月
        </a>
        <h2 class="text-lg font-bold text-gray-800">{{ $year }}年{{ $month }}月</h2>
        <a href="{{ route('admin.staff_schedule.index', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}"
           class="admin-btn admin-btn-info text-sm px-3 py-1.5">
            翌月 <i class="fas fa-chevron-right"></i>
        </a>
        <button type="button" id="ss-bulk-open"
                class="ss-btn ss-btn-bulk ml-auto flex items-center gap-1.5 text-sm px-4 py-1.5">
            <i class="fas fa-users"></i> 一括設定
        </button>
    </div>

    {{-- カレンダー --}}
    <div class="overflow-x-auto">
        <table class="ss-calendar">
            <thead>
                <tr>
                    @foreach(['日','月','火','水','木','金','土'] as $dow)
                        <th>{{ $dow }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $today = \Carbon\Carbon::today()->format('Y-m-d'); @endphp
                @foreach($calendar as $week)
                    <tr>
                        @foreach($week as $day)
                            @php
                                $dateKey    = $day->format('Y-m-d');
                                $inMonth    = $day->month == $month;
                                $isToday    = $dateKey === $today;
                                $daySchedules = $schedules[$dateKey] ?? collect();
                            @endphp
                            <td>
                                <div class="ss-day-num {{ $isToday ? 'is-today' : '' }} {{ !$inMonth ? 'is-other-month' : '' }}">
                                    {{ $day->day }}
                                </div>

                                @if($inMonth)
                                    @foreach($offices as $cate)
                                        @php
                                            $cateSchedules = $daySchedules->where('office_id', $cate->id);
                                            $color = '#6366f1';
                                        @endphp
                                        <div class="ss-cate-block" style="background:{{ $color }}18; border-left: 3px solid {{ $color }};">
                                            <div class="ss-cate-label" style="color:{{ $color }};">{{ $cate->name }}</div>
                                            @forelse($cateSchedules as $ss)
                                                <div class="ss-staff-item">
                                                    <span class="ss-staff-name">{{ $ss->staff->name ?? '?' }}</span>
                                                    <button class="ss-edit-btn"
                                                        style="background:{{ $color }};"
                                                        data-date="{{ $dateKey }}"
                                                        data-staff="{{ $ss->staff_id }}"
                                                        data-cate="{{ $ss->office_id }}"
                                                        data-memo="{{ $ss->memo }}">編集</button>
                                                </div>
                                            @empty
                                                <div class="ss-empty">なし</div>
                                            @endforelse
                                        </div>
                                    @endforeach

                                    <button class="ss-add-btn" data-date="{{ $dateKey }}">＋ 追加</button>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- モーダル --}}
    <div id="ss-modal">
        <div id="ss-modal-box">
            <h3 id="ss-modal-title">スケジュール追加</h3>
            <form id="ss-form">
                <input type="hidden" id="ss-date" name="date">
                <input type="hidden" id="ss-orig-staff" name="original_staff_id">
                <input type="hidden" id="ss-orig-date" name="original_date">
                <input type="hidden" id="ss-orig-cate" name="original_office_id">

                <div class="ss-form-row">
                    <label>事務所カテゴリ</label>
                    <select name="office_id" id="ss-cate">
                        <option value="">-- 選択 --</option>
                        @foreach($offices as $cate)
                            <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ss-form-row">
                    <label>スタッフ <span class="text-red-500">*</span></label>
                    <select name="staff_id" id="ss-staff" required>
                        <option value="">-- 選択 --</option>
                        @foreach($staffs as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ss-form-row">
                    <label>メモ</label>
                    <textarea name="memo" id="ss-memo" rows="2"></textarea>
                </div>

                <div class="ss-delete-check" id="ss-delete-wrap" style="display:none;">
                    <input type="checkbox" id="ss-delete" name="delete_schedule" value="1">
                    <label for="ss-delete">このスケジュールを削除する</label>
                </div>

                <div class="ss-modal-footer">
                    <button type="button" class="ss-btn ss-btn-cancel" id="ss-close">キャンセル</button>
                    <button type="submit" class="ss-btn ss-btn-save">保存</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 一括設定モーダル --}}
    <div id="ss-bulk-modal">
        <div id="ss-bulk-box">
            <h3>
                <i class="fas fa-users mr-2 text-green-600"></i>スケジュール一括設定
                <span style="font-size:0.85rem; font-weight:400; color:#6b7280; margin-left:8px;">
                    {{ $year }}年{{ $month }}月 全日程
                </span>
            </h3>

            <form method="POST" action="{{ route('admin.staff_schedule.bulk-store') }}">
                @csrf
                <input type="hidden" name="bulk_year"  value="{{ $year }}">
                <input type="hidden" name="bulk_month" value="{{ $month }}">
                <div class="sb-date-row" style="margin-bottom:10px; font-size:0.82rem; color:#374151;">
                    <i class="fas fa-calendar-alt text-green-600 mr-1"></i>
                    対象期間：<strong>{{ $year }}年{{ $month }}月1日 〜 {{ \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth()->day }}日</strong>
                    （{{ \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth()->day }}日間）
                    <span style="margin-left:10px; color:#9ca3af; font-size:0.75rem;">※既存スケジュールはスキップされます</span>
                </div>

                <div class="sb-select-all-row">
                    <input type="checkbox" id="sb-select-all">
                    <label for="sb-select-all" style="cursor:pointer;">全スタッフを選択/解除</label>
                    <span style="margin-left:auto; font-size:0.72rem; color:#6b7280;">各スタッフのweekly_schedule設定に従い曜日・事務所を登録 / メモ任意</span>
                </div>

                <div class="sb-list">
                    @foreach($staffs as $staff)
                    <div class="sb-row">
                        <input type="checkbox" name="staffs[{{ $staff->id }}][selected]" value="1" class="sb-chk">
                        <div class="sb-name">
                            @if($staff->color)
                                <span class="sb-dot" style="background:{{ $staff->color }};"></span>
                            @endif
                            {{ $staff->name }}
                            <span style="font-size:0.68rem; color:#9ca3af; font-weight:400;">{{ $staff->work_days_label }}</span>
                        </div>
                        <input type="text" name="staffs[{{ $staff->id }}][memo]" placeholder="メモ（任意）">
                    </div>
                    @endforeach
                </div>

                <div class="ss-modal-footer" style="flex-shrink:0; margin-top:14px;">
                    <button type="button" class="ss-btn ss-btn-cancel" id="sb-close">キャンセル</button>
                    <button type="submit" class="ss-btn ss-btn-bulk">
                        <i class="fas fa-check mr-1"></i> 一括設定
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    (function () {
        const modal      = document.getElementById('ss-modal');
        const form       = document.getElementById('ss-form');
        const title      = document.getElementById('ss-modal-title');
        const fDate      = document.getElementById('ss-date');
        const fStaff     = document.getElementById('ss-staff');
        const fCate      = document.getElementById('ss-cate');
        const fMemo      = document.getElementById('ss-memo');
        const fDel       = document.getElementById('ss-delete');
        const fDelWrap   = document.getElementById('ss-delete-wrap');
        const fOrigStaff = document.getElementById('ss-orig-staff');
        const fOrigDate  = document.getElementById('ss-orig-date');
        const fOrigCate  = document.getElementById('ss-orig-cate');
        const csrf       = document.querySelector('meta[name="csrf-token"]').content;

        function openModal(isEdit) {
            title.textContent = isEdit ? 'スケジュール編集' : 'スケジュール追加';
            fDelWrap.style.display = isEdit ? 'flex' : 'none';
            fDel.checked = false;
            modal.classList.add('open');
        }

        document.getElementById('ss-close').addEventListener('click', () => modal.classList.remove('open'));
        modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('open'); });

        // 追加ボタン
        document.querySelectorAll('.ss-add-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                fDate.value      = this.dataset.date;
                fStaff.value     = '';
                fCate.value      = '';
                fMemo.value      = '';
                fOrigStaff.value = '';
                fOrigDate.value  = '';
                fOrigCate.value  = '';
                openModal(false);
            });
        });

        // 編集ボタン
        document.querySelectorAll('.ss-edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const date  = this.dataset.date;
                const staff = this.dataset.staff;
                const cate  = this.dataset.cate;

                fDate.value      = date;
                fStaff.value     = staff;
                fCate.value      = cate;
                fMemo.value      = this.dataset.memo || '';
                fOrigStaff.value = staff;
                fOrigDate.value  = date;
                fOrigCate.value  = cate;
                openModal(true);
            });
        });

        // ===== 一括設定モーダル =====
        const bulkModal   = document.getElementById('ss-bulk-modal');
        const bulkOpen    = document.getElementById('ss-bulk-open');
        const bulkClose   = document.getElementById('sb-close');
        const selectAll   = document.getElementById('sb-select-all');

        bulkOpen.addEventListener('click', () => bulkModal.classList.add('open'));
        bulkClose.addEventListener('click', () => bulkModal.classList.remove('open'));
        bulkModal.addEventListener('click', e => { if (e.target === bulkModal) bulkModal.classList.remove('open'); });

        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.sb-chk').forEach(chk => { chk.checked = this.checked; });
        });
        document.querySelectorAll('.sb-chk').forEach(chk => {
            chk.addEventListener('change', function () {
                const all  = document.querySelectorAll('.sb-chk');
                const checked = document.querySelectorAll('.sb-chk:checked');
                selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
                selectAll.checked = checked.length === all.length;
            });
        });

        // フォーム送信
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (fDel.checked && !confirm('このスケジュールを削除しますか？')) return;

            const data = new FormData(form);
            // チェックされていない場合はdelete_scheduleを除去
            if (!fDel.checked) data.delete('delete_schedule');

            fetch('{{ route("admin.staff_schedule.store") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                credentials: 'same-origin',
                body: data,
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    modal.classList.remove('open');
                    location.reload();
                } else {
                    alert(res.message || 'エラーが発生しました');
                }
            })
            .catch(() => alert('通信エラーが発生しました'));
        });
    })();
    </script>
    @endpush
</x-admin-layout>
