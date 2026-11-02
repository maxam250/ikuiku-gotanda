<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaRule implements ValidationRule
{
    /**
     * reCAPTCHA を検証するか（フロントの <x-recaptcha> 表示判定にも使用）
     *
     * - RECAPTCHA_ENABLED=false なら常にスキップ
     * - APP_ENV=local ならスキップ
     * - RECAPTCHA_SKIP_HOSTS に一致する Host（例: guide.local）ならスキップ
     */
    public static function shouldVerify(): bool
    {
        if (! (bool) config('services.recaptcha.enabled', true)) {
            return false;
        }

        if (app()->environment('local')) {
            return false;
        }

        $hosts = config('services.recaptcha.skip_hosts', []);
        if (is_array($hosts) && $hosts !== []) {
            $host = request()->header('X-Forwarded-Host') ?: request()->getHost();
            if (in_array($host, $hosts, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! static::shouldVerify()) {
            return;
        }

        if (empty($value)) {
            Log::warning('Recaptcha validation failed: empty token', ['ip' => request()->ip()]);
            $fail('送信を完了できませんでした。時間をおいて再度お試しください。');

            return;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        $body = $response->json();

        if (! $body['success']) {
            Log::warning('Recaptcha validation failed: siteverify', [
                'ip' => request()->ip(),
                'error-codes' => $body['error-codes'] ?? null,
            ]);
            $fail('送信を完了できませんでした。時間をおいて再度お試しください。');

            return;
        }

        // reCAPTCHA v3のスコアチェック
        if (isset($body['score'])) {
            $threshold = config('services.recaptcha.score_threshold', 0.5);
            if ($body['score'] < $threshold) {
                Log::warning('Recaptcha validation failed: low score', [
                    'ip' => request()->ip(),
                    'score' => $body['score'],
                    'threshold' => $threshold,
                ]);
                $fail('送信を完了できませんでした。時間をおいて再度お試しください。');

                return;
            }
        }
    }
}
