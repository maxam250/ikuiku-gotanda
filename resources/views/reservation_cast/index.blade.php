@extends('layouts.app')

@section('content')
<div class="titleArea">
    <h2>RESERVATION</h2>
    <div class="title">予約申し込みフォーム</div>
</div>

<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- タイトルセクション -->

                <!-- タイトルとフォームのコンテナ -->
        <div class="flex flex-col lg:flex-row lg:gap-8">
            <!-- タイトルセクション -->
             @if(Auth::guard('member')->check())
            <div class="w-full lg:w-1/3 mb-8 lg:mb-0">
                <div class="bg-white rounded-lg shadow-md p-6 lg:p-8">
                    
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
            <div class="w-full {{ Auth::guard('member')->check() ? 'lg:w-2/3' : '' }}">
                <div class="bg-white rounded-lg shadow-md p-6 lg:p-8">
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
                            <label class="block text-sm font-medium text-gray-700 mb-3 bg-pink-50 px-3 py-2 rounded-lg">予約形態</label>
                            <div class="flex gap-6">
                                <label class="flex items-center">
                                    <input type="radio" name="kind" id="kind1" value="1" 
                                        {{ old('kind', $oldInput['kind'] ?? '') == '1' || empty(old('kind', $oldInput['kind'] ?? '')) ? 'checked' : '' }}
                                        class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="text-sm text-gray-700">通常予約</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="kind" id="kind2" value="2" 
                                        {{ old('kind', $oldInput['kind'] ?? '') == '2' ? 'checked' : '' }}
                                        class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="text-sm text-gray-700">リクエスト予約</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">日時</label>
                                <input type="date" name="date" id="date" value="{{ old('date', $oldInput['date'] ?? '') }}" autocomplete="off"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">時間</label>
                                <div class="flex items-center gap-2">
                                    <select name="time_start"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @foreach (generateTimeOptions('09:00', '23:00') as $value => $label)
                                        <option value="{{ $value }}" {{ old('time_start', $oldInput['time_start'] ?? '') == $value ? 'selected' : '' }}>{{ $label }}　</option>
                                        @endforeach
                                    </select>
                                    <span class="text-gray-500">～</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">コース</label>
                                <select name="course_id" id="course_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="" {{ old('course_id', $oldInput['course_id'] ?? '') == '' ? 'selected' : '' }}>選択してください</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}"{{ old('course_id', $oldInput['course_id'] ?? '') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="course_time" class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">時間</label>
                                <select name="course_time" id="course_time"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="" {{ old('course_time', $oldInput['course_time'] ?? '') == '' ? 'selected' : '' }}>選択してください</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">キャスト</label>
                                <select name="item_id" id="item_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">指名なし</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}"{{ old('item_id', $oldInput['item_id'] ?? '') == $item->id ? 'selected' : '' }}>{{ $item->item_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="item_name" class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">キャストその他</label>
                                <input type="text" name="item_name" id="item_name" value="{{ old('item_name', $oldInput['item_name'] ?? '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <div class="mb-6" id="cast_myoptions" style="display:none;">
                            <label for="myoption" class="block text-sm font-medium text-gray-700 mb-3 bg-pink-50 px-3 py-2 rounded-lg">キャストマイオプション</label>
                            @php
                                $oldMyOption = old('myoption', $oldInput['myoption'] ?? '');
                                $myOptionVals = is_array($oldMyOption) ? $oldMyOption : explode('-', $oldMyOption);
                            @endphp
                            <div id="myoption_list" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <!-- キャスト選択時にJavaScriptで動的に追加 -->
                            </div>
                        </div>

                        <div class="mb-6" id="cast_mycostumes" style="display:none;">
                            <label for="mycostume" class="block text-sm font-medium text-gray-700 mb-3 bg-pink-50 px-3 py-2 rounded-lg">キャストマイコスチューム</label>
                            @php
                                $oldMyCostume = old('mycostume', $oldInput['mycostume'] ?? '');
                                $myCostumeVals = is_array($oldMyCostume) ? $oldMyCostume : explode('-', $oldMyCostume);
                            @endphp
                            <div id="mycostume_list" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <!-- キャスト選択時にJavaScriptで動的に追加 -->
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="option" class="block text-sm font-medium text-gray-700 mb-3 bg-pink-50 px-3 py-2 rounded-lg">オプション</label>
                            @php
                                $oldOption = old('option', $oldInput['option'] ?? '');
                                $optionVals = is_array($oldOption) ? $oldOption : explode('-', $oldOption);
                            @endphp
                            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($options as $option)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer w-fit">
                                    <input type="checkbox"
                                       name="option[]"
                                       id="option{{ $option->id }}"
                                       value="{{ $option->id }}"
                                       {{ in_array($option->id, $optionVals) ? 'checked' : '' }}
                                       class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">{{ $option->title }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="option_pay" class="block text-sm font-medium text-gray-700 mb-3 bg-pink-50 px-3 py-2 rounded-lg">オプション有料</label>
                            @php
                                $oldOptionPay = old('option_pay', $oldInput['option_pay'] ?? '');
                                $optionPayVals = is_array($oldOptionPay) ? $oldOptionPay : explode('-', $oldOptionPay);
                            @endphp
                            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($option_pays as $option_pay)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer w-fit">
                                    <input type="checkbox"
                                       name="option_pay[]"
                                       id="option_pay{{ $option_pay->id }}"
                                       value="{{ $option_pay->id }}"
                                       {{ in_array($option_pay->id, $optionPayVals) ? 'checked' : '' }}
                                       class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">{{ $option_pay->title }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        @if(Auth::guard('member')->check())
                            <input type="hidden" name="name" id="name" value="{{ old('name', $oldInput['name'] ?? '') }}">
                        @else
                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">名前<span class="text-red-500">※必須</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $oldInput['name'] ?? '') }}" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">メールアドレス<span class="text-red-500">※必須</span></label>
                                    <input type="text" name="email" id="email" value="{{ old('email', $oldInput['email'] ?? '') }}" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="tel" class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">電話番号</label>
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

                        <!-- ↓ここからは屋外以外をセレクトした時のみ表示 -->
                        <div id="indoor-section" style="display:none;">
                            <div class="mb-6">
                                <label for="equipment_id" class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">スタジオ</label>
                                <select name="equipment_id" id="equipment_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="" {{ old('equipment_id', $oldInput['equipment_id'] ?? '') == '' ? 'selected' : '' }}>指定なし</option>
                                    @foreach($equipments as $equipment)
                                        <option value="{{ $equipment->id }}"{{ old('equipment_id', $oldInput['equipment_id'] ?? '') == $equipment->id ? 'selected' : '' }}>{{ $equipment->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3 bg-pink-50 px-3 py-2 rounded-lg">コスチューム</label>
                                <div class="grid md:grid-cols-3 gap-4">
                                    <div>
                                        <select name="costume1" id="costume1"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="" {{ old('costume1', $oldInput['costume1'] ?? '') == '' ? 'selected' : '' }}>なし</option>
                                            @foreach($costumes as $costume)
                                                <option value="{{ $costume->id }}" data-img="{{ asset('storage/costume/' . $costume->filename) }}" {{ old('costume1', $oldInput['costume1'] ?? '') == $costume->id ? 'selected' : '' }}>{{ $costume->name }}</option>
                                            @endforeach
                                        </select>
                                        <div id="costume1-preview" class="mt-2">
                                            <img src="" id="costume1-img" style="display:none; width:100px;" class="rounded-lg">
                                        </div>
                                    </div>

                                    <div>
                                        <select name="costume2" id="costume2"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="" {{ old('costume2', $oldInput['costume2'] ?? '') == '' ? 'selected' : '' }}>なし</option>
                                            @foreach($costumes as $costume)
                                                <option value="{{ $costume->id }}" data-img="{{ asset('storage/costume/' . $costume->filename) }}" {{ old('costume2', $oldInput['costume2'] ?? '') == $costume->id ? 'selected' : '' }}>{{ $costume->name }}</option>
                                            @endforeach
                                        </select>
                                        <div id="costume2-preview" class="mt-2">
                                            <img src="" id="costume2-img" style="display:none; width:100px;" class="rounded-lg">
                                        </div>
                                    </div>

                                    <div>
                                        <select name="costume3" id="costume3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="" {{ old('costume3', $oldInput['costume3'] ?? '') == '' ? 'selected' : '' }}>なし</option>
                                            @foreach($costumes as $costume)
                                                <option value="{{ $costume->id }}" data-img="{{ asset('storage/costume/' . $costume->filename) }}" {{ old('costume3', $oldInput['costume3'] ?? '') == $costume->id ? 'selected' : '' }}>{{ $costume->name }}</option>
                                            @endforeach
                                        </select>
                                        <div id="costume3-preview" class="mt-2">
                                            <img src="" id="costume3-img" style="display:none; width:100px;" class="rounded-lg">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ↑ここからは屋外以外をセレクトした時のみ表示<br>
                        ↓ここからは屋外をセレクトした時のみ表示 -->

                        <div id="outdoor-section" style="display:none;">
                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="area" class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">ご利用エリア</label>
                                    <input type="text" name="area" id="area" value="{{ old('area', $oldInput['area'] ?? '') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="place" class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">具体的な場所</label>
                                    <input type="text" name="place" id="place" value="{{ old('place', $oldInput['place'] ?? '') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="meet" class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">待ち合わせ方法</label>
                                    <input type="text" name="meet" id="meet" value="{{ old('meet', $oldInput['meet'] ?? '') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">住所</label>
                                    <input type="text" name="address" id="address" value="{{ old('address', $oldInput['address'] ?? '') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- ↑ここまでは屋外をセレクトした時のみ表示 -->

                        <div class="mb-8">
                            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2 bg-pink-50 px-3 py-2 rounded-lg">備考</label>
                            <textarea name="comment" id="comment" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('comment', $oldInput['comment'] ?? '') }}</textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="submit" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105">
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

<style>
.image-checkbox-item {
    position: relative;
    text-align: center;
}

.image-checkbox-item input[type="checkbox"] {
    display: none;
}

.image-checkbox-item img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border: 2px solid #ddd;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.image-checkbox-item label {
    cursor: pointer;
    display: block;
}

