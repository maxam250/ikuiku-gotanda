<x-admin-layout>

<x-slot name="header">
スタッフ管理
</x-slot>
<div class="w-full py-8 px-6">
    <!-- ヘッダー -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">スタッフ管理</h1>
        <a href="{{ route('admin.staff.create') }}"
           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            新しいスタッフを作成
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- 検索フォーム（メッセージ管理と同様のスタイル） --}}
    <form method="GET" action="{{ route('admin.staff.index') }}" class="af-search mb-6">
        <div class="af-search-row">
            <div class="af-search-field" style="min-width:110px; max-width:130px;">
                <label for="staff_is_active">ステータス</label>
                <select name="is_active" id="staff_is_active" class="af-select">
                    <option value="">すべて</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>アクティブ</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>非アクティブ</option>
                </select>
            </div>
        </div>
        <div class="af-btn-row af-btn-row-center" style="margin-top:0.5rem;">
            <button type="submit" class="af-btn af-btn-search">
                <i class="fas fa-search"></i> 検索
            </button>
            <a href="{{ route('admin.staff.index') }}" class="af-btn af-btn-reset">
                <i class="fas fa-redo"></i> リセット
            </a>
        </div>
    </form>

    <!-- スタッフ一覧 -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">スタッフ情報</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">勤務情報</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ステータス</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">アクション</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($staffs as $staff)
                            <tr class="hover:bg-gray-50 transition duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    #{{ $staff->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">

                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $staff->name }}</div>
                                            @if($staff->specialties)
                                                <div class="text-sm text-gray-500">{{ $staff->specialties }}</div>
                                            @endif
                                            @if($staff->experience)
                                                <div class="text-xs text-gray-400">経験: {{ $staff->experience }}年</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">

                                    @if($staff->weekly_schedule)
                                        <div class="text-sm text-gray-500">
                                            <span class="font-medium">出勤曜日:</span>
                                            @php
                                                $dayLabels = [
                                                    '1' => '月',
                                                    '2' => '火',
                                                    '3' => '水',
                                                    '4' => '木',
                                                    '5' => '金',
                                                    '6' => '土',
                                                    '7' => '日'
                                                ];

                                                $schedule = is_string($staff->weekly_schedule) ? json_decode($staff->weekly_schedule, true) : $staff->weekly_schedule;

                                                if (is_array($schedule)) {
                                                    foreach ($schedule as $day => $dayInfo) {
                                                        if (isset($dayLabels[$day]) && isset($dayInfo['is_work']) && $dayInfo['is_work'] == '1') {
                                                            echo '<div class="text-xs">';
                                                            echo $dayLabels[$day] . ': ';
                                                            if (isset($dayInfo['cate_id']) && $dayInfo['cate_id']) {
                                                                // 事務所名を取得
                                                                $categoryName = $staff->getOfficeName($dayInfo['cate_id']);
                                                                if ($categoryName) {
                                                                    echo $categoryName;
                                                                } else {
                                                                    echo ' (未設定)';
                                                                }
                                                            } else {
                                                                echo '事務所未設定';
                                                            }
                                                            echo '</div>';
                                                        }
                                                    }
                                                }
                                            @endphp
                                        </div>
                                    @endif
                                    @if($staff->office)
                                        <div class="text-xs text-gray-400">
                                            <span class="font-medium">所属事務所:</span> {{ $staff->office->name }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $staff->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $staff->is_active ? 'アクティブ' : '非アクティブ' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.staff.edit', $staff) }}"
                                           class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition duration-200">
                                            編集
                                        </a>
                                        <form action="{{ route('admin.staff.destroy', $staff) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('本当に削除しますか？')"
                                                    class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition duration-200">
                                                削除
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ページネーション -->
    @if($staffs->hasPages())
        <div class="mt-6">
            {{ $staffs->appends(request()->query())->links() }}
        </div>
    @endif
</div>

</x-admin-layout>
