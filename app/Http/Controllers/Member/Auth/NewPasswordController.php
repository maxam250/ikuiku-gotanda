<?php

namespace App\Http\Controllers\Member\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\Member;
use Carbon\Carbon;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('member.auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $email = $request->email;

        // 平文emailからMemberモデルを取得（暗号化を解読してマッチ）
        $member = Member::all()->first(function ($m) use ($request) {
            try {
                return Crypt::decryptString($m->email) === $request->email;
            } catch (\Exception $e) {
                return false;
            }
        });

        if (!$member) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'パスワードリセットに失敗しました（会員が見つかりません）。']);
        }

        //  DBに保存されているのは【平文のemail】なので、requestのemailで検索
        $tokenData = DB::table('member_password_reset_tokens')
            ->where('email', $request->email)  // ← 平文で検索！
            ->first();

        if (!$tokenData || !Hash::check($request->token, $tokenData->token)) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'トークンが無効か期限切れです。']);
        }

        // 任意：トークンの有効期限チェック（60分）
        if (Carbon::parse($tokenData->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => 'トークンの有効期限が切れています。']);
        }

        // パスワードを更新
        $member->password = Hash::make($request->password);
        $member->setRememberToken(Str::random(60));
        $member->save();

        // トークン削除
        DB::table('member_password_reset_tokens')->where('email', $member->email)->delete();

        event(new PasswordReset($member));

        return redirect()->route('member.login')->with('status', 'パスワードが正常に更新されました。');
    }
}
