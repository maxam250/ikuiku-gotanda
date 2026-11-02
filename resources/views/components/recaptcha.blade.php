@props([
    'action' => 'register',
    /** 対象フォームの id（未指定時は document 内の最初の form） */
    'formId' => null,
    /** クライアントエラー表示用要素の id */
    'errorContainerId' => 'recaptcha-client-error',
])

@php
    $siteKey = config('services.recaptcha.site_key');
    $recaptchaActive = filled($siteKey) && \App\Rules\RecaptchaRule::shouldVerify();
@endphp

@if($recaptchaActive)
<script src="https://www.google.com/recaptcha/api.js?render={{ $siteKey }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = @json($formId)
        ? document.getElementById(@json($formId))
        : document.querySelector('form');

    if (!form) {
        return;
    }

    const errorEl = document.getElementById(@json($errorContainerId));
    const siteKey = @json($siteKey);
    const action = @json($action);

    function showClientError(message) {
        if (!errorEl) {
            window.alert(message);
            return;
        }
        errorEl.textContent = message;
        errorEl.hidden = false;
        errorEl.style.display = 'block';
        errorEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function clearClientError() {
        if (!errorEl) {
            return;
        }
        errorEl.textContent = '';
        errorEl.hidden = true;
        errorEl.style.display = 'none';
    }

    form.addEventListener('submit', function (e) {
        if (typeof grecaptcha === 'undefined') {
            e.preventDefault();
            console.warn('reCAPTCHA script unavailable');
            showClientError('送信を完了できませんでした。ページを再読み込みして再度お試しください。');
            return;
        }

        e.preventDefault();
        clearClientError();

        grecaptcha.ready(function () {
            grecaptcha.execute(siteKey, { action: action })
                .then(function (token) {
                    form.querySelectorAll('input[name="g-recaptcha-response"]').forEach(function (el) {
                        el.remove();
                    });
                    const tokenField = document.createElement('input');
                    tokenField.type = 'hidden';
                    tokenField.name = 'g-recaptcha-response';
                    tokenField.value = token;
                    form.appendChild(tokenField);
                    form.submit();
                })
                .catch(function () {
                    console.warn('reCAPTCHA execute failed');
                    showClientError('送信を完了できませんでした。ページを再読み込みして再度お試しください。');
                });
        });
    });
});
</script>
@endif
