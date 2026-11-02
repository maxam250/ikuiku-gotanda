/**
 * Bootstrap 不使用の確認ダイアログ用モーダル。
 * data-af-modal-open="#要素id" で開く / data-af-modal-close で閉じる。
 */
(function () {
    let activeOverlay = null;
    var lastFocused = null;

    function qs(sel) {
        try {
            return document.querySelector(sel);
        } catch (e) {
            return null;
        }
    }

    function openModal(target) {
        if (!target || typeof target !== 'string') {
            return;
        }
        var el = qs(target.trim());
        if (!el || !el.classList.contains('af-modal-overlay')) {
            return;
        }
        if (activeOverlay && activeOverlay !== el) {
            closeModal();
        }
        lastFocused = document.activeElement;
        activeOverlay = el;
        el.classList.add('is-open');
        el.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        var focusEl = el.querySelector(
            'button:not([disabled]), [href]:not([tabindex="-1"]), textarea:not([disabled]), input:not([type="hidden"]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
        );
        if (focusEl && typeof focusEl.focus === 'function') {
            try {
                focusEl.focus();
            } catch (e) {}
        }
    }

    function closeModal() {
        if (!activeOverlay) {
            return;
        }
        activeOverlay.classList.remove('is-open');
        activeOverlay.setAttribute('aria-hidden', 'true');
        activeOverlay = null;
        document.body.style.overflow = '';
        if (lastFocused && typeof lastFocused.focus === 'function') {
            try {
                lastFocused.focus();
            } catch (e) {}
        }
        lastFocused = null;
    }

    document.addEventListener('click', function (e) {
        var opener = e.target.closest('[data-af-modal-open]');
        if (opener) {
            var tid = opener.getAttribute('data-af-modal-open');
            if (tid) {
                if (opener.matches('a')) {
                    e.preventDefault();
                }
                openModal(tid);
            }
            return;
        }
        if (e.target.closest('[data-af-modal-close]')) {
            e.preventDefault();
            closeModal();
            return;
        }
        if (
            activeOverlay &&
            e.target === activeOverlay
        ) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape' || !activeOverlay) {
            return;
        }
        e.preventDefault();
        closeModal();
    });

    window.AfModal = {
        open: openModal,
        close: closeModal,
    };
})();
