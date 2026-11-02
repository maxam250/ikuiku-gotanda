<x-admin-layout>

    <x-slot name="header">
    スタッフ新規登録
    </x-slot>
<div class="w-full py-8 px-6">
    <!-- ヘッダー -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">スタッフ新規登録</h1>
        <a href="{{ route('admin.staff.index') }}"
           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            戻る
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <div class="font-bold mb-2">入力にエラーがあります：</div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <form action="{{ route('admin.staff.store') }}" method="POST" class="p-6">
            @csrf

            <!-- 基本情報 -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    基本情報
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            名前 <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>



                </div>
            </div>

            <!-- 勤務情報 -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    勤務情報
                </h3>
                <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="default_start_time" class="block text-sm font-medium text-gray-700 mb-2">
                            基本勤務開始時間
                        </label>
                        <input type="time" name="default_start_time" id="default_start_time"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('default_start_time') border-red-500 @enderror"
                               value="{{ old('default_start_time') }}">
                        @error('default_start_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="default_end_time" class="block text-sm font-medium text-gray-700 mb-2">
                            基本勤務終了時間
                        </label>
                        <input type="time" name="default_end_time" id="default_end_time"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('default_end_time') border-red-500 @enderror"
                               value="{{ old('default_end_time') }}">
                        @error('default_end_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">勤務日</label>
                        <div class="grid grid-cols-2 md:grid-cols-7 gap-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="work_days[]" value="monday"
                                       {{ in_array('monday', old('work_days', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">月曜日</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="work_days[]" value="tuesday"
                                       {{ in_array('tuesday', old('work_days', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">火曜日</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="work_days[]" value="wednesday"
                                       {{ in_array('wednesday', old('work_days', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">水曜日</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="work_days[]" value="thursday"
                                       {{ in_array('thursday', old('work_days', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">木曜日</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="work_days[]" value="friday"
                                       {{ in_array('friday', old('work_days', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">金曜日</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="work_days[]" value="saturday"
                                       {{ in_array('saturday', old('work_days', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">土曜日</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="work_days[]" value="sunday"
                                       {{ in_array('sunday', old('work_days', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">日曜日</span>
                            </label>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label for="default_studio_id" class="block text-sm font-medium text-gray-700 mb-2">
                            基本勤務事務所
                        </label>
                        <select name="default_studio_id" id="default_studio_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('default_studio_id') border-red-500 @enderror">
                            <option value="">選択してください</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" {{ old('default_studio_id') == $office->id ? 'selected' : '' }}>
                                    {{ $office->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('default_studio_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div> -->
            </div>

            <!-- 勤務スケジュール設定 -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    勤務スケジュール設定
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">曜日ごとの事務所設定</label>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 mb-4">
                                各曜日ごとに勤務する事務所を設定できます。休みの場合は「休み」を選択してください。
                            </p>

                            @php
                                $weeklySchedule = old('weekly_schedule', []);
                                $days = [
                                    1 => '月曜日',
                                    2 => '火曜日',
                                    3 => '水曜日',
                                    4 => '木曜日',
                                    5 => '金曜日',
                                    6 => '土曜日',
                                    7 => '日曜日'
                                ];
                            @endphp

                            <div class="flex flex-nowrap gap-3" style="overflow-x:auto;">
                                @foreach($days as $dayNum => $dayName)
                                    <div class="bg-white p-4 rounded border" style="flex:0 0 auto; min-width:120px;">
                                        <h4 class="font-medium text-gray-900 mb-3">{{ $dayName }}</h4>

                                        <div class="space-y-3">
                                            <!-- 勤務フラグ -->
                                            <label class="flex items-center">
                                                <input type="checkbox"
                                                       name="weekly_schedule[{{ $dayNum }}][is_work]"
                                                       value="1"
                                                       {{ isset($weeklySchedule[$dayNum]['is_work']) && $weeklySchedule[$dayNum]['is_work'] ? 'checked' : '' }}
                                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                       onchange="toggleDaySchedule({{ $dayNum }})">
                                                <span class="ml-2 text-sm text-gray-700">勤務</span>
                                            </label>

                                            <!-- 事務所選択 -->
                                            <div id="schedule-{{ $dayNum }}" class="{{ isset($weeklySchedule[$dayNum]['is_work']) && $weeklySchedule[$dayNum]['is_work'] ? '' : 'hidden' }}">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">事務所</label>
                                                <select name="weekly_schedule[{{ $dayNum }}][cate_id]"
                                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                    <option value="">選択してください</option>
                                                    @foreach($offices as $office)
                                                        <option value="{{ $office->id }}"
                                                                {{ isset($weeklySchedule[$dayNum]['cate_id']) && $weeklySchedule[$dayNum]['cate_id'] == $office->id ? 'selected' : '' }}>
                                                            {{ $office->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="auto_schedule_enabled" value="1"
                                   {{ old('auto_schedule_enabled') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">自動スケジュール生成を有効にする</span>
                        </label>
                    </div>

                    <div>
                        <label for="auto_schedule_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            自動生成開始日
                        </label>
                        <input type="date" name="auto_schedule_start_date" id="auto_schedule_start_date"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('auto_schedule_start_date') border-red-500 @enderror"
                               value="{{ old('auto_schedule_start_date') }}">
                        @error('auto_schedule_start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="auto_schedule_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            自動生成終了日
                        </label>
                        <input type="date" name="auto_schedule_end_date" id="auto_schedule_end_date"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('auto_schedule_end_date') border-red-500 @enderror"
                               value="{{ old('auto_schedule_end_date') }}">
                        @error('auto_schedule_end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div> -->
                </div>
            </div>

            <script>
                function toggleDaySchedule(dayNum) {
                    const checkbox = document.querySelector(`input[name="weekly_schedule[${dayNum}][is_work]"]`);
                    const scheduleDiv = document.getElementById(`schedule-${dayNum}`);

                    if (checkbox.checked) {
                        scheduleDiv.classList.remove('hidden');
                    } else {
                        scheduleDiv.classList.add('hidden');
                        // チェックボックスが外されたら、カテゴリ選択もクリア
                        const select = scheduleDiv.querySelector('select');
                        if (select) {
                            select.value = '';
                        }
                    }
                }
            </script>


            <!-- 表示設定 -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                    </svg>
                    表示設定
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                            カラーコード
                        </label>
                        <input type="color" name="color" id="color"
                               class="w-full h-10 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('color') border-red-500 @enderror"
                               value="{{ old('color', '#3B82F6') }}">
                        @error('color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">
                            並び順
                        </label>
                        <input type="number" name="sort" id="sort"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('sort') border-red-500 @enderror"
                               value="{{ old('sort', 0) }}" min="0">
                        @error('sort')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-end">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">アクティブ</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- メモ -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    メモ
                </h3>
                <div>
                    <textarea name="memo" id="memo" rows="4"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('memo') border-red-500 @enderror"
                              placeholder="スタッフに関するメモを入力してください">{{ old('memo') }}</textarea>
                    @error('memo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 送信ボタン -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.staff.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                    キャンセル
                </a>
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 font-bold py-2 px-4 rounded transition duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    登録する
                </button>
            </div>
        </form>
    </div>
</div>

</x-admin-layout>
