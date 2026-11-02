<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Member;
use App\Models\MemberRegistration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class UniqueEncryptedEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        Log::info('UniqueEncryptedEmail validation started', ['email' => $value]);

        // 既存会員のメールアドレスとの重複チェック
        $members = Member::all();
        Log::info('Total members to check', ['count' => $members->count()]);

        foreach ($members as $member) {
            try {
                $decryptedEmail = Crypt::decryptString($member->email);
                Log::info('Checking member email', [
                    'member_id' => $member->id,
                    'decrypted_email' => $decryptedEmail,
                    'input_email' => $value,
                    'match' => $decryptedEmail === $value
                ]);

                if ($decryptedEmail === $value) {
                    Log::warning('Email duplicate found in members table', [
                        'member_id' => $member->id,
                        'email' => $value
                    ]);
                    $fail('このメールアドレスは既に使用されています。');
                    return;
                }
            } catch (\Exception $e) {
                // 暗号化されていない古いデータの場合はそのまま比較
                Log::info('Decryption failed, trying plain text comparison', [
                    'member_id' => $member->id,
                    'error' => $e->getMessage()
                ]);

                if ($member->email === $value) {
                    Log::warning('Email duplicate found in members table (plain text)', [
                        'member_id' => $member->id,
                        'email' => $value
                    ]);
                    $fail('このメールアドレスは既に使用されています。');
                    return;
                }
            }
        }

        // 仮登録テーブルでの重複チェック
        $tempRegistration = MemberRegistration::where('email', $value)->first();
        if ($tempRegistration) {
            Log::warning('Email duplicate found in member_registrations table', [
                'registration_id' => $tempRegistration->id,
                'email' => $value
            ]);
            $fail('このメールアドレスは既に仮登録されています。確認メールをご確認ください。');
            return;
        }

        Log::info('UniqueEncryptedEmail validation passed', ['email' => $value]);
    }
}
