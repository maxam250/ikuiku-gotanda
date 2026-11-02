@extends('layouts.app')

@section('breadcrumb_label', $newsItem->title)

@section('content')

<div class="titleArea">
<h2>NEWS</h2>
<div class="title">お知らせ詳細</div>
</div>

<section id="newsDetailArea">
    <div class="news-detail-container">


        <article class="news-detail">
            <header class="news-detail-header">
                <div class="news-detail-date">
                    {{ $newsItem->created_at->format('Y年m月d日') }}
                </div>
                <h1 class="news-detail-title">{{ $newsItem->title }}</h1>
            </header>

            @if ( $newsItem->filename )
            <div class="news-detail-image">
                @if ( $newsItem->link )
                    {{-- リンクあり → そのままジャンプ --}}
                    <a href="{{ $newsItem->link }}" target="_blank" rel="noopener noreferrer" class="news-image-link">
                        <img src="{{ Storage::url('news/'.$newsItem->filename) }}" alt="{{ $newsItem->title }}">
                    </a>
                @else
                    {{-- リンクなし → モーダルで拡大 --}}
                    <img src="{{ Storage::url('news/'.$newsItem->filename) }}"
                         alt="{{ $newsItem->title }}"
                         class="news-modal-trigger"
                         data-src="{{ Storage::url('news/'.$newsItem->filename) }}">
                @endif
            </div>
            @endif

            @if ( $newsItem->filename && !$newsItem->link )
            {{-- 画像モーダル --}}
            <div id="newsImageModal" class="news-modal" role="dialog" aria-modal="true" aria-label="画像拡大表示" hidden>
                <div class="news-modal-backdrop" id="newsModalBackdrop"></div>
                <div class="news-modal-inner">
                    <button class="news-modal-close" id="newsModalClose" aria-label="閉じる">&times;</button>
                    <img src="" id="newsModalImg" alt="{{ $newsItem->title }}">
                </div>
            </div>

            <script>
            (function () {
                const trigger  = document.querySelector('.news-modal-trigger');
                const modal    = document.getElementById('newsImageModal');
                const modalImg = document.getElementById('newsModalImg');
                const backdrop = document.getElementById('newsModalBackdrop');
                const closeBtn = document.getElementById('newsModalClose');

                if (!trigger || !modal) return;

                function openModal() {
                    modalImg.src = trigger.dataset.src;
                    modal.hidden = false;
                    document.body.style.overflow = 'hidden';
                }

                function closeModal() {
                    modal.hidden = true;
                    document.body.style.overflow = '';
                }

                trigger.addEventListener('click', openModal);
                backdrop.addEventListener('click', closeModal);
                closeBtn.addEventListener('click', closeModal);
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && !modal.hidden) closeModal();
                });
            })();
            </script>
            @endif

            <div class="news-detail-content">
                {!! $newsItem->body !!}
            </div>

            @if ( $newsItem->link )
            <div class="news-detail-external">
                <a href="{{ $newsItem->link }}" target="_blank" class="external-link-detail">
                    <i class="fas fa-external-link-alt"></i>
                    外部リンクを開く
                </a>
            </div>
            @endif
        </article>

        <!-- 戻るボタン -->
        <div class="news-detail-actions">
            <x-back-to-list href="{{ route('news.index') }}" text="ニュース一覧に戻る" />
        </div>
    </div>
</section>

@endsection
