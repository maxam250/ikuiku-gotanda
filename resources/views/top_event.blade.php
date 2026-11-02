@if($events->isNotEmpty())
@php
    $eventList = $events->reverse()->values();
@endphp

<section class="top-event-section">

    {{-- メイン画像 + 縦サムネイル --}}
    <div class="tes-wrap">

        {{-- 左：メイン画像 (900×415) --}}
        <div class="tes-main">
            <a class="tes-main-link" href="#" target="_blank" rel="noopener noreferrer" id="tesMainLink">
                <img class="tes-main-img" id="tesMainImg"
                     src="{{ $eventList[0]->filename ? Storage::url('events/'.$eventList[0]->filename) : asset('images/dmy.jpg') }}"
                     alt="{{ $eventList[0]->title ?? '' }}">
            </a>
        </div>

        {{-- 右：縦サムネイルスライダー --}}
        <div class="tes-thumbs-wrap">
            <button class="tes-thumb-btn tes-thumb-up" id="tesThumbUp" aria-label="上へ">
                <i class="fas fa-chevron-up"></i>
            </button>

            <div class="tes-thumbs-viewport" id="tesThumbsViewport">
                <ul class="tes-thumbs-list" id="tesThumbsList">
                    @foreach($eventList as $i => $event)
                    <li class="tes-thumb-item {{ $i === 0 ? 'is-active' : '' }}"
                        data-index="{{ $i }}"
                        data-src="{{ $event->filename ? Storage::url('events/'.$event->filename) : asset('images/dmy.jpg') }}"
                        data-title="{{ $event->title ?? '' }}"
                        data-link="{{ $event->link ?? '' }}"
                        data-modal="{{ ((int)$event->position === 2 && empty($event->link)) ? '1' : '0' }}"
                        aria-label="{{ $event->title ?? 'イベント'.($i+1) }}">
                        <img src="{{ $event->filename ? Storage::url('events/'.$event->filename) : asset('images/dmy.jpg') }}"
                             alt="{{ $event->title ?? '' }}"
                             loading="lazy">
                    </li>
                    @endforeach
                </ul>
            </div>

            <button class="tes-thumb-btn tes-thumb-down" id="tesThumbDown" aria-label="下へ">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
    </div>

    {{-- イベント画像拡大モーダル --}}
    <div class="event-image-modal" id="tesModal" role="dialog" aria-modal="true" hidden>
        <div class="event-image-modal__backdrop" id="tesModalBackdrop"></div>
        <div class="event-image-modal__panel">
            <button type="button" class="event-image-modal__close" id="tesModalClose" aria-label="閉じる">&times;</button>
            <img src="" alt="" class="event-image-modal__img" id="tesModalImg">
        </div>
    </div>

</section>

<style>
/* ── イベントスライダー ─────────────────────────── */
.top-event-section {
    width: 100%;
    max-width: none;
    margin: 0 auto 1.5rem;
    padding: 1.25rem 0 2.5rem;
}

.tes-wrap {
    display: flex;
    gap: 8px;
    align-items: flex-start;
}

/* メイン画像エリア */
.tes-main {
    flex: 1 1 auto;
    max-width: none;
    min-width: 0;
    background: none;
}
.tes-main-link {
    display: block;
    width: 100%;
}
.tes-main-img {
    display: block;
    width: 100%;
    height: 415px;
    object-fit: contain;
    transition: opacity 0.25s ease;
}
.tes-main-img.is-fading { opacity: 0; }

/* サムネイルエリア */
.tes-thumbs-wrap {
    flex: 1 1 0;
    min-width: 0;
    max-width: 130px;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 4px;
    height: 415px;
}

/* 上下ボタン */
.tes-thumb-btn {
    flex-shrink: 0;
    background: #efefef;
    border: none;
    color: #ff75a3;
    padding: 5px 0;
    cursor: pointer;
    border-radius: 4px;
    font-size: 0.7rem;
    text-align: center;
    transition: background 0.2s, color 0.2s;
    line-height: 1;
}
.tes-thumb-btn:hover {
    background: #e6e6e6;
    color: #ff75a3;
}
.tes-thumb-btn:disabled { opacity: 0.25; pointer-events: none; }

/* サムネイルビューポート */
.tes-thumbs-viewport {
    flex: 1 1 0;
    overflow: hidden;
    min-height: 0;
}

/* サムネイルリスト（縦並び、translateYでスクロール） */
.tes-thumbs-list {
    list-style: none;
    margin: 0; padding: 0;
    display: flex;
    flex-direction: column;
    gap: 5px;
    transition: transform 0.3s ease;
}

