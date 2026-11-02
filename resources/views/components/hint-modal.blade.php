@props(['title' => 'ヒント', 'id' => null])

@php
    $modalId   = $id ?? 'hint-modal-' . uniqid();
    $overlayId = $modalId . '-overlay';
@endphp

{{-- CSS・グローバルJS は1回だけ出力 --}}
@once
<style>
/* ===== hint-modal ===== */
.hint-icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1.5rem;
    height: 1.5rem;
    background: #4bbee9;
    color: #fff;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 0.78rem;
    line-height: 1;
    transition: background 0.18s, transform 0.18s;
    vertical-align: middle;
    flex-shrink: 0;
}
.hint-icon-btn:hover {
    background: #2aaad4;
    transform: scale(1.12);
}

.hint-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(50, 80, 110, 0.42);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9500;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s, visibility 0.3s;
}
.hint-modal-overlay.is-open {
    opacity: 1;
    visibility: visible;
}

.hint-modal-panel {
    position: relative;
    background: linear-gradient(160deg, #ffffff 0%, #f2fbfe 60%, #e4f7fc 100%);
    border: 2px solid #4bbee9;
    border-radius: 12px;
    padding: 1.8rem 2rem;
    max-width: 480px;
    width: 92%;
    color: #1a3a45;
    box-shadow:
        0 8px 36px rgba(75, 190, 233, 0.22),
        inset 0 0 24px rgba(255, 255, 255, 0.55);
    clip-path: inset(50% 0 50% 0 round 12px);
    transition: clip-path 0.42s cubic-bezier(0.16, 1, 0.3, 1);
}
.hint-modal-overlay.is-open .hint-modal-panel {
    clip-path: inset(0% 0 0% 0 round 12px);
}
.hint-modal-panel::after {
    content: '';
    position: absolute;
    top: 0; left: 12%; right: 12%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #4bbee9, transparent);
    border-radius: 2px;
}

.hint-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding-bottom: 0.7rem;
    border-bottom: 1px solid rgba(75, 190, 233, 0.3);
}
.hint-modal-title {
    font-size: 1rem;
    font-weight: bold;
    color: #4bbee9;
    letter-spacing: 0.05em;
    margin: 0;
}
.hint-modal-close {
    background: none;
    border: 1px solid rgba(75, 190, 233, 0.5);
    color: #4bbee9;
    width: 26px;
    height: 26px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-left: 0.8rem;
    transition: background 0.18s, color 0.18s;
}
.hint-modal-close:hover {
    background: #4bbee9;
    color: #fff;
}
.hint-modal-body {
    font-size: 0.93rem;
    line-height: 1.85;
    color: #2a4a55;
    opacity: 0;
    transform: translateY(8px);
    transition: opacity 0.3s 0.25s, transform 0.3s 0.25s;
}
.hint-modal-overlay.is-open .hint-modal-body {
    opacity: 1;
    transform: translateY(0);
}
</style>

<script>
(function () {
    if (window.__hintModal) return;
    window.__hintModal = {
        open: function (id) {
            var el = document.getElementById(id);
            if (!el) return;
            el.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        },
        close: function (id) {
            var el = document.getElementById(id);
            if (el) el.classList.remove('is-open');
            if (!document.querySelector('.hint-modal-overlay.is-open')) {
                document.body.style.overflow = '';
            }
        }
    };
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        document.querySelectorAll('.hint-modal-overlay.is-open').forEach(function (el) {
            el.classList.remove('is-open');
        });
        document.body.style.overflow = '';
    });
})();
</script>
@endonce

{{-- ヒントアイコンボタン --}}
<button
    type="button"
    class="hint-icon-btn"
    onclick="window.__hintModal.open('{{ $overlayId }}')"
    aria-label="ヒントを表示"
    aria-haspopup="dialog"
><i class="fas fa-question"></i></button>

{{-- モーダル本体 --}}
<div
    class="hint-modal-overlay"
    id="{{ $overlayId }}"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $overlayId }}-title"
    onclick="if(event.target===this)window.__hintModal.close('{{ $overlayId }}')"
>
    <div class="hint-modal-panel">
        <div class="hint-modal-header">
            <p class="hint-modal-title" id="{{ $overlayId }}-title">
                <i class="fas fa-lightbulb" style="margin-right:0.4em;"></i>{{ $title }}
            </p>
            <button
                type="button"
                class="hint-modal-close"
                onclick="window.__hintModal.close('{{ $overlayId }}')"
                aria-label="閉じる"
            >&times;</button>
        </div>
        <div class="hint-modal-body">
            {{ $slot }}
        </div>
    </div>
</div>
