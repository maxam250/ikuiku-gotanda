<?php

namespace App\Http\Controllers\Member\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use App\Models\Member;
use Illuminate\Support\Facades\Crypt;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('member.auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->email;

        // 暗号化されたメールアドレスを復号して一致するMemberを検索
        $member = Member::all()->first(function ($member) use ($email) {
            try {
                return Crypt::decryptString($member->email) === $email;
            } catch (\Exception $e) {
                return false;
            }
        });

        if (!$member) {
            return back()->withInput($request->only('email'))
                        ->withErrors(['email' => 'このメールアドレスは登録されていません。']);
        }

        // トークン生成
        $token = Password::broker('members')->createToken($member);

        // 通知送信（暗号化解除済みアドレスで送信）
        $member->notify(new \App\Notifications\MemberResetPasswordNotification($token));

        return back()->with('status', 'パスワードリセットリンクをメールで送信しました。');
    }
}
