<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberRegistration;
use App\Rules\RecaptchaRule;
use App\Rules\DisposableEmailRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Rules\UniqueEncryptedEmail;
use App\Rules\UniqueLoginId;


use App\Models\Member;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;


class RegisterController extends Controller
{
    //

    public function showForm(): View
    {
        return view('member.auth.register');
    }

    public function register(Request $request)
    {

        $rules = [
            'name' => 'required|string|max:255',
            'loginid' => ['required', 'string', 'max:255', new UniqueLoginId],
            'email' => ['required', 'email', 'max:255', new UniqueEncryptedEmail, new DisposableEmailRule()],
            'password' => 'required|string|min:8|confirmed',
        ];

        if (RecaptchaRule::shouldVerify()) {
            $rules['g-recaptcha-response'] = ['required', new RecaptchaRule()];
        }

        $validated = $request->validate($rules);

        $token = uniqid(); // トークン生成（URL改ざん防止用ではなく一意性用）

        $temp = MemberRegistration::create([
            'name' => $validated['name'],
            'loginid' => $validated['loginid'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 署名付きURL（例: /member/verify/email/{id}?expires=...）
        $url = URL::temporarySignedRoute(
            'member.verify.email',
            now()->addMinutes(60),
            ['id' => $temp->id]
        );

        // テスト用URLも生成
        $testUrl = route('member.test.verify', ['id' => $temp->id]);

        \Log::info('Registration email sent', [
            'temp_id' => $temp->id,
            'email' => $validated['email'],
            'verification_url' => $url,
            'test_url' => $testUrl
        ]);

        Mail::to($validated['email'])->send(new \App\Mail\VerifyMemberEmail($url));

        // メール送信完了ページにリダイレクト（メールアドレスも渡す）
        return redirect()->route('member.email.sent')->with('email', $validated['email']);
    }


}
