@extends('layouts.app')

@section('breadcrumb_label', $gallery->title)

@section('content')
<div class="titleArea">
<h2>GRAVURE GALLERY</h2>
<div class="title">グラビアギャラリー</div>
</div>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-2">
    <div class="mb-8">
        <x-back-to-list href="{{ route('gallery.index') }}" />

        {{-- タイトル・情報枠 --}}
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200/50 p-6 lg:p-8 mb-8">
            <div class="flex flex-col lg:flex-row gap-6">
                {{-- 左側：ギャラリー情報 --}}
                <div class="flex-1">
                    <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 mb-4">{{ $gallery->title }}</h1>

                    @if ($gallery->description)
                        <div class="text-slate-700 mb-4">
                            <p>{{ $gallery->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-slate-500">公開日：</span>
                            <span class="text-slate-800">{{ $gallery->created_at ? $gallery->created_at->format('Y/m/d') : '日付不明' }}</span>
                        </div>

                    </div>
                </div>

                {{-- 右側：キャスト情報 --}}
                @if ($gallery->item)
                <div class="lg:w-80 flex-shrink-0">
                    <div class="bg-slate-50 rounded-xl p-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0">
                                <a href="{{ route('items.show', $gallery->item->id) }}" ><img src="{{ $gallery->item->th_flename() }}" alt="{{ $gallery->item->item_name }}" class="w-full h-full object-cover"></a>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-slate-800 mb-2"><a href="{{ route('items.show', $gallery->item->id) }}" >{{ $gallery->item->item_name }}</a></h4>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @php
        $galleryVideos = $gallery->images->where('type', 'video');
        $galleryPhotos = $gallery->images->where('type', '!=', 'video');
    @endphp

    {{-- 動画一覧 --}}
    @if ($galleryVideos->isNotEmpty())
        <div class="mb-8">
            @foreach ($galleryVideos as $video)
            <div class="mb-4 rounded-xl overflow-hidden shadow-md bg-black">
                <video
                    src="{{ Storage::disk('public')->url($video->filename) }}"
                    controls
                    playsinline
                    style="width:100%; max-height:480px; display:block;"
                ></video>
            </div>
            @endforeach
        </div>
    @endif

    {{-- ギャラリー画像一覧 --}}
    @if ($galleryPhotos->isNotEmpty())
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
            @foreach ($galleryPhotos->values() as $index => $image)
                <div class="group cursor-pointer" onclick="openGalleryModal({{ $index }})">
                    <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border border-slate-200/50">
                        <div class="aspect-square overflow-hidden relative">
                            <img src="{{ Storage::url('gallery/' . $image->filename) }}" alt="画像" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center">
                                <i class="fas fa-expand text-white opacity-0 group-hover:opacity-100 text-2xl transition-opacity duration-300"></i>
                            </div>
                        </div>

                        @if ($image->caption)
                            <div class="p-3">
                                <p class="text-sm text-slate-700 line-clamp-2">{{ $image->caption }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @elseif ($galleryVideos->isEmpty())
        <div class="text-center py-12">
            <i class="fas fa-images text-6xl text-slate-300 mb-4"></i>
            <p class="text-slate-500">画像はまだ登録されていません。</p>
        </div>
    @endif
</section>

{{-- ギャラリーモーダル --}}
<div id="galleryModal" class="fixed inset-0 bg-black/90 z-[9999] hidden gallery-modal-overlay" data-image-count="{{ $galleryPhotos->count() }}">
    <div class="h-full flex flex-col relative gallery-modal-content max-h-screen" style="top: 0; left: 0; right: 0; bottom: 0;">
        {{-- モーダルヘッダー --}}
        <div class="flex justify-between items-center p-4 bg-black/70 border-b border-white/20">
            <div class="flex items-center gap-3">
                <h3 class="text-white font-bold text-lg">{{ $gallery->title }}</h3>
            </div>
            <button onclick="closeGalleryModal()" class="hover:text-red-400 text-3xl font-bold bg-white/10 hover:bg-white/20 rounded-full w-10 h-10 flex items-center justify-center transition-all duration-200">
                ×
            </button>
        </div>

                {{-- スライダーコンテナ --}}
        <div class="flex-1 relative pb-16 sm:pb-24" style="height: calc(100vh - 120px);">
            <div class="swiper gallery-swiper h-full">
                <div class="swiper-wrapper">
                    @foreach ($galleryPhotos as $image)
                    <div class="swiper-slide flex items-center justify-center px-4 py-2 sm:py-8">
                        <div class="max-w-4xl w-full">
                            <img src="{{ Storage::url('gallery/' . $image->filename) }}" alt="画像" class="w-full h-auto max-h-[calc(100vh-200px)] object-contain rounded-lg mx-auto">
                            @if ($image->caption)
                                <div class="mt-6 text-center">
                                    <p class="text-white bg-black/60 inline-block px-4 py-2 rounded-lg max-w-2xl">{{ $image->caption }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- ナビゲーションボタン --}}
                <div class="swiper-button-next !text-white !top-1/2"></div>
                <div class="swiper-button-prev !text-white !top-1/2"></div>
            </div>
        </div>

        {{-- ページネーションエリア --}}
        <div class="absolute bottom-8 sm:bottom-16 left-1/2 transform -translate-x-1/2 z-10 w-full flex justify-center">
            <div class="swiper-pagination gallery-pagination"></div>
        </div>

        {{-- 下部閉じるボタン --}}
        <div class="absolute bottom-2 sm:bottom-4 left-1/2 transform -translate-x-1/2 z-10">
            <button onclick="closeGalleryModal()" class="gallery-close-bottom-btn">
                <i class="fas fa-times mr-2"></i>
                ギャラリーを閉じる
            </button>
        </div>
    </div>
</div>

<style>
/* ギャラリーページネーションのカスタムスタイル */
.gallery-pagination .swiper-pagination-bullet {
    background: rgba(156, 163, 175, 0.7) !important; /* 薄めのグレー */
    opacity: 1 !important;
    width: 12px !important;
    height: 12px !important;
    margin: 0 6px !important;
}

.gallery-pagination .swiper-pagination-bullet-active {
    background: #ec4899 !important; /* ピンク系 */
    transform: scale(1.2) !important;
}

/* スマホサイズ対応 */
@media (max-width: 768px) {
    #galleryModal {
        padding: 0;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100vw;
        height: 100vh;
    }

    #galleryModal .gallery-modal-content {
        max-height: 100vh;
        margin: 0;
        border-radius: 0;
        height: 100vh;
        width: 100vw;
    }

    #galleryModal .p-4 {
        padding: 0.75rem;
    }

    #galleryModal .text-lg {
        font-size: 1rem;
    }

    #galleryModal .text-sm {
        font-size: 0.75rem;
    }

    #galleryModal .px-4 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    #galleryModal .py-8 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    #galleryModal .py-2 {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    #galleryModal .bottom-8 {
        bottom: 2rem;
    }

    #galleryModal .bottom-2 {
        bottom: 0.5rem;
    }

    #galleryModal .pb-16 {
        padding-bottom: 4rem;
    }

    #galleryModal .flex-1 {
        height: calc(100vh - 120px);
    }

    #galleryModal .max-h-\[calc\(100vh-200px\)\] {
        max-height: calc(100vh - 200px);
    }
}

/* ギャラリーモーダル×ボタン */
.gallery-close-btn {
    position: relative;
    width: 3rem;
    height: 3rem;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.gallery-close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.8);
    transform: scale(1.1);
    color: white;
}

.gallery-close-btn span {
    line-height: 1;
    margin-top: -2px;
}

/* 下部閉じるボタン */
.gallery-close-bottom-btn {
    background: rgba(0, 0, 0, 0.8);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 2rem;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
}

.gallery-close-bottom-btn:hover {
    background: rgba(239, 68, 68, 0.8);
    border-color: rgba(255, 255, 255, 0.8);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
}
</style>

@endsection
