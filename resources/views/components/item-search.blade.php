<div class="bg-white rounded-2xl shadow-sm border border-slate-200/50 mb-8">
    <div class="border-b border-slate-200/50">
        <button onclick="toggleItemSearch()" class="w-full flex items-center justify-between text-left">
            <div class="flex items-center gap-3 p-4 lg:p-6 ">
                <i class="fas fa-search text-2xl text-slate-400"></i>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">検索・絞り込み</h3>
                    <p class="text-sm text-slate-600">キャストを条件で絞り込み検索</p>
                </div>
            </div>
            <i class="fas fa-chevron-down transition-transform duration-300 search-toggle-icon text-slate-400 mr-4 lg:mr-6"></i>
        </button>
    </div>

    <div id="itemSearchContent" class="search-content">
        <form method="GET" action="{{ route('item.index') }}" class="p-4 lg:p-6">

            {{-- タブナビゲーション --}}
            <div class="flex gap-1 mb-5 border-b border-slate-200">
                <button type="button" onclick="switchSearchTab('name')" id="tab-name"
                    class="search-tab-btn active px-4 py-2 text-sm font-semibold rounded-t-lg transition-colors duration-200">
                    <i class="fas fa-user mr-1"></i>キャスト名
                </button>
                <button type="button" onclick="switchSearchTab('size')" id="tab-size"
                    class="search-tab-btn px-4 py-2 text-sm font-semibold rounded-t-lg transition-colors duration-200">
                    <i class="fas fa-ruler mr-1"></i>サイズ
                </button>
            </div>

            {{-- キャスト名タブ --}}
            <div id="tab-content-name" class="search-tab-content">
                <label class="block text-sm font-semibold text-slate-700 mb-3">キャスト名</label>
                <input type="text" name="name" value="{{ request('name') }}" placeholder="キャスト名を入力"
                       class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors duration-200 hover:border-slate-200 hover:bg-slate-50/50">
            </div>

            {{-- サイズタブ --}}
            <div id="tab-content-size" class="search-tab-content hidden">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- 身長 --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-2">身長 (cm)</label>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(['145-150', '150-155', '155-160', '160-165', '165-170', '170-180'] as $range)
                            <label class="size-checkbox">
                                <input type="checkbox" name="height[]" value="{{ $range }}"
                                       {{ in_array($range, request('height', [])) ? 'checked' : '' }} class="hidden">
                                <span class="size-btn">{{ $range }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- バスト --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-2">バスト (cm)</label>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(['80-85', '85-90', '90-95', '95-100', '100-105', '105-110'] as $range)
                            <label class="size-checkbox">
                                <input type="checkbox" name="bust[]" value="{{ $range }}"
                                       {{ in_array($range, request('bust', [])) ? 'checked' : '' }} class="hidden">
                                <span class="size-btn">{{ $range }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- ウェスト --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-2">ウェスト (cm)</label>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(['55-60', '60-65', '65-70', '70-75', '75-80', '80-85'] as $range)
                            <label class="size-checkbox">
                                <input type="checkbox" name="waist[]" value="{{ $range }}"
                                       {{ in_array($range, request('waist', [])) ? 'checked' : '' }} class="hidden">
                                <span class="size-btn">{{ $range }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- ヒップ --}}
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-2">ヒップ (cm)</label>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(['80-85', '85-90', '90-95', '95-100', '100-105', '105-110'] as $range)
                            <label class="size-checkbox">
                                <input type="checkbox" name="hip[]" value="{{ $range }}"
                                       {{ in_array($range, request('hip', [])) ? 'checked' : '' }} class="hidden">
                                <span class="size-btn">{{ $range }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- 検索ボタン --}}
            <div class="flex justify-end gap-2 mt-5 pt-4 border-t border-slate-200">
                <a href="{{ route('item.index') }}" class="inline-flex items-center bg-slate-100 text-slate-700 font-semibold py-2 px-4 rounded-xl hover:bg-slate-50 transition-colors duration-200 text-sm">
                    <i class="fas fa-times mr-1.5"></i>リセット
                </a>
                <button type="submit" class="inline-flex items-center bg-gradient-to-r from-pink-500 to-pink-600 text-white font-semibold py-2 px-5 rounded-xl hover:from-pink-600 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg text-sm">
                    <i class="fas fa-search mr-1.5"></i>検索する
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* 検索アコーディオンのスタイル */
.search-content {
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: max-height 0.4s ease-in-out, opacity 0.3s ease-in-out;
}

