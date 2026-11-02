@extends('layouts.app')

@section('content')
<div class="titleArea">
    <h2>RESERVATION</h2>
    <div class="title">予約申し込みフォーム</div>
</div>

<div class="reservation-page">
    <div class="reservation-shell">
        <!-- タイトルセクション -->

                <!-- タイトルとフォームのコンテナ -->
        <div class="w-full">
            <!-- タイトルセクション -->
             @if(Auth::guard('member')->check())
            <div class="w-full mb-8">
                <div class="reservation-card">

                    <!-- 会員情報表示 -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-blue-800 font-semibold text-sm">
                            [ 会員番号:{{ Auth::guard('member')->user()->member_no }} ]
                        </p>
                        <p class="text-blue-800 font-semibold">
                            {{ Auth::guard('member')->user()->name }} 様
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- フォームセクション -->
            <div class="w-full">
                <div class="reservation-card">
                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <ul class="text-red-700 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="post" action="{{ route('reservation.confirm') }}">
                        @csrf

                        <div class="mb-6">
                            <label class="reservation-field-label">予約形態</label>
                            <div class="flex flex-wrap gap-4">
                                <input type="radio" name="kind" id="kind1" value="1" class="hidden peer/kind1"
                                    {{ old('kind', $oldInput['kind'] ?? '') == '1' || empty(old('kind', $oldInput['kind'] ?? '')) ? 'checked' : '' }}>
                                <label for="kind1"
                                    class="peer-checked/kind1:bg-blue-400 peer-checked/kind1:text-white
                                        px-4 py-2 rounded border border-gray-300 text-gray-700 text-sm cursor-pointer
                                        hover:bg-blue-100 transition">
                                    通常予約
                                </label>

                                <input type="radio" name="kind" id="kind2" value="2" class="hidden peer/kind2"
                                    {{ old('kind', $oldInput['kind'] ?? '') == '2' ? 'checked' : '' }}>
                                <label for="kind2"
                                    class="peer-checked/kind2:bg-blue-400 peer-checked/kind2:text-white
                                        px-4 py-2 rounded border border-gray-300 text-gray-700 text-sm cursor-pointer
                                        hover:bg-blue-100 transition">
                                    リクエスト予約
                                </label>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="date" class="reservation-field-label">日時</label>
                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <input type="date" name="date" id="date" value="{{ old('date', $oldInput['date'] ?? '') }}" autocomplete="off"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <select name="time_start"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            @foreach (generateTimeOptions('10:00', '22:00') as $value => $label)
                                            <option value="{{ $value }}" {{ old('time_start', $oldInput['time_start'] ?? '') == $value ? 'selected' : '' }}>{{ $label }}　</option>
                                            @endforeach
                                        </select>
                                        <span class="text-gray-500">～</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="course_id" class="reservation-field-label">コース</label>
                                <select name="course_id" id="course_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="" {{ old('course_id', $oldInput['course_id'] ?? '') == '' ? 'selected' : '' }}>選択してください</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}"{{ old('course_id', $oldInput['course_id'] ?? '') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="course_time" class="reservation-field-label">利用時間</label>
                                <select name="course_time" id="course_time"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="" {{ old('course_time', $oldInput['course_time'] ?? '') == '' ? 'selected' : '' }}>選択してください</option>

                                    @foreach (generateMinuteOptions(60, 480, 30) as $value => $label)
                                        <option value="{{ $value }}" {{ old('course_time', $oldInput['course_time'] ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                {{-- <select name="course_time" id="course_time"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="" {{ old('course_time', $oldInput['course_time'] ?? '') == '' ? 'selected' : '' }}>選択してください</option>
                                </select> --}}
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="item_id" class="reservation-field-label">キャスト</label>
                                <select name="item_id" id="item_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">指名なし</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}"{{ old('item_id', $oldInput['item_id'] ?? '') == $item->id ? 'selected' : '' }}>{{ $item->item_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="item_name_other" class="reservation-field-label">キャストその他</label>
                                <input type="text" name="item_name_other" id="item_name_other" value="{{ old('item_name_other', $oldInput['item_name_other'] ?? '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <div class="mb-6" id="cast_myoptions" style="display:none;">
                            <label for="myoption" class="reservation-field-label">キャストのマイオプション</label>
                            @php
                                $oldMyOption = old('myoption', $oldInput['myoption'] ?? '');
                                $myOptionVals = is_array($oldMyOption) ? $oldMyOption : explode('-', $oldMyOption);
                                $myOptionVals = array_map('strval', array_values(array_filter((array) $myOptionVals, static fn ($v) => $v !== '' && $v !== null)));
                            @endphp
                            <div id="myoption_list" class="myitem-grid">
                                <!-- キャスト選択時にJavaScriptで動的に追加 -->
                            </div>
                        </div>

                        <div class="mb-6" id="cast_mycostumes" style="display:none;">
                            <label for="mycostume" class="reservation-field-label">キャストのマイコスチューム</label>
                            @php
                                $oldMyCostumeRaw = old('mycostume', $oldInput['mycostume'] ?? '');
                                if (is_array($oldMyCostumeRaw)) {
                                    $selectedMycostumeId = (string) ($oldMyCostumeRaw[0] ?? '');
                                } elseif (is_string($oldMyCostumeRaw) && strpos($oldMyCostumeRaw, '-') !== false) {
                                    $parts = explode('-', $oldMyCostumeRaw);
                                    $selectedMycostumeId = (string) ($parts[0] ?? '');
                                } else {
                                    $selectedMycostumeId = (string) ($oldMyCostumeRaw ?? '');
                                }
                            @endphp
                            <div id="mycostume_list" class="myitem-grid">
                                <!-- キャスト選択時にJavaScriptで動的に追加 -->
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="option" class="reservation-field-label">オプション</label>
                            @php
                                $oldOption = old('option', $oldInput['option'] ?? []);
                                $optionVals = is_array($oldOption) ? $oldOption : (is_string($oldOption) ? explode('-', $oldOption) : []);
                            @endphp
                            <div class="flex flex-wrap gap-8">
                                @foreach($options as $option)
                                    <input type="checkbox"
                                        name="option[]"
                                        id="option{{ $option->id }}"
                                        value="{{ $option->id }}"
                                        {{ in_array($option->id, $optionVals) ? 'checked' : '' }}
                                        class="sr-only reservation-option-check">
                                    <label for="option{{ $option->id }}"
                                        class="reservation-option-label flex items-center px-2 py-1 border border-gray-300 rounded-lg bg-white hover:bg-blue-100 cursor-pointer w-fit transition">
                                        <span class="text-xs">{{ $option->title }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="option_pay" class="reservation-field-label">オプション有料</label>
                            @php
                                $oldOptionPay = old('option_pay', $oldInput['option_pay'] ?? []);
                                $optionPayVals = is_array($oldOptionPay) ? $oldOptionPay : (is_string($oldOptionPay) ? explode('-', $oldOptionPay) : []);
                            @endphp
                            <div class="flex flex-wrap gap-8">
                                @foreach($option_pays as $option_pay)
                                <input type="checkbox"
                                        name="option_pay[]"
                                        id="option_pay{{ $option_pay->id }}"
                                        value="{{ $option_pay->id }}"
                                        {{ in_array($option_pay->id, $optionPayVals) ? 'checked' : '' }}
                                        class="sr-only reservation-option-pay-check">

                                <label for="option_pay{{ $option_pay->id }}"
                                        class="reservation-option-pay-label flex flex-col items-start px-2 py-1 border border-gray-300 rounded-lg bg-white hover:bg-blue-100 cursor-pointer w-fit transition">
                                    <span class="text-xs">{{ $option_pay->title }}</span>
                                    <span class="text-xs">{{ number_format($option_pay->price) ?? '' }}円</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- 屋内コース時のみ表示（スタジオは非表示・equipment_id は hidden で送信） -->
                        <div id="indoor-section" style="display:none;">
                            <input type="hidden" name="equipment_id" value="{{ old('equipment_id', $oldInput['equipment_id'] ?? '') }}">
                            <div class="mb-5">
                                <label class="reservation-field-label">コスチューム</label>
                                <select name="costume1" id="costume1"
                                    class="reservation-costume-select w-full sm:w-64 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                                    <option value="" {{ old('costume1', $oldInput['costume1'] ?? '') == '' ? 'selected' : '' }}>なし</option>
                                    @foreach($costumeCates as $cate)
                                        @if($cate->costumes->count())
                                            <optgroup label="{{ $cate->title }}">
                                                @foreach($cate->costumes as $costume)
                                                    <option value="{{ $costume->id }}"
                                                        data-img="{{ asset('storage/costume/' . $costume->filename) }}"
                                                        {{ old('costume1', $oldInput['costume1'] ?? '') == $costume->id ? 'selected' : '' }}>
                                                        {{ $costume->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                </select>
                                <div id="costume1-preview" class="mt-2">
                                    <img src="" id="costume1-img" alt="" style="display:none;" class="w-24 h-24 object-cover rounded-lg">
                                </div>
                            </div>
                        </div>

                        {{-- ↑ここからは屋外以外をセレクトした時のみ表示<br>
                        ↓ここからは屋外をセレクトした時のみ表示 --}}

                        <div id="outdoor-section" style="display:none;">
                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="area" class="reservation-field-label">ご利用エリア</label>
                                    <input type="text" name="area" id="area" value="{{ old('area', $oldInput['area'] ?? '') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="place" class="reservation-field-label">具体的な場所</label>
                                    <input type="text" name="place" id="place" value="{{ old('place', $oldInput['place'] ?? '') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="meet" class="reservation-field-label">待ち合わせ方法</label>
                                    <input type="text" name="meet" id="meet" value="{{ old('meet', $oldInput['meet'] ?? '') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="address" class="reservation-field-label">住所</label>
                                    <input type="text" name="address" id="address" value="{{ old('address', $oldInput['address'] ?? '') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- ↑ここまでは屋外をセレクトした時のみ表示 -->

                        @if(Auth::guard('member')->check())
                            <input type="hidden" name="name" id="name" value="{{ old('name', $oldInput['name'] ?? '') }}">
                        @else
                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="name" class="reservation-field-label">名前<span class="reservation-required">※必須</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $oldInput['name'] ?? '') }}" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="email" class="reservation-field-label">メールアドレス<span class="reservation-required">※必須</span></label>
                                    <input type="text" name="email" id="email" value="{{ old('email', $oldInput['email'] ?? '') }}" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <div class="w-full sm:w-1/2 mb-6">
                                <label for="tel" class="reservation-field-label">電話番号<span class="reservation-required">※必須</span></label>
                                @php
                                    $oldTel = old('tel', $oldInput['tel'] ?? '');
                                    $telParts = is_array($oldTel) ? $oldTel : explode('-', $oldTel);
                                @endphp
                                <div class="flex gap-2">
                                    <input type="text" name="tel[]" id="tel" value="{{ $telParts[0] ?? '' }}" placeholder="090"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center min-w-0">
                                    <span class="flex items-center text-gray-500">-</span>
                                    <input type="text" name="tel[]" id="tel2" value="{{ $telParts[1] ?? '' }}" placeholder="0000"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center min-w-0">
                                    <span class="flex items-center text-gray-500">-</span>
                                    <input type="text" name="tel[]" id="tel3" value="{{ $telParts[2] ?? '' }}" placeholder="0000"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center min-w-0">
                                </div>
                            </div>
                        @endif

                        <div class="mb-5">
                            <label for="comment" class="reservation-field-label">備考</label>
                            <textarea name="comment" id="comment" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('comment', $oldInput['comment'] ?? '') }}</textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="submit"
                                class="flex-1 bg-blue-400 hover:bg-blue-700 text-slate-900 font-medium py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105">
                                <i class="fas fa-check mr-2"></i>
                                確認画面へ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    /*
        var courseId = "{{ old('course_id', $oldInput['course_id'] ?? '') }}";
        var courseFeeSelect = document.getElementById('course_time');
        var oldCourseTime = "{{ old('course_time', $oldInput['course_time'] ?? '') }}";

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

        // キャスト選択時の処理を追加
        const itemSelect = document.getElementById('item_id');
        const myoptionsDiv = document.getElementById('cast_myoptions');
        const myoptionList = document.getElementById('myoption_list');
        const mycostumesDiv = document.getElementById('cast_mycostumes');
        const mycostumeList = document.getElementById('mycostume_list');

        const oldMyoptionIds = @json($myOptionVals ?? []);
        const selectedMycostumeIdBlade = @json($selectedMycostumeId ?? '');

        function isMyoptionChecked(id) {
            return oldMyoptionIds.map(String).includes(String(id)) ? 'checked' : '';
        }

        function isMycostumeChecked(id) {
            return selectedMycostumeIdBlade !== '' && String(selectedMycostumeIdBlade) === String(id)
                ? 'checked' : '';
        }

        itemSelect.addEventListener('change', function() {
            const selectedItemId = this.value;
            if (selectedItemId) {
                fetch(`/get-cast-items/${selectedItemId}`)
                    .then(response => response.json())
                    .then(data => {
                        const myOptions = data.myOptions ?? data.myoptions ?? [];
                        const myCostumes = data.myCostumes ?? data.mycostumes ?? [];

                        if (myOptions.length > 0) {
                            myoptionList.innerHTML = myOptions.map(option => `
                                <div class="image-checkbox-item">
                                    <input type="checkbox"
                                        name="myoption[]"
                                        id="myoption${option.id}"
                                        value="${option.id}"
                                        ${isMyoptionChecked(option.id)}>
                                    <label for="myoption${option.id}">
                                        <div class="title">${option.name}</div>
                                        ${option.filename
                                            ? `<span class="myitem-thumb"><img src="/storage/myoption/${option.filename}" alt="${option.name}"></span>`
                                            : ''}
                                        <div class="price">${new Intl.NumberFormat('ja-JP').format(option.price)}円</div>
                                    </label>
                                </div>
                            `).join('');
                            myoptionsDiv.style.display = 'block';
                        } else {
                            myoptionsDiv.style.display = 'none';
                        }

                        if (myCostumes.length > 0) {
                            mycostumeList.innerHTML = myCostumes.map(costume => `
                                <div class="image-checkbox-item">
                                    <input type="radio"
                                        name="mycostume"
                                        id="mycostume${costume.id}"
                                        value="${costume.id}"
                                        ${isMycostumeChecked(costume.id)}>
                                    <label for="mycostume${costume.id}">
                                        <div class="title">${costume.name}</div>
                                        ${costume.filename
                                            ? `<span class="myitem-thumb"><img src="/storage/mycostume/${costume.filename}" alt=""></span>`
                                            : ''}
                                    </label>
                                </div>
                            `).join('');
                            mycostumesDiv.style.display = 'block';
                        } else {
                            mycostumesDiv.style.display = 'none';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                myoptionsDiv.style.display = 'none';
                mycostumesDiv.style.display = 'none';
            }
        });

        // 初期表示時にキャストが選択されている場合の処理
        if (itemSelect.value) {
            itemSelect.dispatchEvent(new Event('change'));
        }
    });

    // フォームに画像を付与
    document.addEventListener('DOMContentLoaded', function () {
        const pairs = [
            { selectId: 'costume1', imgId: 'costume1-img' },
        ];

        pairs.forEach(pair => {
            const select = document.getElementById(pair.selectId);
            const img = document.getElementById(pair.imgId);

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
            updateImage(); // 初期化
        });
    });

    // 屋外だった時のみ表示
    document.addEventListener("DOMContentLoaded", function () {
        const courseSelect = document.getElementById("course_id");
        const outdoorSection = document.getElementById("outdoor-section");
        const indoorSection = document.getElementById("indoor-section");

        function toggleOutdoorSection() {
            const selected = parseInt(courseSelect.value);
            if (selected === 3) {   //屋外id == 3
                outdoorSection.style.display = "block";
                indoorSection.style.display = "none";
            } else {
                outdoorSection.style.display = "none";
                indoorSection.style.display = "block";
            }
        }

        // 初回表示時に判定
        toggleOutdoorSection();

        // セレクトボックス変更時にも反映
        courseSelect.addEventListener("change", toggleOutdoorSection);
    });
</script>

@endsection
