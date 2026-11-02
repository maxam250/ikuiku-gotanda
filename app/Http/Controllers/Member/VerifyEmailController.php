<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberRegistration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Mail\MemberRegistrationComplete;

class VerifyEmailController extends Controller
{
    //
    public function __invoke(Request $request, $id)
    {
        \Log::info('VerifyEmailController called', [
            'id' => $id,
            'url' => $request->fullUrl(),
            'signature_valid' => $request->hasValidSignature()
        ]);

        if (! $request->hasValidSignature()) {
            \Log::error('Invalid signature', ['url' => $request->fullUrl()]);
            abort(403, '署名が無効です。');
        }

        // 既に認証済みかどうかをチェック
        $temp = MemberRegistration::find($id);

        if (!$temp) {
            \Log::info('Registration already completed or invalid ID', ['id' => $id]);
            // 既に登録完了している場合は、完了ページを表示
            try {
                // ビューに必要なデータを渡す
                $shops = \App\Models\Shop::first();
                return view('member.auth.registration-complete', compact('shops'));
            } catch (\Exception $e) {
                \Log::error('Error rendering registration complete view (already completed)', [
                    'error' => $e->getMessage()
                ]);

                return response('
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>登録完了</title>
                        <meta charset="utf-8">
                    </head>
                    <body>
                        <h1>会員登録は既に完了しています</h1>
                        <p>認証は正常に完了しています。</p>
                        <p><a href="/member/dashboard">マイページへ</a></p>
                        <p><a href="/">トップページへ</a></p>
                    </body>
                    </html>
                ');
            }
        }

        $member = Member::create([
            'name' => $temp->name,
            'loginid' => $temp->loginid,
            'email' => Crypt::encryptString($temp->email),
            // 'email_plain' => $temp->email,
            'password' => $temp->password,
        ]);

        $temp->delete();

        Auth::guard('member')->login($member);

        \Log::info('Member registration completed', ['member_id' => $member->id]);

        // 登録完了メールを送信
        try {
            $shops = \App\Models\Shop::first();
            $memberEmail = Crypt::decryptString($member->email);
            Mail::to($memberEmail)->send(new MemberRegistrationComplete($member, $shops));
            \Log::info('Registration complete email sent', ['member_id' => $member->id, 'email' => $memberEmail]);
        } catch (\Exception $e) {
            \Log::error('Failed to send registration complete email', [
                'member_id' => $member->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }

        \Log::info('Redirecting to registration complete page', [
            'route' => 'member.registration.complete',
            'url' => route('member.registration.complete')
        ]);

                // 登録完了ページを表示
        \Log::info('Attempting to return registration complete view');

        try {
            // ビューに必要なデータを渡す
            $shops = \App\Models\Shop::first();
            return view('member.auth.registration-complete', compact('shops'));
        } catch (\Exception $e) {
            \Log::error('Error rendering registration complete view', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // エラーが発生した場合のフォールバック
            return response('
                <!DOCTYPE html>
                <html>
                <head>
                    <title>登録完了</title>
                    <meta charset="utf-8">
                </head>
                <body>
                    <h1>会員登録が完了しました！</h1>
                    <p>ご登録いただき、ありがとうございます。</p>
                    <p><a href="/member/dashboard">マイページへ</a></p>
                    <p><a href="/">トップページへ</a></p>
                </body>
                </html>
            ');
        }
    }
}
