<x-admin-layout>
    <x-slot name="header">
        予約管理編集
    </x-slot>

    <!-- 関連ページへのナビゲーション -->
    <div class="mb-6 flex flex-wrap gap-3">
        <a href="{{ route('admin.member_points.edit', ['member'=> $member->id]) }}"
           class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
            ポイント追加/削除
        </a>
        <a href="{{ route('admin.member_histories.index', ['member'=> $member->id]) }}"
           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            利用履歴一覧
        </a>
        <a href="{{ route('admin.member_histories.create', ['member'=> $member->id]) }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            利用履歴追加
        </a>
    </div>

    <div class="mb-6">
        <a href="{{ route('admin.member_histories.index', ['member'=> $member->id]) }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            戻る
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">入力エラーがあります</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">予約管理編集</h3>
        </div>

        <form action="{{ route('admin.member_histories.update', ['member'=>$member->id, 'history'=>$history->id]) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- フラグ -->
                <div>
                    <label for="flag" class="block text-sm font-medium text-gray-700 mb-2">ステータス</label>
                    <select name="flag" id="flag" data-history-id="{{ $history->id }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="0" {{ old('flag',$history['flag']) == 0 ? 'selected' : '' }}>未設定</option>
                        <option value="1" {{ old('flag',$history['flag']) == 1 ? 'selected' : '' }}>確認中</option>
                        <option value="2" {{ old('flag',$history['flag']) == 2 ? 'selected' : '' }}>確定</option>
                        <option value="3" {{ old('flag',$history['flag']) == 3 ? 'selected' : '' }}>保留</option>
                        <option value="4" {{ old('flag',$history['flag']) == 4 ? 'selected' : '' }}>キャンセル</option>
                    </select>
                </div>

                <!-- 予約種別 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">予約種別</label>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="radio" name="kind" id="kind1" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" {{ old('kind', $history['kind'] ?? '') == '1' || empty(old('kind', $history['kind'] ?? '')) ? 'checked' : '' }}>
                            <label for="kind1" class="ml-2 block text-sm text-gray-900">通常予約</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" name="kind" id="kind2" value="2" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" {{ old('kind', $history['kind'] ?? '') == '2' ? 'checked' : '' }}>
                            <label for="kind2" class="ml-2 block text-sm text-gray-900">リクエスト予約</label>
                        </div>
                    </div>
                </div>
            </div>
        <div class="form-group">
            <label for="date">日時</label>
            <input type="date" name="date" id="date" class="form-control size_s" value="{{ old('date', $history['date']->format('Y-m-d') ?? '') }}">&nbsp;
            @php
                 $timeStart = isset($history['time_start'])
                    ? \Carbon\Carbon::parse($history['time_start'])->format('H:i')
                    : '';
            @endphp

            <select name="time_start">
                @foreach (generateTimeOptions('09:00', '23:00') as $value => $label)
                <option value="{{ $value }}" {{ old('time_start', $timeStart ?? '') == $label ? 'selected' : '' }}>{{ $label }}　</option>
                @endforeach
            </select><div class="unit">～</div>
        </div>


        <div class="form-group">
            <label for="course_id">コース</label>
            <select name="course_id" id="course_id" class="form-control size_s">
                <option value="" disabled selected>選択してください</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}"{{ old('course_id', $history['course_id'] ?? '') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="course_time">時間</label>
            <select name="course_time" id="course_time" class="form-control size_s">
                <option value="" disabled selected>選択してください</option>
            </select>
        </div>

        <div class="form-group">
            <label for="item_id">キャスト</label>

            <input type="text" name="item_name" id="item_name" class="form-control size_s" value="{{ old('item_name', $history['item_name'] ?? '') }}">&emsp;
            <select name="item_id" id="item_id" class="form-controlx size_s">
                <option value="">指名なし</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" data-name="{{ $item->item_name }}"{{ old('item_id', $history['item_id'] ?? '') == $item->id ? 'selected' : '' }}>{{ $item->item_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="item_name">キャストその他</label>
            <input type="text" name="item_name_other" id="item_name_other" class="form-control" value="{{ old('item_name_other', $history['item_name_other'] ?? '') }}">
        </div>

        <div class="form-group">
            <label for="myoption">マイオプション</label>
            <div class="flex form_parts" id="myoption-container">
                @if($history->item && $history->item->myOptions)
                    @foreach($history->item->myOptions as $myOption)
                        <div class="option-item">
                            <input type="checkbox"
                                   name="myoption[]"
                                   id="myoption_{{ $myOption->id }}"
                                   value="{{ $myOption->id }}"
                                   {{ in_array($myOption->id, $history->myoption ?? []) ? 'checked' : '' }}>
                            <label for="myoption_{{ $myOption->id }}">
                                @if($myOption->filename)
                                    <img src="{{ Storage::url('myoption/' . $myOption->filename) }}"
                                         alt="{{ $myOption->name }}"
                                         style="width:100px; margin-bottom:5px;">
                                @endif
                                <span>{{ $myOption->name }}: {{ number_format($myOption->price) }}円</span>
                            </label>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="form-group">
            <label for="mycostume">マイコスチューム</label>
            <div class="flex form_parts" id="mycostume-container">
                @if($history->item && $history->item->myCostumes)
                    @foreach($history->item->myCostumes as $myCostume)
                        <div class="costume-item">
                            <input type="checkbox"
                                   name="mycostume[]"
                                   id="mycostume_{{ $myCostume->id }}"
                                   value="{{ $myCostume->id }}"
                                   {{ in_array($myCostume->id, $history->mycostume ?? []) ? 'checked' : '' }}>
                            <label for="mycostume_{{ $myCostume->id }}">
                                @if($myCostume->filename)
                                    <img src="{{ Storage::url('mycostume/' . $myCostume->filename) }}"
                                         alt="{{ $myCostume->name }}"
                                         style="width:100px; margin-bottom:5px;">
                                @endif
                                <span>{{ $myCostume->name }}: {{ number_format($myCostume->price) }}円</span>
                            </label>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="form-group">
            <label for="option">オプション</label>
            <div class="flex form_parts">
            @php
                $oldOption = old('option', $history['option'] ?? '');
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

        <div class="form-group form_parts">
            <label for="option_pay">オプション有料</label>
            @php
                $oldOptionPay = old('option_pay', $history['option_pay'] ?? '');
                $optionPayVals = is_array($oldOptionPay) ? $oldOptionPay : explode('-', $oldOptionPay);

            @endphp
            @foreach($option_pays as $option_pay)
            <input type="checkbox"
               name="option_pay[]"
               id="option_pay{{ $option_pay->id }}"
               value="{{ $option_pay->id }}"
               {{ in_array($option_pay->id, $optionPayVals) ? 'checked' : '' }}>
            <label for="option_pay{{ $option_pay->id }}" class="checkbox">{{ $option_pay->title }}</label>
            @endforeach
        </div>

        <div class="form-group">
            <label for="name">名前</label>
            <input type="text" name="name" id="name" class="form-controlx size_s" value="{{ old('name', $history->name) }}" required>
            &emsp;
            <select name="member_id" id="member_id">
                <option value="0" data-name="">非会員</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" data-name="{{ $member->name }}"{{ old('member_id', $history->member_id) == $member->id ? 'selected' : '' }}>[{{ $member->id }}] {{ $member->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="text" name="email" id="email" class="form-control" value="{{ old('email', $history['email'] ?? '') }}" >
        </div>

        <div class="form-group">
            <label for="tel">電話番号</label>
            @php
                $oldTel = old('tel', $history['tel'] ?? '');
                $telParts = is_array($oldTel) ? $oldTel : explode('-', $oldTel);
            @endphp
            <input type="text" name="tel[]" id="tel" class="size_s" value="{{ $telParts[0] ?? '' }}"  placeholder="090"><div class="unit">-&emsp;</div>
            <input type="text" name="tel[]" id="tel2" class="size_s" value="{{ $telParts[1] ?? '' }}"  placeholder="0000"><div class="unit">-&emsp;</div>
            <input type="text" name="tel[]" id="tel3" class="size_s" value="{{ $telParts[2] ?? '' }}"  placeholder="0000">
        </div>

        <div class="form-group">
            <label for="equipment_id">スタジオ</label>
            <select name="equipment_id" id="equipment_id" class="form-control">
                <option value="">指定なし</option>
                @foreach($equipments as $equipment)
                    <option value="{{ $equipment->id }}"{{ old('equipment_id', $history['equipment_id'] ?? '') == $equipment->id ? 'selected' : '' }}>{{ $equipment->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            @php
                $oldCostume = old('costume', $history['costume'] ?? '');
                $costumeVals = is_array($oldCostume) ? $oldCostume : explode('-', $oldCostume);
                $costumeVals1 = $costumeVals[0] ?? '';
                $costumeVals2 = $costumeVals[1] ?? '';
                $costumeVals3 = $costumeVals[2] ?? '';
            @endphp
            <label for="costume1">コスチューム</label>
            <section class="mr-3">
            <select name="costume1" id="costume1" class="form-controlx">
                <option value="">なし</option>
                @foreach($costumes as $costume)
                    <option value="{{ $costume->id }}" data-img="{{ Storage::url('costume/' . $costume->filename) }}" {{ $costume->id == $costumeVals1 ? 'selected' : '' }}>{{ $costume->name }}</option>
                @endforeach
            </select>
            <div id="costume1-preview" style="margin-top:10px;">
                <img src="" id="costume1-img" style="display:none; width:100px;">
            </div>
            </section>

            <section class="mr-3">
                <select name="costume2" id="costume2" class="form-controlx">
                    <option value="">なし</option>
                    @foreach($costumes as $costume)
                        <option value="{{ $costume->id }}" data-img="{{ Storage::url('costume/' . $costume->filename) }}" {{ $costume->id == $costumeVals2 ? 'selected' : '' }}>{{ $costume->name }}</option>
                    @endforeach
                </select>
                <div id="costume2-preview" style="margin-top:10px;">
                    <img src="" id="costume2-img" style="display:none; width:100px;">
                </div>
            </section>

            <section>
                <select name="costume3" id="costume3" class="form-controlx">
                    <option value="">なし</option>
                    @foreach($costumes as $costume)
                        <option value="{{ $costume->id }}" data-img="{{ Storage::url('costume/' . $costume->filename) }}" {{ $costume->id == $costumeVals3 ? 'selected' : '' }}>{{ $costume->name }}</option>
                    @endforeach
                </select>
                <div id="costume3-preview" style="margin-top:10px;">
                    <img src="" id="costume3-img" style="display:none; width:100px;">
                </div>
            </section>
        </div>
        <div class="form-group">
            <label for="area">ご利用エリア</label>
            <input type="text" name="area" id="area" class="form-control size_m" value="{{ old('area', $history['area'] ?? '') }}" >
        </div>

        <div class="form-group">
            <label for="place">具体的な場所</label>
            <input type="text" name="place" id="place" class="form-control size_m" value="{{ old('place', $history['place'] ?? '') }}" >
        </div>

        <div class="form-group">
            <label for="meet">待ち合わせ方法</label>
            <input type="text" name="meet" id="meet" class="form-control size_m" value="{{ old('meet', $history['meet'] ?? '') }}" >
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" class="form-control size_m" value="{{ old('address', $history['address'] ?? '') }}" >
        </div>

        <div class="form-group">
            <label for="comment">備考</label>
            <textarea name="comment" id="comment" class="form-control">{{ old('comment', $history['comment'] ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label for="memo">メモ</label>
            <textarea name="memo" id="memo" class="form-control">{{ old('memo', $history['memo'] ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label for="memo_share">メモ（共有用）</label>
            <textarea name="memo_share" id="memo_share" class="form-control">{{ old('memo_share', $history['memo_share'] ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label for="memo_result">予約完了時のメモ</label>
            <textarea name="memo_result" id="memo_result" class="form-control">{{ old('memo_result', $history['memo_result'] ?? '') }}</textarea>
        </div>


        <div class="form-group">
            <label for="fee_course">コース料金</label>
            <input type="number" name="fee_course" id="fee_course" class="form-control size_s" value="{{ old('fee_course', $history['fee_course'] ?? 0) }}" onchange="updateTotalPrice()">
        </div>

        <div class="form-group">
            <label for="fee_option">オプション料金</label>
            <input type="number" name="fee_option" id="fee_option" class="form-control size_s" value="{{ old('fee_option', $history['fee_option'] ?? 0) }}" onchange="updateTotalPrice()">
        </div>

        <div class="form-group">
            <label for="fee_adjust">調整金額</label>
            <input type="number" name="fee_adjust" id="fee_adjust" class="form-control size_s" value="{{ old('fee_adjust', $history['fee_adjust'] ?? 0) }}" onchange="updateTotalPrice()">
        </div>

        <div class="form-group">
            <label for="fee_total">合計金額</label>
            <input type="number" name="fee_total" id="fee_total" class="form-control size_s" value="{{ old('fee_total', $history['fee_total'] ?? 0) }}" readonly>
        </div>

        <button type="submit" class="btn btn-primary"> 更新する </button>
    </form>

</section>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        var courseId = "{{ old('course_id', $history['course_id']) }}";
        var courseFeeSelect = document.getElementById('course_time');
        var oldCourseTime = "{{ old('course_time', $history['course_time']) }}";

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

        // 現在の予約データをJavaScriptで利用できるようにする
        const currentMyOptions = {!! json_encode($history->myoption ?? []) !!};
        const currentMyCostumes = {!! json_encode($history->mycostume ?? []) !!};

        // キャスト選択時の処理
        document.getElementById('item_id').addEventListener('change', function() {
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
                        <div class="option-item">
                            <input type="checkbox"
                                   name="myoption[]"
                                   id="myoption_${option.id}"
                                   value="${option.id}"
                                   ${currentMyOptions.includes(option.id) ? 'checked' : ''}>
                            <label for="myoption_${option.id}">
                                ${option.filename ?
                                    `<img src="/uploads/myoption/${option.filename}"
                                          alt="${option.name}"
                                          style="width:100px; margin-bottom:5px;">` : ''}
                                <span>${option.name}: ${new Intl.NumberFormat('ja-JP').format(option.price)}円</span>
                            </label>
                        </div>
                    `).join('');

                    // マイコスチュームの表示を更新
                    mycostumeContainer.innerHTML = data.myCostumes.map(costume => `
                        <div class="costume-item">
                            <input type="checkbox"
                                   name="mycostume[]"
                                   id="mycostume_${costume.id}"
                                   value="${costume.id}"
                                   ${currentMyCostumes.includes(costume.id) ? 'checked' : ''}>
                            <label for="mycostume_${costume.id}">
                                ${costume.filename ?
                                    `<img src="/uploads/mycostume/${costume.filename}"
                                          alt="${costume.name}"
                                          style="width:100px; margin-bottom:5px;">` : ''}
                                <span>${costume.name}: ${new Intl.NumberFormat('ja-JP').format(costume.price)}円</span>
                            </label>
                        </div>
                    `).join('');

                    // チェックボックスのイベントリスナーを追加
                    document.querySelectorAll('input[name="myoption[]"]').forEach(checkbox => {
                        checkbox.addEventListener('change', updateTotalPrice);
                    });
                    document.querySelectorAll('input[name="mycostume[]"]').forEach(checkbox => {
                        checkbox.addEventListener('change', updateTotalPrice);
                    });

                    // 料金を更新
                    updateTotalPrice();
                })
                .catch(error => console.error('Error:', error));
        });

        // 合計金額を更新する関数
        function updateTotalPrice() {
            let total = 0;

            // コース料金
            const courseFee = parseInt(document.getElementById('fee_course').value) || 0;
            total += courseFee;

            // オプション料金
            const optionFee = parseInt(document.getElementById('fee_option').value) || 0;
            total += optionFee;

            // マイオプション料金
            document.querySelectorAll('input[name="myoption[]"]:checked').forEach(checkbox => {
                const priceText = checkbox.closest('.option-item').querySelector('span').textContent;
                const price = parseInt(priceText.match(/(\d+),?(\d+)?/)[0].replace(',', '')) || 0;
                total += price;
            });

            // マイコスチューム料金
            document.querySelectorAll('input[name="mycostume[]"]:checked').forEach(checkbox => {
                const priceText = checkbox.closest('.costume-item').querySelector('span').textContent;
                const price = parseInt(priceText.match(/(\d+),?(\d+)?/)[0].replace(',', '')) || 0;
                total += price;
            });

            // 調整金額
            const adjustFee = parseInt(document.getElementById('fee_adjust').value) || 0;
            total += adjustFee;

            // 合計金額を更新
            document.getElementById('fee_total').value = total;
        }

        // 初期表示時のイベントリスナー設定
        document.addEventListener('DOMContentLoaded', function() {
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
                checkbox.addEventListener('change', updateTotalPrice);
            });
            document.querySelectorAll('input[name="mycostume[]"]').forEach(checkbox => {
                checkbox.addEventListener('change', updateTotalPrice);
            });

            // 初期表示時の合計金額を計算
            updateTotalPrice();
        });

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

        // 各コスチュームセレクトボックスにプレビュー機能を設定
        document.addEventListener('DOMContentLoaded', function() {
            handleCostumePreview('costume1');
            handleCostumePreview('costume2');
            handleCostumePreview('costume3');
        });

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
    });
</script>


</x-admin-layout>
