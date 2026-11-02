<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Member;
use App\Models\MemberRegistration;
use Illuminate\Support\Facades\Log;

class UniqueLoginId implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        Log::info('UniqueLoginId validation started', ['loginid' => $value]);

        // 既存会員のloginidとの重複チェック
        $memberExists = Member::where('loginid', $value)->exists();
        if ($memberExists) {
            Log::warning('LoginId duplicate found in members table', ['loginid' => $value]);
            $fail('このログインIDは既に使用されています。');
            return;
        }

        // 仮登録テーブルでの重複チェック
        $tempRegistration = MemberRegistration::where('loginid', $value)->first();
        if ($tempRegistration) {
            Log::warning('LoginId duplicate found in member_registrations table', [
                'registration_id' => $tempRegistration->id,
                'loginid' => $value
            ]);
            $fail('このログインIDは既に仮登録されています。確認メールをご確認ください。');
            return;
        }

        Log::info('UniqueLoginId validation passed', ['loginid' => $value]);
    }
}

