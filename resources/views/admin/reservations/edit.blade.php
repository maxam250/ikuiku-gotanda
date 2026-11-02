<x-admin-layout>


    <x-slot name="header">
    予約管理編集
    </x-slot>

    <div class="back">
        <a href="{{ route('admin.reservations.index') }}" class="admin-btn admin-btn-info">
            <i class="fas fa-arrow-left"></i>戻る
        </a>
    </div>

    <section class="MessageArea">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    </section>

    <section>
        <div class="form-table">
    <form action="{{ route('admin.reservations.update', $reservations->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')


            <div class="form_section grid grid-cols-1 sm:grid-cols-3 gap-8">
                <div class="form_group form_parts">
                    <label>ステータス</label>
                    <select name="flag" class="flag-select w-full" data-reservation-id="{{ $reservations->id }}">
                        <option value="0" {{ old('flag',$reservations['flag']) == 0 ? 'selected' : '' }}>未設定</option>
                        <option value="1" {{ old('flag',$reservations['flag']) == 1 ? 'selected' : '' }}>確認中</option>
                        <option value="2" {{ old('flag',$reservations['flag']) == 2 ? 'selected' : '' }}>確定</option>
                        <option value="3" {{ old('flag',$reservations['flag']) == 3 ? 'selected' : '' }}>保留</option>
                        <option value="4" {{ old('flag',$reservations['flag']) == 4 ? 'selected' : '' }}>キャンセル</option>
                    </select>
                </div>
                <div class="form_group form_parts">
                    <label>予約タイプ</label>
                    <input type="radio" name="kind" id="kind1" class="w-full" value="1" {{ old('kind', $reservations['kind'] ?? '') == '1' || empty(old('kind', $reservations['kind'] ?? '')) ? 'checked' : '' }}>
                    <label for="kind1" class="radio">通常予約</label>
                    <input type="radio" name="kind" id="kind2" class="w-full" value="2" {{ old('kind', $reservations['kind'] ?? '') == '2' ? 'checked' : '' }}>
                    <label for="kind2" class="radio">リクエスト予約</label>
                </div>

                {{-- 担当スタッフ --}}
                <div class="form_section">
                    <div class="form_group form_parts">
                        <label>担当スタッフ</label>
                        <select name="staff_id" id="staff_id" class="w-full">
                            <option value="">-- 未選択 --</option>
                            @foreach($staffs as $staff)
                                <option value="{{ $staff->id }}"
                                    {{ old('staff_id', $reservations['staff_id'] ?? session('selected_staff.id')) == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>


            <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="form_group">
                    <label for="date">日時</label>
                    <div class="flex items-center gap-2" style="flex:1;">
                    <input type="date" name="date" id="date" class="w-auto min-w-[10rem]" value="{{ old('date', $reservations['date']->format('Y-m-d') ?? '') }}">
                    @php
                        $timeStart = isset($reservations['time_start'])
                            ? \Carbon\Carbon::parse($reservations['time_start'])->format('H:i')
                            : '';
                    @endphp
                    <select name="time_start" style="flex:1;">
                        @foreach (generateTimeOptions('09:00', '23:00') as $value => $label)
                        <option value="{{ $value }}" {{ old('time_start', $timeStart ?? '') == $label ? 'selected' : '' }}>{{ $label }}　</option>
                        @endforeach
                    </select><div class="unit">～</div>
                    </div>
                </div>
            </div>

            <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="form_group">
                    <label for="course_id">コース</label>
                    <select name="course_id" id="course_id" class="w-full">
                        <option value="" disabled selected>選択してください</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}"{{ old('course_id', $reservations['course_id'] ?? '') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form_group">
                    <label for="course_time">時間</label>
                    <select name="course_time" id="course_time" class="">
                        <option value="" disabled selected>選択してください</option>
                        @foreach (generateMinuteOptions(60, 300, 30) as $value => $label)
                            <option value="{{ $value }}" {{ old('course_time', $reservations['course_time'] ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="form_group">
                    <label for="item_id">キャスト</label>
                    <div class="flex items-center flex-nowrap gap-2">
                    <input type="text" name="item_name" id="item_name" class="size_s" value="{{ old('item_name', $reservations['item_name'] ?? '') }}">
                    <select name="item_id" id="item_id" class="select-fit">
                        <option value="">指名なし</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-name="{{ $item->item_name }}"{{ old('item_id', $reservations['item_id'] ?? '') == $item->id ? 'selected' : '' }}>{{ $item->item_name }}</option>
                        @endforeach
                    </select>
                    </div>
                </div>
                <div class="form_group">
                    <label for="item_name_other">キャストその他</label>
                    <input type="text" name="item_name_other" id="item_name_other" class="w-full" value="{{ old('item_name_other', $reservations['item_name_other'] ?? '') }}">
                </div>
            </div>

            <div class="form_section">
                <div class="form_group">
                    <label for="myoption">マイオプション</label>
                    <div class="flex flex-wrap form_parts" id="myoption-container">
                        @if($reservations->item && $reservations->item->myOptions)
                            @foreach($reservations->item->myOptions as $myOption)
                                <div class="option-item {{ in_array($myOption->id, $reservations->myoption ?? []) ? 'selected' : '' }}">
                                    <input type="checkbox"
                                        name="myoption[]"
                                        id="myoption_{{ $myOption->id }}"
                                        value="{{ $myOption->id }}"
                                        data-price="{{ $myOption->price }}"
                                        {{ in_array($myOption->id, $reservations->myoption ?? []) ? 'checked' : '' }}>
                                    <label for="myoption_{{ $myOption->id }}" class="my-item-label">
                                        @if($myOption->filename)
                                            <img src="{{ Storage::url('myoption/' . $myOption->filename) }}"
                                                alt=""
                                                class="my-item-thumb"
                                                width="60"
                                                height="60">
                                        @endif
                                        <span class="my-item-name">{{ $myOption->name }}</span>
                                        <span class="my-item-price">{{ number_format($myOption->price) }}円</span>
                                    </label>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="form_section">
                <div class="form_group">
                    <label for="mycostume">マイコスチューム</label>
                    <div class="flex flex-wrap form_parts" id="mycostume-container">
                        @if($reservations->item && $reservations->item->myCostumes)
                            @foreach($reservations->item->myCostumes as $myCostume)
                                <div class="costume-item {{ in_array($myCostume->id, $reservations->mycostume ?? []) ? 'selected' : '' }}">
                                    <input type="checkbox"
                                        name="mycostume[]"
                                        id="mycostume_{{ $myCostume->id }}"
                                        value="{{ $myCostume->id }}"
                                        data-price="{{ $myCostume->price }}"
                                        {{ in_array($myCostume->id, $reservations->mycostume ?? []) ? 'checked' : '' }}>
                                    <label for="mycostume_{{ $myCostume->id }}" class="my-item-label">
                                        @if($myCostume->filename)
                                            <img src="{{ Storage::url('mycostume/' . $myCostume->filename) }}"
                                                alt=""
                                                class="my-item-thumb"
                                                width="60"
                                                height="60">
                                        @endif
                                        <span class="my-item-name">{{ $myCostume->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>


            <div class="form_section">
                <div class="form_group form_parts">
                    <label for="option">オプション</label>
                    @php
                        $oldOption = old('option', $reservations['option'] ?? '');
                        $optionVals = is_array($oldOption) ? $oldOption : explode('-', $oldOption);
                    @endphp
                    @foreach($options as $option)
                    <input type="checkbox"
                    name="option[]"
                    id="option{{ $option->id }}"
                    value="{{ $option->id }}"
                    {{ in_array($option->id, $optionVals) ? 'checked' : '' }}>
                    <label for="option{{ $option->id }}" class="checkbox">{{ $option->title }}</label>
                    @endforeach
                </div>
            </div>

            <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="form_group form_parts">
                    <label for="option_pay">オプション有料</label>
                    @php
                        $oldOptionPay = old('option_pay', $reservations['option_pay'] ?? '');
                        $optionPayVals = is_array($oldOptionPay) ? $oldOptionPay : explode('-', $oldOptionPay);
                    @endphp
                    @foreach($option_pays as $option_pay)
                    <input type="checkbox"
                    name="option_pay[]"
                    id="option_pay{{ $option_pay->id }}"
                    value="{{ $option_pay->id }}"
                    data-price="{{ $option_pay->price ?? 0 }}"
                    {{ in_array($option_pay->id, $optionPayVals) ? 'checked' : '' }}>
                    <label for="option_pay{{ $option_pay->id }}" class="checkbox">{{ $option_pay->title }}
                    @if($option_pay->price) ({{ number_format($option_pay->price) }}円) @endif
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="form_group">
                    <label for="name">名前</label>
                    <div class="flex items-center flex-nowrap gap-2">
                    <input type="text" name="name" id="name" class="size_s" value="{{ old('name', $reservations['name'] ?? '') }}" required>
                    <select name="member_id" id="member_id" class="select-fit">
                        <option value="0" data-name="">非会員</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" data-name="{{ $member->name }}"{{ old('member_id', $reservations['member_id']) == $member->id ? 'selected' : '' }}>[{{ $member->id }}] {{ $member->name }}</option>
                        @endforeach
                    </select>
                    </div>
                </div>
            </div>

            <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="form_group">
                    <label for="email">メールアドレス</label>
                    <input type="text" name="email" id="email" class="w-full" value="{{ old('email', $reservations['email'] ?? '') }}" >
                </div>
                <div class="form_group">
                    <label for="tel">電話番号</label>
                    @php
                        $oldTel = old('tel', $reservations['tel'] ?? '');
                        $telParts = is_array($oldTel) ? $oldTel : explode('-', $oldTel);
                    @endphp
                    <div class="flex items-center gap-2" style="flex:1;">
                    <input type="text" name="tel[]" id="tel" class="w-28" value="{{ $telParts[0] ?? '' }}"  placeholder="090"><div class="unit">-</div>
                    <input type="text" name="tel[]" id="tel2" class="w-28" value="{{ $telParts[1] ?? '' }}"  placeholder="0000"><div class="unit">-</div>
                    <input type="text" name="tel[]" id="tel3" class="w-28" value="{{ $telParts[2] ?? '' }}"  placeholder="0000">
                    </div>
                </div>
            </div>

            <!-- <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="form_group">
                    <label for="equipment_id">スタジオ</label>
                    <select name="equipment_id" id="equipment_id" class="w-full">
                        <option value="">指定なし</option>
                        @foreach($equipments as $equipment)
                            <option value="{{ $equipment->id }}"{{ old('equipment_id', $reservations['equipment_id'] ?? '') == $equipment->id ? 'selected' : '' }}>{{ $equipment->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div> -->

            <div class="form_section">
                <div class="form_group">
                    @php
                        $oldCostume = old('costume', $reservations['costume'] ?? '');
                        $costumeVals = is_array($oldCostume) ? $oldCostume : explode('-', $oldCostume);
                        $costumeVals1 = $costumeVals[0] ?? '';
                        $costumeVals2 = $costumeVals[1] ?? '';
                        $costumeVals3 = $costumeVals[2] ?? '';
                    @endphp
                    <label for="costume1">コスチューム</label>
                    <div class="w-full flex items-center gap-4">
                    <section class="mr-3">
                    <select name="costume1" id="costume1" class="w-fullx">
                        <option value="">コスチューム①</option>
                        @foreach($costumeCates as $cate)
                            @if($cate->costumes->count())
                                <optgroup label="{{ $cate->title }}">
                                    @foreach($cate->costumes as $costume)
                                        <option value="{{ $costume->id }}" data-img="{{ Storage::url('costume/' . $costume->filename) }}" {{ $costume->id == $costumeVals1 ? 'selected' : '' }}>{{ $costume->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                    <div id="costume1-preview" style="margin-top:10px;">
                        <img src="" id="costume1-img" style="display:none; width:100px;">
                    </div>
                    </section>

                    <section class="mr-3">
                        <select name="costume2" id="costume2" class="w-fullx">
                            <option value="">コスチューム②</option>
                            @foreach($costumeCates as $cate)
                                @if($cate->costumes->count())
                                    <optgroup label="{{ $cate->title }}">
                                        @foreach($cate->costumes as $costume)
                                            <option value="{{ $costume->id }}" data-img="{{ Storage::url('costume/' . $costume->filename) }}" {{ $costume->id == $costumeVals2 ? 'selected' : '' }}>{{ $costume->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                        <div id="costume2-preview" style="margin-top:10px;">
                            <img src="" id="costume2-img" style="display:none; width:100px;">
                        </div>
                    </section>

                    <section>
                        <select name="costume3" id="costume3" class="w-fullx">
                            <option value="">コスチューム③</option>
                            @foreach($costumeCates as $cate)
                                @if($cate->costumes->count())
                                    <optgroup label="{{ $cate->title }}">
                                        @foreach($cate->costumes as $costume)
                                            <option value="{{ $costume->id }}" data-img="{{ Storage::url('costume/' . $costume->filename) }}" {{ $costume->id == $costumeVals3 ? 'selected' : '' }}>{{ $costume->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                        <div id="costume3-preview" style="margin-top:10px;">
                            <img src="" id="costume3-img" style="display:none; width:100px;">
                        </div>
                    </section>
                    </div>
                </div>
            </div>

            <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="form_group">
                    <label for="area">ご利用エリア</label>
                    <input type="text" name="area" id="area" class="w-full" value="{{ old('area', $reservations['area'] ?? '') }}" >
                </div>
                <div class="form_group">
                    <label for="place">具体的な場所</label>
                    <input type="text" name="place" id="place" class="w-full" value="{{ old('place', $reservations['place'] ?? '') }}" >
                </div>
            </div>

            <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="form_group">
                    <label for="meet">待ち合わせ方法</label>
                    <input type="text" name="meet" id="meet" class="w-full" value="{{ old('meet', $reservations['meet'] ?? '') }}" >
                </div>
                <div class="form_group">
                    <label for="address">住所</label>
                    <input type="text" name="address" id="address" class="w-full" value="{{ old('address', $reservations['address'] ?? '') }}" >
                </div>
            </div>
            <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="form_group">
                    <label for="comment">備考</label>
                    <textarea name="comment" id="comment" class="w-full h-40 text-sm">{{ old('comment', $reservations['comment'] ?? '') }}</textarea>
                </div>
                <div class="form_group">
                    <label for="memo">メモ</label>
                    <textarea name="memo" id="memo" class="w-full h-40 text-sm">{{ old('memo', $reservations['memo'] ?? '') }}</textarea>
                </div>
            </div>
            <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="form_group">
                    <label for="memo_share">メモ（共有用）</label>
                    <textarea name="memo_share" id="memo_share" class="w-full h-40 text-sm">{{ old('memo_share', $reservations['memo_share'] ?? '') }}</textarea>
                </div>
                <div class="form_group">
                    <label for="memo_result">予約完了時のメモ</label>
                    <textarea name="memo_result" id="memo_result" class="w-full h-40 text-sm">{{ old('memo_result', $reservations['memo_result'] ?? '') }}</textarea>
                </div>
            </div>
                <!-- 料金設定セクション -->
                <div class="fee-section">
                    <h3 class="fee-section-title">料金設定</h3>
                    <div class="fee-grid">
                        <div class="form_group fee-item">
                            <label for="fee_course">コース料金</label>
                            <input type="number" name="fee_course" id="fee_course" class="w-full size_s fee-input" value="{{ old('fee_course', $reservations['fee_course'] ?? 0) }}" readonly>
                            <div class="unit">円</div>
                            <small class="fee-note">※自動計算</small>
                        </div>
                        <div class="form_group fee-item">
                            <label for="fee_option">オプション料金</label>
                            <input type="number" name="fee_option" id="fee_option" class="w-full size_s fee-input" value="{{ old('fee_option', $reservations['fee_option'] ?? 0) }}" readonly>
                            <div class="unit">円</div>
                            <small class="fee-note">※自動計算</small>
                        </div>
                        <div class="form_group fee-item">
                            <label for="fee_myoption">マイオプション料金</label>
                            <input type="number" name="fee_myoption" id="fee_myoption" class="w-full size_s fee-input" value="{{ old('fee_myoption', $reservations['fee_myoption'] ?? 0) }}" readonly>
                            <div class="unit">円</div>
                            <small class="fee-note">※自動計算</small>
                        </div>
                        {{--
                        <div class="form_group fee-item">
                            <label for="fee_mycostume">マイコスチューム料金</label>
                            <input type="number" name="fee_mycostume" id="fee_mycostume" class="w-full size_s fee-input" value="{{ old('fee_mycostume', $reservations['fee_mycostume'] ?? 0) }}" readonly>
                            <div class="unit">円</div>
                            <small class="fee-note">※自動計算</small>
                        </div>
                        --}}
                        <div class="form_group fee-item">
                            <label for="fee_adjust">調整金額</label>
                            <input type="number" name="fee_adjust" id="fee_adjust" class="w-full size_s fee-input" value="{{ old('fee_adjust', $reservations['fee_adjust'] ?? 0) }}" onchange="updateTotalPrice()">
                            <div class="unit">円</div>
                        </div>
                        <div class="form_group">
                            <label for="fee_adjust_memo">調整金メモ</label>
                            <textarea name="fee_adjust_memo" id="fee_adjust_memo" class="w-full">{{ old('fee_adjust_memo', $reservations['fee_adjust_memo']) }}</textarea>
                        </div>
                    </div>
                </div>


                <div class="form_group total-price-section">
                    <label class="form_label total-label" for="fee_total">合計金額</label>
                    <div class="total-price-row">
                        <input type="number" name="fee_total" id="fee_total" class="total-input" style="width:200px" value="{{ old('fee_total', $reservations['fee_total'] ?? 0) }}" readonly>
                        <div class="unit total-unit">円</div>
                        <span id="fee_total_intax_display" class="total-intax-text ml-6" aria-live="polite"></span>
                    </div>
                </div>
                <div class="form_group">
                    <button type="submit" class="admin-btn admin-btn-submit">
                <i class="fas fa-save"></i>更新する
            </button>
                </div>
        </div>
    </form>
    </section>

<script>
function fitSelectWidth(sel) {
    const span = document.createElement('span');
    span.style.cssText = 'visibility:hidden;position:fixed;white-space:nowrap;font-size:14px;padding:0 2.5em;';
    span.textContent = sel.options[sel.selectedIndex]?.text ?? '';
    document.body.appendChild(span);
    const w = span.offsetWidth;
    document.body.removeChild(span);
    sel.style.width = Math.max(80, w) + 'px';
}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.select-fit').forEach(sel => {
        fitSelectWidth(sel);
        sel.addEventListener('change', () => fitSelectWidth(sel));
    });
});

    document.addEventListener('DOMContentLoaded', function() {
        const TAX_INTAX = @json((float) config('tax.intax'));

        function formatIntaxDisplay(exTaxTotal) {
            const n = Number(exTaxTotal) || 0;
            const intax = Math.round(n * TAX_INTAX);
            return '税込 ' + new Intl.NumberFormat('ja-JP').format(intax) + '円';
        }

        function refreshFeeTotalIntaxDisplay() {
            const el = document.getElementById('fee_total_intax_display');
            const totalField = document.getElementById('fee_total');
            if (!el || !totalField) return;
            el.textContent = formatIntaxDisplay(totalField.value);
        }

       /*
        var courseId = "{{ old('course_id', $reservations['course_id']) }}";
        var courseFeeSelect = document.getElementById('course_time');
        var oldCourseTime = "{{ old('course_time', $reservations['course_time']) }}";

        function loadCourseFees(courseId) {
            if (courseId) {
                fetch(`/get-course-fees/${courseId}`)
                    .then(response => response.json())
                    .then(data => {
                        courseFeeSelect.innerHTML = '<option value="" disabled selected>選択してください</option>';
                        data.forEach(courseFee => {
                            var option = document.createElement('option');
                            option.value = courseFee.id;
                            option.textContent = courseFee.course;
                            if (courseFee.id == oldCourseTime) {
                                option.selected = true;
                            }
                            courseFeeSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        // 初期ロード時にコース時間をロード
        loadCourseFees(courseId);

        // コース変更時のイベントリスナー
        document.getElementById('course_id').addEventListener('change', function() {
            loadCourseFees(this.value);
        });
*/
        // 現在の予約データをJavaScriptで利用できるようにする
        const currentMyOptions = @json($reservations->myoption ?? []);
        const currentMyCostumes = @json($reservations->mycostume ?? []);

        // コース、時間、キャスト変更時の料金計算
        document.getElementById('course_id').addEventListener('change', function() {
            calculateFees();
        });

        document.getElementById('course_time').addEventListener('change', function() {
            calculateFees();
        });

        // 料金計算関数
        function calculateFees() {
            const courseId = document.getElementById('course_id').value;
            const courseTime = document.getElementById('course_time').value;
            const itemId = document.getElementById('item_id').value;

            if (courseId && courseTime && itemId) {
                fetch(`/kanrigamen/reservation/calculate-fees`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        course_id: courseId,
                        course_time: courseTime,
                        item_id: itemId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('fee_course').value = data.fee_course;
                        // 合計金額を更新
                        updateTotalPrice();
                    } else {
                        console.error('料金計算エラー:', data.error);
                    }
                })
                .catch(error => {
                    console.error('料金計算API呼び出しエラー:', error);
                });
            } else {
                // いずれかの値が未選択の場合は料金をクリア
                if (document.getElementById('fee_course')) {
                    document.getElementById('fee_course').value = 0;
                }
                updateTotalPrice();
            }
        }

        // キャスト選択時の処理（料金計算も含む）
        document.getElementById('item_id').addEventListener('change', function() {
            // 料金計算を実行
            calculateFees();
            const itemId = this.value;
            const myoptionContainer = document.getElementById('myoption-container');
            const mycostumeContainer = document.getElementById('mycostume-container');

            // キャストが選択されていない場合、コンテナを空にして料金を更新
            if (!itemId) {
                myoptionContainer.innerHTML = '';
                mycostumeContainer.innerHTML = '';
                updateTotalPrice();
                return;
            }

            // キャストのマイオプションとマイコスチュームを取得
            fetch(`/kanrigamen/cast-items/${itemId}`)
                .then(response => response.json())
                .then(data => {
                    // マイオプションの表示を更新
                    myoptionContainer.innerHTML = data.myOptions.map(option => `
                        <div class="option-item ${currentMyOptions.includes(option.id) ? 'selected' : ''}">
                            <input type="checkbox"
                                   name="myoption[]"
                                   id="myoption_${option.id}"
                                   value="${option.id}"
                                   data-price="${option.price}"
                                   ${currentMyOptions.includes(option.id) ? 'checked' : ''}>
                            <label for="myoption_${option.id}" class="my-item-label">
                                ${option.image_url ?
                                    `<img src="${option.image_url}"
                                          alt=""
                                          class="my-item-thumb"
                                          width="60"
                                          height="60">` : ''}
                                <span class="my-item-name">${option.name}</span>
                                <span class="my-item-price">${new Intl.NumberFormat('ja-JP').format(option.price)}円</span>
                            </label>
                        </div>
                    `).join('');

                    // マイコスチュームの表示を更新
                    mycostumeContainer.innerHTML = data.myCostumes.map(costume => `
                        <div class="costume-item ${currentMyCostumes.includes(costume.id) ? 'selected' : ''}">
                            <input type="checkbox"
                                   name="mycostume[]"
                                   id="mycostume_${costume.id}"
                                   value="${costume.id}"
                                   data-price="${costume.price}"
                                   ${currentMyCostumes.includes(costume.id) ? 'checked' : ''}>
                            <label for="mycostume_${costume.id}" class="my-item-label">
                                ${costume.image_url ?
                                    `<img src="${costume.image_url}"
                                          alt=""
                                          class="my-item-thumb"
                                          width="60"
                                          height="60">` : ''}
                                <span class="my-item-name">${costume.name}</span>
                            </label>
                        </div>
                    `).join('');

                    // チェックボックスのイベントリスナーを追加
                    document.querySelectorAll('input[name="myoption[]"]').forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            // 選択状態に応じてクラスを切り替え
                            const parentItem = this.closest('.option-item');
                            if (this.checked) {
                                parentItem.classList.add('selected');
                            } else {
                                parentItem.classList.remove('selected');
                            }
                            updateMyOptionFee();
                            updateTotalPrice();
                        });
                    });
                    document.querySelectorAll('input[name="mycostume[]"]').forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            // 選択状態に応じてクラスを切り替え
                            const parentItem = this.closest('.costume-item');
                            if (this.checked) {
                                parentItem.classList.add('selected');
                            } else {
                                parentItem.classList.remove('selected');
                            }
                            updateMyCostumeFee();
                            updateTotalPrice();
                        });
                    });

                    updateMyOptionFee();
                    updateMyCostumeFee();
                    updateTotalPrice();
                })
                .catch(error => console.error('Error:', error));
        });

        // マイオプション料金を更新する関数
        function updateMyOptionFee() {
            let total = 0;
            document.querySelectorAll('input[name="myoption[]"]:checked').forEach(checkbox => {
                const raw = checkbox.getAttribute('data-price');
                const n = raw != null && raw !== '' ? parseInt(String(raw).replace(/,/g, ''), 10) : 0;
                total += Number.isFinite(n) ? n : 0;
            });

            const myOptionField = document.getElementById('fee_myoption');
            if (myOptionField) {
                myOptionField.value = total;
            }
        }

        // オプションPay料金を更新する関数
        function updateOptionPayFee() {
            let total = 0;
            document.querySelectorAll('input[name="option_pay[]"]:checked').forEach(checkbox => {
                const price = parseInt(checkbox.getAttribute('data-price')) || 0;
                total += price;
            });

            const optionField = document.getElementById('fee_option');
            if (optionField) {
                optionField.value = total;
            }
        }

        // マイコスチューム料金を更新する関数
        function updateMyCostumeFee() {
            let total = 0;
            document.querySelectorAll('input[name="mycostume[]"]:checked').forEach(checkbox => {
                const raw = checkbox.getAttribute('data-price');
                const n = raw != null && raw !== '' ? parseInt(String(raw).replace(/,/g, ''), 10) : 0;
                total += Number.isFinite(n) ? n : 0;
            });

            const myCostumeField = document.getElementById('fee_mycostume');
            if (myCostumeField) {
                myCostumeField.value = total;
            }
        }

        // 合計金額を更新する関数
        function updateTotalPrice() {
            let total = 0;

            // コース料金
            const courseFee = parseInt(document.getElementById('fee_course').value) || 0;
            total += courseFee;

            // オプション料金（オプションPAY）
            const optionFee = parseInt(document.getElementById('fee_option').value) || 0;
            total += optionFee;

            // マイオプション料金
            const myOptionEl = document.getElementById('fee_myoption');
            if (myOptionEl) {
                total += parseInt(myOptionEl.value, 10) || 0;
            }

            // マイコスチューム料金
            const myCostumeEl = document.getElementById('fee_mycostume');
            if (myCostumeEl) {
                total += parseInt(myCostumeEl.value, 10) || 0;
            }

            // 調整金額
            const adjustFee = parseInt(document.getElementById('fee_adjust').value) || 0;
            total += adjustFee;

            // 合計金額を更新
            document.getElementById('fee_total').value = total;
            refreshFeeTotalIntaxDisplay();
        }

        // コスチュームのプレビュー表示を処理する関数
        function handleCostumePreview(selectId) {
            const select = document.getElementById(selectId);
            const img = document.getElementById(selectId + '-img');

            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value === '') {
                    img.style.display = 'none';
                    img.src = '';
                } else {
                    const imgUrl = selectedOption.getAttribute('data-img');
                    img.src = imgUrl;
                    img.style.display = 'block';
                }
            });

            // 初期表示時のプレビュー設定
            const initialOption = select.options[select.selectedIndex];
            if (initialOption && initialOption.value !== '') {
                const imgUrl = initialOption.getAttribute('data-img');
                img.src = imgUrl;
                img.style.display = 'block';
            }
        }

        // 初期表示時のイベントリスナー設定
        // 料金入力フィールドのイベントリスナー
        ['fee_course', 'fee_option', 'fee_adjust'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', updateTotalPrice);
                element.addEventListener('input', updateTotalPrice);
            }
        });

        // マイオプションとマイコスチュームのチェックボックスのイベントリスナー
        document.querySelectorAll('input[name="myoption[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // 選択状態に応じてクラスを切り替え
                const parentItem = this.closest('.option-item');
                if (this.checked) {
                    parentItem.classList.add('selected');
                } else {
                    parentItem.classList.remove('selected');
                }
                updateMyOptionFee();
                updateTotalPrice();
            });
        });
        document.querySelectorAll('input[name="mycostume[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // 選択状態に応じてクラスを切り替え
                const parentItem = this.closest('.costume-item');
                if (this.checked) {
                    parentItem.classList.add('selected');
                } else {
                    parentItem.classList.remove('selected');
                }
                updateMyCostumeFee();
                updateTotalPrice();
            });
        });

        // オプションPayのチェックボックスにイベントリスナーを追加
        document.querySelectorAll('input[name="option_pay[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateOptionPayFee();
                updateTotalPrice();
            });
        });

        // 各コスチュームセレクトボックスにプレビュー機能を設定
        handleCostumePreview('costume1');
        handleCostumePreview('costume2');
        handleCostumePreview('costume3');

        // member_id→name自動入力
        const memberSelect = document.getElementById('member_id');
        const nameInput = document.getElementById('name');
        if (memberSelect && nameInput) {
            memberSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const memberName = selectedOption.getAttribute('data-name') || '';
                nameInput.value = memberName;
            });
        }

        // item_id→item_name自動入力
        const itemSelect = document.getElementById('item_id');
        const itemNameInput = document.getElementById('item_name');
        if (itemSelect && itemNameInput) {
            itemSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const castName = selectedOption.getAttribute('data-name') || '';
                itemNameInput.value = castName;
            });
        }

        // 初期表示時の各料金を計算
        updateOptionPayFee();

        // 初期表示時：チェック状態からマイ料金を同期してから合計
        updateMyOptionFee();
        updateMyCostumeFee();
        updateTotalPrice();
    });
</script>


</x-admin-layout>
