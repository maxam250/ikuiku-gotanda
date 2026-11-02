
<x-admin-layout>
    <x-slot name="header">
        利用履歴追加
    </x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.member_histories.index', ['member' => $member->id]) }}"
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

    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                {{ $member->name }}様の利用履歴追加
            </h3>
        </div>

        <form action="{{ route('admin.member_histories.store', ['member'=>$member->id]) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- フラグ -->
                <div>
                    <label for="flag" class="block text-sm font-medium text-gray-700 mb-2">ステータス</label>
                    <select name="flag" id="flag" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="0" {{ old('flag') == 0 ? 'selected' : '' }}>未設定</option>
                        <option value="1" {{ old('flag') == 1 ? 'selected' : '' }}>確認中</option>
                        <option value="2" {{ old('flag') == 2 ? 'selected' : '' }}>確定</option>
                        <option value="3" {{ old('flag') == 3 ? 'selected' : '' }}>保留</option>
                    </select>
                </div>

                <!-- 予約種別 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">予約種別</label>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="radio" name="kind" id="kind1" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" {{ old('kind' ?? '') == '1' || empty(old('kind' ?? '')) ? 'checked' : '' }}>
                            <label for="kind1" class="ml-2 block text-sm text-gray-900">通常予約</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" name="kind" id="kind2" value="2" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" {{ old('kind' ?? '') == '2' ? 'checked' : '' }}>
                            <label for="kind2" class="ml-2 block text-sm text-gray-900">リクエスト予約</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 日時 -->
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">日時</label>
                <div class="flex items-center space-x-2">
                    <input type="date" name="date" id="date"
                           class="block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('date' ?? '') }}">
                    <select name="time_start" class="block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @foreach (generateTimeOptions('09:00', '23:00') as $value => $label)
                        <option value="{{ $value }}" {{ old('time_start' ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="text-gray-500">～</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- コース -->
                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">コース</label>
                    <select name="course_id" id="course_id" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="" disabled selected>選択してください</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}"{{ old('course_id' ?? '') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 時間 -->
                <div>
                    <label for="course_time" class="block text-sm font-medium text-gray-700 mb-2">時間</label>
                    <select name="course_time" id="course_time" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="" disabled selected>選択してください</option>
                    </select>
                </div>
            </div>

            <!-- キャスト -->
            <div>
                <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2">キャスト</label>
                <div class="flex items-center space-x-2">
                    <input type="text" name="item_name" id="item_name"
                           class="block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('item_name') ?? '' }}">
                    <select name="item_id" id="item_id" class="block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">指名なし</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-name="{{ $item->item_name }}"{{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->item_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- キャストその他 -->
            <div>
                <label for="item_name_other" class="block text-sm font-medium text-gray-700 mb-2">キャストその他</label>
                <input type="text" name="item_name_other" id="item_name_other"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('item_name_other') ?? '' }}">
            </div>

            <!-- オプション -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">オプション</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @php
                        $oldOption = old('option' ?? '');
                        $optionVals = is_array($oldOption) ? $oldOption : explode('-', $oldOption);
                    @endphp
                    @foreach($options as $option)
                    <div class="flex items-center">
                        <input type="checkbox" name="option[]" id="option{{ $option->id }}" value="{{ $option->id }}"
                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                               {{ in_array($option->id, $optionVals) ? 'checked' : '' }}>
                        <label for="option{{ $option->id }}" class="ml-2 block text-sm text-gray-900">{{ $option->title }}</label>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- 会員情報 -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">名前 <span class="text-red-500">*</span></label>
                <div class="flex items-center space-x-2">
                    <input type="text" name="name" id="name" required
                           class="block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('name') ?? '' }}">
                    <select name="member_id" id="member_id" class="block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="0" data-name="">非会員</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" data-name="{{ $member->name }}"{{ old('member_id') == $member->id ? 'selected' : '' }}>[{{ $member->id }}] {{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- メールアドレス -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">メールアドレス <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" required
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('email' ?? '') }}">
            </div>

            <!-- 電話番号 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">電話番号</label>
                @php
                    $oldTel = old('tel' ?? '');
                    $telParts = is_array($oldTel) ? $oldTel : explode('-', $oldTel);
                @endphp
                <div class="flex items-center space-x-2">
                    <input type="text" name="tel[]" placeholder="090"
                           class="block w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ $telParts[0] ?? '' }}">
                    <span class="text-gray-500">-</span>
                    <input type="text" name="tel[]" placeholder="0000"
                           class="block w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ $telParts[1] ?? '' }}">
                    <span class="text-gray-500">-</span>
                    <input type="text" name="tel[]" placeholder="0000"
                           class="block w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ $telParts[2] ?? '' }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- ご利用エリア -->
                <div>
                    <label for="area" class="block text-sm font-medium text-gray-700 mb-2">ご利用エリア</label>
                    <input type="text" name="area" id="area"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('area') }}">
                </div>

                <!-- 具体的な場所 -->
                <div>
                    <label for="place" class="block text-sm font-medium text-gray-700 mb-2">具体的な場所</label>
                    <input type="text" name="place" id="place"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('place') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 待ち合わせ方法 -->
                <div>
                    <label for="meet" class="block text-sm font-medium text-gray-700 mb-2">待ち合わせ方法</label>
                    <input type="text" name="meet" id="meet"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('meet') }}">
                </div>

                <!-- 住所 -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">住所</label>
                    <input type="text" name="address" id="address"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('address') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 備考 -->
                <div>
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">備考</label>
                    <textarea name="comment" id="comment" rows="4"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('comment') }}</textarea>
                </div>

                <!-- メモ -->
                <div>
                    <label for="memo" class="block text-sm font-medium text-gray-700 mb-2">メモ</label>
                    <textarea name="memo" id="memo" rows="4"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('memo') }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- メモ（共有用） -->
                <div>
                    <label for="memo_share" class="block text-sm font-medium text-gray-700 mb-2">メモ（共有用）</label>
                    <textarea name="memo_share" id="memo_share" rows="4"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('memo_share') }}</textarea>
                </div>

                <!-- 予約完了時のメモ -->
                <div>
                    <label for="memo_result" class="block text-sm font-medium text-gray-700 mb-2">予約完了時のメモ</label>
                    <textarea name="memo_result" id="memo_result" rows="4"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('memo_result') }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- コース料金 -->
                <div>
                    <label for="fee_course" class="block text-sm font-medium text-gray-700 mb-2">コース料金</label>
                    <div class="relative">
                        <input type="number" name="fee_course" id="fee_course"
                               class="block w-full px-3 py-2 pr-12 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('fee_course') }}">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500 text-sm">円</span>
                        </div>
                    </div>
                </div>

                <!-- オプション料金 -->
                <div>
                    <label for="fee_option" class="block text-sm font-medium text-gray-700 mb-2">オプション料金</label>
                    <div class="relative">
                        <input type="number" name="fee_option" id="fee_option"
                               class="block w-full px-3 py-2 pr-12 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('fee_option') }}">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500 text-sm">円</span>
                        </div>
                    </div>
                </div>

                <!-- 調整金 -->
                <div>
                    <label for="fee_adjust" class="block text-sm font-medium text-gray-700 mb-2">調整金</label>
                    <div class="relative">
                        <input type="number" name="fee_adjust" id="fee_adjust"
                               class="block w-full px-3 py-2 pr-12 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('fee_adjust') }}">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500 text-sm">円</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 調整金メモ -->
            <div>
                <label for="fee_adjust_memo" class="block text-sm font-medium text-gray-700 mb-2">調整金メモ</label>
                <textarea name="fee_adjust_memo" id="fee_adjust_memo" rows="3"
                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('fee_adjust_memo') }}</textarea>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    登録する
                </button>
            </div>
        </form>
    </div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    var courseId = "{{ old('course_id') }}";
    var courseFeeSelect = document.getElementById('course_time');
    var oldCourseTime = "{{ old('course_time') }}";

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
