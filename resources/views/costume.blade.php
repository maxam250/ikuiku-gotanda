@extends('layouts.app')

@section('content')
<div class="titleArea">
<h2>COSTUMES</h2>
<div class="title">コスチューム一覧</div>
</div>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-2">

    <!-- カテゴリタブ -->
    @if($categories->count() > 0)
    <div class="bg-white rounded-2xl shadow-lg border border-slate-200/50 mb-8">
        <div class="p-4 lg:p-6">
            <div class="flex flex-wrap gap-2">
                <!-- 全て表示ボタン -->
                <button onclick="filterCategory('all')"
                        class="btn size_s category-tab {{ !$selectedCategory || $selectedCategory === 'all' ? 'active' : '' }}"
                        data-category="all">
                    <i class="fas fa-th-large mr-2"></i>
                    全て
                </button>

                <!-- 動的カテゴリタブ -->
                @foreach($categories as $category)
                <button onclick="filterCategory('{{ $category->id }}')"
                        class="btn size_s category-tab {{ $selectedCategory == $category->id ? 'active' : '' }}"
                        data-category="{{ $category->id }}">
                    <i class="fas fa-tag mr-2"></i>
                    {{ $category->title }}
                </button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- コスチューム一覧 -->
    @if($costumes->count() > 0)
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 lg:gap-6">
        @foreach ($costumes as $costume)
            <div class="group costume-item" data-category="{{ $costume->cate }}">
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-slate-200/50 cursor-pointer"
                     onclick="openCostumeModal({{ json_encode([
                         'id' => $costume->id,
                         'name' => $costume->name,
                         'body' => $costume->body,
                         'filename' => $costume->filename,
                         'cate' => $costume->cate,
                         'costume_cate' => $costume->costume_cate ? [
                             'id' => $costume->costume_cate->id,
                             'title' => $costume->costume_cate->title
                         ] : null
                     ]) }})">
                    <div class="relative aspect-[3/4] overflow-hidden">
                        <img src="{{ !empty($costume->filename) ? Storage::url('costume/' . $costume->filename) : asset('images/dmy.jpg') }}"
                             alt="{{ $costume->name }}"
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">

                        <!-- カテゴリバッジ -->
                        @if($costume->costume_cate)
                        <div class="absolute top-2 right-2">
                            <span class="text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg
                            @if($costume->costume_cate->id == 1) bg-purple-600
                            @elseif($costume->costume_cate->id == 2) bg-blue-600
                            @elseif($costume->costume_cate->id == 3) bg-green-600
                            @elseif($costume->costume_cate->id == 4) bg-red-600
                            @elseif($costume->costume_cate->id == 5) bg-orange-700
                            @elseif($costume->costume_cate->id == 6) bg-gray-600
                            @endif
                            ">
                                {{ $costume->costume_cate->title }}
                            </span>
                        </div>
                        @endif
                    </div>

                    <div class="p-3">
                        <h3 class="font-bold text-slate-800 text-sm mb-1 line-clamp-2">{{ $costume->name }}</h3>

                        @if($costume->body)
                        <p class="text-xs text-slate-600 line-clamp-2">{{ $costume->body }}</p>
                        @endif

                        <!-- 詳細表示ボタン -->
                        <div class="mt-2 pt-2 border-t border-slate-100">
                            <span class="inline-flex items-center gap-1 font-medium text-xs transition-colors duration-200
                            @if($costume->costume_cate && $costume->costume_cate->id == 1) text-purple-600
                            @elseif($costume->costume_cate && $costume->costume_cate->id == 2) text-blue-600
                            @elseif($costume->costume_cate && $costume->costume_cate->id == 3) text-green-600
                            @elseif($costume->costume_cate && $costume->costume_cate->id == 4) text-red-600
                            @elseif($costume->costume_cate && $costume->costume_cate->id == 5) text-orange-700
                            @elseif($costume->costume_cate && $costume->costume_cate->id == 6) text-gray-600
                            @endif
                            ">
                                <i class="fas fa-search text-xs"></i>
                                <span>詳細を見る</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-16">
        <i class="fas fa-tshirt text-6xl text-slate-300 mb-4"></i>
        <h3 class="text-xl font-bold text-slate-600 mb-2">コスチュームが見つかりませんでした</h3>
        <p class="text-slate-500 mb-6">別のカテゴリをお試しください</p>
        <button onclick="filterCategory('all')" class="btn size_m">
            <i class="fas fa-th-large mr-2"></i>
            全てのコスチュームを表示
        </button>
    </div>
    @endif

    <!-- コスチューム件数表示 -->
    @if($selectedCategory && $selectedCategory !== 'all')
    <div class="mt-8 text-center">
        <p class="text-slate-600">
            <i class="fas fa-filter mr-1"></i>
            カテゴリ内: <span class="font-bold text-slate-800">{{ $costumes->count() }}件</span>のコスチュームが見つかりました
        </p>
    </div>
    @endif
</section>

<!-- コスチュームモーダル -->
<div id="costumeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[9999] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[calc(100vh-100px)] overflow-hidden">
        <!-- モーダルヘッダー -->
        <div class="flex items-center justify-between p-6 border-b border-slate-200">
            <h3 id="modalTitle" class="text-2xl font-bold text-slate-800"></h3>
            <button onclick="closeCostumeModal()" class="btn size_s">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- モーダルコンテンツ -->
        <div class="p-6 overflow-y-auto max-h-[calc(100vh-240px)]">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- 画像 -->
                <div class="aspect-[3/4] overflow-hidden rounded-xl">
                    <img id="modalImage" src="" alt="" class="w-full h-full object-cover">
                </div>

                <!-- 詳細情報 -->
                <div class="space-y-4">
                    <!-- カテゴリ -->
                    <div id="costumeModalCategoryContainer" class="hidden">
                        <span id="costumeModalCategory" class="inline-block px-3 py-1 rounded-full text-sm font-medium">
                        </span>
                    </div>

                    <!-- 説明 -->
                    <div id="modalDescriptionContainer" class="hidden">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">説明</label>
                        <div id="modalDescription" class="text-slate-600 whitespace-pre-line"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- モーダルフッター -->
        <div class="flex justify-end gap-3 p-6 border-t border-slate-200">
            <button onclick="closeCostumeModal()" class="cancel">
                <i class="fas fa-times mr-2"></i>
                閉じる
            </button>
        </div>
    </div>