.search-content.show {
    max-height: 1000px;
    opacity: 1;
}

.search-toggle-icon.active {
    transform: rotate(180deg);
}

/* タブのスタイル */
.search-tab-btn {
    background: transparent !important;
    color: #94a3b8 !important;
    border: none !important;
    border-bottom: 2px solid transparent !important;
    margin-bottom: -1px;
    text-decoration: none !important;
    cursor: pointer;
}

.search-tab-btn:hover {
    color: #ec4899 !important;
    background: #fef7f7 !important;
}

.search-tab-btn.active {
    color: #ec4899 !important;
    border-bottom-color: #ec4899 !important;
    background: transparent !important;
}

/* サイズボタンのスタイル */
.size-checkbox input:checked + .size-btn {
    background: #f9a8d4 !important;
    color: #831843 !important;
    border-color: #ec4899 !important;
    text-decoration: none !important;
}

.size-btn {
    display: inline-block !important;
    padding: 0.25rem 0.625rem !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
    background: white !important;
    color: #6b7280 !important;
    font-size: 0.75rem !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    text-decoration: none !important;
}

.size-btn:hover {
    border-color: #e5e7eb !important;
    background: #fafafa !important;
    color: #6b7280 !important;
    text-decoration: none !important;
}

/* オプションボタンのスタイル */
.option-checkbox input:checked + .option-btn {
    background: #ddd6fe !important;
    color: #5b21b6 !important;
    border-color: #8b5cf6 !important;
    text-decoration: none !important;
}

.option-btn {
    display: inline-block !important;
    padding: 0.5rem 1rem !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.625rem !important;
    background: white !important;
    color: #6b7280 !important;
    font-size: 0.875rem !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    text-decoration: none !important;
}

.option-btn:hover {
    border-color: #e5e7eb !important;
    background: #fafafa !important;
    color: #6b7280 !important;
    text-decoration: none !important;
}

/* 検索ボタンのスタイル */
button[type="submit"] {
    background: linear-gradient(to right, #ec4899, #db2777) !important;
    color: white !important;
    border: none !important;
    text-decoration: none !important;
}

button[type="submit"]:hover {
    background: linear-gradient(to right, #db2777, #be185d) !important;
    color: white !important;
    text-decoration: none !important;
}

/* リセットボタンのスタイル */
a[href*="item.index"] {
    background: #f1f5f9 !important;
    color: #475569 !important;
    border: 1px solid #e2e8f0 !important;
    text-decoration: none !important;
}

a[href*="item.index"]:hover {
    background: #f8fafc !important;
    color: #475569 !important;
    border: 1px solid #cbd5e1 !important;
    text-decoration: none !important;
}

/* 入力フィールドのスタイル */
input[type="text"] {
    border: 1px solid #d1d5db !important;
    background: white !important;
    color: #374151 !important;
}

input[type="text"]:hover {
    border-color: #e5e7eb !important;
    background: #fafafa !important;
}

input[type="text"]:focus {
    ring-color: #ec4899 !important;
    border-color: #ec4899 !important;
    background: white !important;
}

/* 検索・絞り込みボタンのスタイル */
button[onclick="toggleItemSearch()"] {
    background: transparent !important;
    color: inherit !important;
    border: none !important;
    text-decoration: none !important;
    padding: 0 !important;
    width: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    text-align: left !important;
    transition: all 0.2s ease !important;
}

button[onclick="toggleItemSearch()"]:hover {
    background: #fef7f7 !important;
    color: #ec4899 !important;
    border: none !important;
    text-decoration: none !important;
}

button[onclick="toggleItemSearch()"]:hover .fas {
    color: #ec4899 !important;
}

button[onclick="toggleItemSearch()"]:hover h3 {
    color: #ec4899 !important;
}

button[onclick="toggleItemSearch()"]:hover p {
    color: #f472b6 !important;
}
</style>

<script>
function toggleItemSearch() {
    const content = document.getElementById('itemSearchContent');
    const icon = document.querySelector('.search-toggle-icon');

    if (content.classList.contains('show')) {
        content.classList.remove('show');
        icon.classList.remove('active');
    } else {
        content.classList.add('show');
        icon.classList.add('active');
    }
}

function switchSearchTab(tab) {
    document.querySelectorAll('.search-tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.search-tab-btn').forEach(el => el.classList.remove('active'));

    document.getElementById('tab-content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.add('active');
}
</script>