/* 各サムネイル */
.tes-thumb-item {
    flex-shrink: 0;
    cursor: pointer;
    opacity: 0.55;
    border: 2px solid transparent;
    border-radius: 3px;
    overflow: hidden;
    transition: opacity 0.2s, border-color 0.2s;
}
.tes-thumb-item img {
    display: block;
    width: 100%;
    aspect-ratio: 16 / 9;
    object-fit: cover;
}
.tes-thumb-item:hover { opacity: 0.85; }
.tes-thumb-item.is-active {
    opacity: 1;
    border-color: var(--ui-accent);
}

/* レスポンシブ — モバイルは横スライダー */
@media (max-width: 1070px) {
    .tes-wrap { flex-direction: column; gap: 6px; }
    .tes-main { flex: none; width: 100%; max-width: 100%; background: none; }
    .tes-main-img { height: auto; aspect-ratio: 900 / 415; object-fit: contain; }

    /* サムネイルラッパー：横並び（左ボタン｜ビューポート｜右ボタン） */
    .tes-thumbs-wrap {
        flex-direction: row;
        align-items: center;
        max-width: 100%;
        width: 100%;
        height: auto;
    }

    /* ビューポートは JS の translateX で制御するため overflow: hidden */
    .tes-thumbs-viewport {
        overflow: hidden;
        flex: 1;
        min-width: 0;
    }

    /* リストを横並び */
    .tes-thumbs-list { flex-direction: row; }

    /* 各サムネイル幅（3枚分が見えるくらい） */
    .tes-thumb-item { width: 110px; flex-shrink: 0; }

    /* ボタン：縦長になるよう調整 */
    .tes-thumb-btn {
        padding: 0;
        width: 26px;
        min-width: 26px;
        height: 62px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* ↑↓アイコン → ←→アイコンに上書き */
    .tes-thumb-up   .fa-chevron-up::before   { content: '\f053'; } /* fa-chevron-left  */
    .tes-thumb-down .fa-chevron-down::before { content: '\f054'; } /* fa-chevron-right */
}
</style>

<script>
(function () {
    var items   = Array.from(document.querySelectorAll('.tes-thumb-item'));
    var mainImg = document.getElementById('tesMainImg');
    var mainLink= document.getElementById('tesMainLink');
    var list    = document.getElementById('tesThumbsList');
    var viewport= document.getElementById('tesThumbsViewport');
    var btnUp   = document.getElementById('tesThumbUp');
    var btnDown = document.getElementById('tesThumbDown');
    var modal   = document.getElementById('tesModal');
    var modalImg= document.getElementById('tesModalImg');
    var modalClose = document.getElementById('tesModalClose');
    var modalBackdrop = document.getElementById('tesModalBackdrop');

    if (!items.length || !mainImg) return;

    var current    = 0;
    var scrollPos  = 0;
    var ITEM_GAP   = 5;
    var autoTimer  = null;
    var wasMobile  = window.innerWidth <= 1070;

    /* ── モバイル判定 ── */
    function isMobile() { return window.innerWidth <= 1070; }

    /* ── ボタン状態更新 ── */
    function updateButtons() {
        var maxScroll;
        if (isMobile()) {
            var itemW = items[0] ? items[0].offsetWidth + ITEM_GAP : 0;
            maxScroll = Math.max(0, items.length * itemW - viewport.offsetWidth);
        } else {
            var itemH = items[0] ? items[0].offsetHeight + ITEM_GAP : 0;
            maxScroll = Math.max(0, items.length * itemH - viewport.offsetHeight);
        }
        btnUp.disabled   = scrollPos <= 0;
        btnDown.disabled = scrollPos >= maxScroll - 1;
    }

    /* ── サムネイルスクロール ── */
    function scrollThumbs(dir) {
        if (isMobile()) {
            var itemW     = items[0] ? items[0].offsetWidth + ITEM_GAP : 0;
            var maxScroll = Math.max(0, items.length * itemW - viewport.offsetWidth);
            scrollPos = Math.max(0, Math.min(maxScroll, scrollPos + dir * itemW));
            list.style.transform = 'translateX(-' + scrollPos + 'px)';
        } else {
            var itemH     = items[0] ? items[0].offsetHeight + ITEM_GAP : 0;
            var maxScroll = Math.max(0, items.length * itemH - viewport.offsetHeight);
            scrollPos = Math.max(0, Math.min(maxScroll, scrollPos + dir * itemH));
            list.style.transform = 'translateY(-' + scrollPos + 'px)';
        }
        updateButtons();
    }

    /* ── メイン画像切り替え ── */
    function goTo(index) {
        if (index === current && mainImg.src) return;
        current = index;

        var item = items[index];
        var src  = item.dataset.src;
        var link = item.dataset.link;
        var isModal = item.dataset.modal === '1';

        /* フェード切り替え */
        mainImg.classList.add('is-fading');
        setTimeout(function () {
            mainImg.src = src;
            mainImg.alt = item.dataset.title;
            mainImg.classList.remove('is-fading');
        }, 250);

        /* リンク設定 */
        if (isModal) {
            mainLink.href = '#';
            mainLink.removeAttribute('target');
            mainLink.dataset.modal = '1';
            mainLink.dataset.modalSrc = src;
        } else if (link) {
            mainLink.href = link;
            mainLink.target = '_blank';
            delete mainLink.dataset.modal;
        } else {
            mainLink.href = '#';
            mainLink.removeAttribute('target');
            delete mainLink.dataset.modal;
        }

        /* アクティブ状態 */
        items.forEach(function (el, i) {
            el.classList.toggle('is-active', i === index);
        });

        /* アクティブなサムネイルが見えるようスクロール調整 */
        if (items[0]) {
            if (isMobile()) {
                var itemW    = items[0].offsetWidth + ITEM_GAP;
                var activeL  = index * itemW;
                var viewW    = viewport.offsetWidth;
                if (activeL < scrollPos) {
                    scrollPos = activeL;
                    list.style.transform = 'translateX(-' + scrollPos + 'px)';
                } else if (activeL + items[0].offsetWidth > scrollPos + viewW) {
                    scrollPos = activeL + items[0].offsetWidth - viewW;
                    list.style.transform = 'translateX(-' + scrollPos + 'px)';
                }
            } else {
                var itemH    = items[0].offsetHeight + ITEM_GAP;
                var activeTop = index * itemH;
                var viewH    = viewport.offsetHeight;
                if (activeTop < scrollPos) {
                    scrollPos = activeTop;
                    list.style.transform = 'translateY(-' + scrollPos + 'px)';
                } else if (activeTop + items[0].offsetHeight > scrollPos + viewH) {
                    scrollPos = activeTop + items[0].offsetHeight - viewH;
                    list.style.transform = 'translateY(-' + scrollPos + 'px)';
                }
            }
            updateButtons();
        }
    }

    /* ── オートプレイ ── */
    function startAuto() {
        autoTimer = setInterval(function () {
            goTo((current + 1) % items.length);
        }, 5000);
    }
    function resetAuto() { clearInterval(autoTimer); startAuto(); }

    /* ── イベント登録 ── */
    items.forEach(function (item, i) {
        item.addEventListener('click', function () { goTo(i); resetAuto(); });
    });

    btnUp.addEventListener('click', function () { scrollThumbs(-1); resetAuto(); });
    btnDown.addEventListener('click', function () { scrollThumbs(1); resetAuto(); });

    /* ── スワイプ（モバイル横スライド用） ── */
    var touchStartX = 0;
    var touchStartY = 0;
    viewport.addEventListener('touchstart', function (e) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    }, { passive: true });
    viewport.addEventListener('touchend', function (e) {
        if (!isMobile()) return;
        var dx = touchStartX - e.changedTouches[0].clientX;
        var dy = Math.abs(touchStartY - e.changedTouches[0].clientY);
        if (Math.abs(dx) > 40 && Math.abs(dx) > dy) {
            scrollThumbs(dx > 0 ? 1 : -1);
            resetAuto();
        }
    }, { passive: true });

    /* ── リサイズ時にモード切替・スクロール位置リセット ── */
    window.addEventListener('resize', function () {
        var nowMobile = isMobile();
        if (nowMobile !== wasMobile) {
            scrollPos = 0;
            list.style.transform = nowMobile ? 'translateX(0)' : 'translateY(0)';
            wasMobile = nowMobile;
            updateButtons();
        }
    });

    /* メイン画像クリック（モーダル） */
    mainLink.addEventListener('click', function (e) {
        if (this.dataset.modal === '1') {
            e.preventDefault();
            modalImg.src = this.dataset.modalSrc;
            modal.hidden = false;
            document.body.style.overflow = 'hidden';
        }
    });

    /* モーダル閉じる */
    function closeModal() { modal.hidden = true; document.body.style.overflow = ''; }
    modalClose.addEventListener('click', closeModal);
    modalBackdrop.addEventListener('click', closeModal);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

    /* 初期化 */
    updateButtons();
    goTo(0);
    startAuto();
})();
</script>
@endif