</div>

<style>
/* カテゴリタブ（form.css btn ベース） */
.category-tab {
    display: inline-flex !important;
    align-items: center !important;
    margin: 0.25rem !important;
    background: #EEE !important;
    color: #333 !important;
    border: 1px solid #BBB !important;
    border-radius: 4px !important;
}

.category-tab:hover {
    background: #ddd !important;
    color: #333 !important;
}

.category-tab.active {
    background: #2c3038 !important;
    color: #fff !important;
    border-color: #2c3038 !important;
}

.category-tab.active:hover {
    background: #4f535f !important;
    color: #fff !important;
}

/* テキストの行制限 */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* モーダルアニメーション */
#costumeModal.show {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* スマホサイズ対応 */
@media (max-width: 768px) {
    #costumeModal {
        padding: 0.5rem;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100vw;
        height: 100vh;
    }

    #costumeModal .bg-white {
        max-height: calc(100vh - 1rem);
        margin: 0.5rem;
        height: calc(100vh - 1rem);
        width: calc(100vw - 1rem);
    }

    #costumeModal .p-6 {
        padding: 1rem;
    }

    #costumeModal .text-2xl {
        font-size: 1.25rem;
    }

    #costumeModal .grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    #costumeModal .aspect-\[3\/4\] {
        aspect-ratio: 3/4;
        max-height: calc(100vh - 300px);
    }

    #costumeModal .max-h-\[calc\(100vh-240px\)\] {
        max-height: calc(100vh - 240px);
    }
}
</style>


<script>
    const costumeBaseUrl = "{{ Storage::url('costume/') }}";
    const defaultImage = "{{ asset('images/dmy.jpg') }}";
</script>

<script>
// カテゴリフィルタリング機能
function filterCategory(categoryId) {
    // URLパラメータを更新してページをリロード
    const url = new URL(window.location);
    if (categoryId === 'all') {
        url.searchParams.delete('category');
    } else {
        url.searchParams.set('category', categoryId);
    }
    window.location.href = url.toString();
}

// コスチュームモーダル表示
function openCostumeModal(costume) {
    console.log('Opening modal for costume:', costume);

    const modal = document.getElementById('costumeModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalImage = document.getElementById('modalImage');
    const modalCategory = document.getElementById('costumeModalCategory');
    const modalCategoryContainer = document.getElementById('costumeModalCategoryContainer');
    const modalDescription = document.getElementById('modalDescription');
    const modalDescriptionContainer = document.getElementById('modalDescriptionContainer');

    // 一旦 src をクリアする（強制的に再描画させる）
    modalImage.src = '';

    // モーダルを表示
    modal.classList.remove('hidden');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';

    // コスチューム情報を設定
    console.log('Setting title to:', costume.name);
    modalTitle.textContent = costume.name;

    requestAnimationFrame(() => {
        // タイトルを再設定（確実にするため）
        modalTitle.textContent = costume.name;

        // 画像を設定
        if (costume.filename) {
            modalImage.src = costumeBaseUrl + costume.filename;
        } else {
            modalImage.src = defaultImage;
        }
    }, 10);
    modalImage.alt = costume.name;

    // カテゴリ情報を設定
    console.log('Costume data:', costume);
    console.log('Costume category:', costume.costume_cate);
    if (costume.costume_cate) {
        console.log('Category title:', costume.costume_cate.title);
        modalCategory.textContent = costume.costume_cate.title;

        // カテゴリIDに応じたクラスを設定
        modalCategory.className = 'inline-block px-3 py-1 rounded-full text-sm font-medium';
        const categoryId = costume.costume_cate.id;
        if (categoryId == 1) {
            modalCategory.classList.add('bg-purple-100', 'text-purple-800');
        } else if (categoryId == 2) {
            modalCategory.classList.add('bg-blue-100', 'text-blue-800');
        } else if (categoryId == 3) {
            modalCategory.classList.add('bg-green-100', 'text-green-800');
        } else if (categoryId == 4) {
            modalCategory.classList.add('bg-red-100', 'text-red-800');
        } else if (categoryId == 5) {
            modalCategory.classList.add('bg-orange-100', 'text-orange-800');
        } else if (categoryId == 6) {
            modalCategory.classList.add('bg-gray-100', 'text-gray-800');
        }

        modalCategoryContainer.classList.remove('hidden');
    } else {
        modalCategoryContainer.classList.add('hidden');
    }

    // 説明を設定
    if (costume.body && costume.body.trim() !== '') {
        modalDescription.textContent = costume.body;
        modalDescriptionContainer.classList.remove('hidden');
    } else {
        modalDescriptionContainer.classList.add('hidden');
    }


}

// コスチュームモーダル閉じる
function closeCostumeModal() {
    const modal = document.getElementById('costumeModal');
    modal.classList.add('hidden');
    modal.classList.remove('show');
    document.body.style.overflow = 'auto';
}

// ESCキーでモーダルを閉じる
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeCostumeModal();
    }
});

// モーダル背景クリックで閉じる
document.getElementById('costumeModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeCostumeModal();
    }
});
</script>

@endsection
