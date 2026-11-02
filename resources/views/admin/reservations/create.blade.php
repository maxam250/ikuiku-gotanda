
<x-admin-layout>


<x-slot name="header">
予約新規登録
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
</section>

<section>

    <div class="form-table">
<form action="{{ route('admin.reservations.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form_section grid grid-cols-1 sm:grid-cols-3 gap-8">
        <div class="form_group form_parts">
            <label>ステータス</label>
            <select name="flag" class="flag-select w-full">
                <option value="0" {{ old('flag') == 0 ? 'selected' : '' }}>未設定</option>
                <option value="1" {{ old('flag') == 1 ? 'selected' : '' }}>確認中</option>
                <option value="2" {{ old('flag') == 2 ? 'selected' : '' }}>確定</option>
                <option value="3" {{ old('flag') == 3 ? 'selected' : '' }}>保留</option>
            </select>
        </div>

        <div class="form_group form_parts">
            <label>予約タイプ</label>
            <input type="radio" name="kind" id="kind1" class="" value="1" {{ old('kind' ?? '') == '1' || empty(old('kind' ?? '')) ? 'checked' : '' }}>
            <label for="kind1" class="radio" style="font-size: 0.65rem;">通常予約</label>

            <input type="radio" name="kind" id="kind2" class="" value="2" {{ old('kind' ?? '') == '2' ? 'checked' : '' }}>
            <label for="kind2" class="radio" style="font-size: 0.65rem;">リクエスト予約</label>
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
            <input type="date" name="date" id="date" class="w-auto min-w-[10rem]" value="{{ old('date') }}" autocomplete="off">
            <select name="time_start" style="flex:1;">
                @foreach (generateTimeOptions('09:00', '23:00') as $value => $label)
                <option value="{{ $value }}" {{ old('time_start' ?? '') == $value ? 'selected' : '' }}>{{ $label }}　</option>
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
                    <option value="{{ $course->id }}"{{ old('course_id' ?? '') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="form_group">
            <label for="course_time">時間</label>
            <select name="course_time" id="course_time" class="w-auto">
                <option value="" disabled selected>選択してください</option>
                @foreach (generateMinuteOptions(60, 480, 30) as $value => $label)
                    <option value="{{ $value }}" {{ old('course_time') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
        <div class="form_group">
            <label for="item_id">キャスト</label>
            <div class="flex items-center flex-nowrap gap-2">
            <input type="text" name="item_name" id="item_name" class="w-auto min-w-[10rem]" value="{{ old('item_name') ?? '' }}">
            <select name="item_id" id="item_id" class="select-fit">
                <option value="">指名なし</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" data-name="{{ $item->item_name }}"{{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->item_name }}</option>
                @endforeach
            </select>
            </div>
        </div>

        <div class="form_group">
            <label for="item_id">キャストその他</label>
            <input type="text" name="item_name_other" id="item_name_other" class="w-full" value="{{ old('item_name_other') ?? '' }}">
        </div>
    </div>


    <div class="form_section">
        <div class="form_group">
            <label for="myoption">マイオプション</label>
            <div class="flex form_parts" id="myoption-container">
                @php
                    $oldMyOption = old('myoption', []);
                    $myOptionVals = is_array($oldMyOption) ? $oldMyOption : [];
                @endphp
                <!-- マイオプションは動的に追加されます -->
            </div>
        </div>
    </div>
    <div class="form_section">
        <div class="form_group">
            <label for="mycostume">マイコスチューム</label>
            <div class="flex form_parts" id="mycostume-container">
                @php
                    $oldMyCostume = old('mycostume', []);
                    $myCostumeVals = is_array($oldMyCostume) ? $oldMyCostume : [];
                @endphp
                <!-- マイコスチュームは動的に追加されます -->
            </div>
        </div>
    </div>


    <div class="form_section">
        <div class="form_group">
            <label for="option">オプション</label>
            <div class="flex flex-wrap form_parts w-full">
            @php
                $oldOption = old('option' ?? '');
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
    </div>

    <div class="form_section">
        <div class="form_group">
            <label for="option_pay">オプションPAY</label>
            <div class="flex form_parts">
            @php
                $oldOption = old('option_pay' ?? '');
                $optionVals = is_array($oldOption) ? $oldOption : explode('-', $oldOption);
            @endphp
            @foreach($option_pays as $option_pay)
            <input type="checkbox"
            name="option_pay[]"
            id="option_pay{{ $option_pay->id }}"
            value="{{ $option_pay->id }}"
            data-price="{{ $option_pay->price ?? 0 }}"
            {{ in_array($option_pay->id, $optionVals) ? 'checked' : '' }}>
        <label for="option_pay{{ $option_pay->id }}" class="checkbox">{{ $option_pay->title }}
            @if($option_pay->price) ({{ number_format($option_pay->price) }}円) @endif
        </label>
            @endforeach
            </div>
        </div>
    </div>


    <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
        <div class="form_group">
            <label for="name">名前</label>
            <div class="flex items-center flex-nowrap gap-2">
            <input type="text" name="name" id="name" class="w-auto min-w-[10rem]"  value="{{ old('name') ?? '' }}" required>
            <select name="member_id" id="member_id" class="select-fit">
                <option value="0" data-name="">非会員</option>
            @foreach($members as $member)
                <option value="{{ $member->id }}" data-name="{{ $member->name }}"{{ old('member_id') == $member->id ? 'selected' : '' }}>[{{ $member->id }}] {{ $member->name }}</option>
            @endforeach
            </select>
            </div>
            <div class="registered-members-panel mt-2">
                <p class="registered-members-title">登録済み会員</p>
                <div class="registered-members-list" id="registered-members-list">
                    @foreach($members->take(24) as $member)
                        <button type="button" class="registered-member-chip" data-member-id="{{ $member->id }}" data-member-name="{{ $member->name }}">
                            [{{ $member->id }}] {{ $member->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


    <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
        <div class="form_group">
            <label for="email">メールアドレス</label>
            <input type="text" name="email" id="email" class="w-full" value="{{ old('email' ?? '') }}">
        </div>
        <div class="form_group">
            <label for="tel">電話番号</label>
            @php
                $oldTel = old('tel' ?? '');
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
            <select name="equipment_id" id="equipment_id" class="">
                <option value="" disabled selected>指定なし</option>
                @foreach($equipments as $equipment)
                    <option value="{{ $equipment->id }}"{{ old('equipment_id', $oldInput['equipment_id'] ?? '') == $equipment->id ? 'selected' : '' }}>{{ $equipment->title }}</option>
                @endforeach
            </select>
        </div>
    </div> -->


    <div class="form_section">
        <div class="form_group">
            <label for="costume1">コスチューム</label>

            <div class="flex">
            <section class="mr-3">
            <select name="costume1" id="costume1" class="w-full">
                <option value="" disabled selected>コスチューム①</option>
                @foreach($costumeCates as $cate)
                    @if($cate->costumes->count())
                        <optgroup label="{{ $cate->title }}">
                            @foreach($cate->costumes as $costume)
                                <option value="{{ $costume->id }}" data-img="{{ Storage::url('costume/' . $costume->filename) }}" {{ old('costume1', $oldInput['costume1'] ?? '') == $costume->id ? 'selected' : '' }}>{{ $costume->name }}</option>
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
                <select name="costume2" id="costume2" class="w-full">
                    <option value="" disabled selected>コスチューム②</option>
                    @foreach($costumeCates as $cate)
                        @if($cate->costumes->count())
                            <optgroup label="{{ $cate->title }}">
                                @foreach($cate->costumes as $costume)
                                    <option value="{{ $costume->id }}" data-img="{{ Storage::url('costume/' . $costume->filename) }}" {{ old('costume2', $oldInput['costume2'] ?? '') == $costume->id ? 'selected' : '' }}>{{ $costume->name }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
                <div id="costume1-preview" style="margin-top:10px;">
                    <img src="" id="costume2-img" style="display:none; width:100px;">
                </div>
            </section>

            <section>
                <select name="costume3" id="costume3" class="w-full">
                    <option value="" disabled selected>コスチューム③</option>
                    @foreach($costumeCates as $cate)
                        @if($cate->costumes->count())
                            <optgroup label="{{ $cate->title }}">
                                @foreach($cate->costumes as $costume)
                                    <option value="{{ $costume->id }}" data-img="{{ Storage::url('costume/' . $costume->filename) }}" {{ old('costume3', $oldInput['costume3'] ?? '') == $costume->id ? 'selected' : '' }}>{{ $costume->name }}</option>
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
            <input type="text" name="area" id="area" class="w-full" value="{{ old('area') }}" >
        </div>

        <div class="form_group">
            <label for="place">具体的な場所</label>
            <input type="text" name="place" id="place" class="w-full" value="{{ old('place') }}" >
        </div>
    </div>


    <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
        <div class="form_group">
            <label for="meet">待ち合わせ方法</label>
            <input type="text" name="meet" id="meet" class="w-full" value="{{ old('meet') }}" >
        </div>

        <div class="form_group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" class="w-full" value="{{ old('address') }}" >
        </div>
    </div>

    <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
        <div class="form_group">
            <label for="comment">備考</label>
            <textarea name="comment" id="comment" class="w-full h-32">{{ old('comment') }}</textarea>
        </div>

        <div class="form_group">
            <label for="memo">メモ</label>
            <textarea name="memo" id="memo" class="w-full h-32">{{ old('memo') }}</textarea>
        </div>
    </div>

    <div class="form_section grid grid-cols-1 sm:grid-cols-2 gap-8">
        <div class="form_group">
            <label for="memo_share">メモ（共有用）</label>
            <textarea name="memo_share" id="memo_share" class="w-full h-32">{{ old('memo_share') }}</textarea>
        </div>
        <div class="form_group">
            <label for="memo_result">予約完了時のメモ</label>
            <textarea name="memo_result" id="memo_result" class="w-full h-32">{{ old('memo_result') }}</textarea>
        </div>
    </div>


        <!-- 料金設定セクション -->
        <div class="fee-section">
            <h3 class="fee-section-title">料金設定</h3>
            <div class="fee-grid">
                <div class="form_group fee-item">
                    <label for="fee_course">コース料金</label>
                    <input type="number" name="fee_course" id="fee_course" class="size_s fee-input" value="{{ old('fee_course') }}" readonly>
                    <div class="unit">円</div>
                    <small class="fee-note">※自動計算</small>
                </div>
                <div class="form_group fee-item">
                    <label for="fee_option">オプション料金</label>
                    <input type="number" name="fee_option" id="fee_option" class="size_s fee-input" value="{{ old('fee_option') }}" readonly>
                    <div class="unit">円</div>
                    <small class="fee-note">※自動計算</small>
                </div>
                <div class="form_group fee-item">
                    <label for="fee_myoption">マイオプション料金</label>
                    <input type="number" name="fee_myoption" id="fee_myoption" class="size_s fee-input" value="{{ old('fee_myoption') }}" readonly>
                    <div class="unit">円</div>
                    <small class="fee-note">※自動計算</small>
                </div>
                {{--
                <div class="form_group fee-item">
                    <label for="fee_mycostume">マイコスチューム料金</label>
                    <input type="number" name="fee_mycostume" id="fee_mycostume" class="size_s fee-input" value="{{ old('fee_mycostume') }}" readonly>
                    <div class="unit">円</div>
                    <small class="fee-note">※自動計算</small>
                </div>
                --}}
                <div class="form_group fee-item">
                    <label for="fee_adjust">調整金</label>
                    <input type="number" name="fee_adjust" id="fee_adjust" class="size_s fee-input" value="{{ old('fee_adjust') }}" >
                    <div class="unit">円</div>
                </div>
                <div class="form_group">
                    <label for="fee_adjust_memo">調整金メモ</label>
                    <textarea name="fee_adjust_memo" id="fee_adjust_memo" class="w-full">{{ old('fee_adjust_memo') }}</textarea>
                </div>
            </div>
        </div>

        <div class="form_group total-price-section">
            <label for="fee_total" class="total-label">合計金額</label>
            <div class="total-price-row">
                <input type="number" name="fee_total" id="fee_total" class="total-input" style="width:200px" value="{{ old('fee_total') }}" readonly>
                <div class="unit total-unit">円</div>
                <span id="fee_total_intax_display" class="total-intax-text ml-6" aria-live="polite"></span>
            </div>
        </div>

                <div class="form_group">

                <button type="submit" class="admin-btn admin-btn-submit">
                <i class="fas fa-plus"></i>登録する
            </button>
            </div>
    </form>
</div>
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

    // コース変更時の料金計算
    document.getElementById('course_id').addEventListener('change', function() {
        calculateFees();
    });

    document.getElementById('course_time').addEventListener('change', function() {
        calculateFees();
    });

    // 調整金の手動入力時に合計を更新
    const adjustField = document.getElementById('fee_adjust');
    if (adjustField) {
        adjustField.addEventListener('input', updateTotalPrice);
        adjustField.addEventListener('change', updateTotalPrice);
    }

    // オプションPayのチェックボックスにイベントリスナーを追加
    document.querySelectorAll('input[name="option_pay[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateOptionPayFee();
            updateTotalPrice();
        });
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
                    // オプションPAYはチェック状態から再計算（直前までの合計を残さない）
                    document.getElementById('fee_option').value = 0;
                    document.getElementById('fee_myoption').value = 0;
                    const feeMycostumeEl = document.getElementById('fee_mycostume');
                    if (feeMycostumeEl) {
                        feeMycostumeEl.value = 0;
                    }
                    updateOptionPayFee();
                    updateMyOptionFee();
                    updateMyCostumeFee();

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
            document.getElementById('fee_course').value = 0;
            updateTotalPrice();
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

        // マイコスチューム料金（入力欄がある場合のみ）
        const myCostumeEl = document.getElementById('fee_mycostume');
        if (myCostumeEl) {
            total += parseInt(myCostumeEl.value, 10) || 0;
        }

        // 調整金額
        const adjustFee = parseInt(document.getElementById('fee_adjust').value) || 0;
        total += adjustFee;

        // 合計金額をフィールドに表示
        const totalField = document.getElementById('fee_total');
        if (totalField) {
            totalField.value = total;
        }
        refreshFeeTotalIntaxDisplay();
    }

    // フォームに画像を付与（コスチューム選択プレビュー）
    (function setupCostumePreviewImages() {
        const pairs = [
            { selectId: 'costume1', imgId: 'costume1-img' },
            { selectId: 'costume2', imgId: 'costume2-img' },
            { selectId: 'costume3', imgId: 'costume3-img' },
        ];

        pairs.forEach(pair => {
            const select = document.getElementById(pair.selectId);
            const img = document.getElementById(pair.imgId);
            if (!select || !img) return;

            const updateImage = () => {
                const selected = select.options[select.selectedIndex];
                const src = selected.getAttribute('data-img');
                if (src) {
                    img.src = src;
                    img.style.display = 'inline';
                } else {
                    img.style.display = 'none';
                }
            };

            select.addEventListener('change', updateImage);
            updateImage();
        });
    })();

    // キャスト変更：料金API + マイオプション／マイコスチューム一覧（updateMyOptionFee と同じスコープ内で定義）
    document.getElementById('item_id').addEventListener('change', function() {
        calculateFees();

        const itemId = this.value;

        // キャスト名を自動入力
        const selectedOption = this.options[this.selectedIndex];
        const castName = selectedOption.getAttribute('data-name') || '';
        document.getElementById('item_name').value = castName;

        if (itemId) {
            fetch(`/kanrigamen/cast-items/${itemId}`)
                .then(response => response.json())
                .then(data => {
                    // マイオプションの表示
                    const myoptionContainer = document.getElementById('myoption-container');
                    if (myoptionContainer) {
                        myoptionContainer.innerHTML = '';
                        data.myOptions.forEach(option => {
                            const div = document.createElement('div');
                            div.className = 'option-item';
                            div.innerHTML = `
                                <input type="checkbox"
                                    name="myoption[]"
                                    id="myoption${option.id}"
                                    value="${option.id}"
                                    data-price="${option.price ?? 0}">
                                <label for="myoption${option.id}" class="my-item-label">
                                    ${option.image_url ? `<img src="${option.image_url}" alt="" class="my-item-thumb" width="60" height="60">` : ''}
                                    <span class="my-item-name">${option.name}</span>
                                    <span class="my-item-price">${new Intl.NumberFormat('ja-JP').format(option.price)}円</span>
                                </label>
                            `;

                            // チェックボックスにイベントリスナーを追加
                            const checkbox = div.querySelector('input[type="checkbox"]');
                            const syncMyOptionFromCheckbox = () => {
                                const parentItem = checkbox.closest('.option-item');
                                if (checkbox.checked) {
                                    parentItem.classList.add('selected');
                                } else {
                                    parentItem.classList.remove('selected');
                                }
                                updateMyOptionFee();
                                updateTotalPrice();
                            };
                            checkbox.addEventListener('change', syncMyOptionFromCheckbox);
                            checkbox.addEventListener('input', syncMyOptionFromCheckbox);

                            myoptionContainer.appendChild(div);
                        });
                    }

                    // マイコスチュームの表示
                    const mycostumeContainer = document.getElementById('mycostume-container');
                    mycostumeContainer.innerHTML = '';
                    data.myCostumes.forEach(costume => {
                        const div = document.createElement('div');
                        div.className = 'costume-item';
                        div.innerHTML = `
                            <input type="checkbox"
                                name="mycostume[]"
                                id="mycostume${costume.id}"
                                value="${costume.id}"
                                data-price="${costume.price ?? 0}">
                            <label for="mycostume${costume.id}" class="my-item-label">
                                ${costume.image_url ? `<img src="${costume.image_url}" alt="" class="my-item-thumb" width="60" height="60">` : ''}
                                <span class="my-item-name">${costume.name}</span>
                            </label>
                        `;

                        // チェックボックスにイベントリスナーを追加
                        const checkbox = div.querySelector('input[type="checkbox"]');
                        const syncMyCostumeFromCheckbox = () => {
                            const parentItem = checkbox.closest('.costume-item');
                            if (checkbox.checked) {
                                parentItem.classList.add('selected');
                            } else {
                                parentItem.classList.remove('selected');
                            }
                            updateMyCostumeFee();
                            updateTotalPrice();
                        };
                        checkbox.addEventListener('change', syncMyCostumeFromCheckbox);
                        checkbox.addEventListener('input', syncMyCostumeFromCheckbox);

                        mycostumeContainer.appendChild(div);
                    });

                    updateMyOptionFee();
                    updateMyCostumeFee();
                    updateTotalPrice();
                })
                .catch(error => console.error('Error:', error));
        } else {
            // キャスト名をクリア
            document.getElementById('item_name').value = '';

            const myoptionContainer = document.getElementById('myoption-container');
            const mycostumeContainer = document.getElementById('mycostume-container');
            if (myoptionContainer) myoptionContainer.innerHTML = '';
            if (mycostumeContainer) mycostumeContainer.innerHTML = '';

            // マイオプション・マイコスチューム料金をクリア
            updateMyOptionFee();
            updateMyCostumeFee();
            updateTotalPrice();
        }
    });

    // 初期表示時にキャストが選択されている場合、マイオプションとマイコスチュームを読み込む
    const selectedItemId = document.getElementById('item_id').value;
    if (selectedItemId) {
        document.getElementById('item_id').dispatchEvent(new Event('change'));
    }

    // 初期表示時の料金計算
    updateOptionPayFee();
    updateMyOptionFee();
    updateMyCostumeFee();
    updateTotalPrice();

    // 会員選択時の自動名前入力
    const memberSelect = document.getElementById('member_id');
    const nameInput = document.getElementById('name');
    const memberChips = document.querySelectorAll('.registered-member-chip');
    if (memberSelect && nameInput) {
        memberSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const memberName = selectedOption.getAttribute('data-name') || '';
            nameInput.value = memberName;
        });
    }

    if (memberSelect && nameInput && memberChips.length > 0) {
        memberChips.forEach((chip) => {
            chip.addEventListener('click', function() {
                const memberId = this.getAttribute('data-member-id') || '0';
                const memberName = this.getAttribute('data-member-name') || '';
                memberSelect.value = memberId;
                nameInput.value = memberName;
                memberSelect.dispatchEvent(new Event('change'));
            });
        });
    }

    refreshFeeTotalIntaxDisplay();
});

</script>

<style>
.registered-members-panel {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fafafa;
    padding: 0.55rem 0.65rem;
}
.registered-members-title {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    margin: 0 0 0.35rem;
}
.registered-members-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
    max-height: 140px;
    overflow: auto;
}
.registered-member-chip {
    border: 1px solid #d1d5db;
    border-radius: 999px;
    background: #fff;
    color: #374151;
    font-size: 0.72rem;
    padding: 0.18rem 0.55rem;
    line-height: 1.3;
    cursor: pointer;
}
.registered-member-chip:hover {
    background: #ede9fe;
    border-color: #c4b5fd;
    color: #5b21b6;
}
</style>



</x-admin-layout>