.image-checkbox-item input[type="checkbox"]:checked + label img {
    border-color: #4CAF50;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
}

.image-checkbox-item .title {
    margin-top: 5px;
    font-size: 0.9em;
    word-break: break-all;
}

.image-checkbox-item .price {
    font-size: 0.8em;
    color: #666;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        // キャスト選択時の処理を追加
        const itemSelect = document.getElementById('item_id');
        const myoptionsDiv = document.getElementById('cast_myoptions');
        const myoptionList = document.getElementById('myoption_list');
        const mycostumesDiv = document.getElementById('cast_mycostumes');
        const mycostumeList = document.getElementById('mycostume_list');

        itemSelect.addEventListener('change', function() {
            const selectedItemId = this.value;
            if (selectedItemId) {
                fetch(`/get-cast-items/${selectedItemId}`)
                    .then(response => response.json())
                    .then(data => {
                        // マイオプションの表示
                        if (data.myOptions && data.myOptions.length > 0) {
                            myoptionList.innerHTML = data.myOptions.map(option => `
                                <div class="image-checkbox-item">
                                    <input type="checkbox"
                                        name="myoption[]"
                                        id="myoption${option.id}"
                                        value="${option.id}"
                                        ${isChecked('myoption', option.id)}>
                                    <label for="myoption${option.id}">
                                        <img src="/storage/myoption/${option.filename}" alt="${option.name}">
                                        <div class="title">${option.name}</div>
                                        <div class="price">${new Intl.NumberFormat('ja-JP').format(option.price)}円</div>
                                    </label>
                                </div>
                            `).join('');
                            myoptionsDiv.style.display = 'block';
                        } else {
                            myoptionsDiv.style.display = 'none';
                        }

                        // マイコスチュームの表示
                        if (data.myCostumes && data.myCostumes.length > 0) {
                            mycostumeList.innerHTML = data.myCostumes.map(costume => `
                                <div class="image-checkbox-item">
                                    <input type="checkbox"
                                        name="mycostume[]"
                                        id="mycostume${costume.id}"
                                        value="${costume.id}"
                                        ${isChecked('mycostume', costume.id)}>
                                    <label for="mycostume${costume.id}">
                                        <img src="/storage/mycostume/${costume.filename}" alt="${costume.name}">
                                        <div class="title">${costume.name}</div>
                                        <div class="price">${new Intl.NumberFormat('ja-JP').format(costume.price)}円</div>
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

        // チェックボックスの状態を復元するヘルパー関数
        function isChecked(type, id) {
            const oldValues = type === 'myoption' ? {!! json_encode($myOptionVals) !!} : {!! json_encode($myCostumeVals) !!};
            return oldValues.includes(id.toString()) ? 'checked' : '';
        }

        // 初期表示時にキャストが選択されている場合の処理
        if (itemSelect.value) {
            itemSelect.dispatchEvent(new Event('change'));
        }
    });

    // フォームに画像を付与
    document.addEventListener('DOMContentLoaded', function () {
        const pairs = [
            { selectId: 'costume1', imgId: 'costume1-img' },
            { selectId: 'costume2', imgId: 'costume2-img' },
            { selectId: 'costume3', imgId: 'costume3-img' },
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
