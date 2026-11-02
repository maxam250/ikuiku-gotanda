@extends('layouts.app')
@section('breadcrumb_label', 'イベント情報')
@section('content')
<div class="titleArea">
    <h2>EVENTS</h2>
    <div class="title">イベント</div>
</div>

<section id="eventArea" class="event-section">
    <div class="event-container">
        @forelse ($events as $event)
        <div class="event-card">
            <div class="event-card-image">
                @if ($event->filename)
                    <img src="{{ Storage::url('events/'.$event->filename) }}" alt="{{ $event->title }}" loading="lazy" class="event-img-zoomable">
                @else
                    <img src="{{ asset('images/dmy.jpg') }}" alt="{{ $event->title }}" loading="lazy" class="event-img-zoomable">
                @endif
            </div>
            <div class="event-card-body">
                <h3 class="event-card-title">{{ $event->title }}</h3>
                <div class="event-card-text-wrap is-collapsed">
                    <div class="event-card-text">{!! $event->body !!}</div>
                </div>
                <button type="button" class="event-card-toggle" aria-expanded="false">
                    <span class="toggle-label">続きを見る</span>
                    <i class="toggle-arrow">&#9660;</i>
                </button>
                @if ($event->link)
                    <div class="event-card-link">
                        <a href="{{ $event->link }}" target="_blank" rel="noopener">詳細・予約はこちら</a>
                    </div>
                @endif
            </div>
        </div>
        @empty
        <p class="event-empty">現在公開中のイベントはありません。</p>
        @endforelse
    </div>
</section>

{{-- 画像モーダル --}}
<div id="eventImgModal" class="event-modal" role="dialog" aria-modal="true">
    <button class="event-modal-close" id="eventModalClose" aria-label="閉じる">&times;</button>
    <div class="event-modal-backdrop" id="eventModalBackdrop"></div>
    <div class="event-modal-content">
        <img id="eventModalImg" src="" alt="">
    </div>
</div>

<script>
(function () {
    /* ---- 画像モーダル ---- */
    const modal    = document.getElementById('eventImgModal');
    const modalImg = document.getElementById('eventModalImg');
    const closeBtn = document.getElementById('eventModalClose');
    const backdrop = document.getElementById('eventModalBackdrop');

    function openModal(src, alt) {
        modalImg.src = src;
        modalImg.alt = alt;
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
        modalImg.src = '';
    }

    document.querySelectorAll('.event-img-zoomable').forEach(function (img) {
        img.addEventListener('click', function () { openModal(this.src, this.alt); });
    });
    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

    /* ---- テキストアコーディオン ---- */
    document.querySelectorAll('.event-card-toggle').forEach(function (btn) {
        const wrap  = btn.previousElementSibling; // .event-card-text-wrap
        const label = btn.querySelector('.toggle-label');

        // テキストが短い場合はボタンを非表示
        if (wrap.scrollHeight <= wrap.clientHeight + 2) {
            wrap.classList.remove('is-collapsed');
            btn.style.display = 'none';
            return;
        }

        btn.addEventListener('click', function () {
            const isOpen = !wrap.classList.contains('is-collapsed');
            if (isOpen) {
                // 閉じる：現在の高さを明示してからクラス付与（アニメのため）
                wrap.style.maxHeight = wrap.scrollHeight + 'px';
                requestAnimationFrame(function () {
                    wrap.style.maxHeight = '';
                    wrap.classList.add('is-collapsed');
                });
                btn.classList.remove('is-open');
                btn.setAttribute('aria-expanded', 'false');
                label.textContent = '続きを見る';
            } else {
                // 開く
                wrap.classList.remove('is-collapsed');
                wrap.style.maxHeight = wrap.scrollHeight + 'px';
                wrap.addEventListener('transitionend', function handler() {
                    wrap.style.maxHeight = '';
                    wrap.removeEventListener('transitionend', handler);
                });
                btn.classList.add('is-open');
                btn.setAttribute('aria-expanded', 'true');
                label.textContent = '閉じる';
            }
        });
    });
})();
</script>

@endsection
