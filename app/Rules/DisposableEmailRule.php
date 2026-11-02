<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DisposableEmailRule implements ValidationRule
{
    /**
     * 使い捨てメールアドレスのドメインリスト
     */
    private array $disposableDomains = [
        '10minutemail.com',
        'guerrillamail.com',
        'mailinator.com',
        'temp-mail.org',
        'throwaway.email',
        'yopmail.com',
        'maildrop.cc',
        'tempmail.net',
        'getnada.com',
        '33mail.com',
        'dispostable.com',
        'spamgourmet.com',
        'trashmail.com',
        'emailondeck.com',
        'fakeinbox.com',
        'sharklasers.com',
        'grr.la',
        'guerrillamail.info',
        'guerrillamail.net',
        'guerrillamail.org',
        'guerrillamailblock.com',
        'pokemail.net',
        'spam4.me',
        'tempail.com',
        'tempemail.com',
        'tempinbox.com',
        'tempmailaddress.com',
        '20minutemail.com',
        'mohmal.com',
        'mytemp.email',
    ];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $domain = strtolower(substr(strrchr($value, '@'), 1));

        if (in_array($domain, $this->disposableDomains)) {
            $fail('使い捨てメールアドレスは使用できません。');
            return;
        }

        // 一般的な使い捨てメールのパターンをチェック
        $suspiciousPatterns = [
            '/^temp/',
            '/^throw/',
            '/^fake/',
            '/^spam/',
            '/mail$/',
            '/^guerrilla/',
            '/^10min/',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $domain)) {
                $fail('使い捨てメールアドレスの可能性があります。正規のメールアドレスを使用してください。');
                return;
            }
        }
    }
}
