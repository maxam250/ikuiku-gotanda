<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Mail\NewsletterConfirmation;
use App\Rules\RecaptchaRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    /**
     * 登録フォーム表示
     */
    public function showRegisterForm()
    {
        return view('newsletter.register');
    }

    /**
     * メールマガジン登録
     */
    public function register(Request $request)
    {
        // レート制限チェック（1時間に5回まで）
        $key = 'newsletter-register:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => '登録試行回数が上限に達しました。' . ceil($seconds / 60) . '分後に再試行してください。'
            ]);
        }

        // バリデーション
        $rules = [
            'email' => ['required', 'email', 'max:255'],
        ];
        $messages = [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => '有効なメールアドレスを入力してください。',
        ];

        if (RecaptchaRule::shouldVerify()) {
            $rules['g-recaptcha-response'] = ['required', new RecaptchaRule()];
        }

        $validated = $request->validate($rules, $messages);

        // ハニーポットチェック
        if ($request->filled('website') || $request->filled('url') || $request->filled('phone_number')) {
            \Log::warning('Newsletter registration honeypot triggered', [
                'ip' => $request->ip(),
            ]);
            return back()->with('success', '登録が完了しました。'); // スパムには成功メッセージを返す
        }

        $email = strtolower(trim($validated['email']));

        // 既存の購読者をチェック
        $existing = NewsletterSubscriber::findByEmail($email);

        if ($existing) {
            if ($existing->isVerified() && !$existing->isUnsubscribed()) {
                // 既に登録済み
                return back()->with('info', 'このメールアドレスは既に登録されています。');
            } elseif ($existing->isUnsubscribed()) {
                // 解除済みの場合は再登録
                $existing->unsubscribed_at = null;
                $existing->token = NewsletterSubscriber::generateToken();
                $existing->ip_address = $request->ip();
                $existing->save();
            } else {
                // 未確認の場合はトークンを再生成
                $existing->token = NewsletterSubscriber::generateToken();
                $existing->ip_address = $request->ip();
                $existing->save();
            }
            $subscriber = $existing;
        } else {
            // 新規登録
            $subscriber = NewsletterSubscriber::create([
                'email' => $email,
                'token' => NewsletterSubscriber::generateToken(),
                'ip_address' => $request->ip(),
            ]);
        }

        // 確認メール送信
        try {
            Mail::to($email)->send(new NewsletterConfirmation($subscriber));
            RateLimiter::hit($key, 3600); // 1時間
            return back()->with('success', '確認メールを送信しました。メール内のリンクをクリックして登録を完了してください。');
        } catch (\Exception $e) {
            \Log::error('Newsletter confirmation email failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return back()->withErrors(['email' => 'メール送信に失敗しました。しばらくしてから再度お試しください。']);
        }
    }

    /**
     * 確認（二重オプトイン）
     */
    public function confirm($token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->first();

        if (!$subscriber) {
            return view('newsletter.confirm', [
                'success' => false,
                'message' => '無効なトークンです。',
            ]);
        }

        if ($subscriber->isVerified()) {
            return view('newsletter.confirm', [
                'success' => true,
                'message' => '既に登録済みです。',
            ]);
        }

        $subscriber->verified_at = now();
        $subscriber->save();

        return view('newsletter.confirm', [
            'success' => true,
            'message' => 'メールマガジンの登録が完了しました。',
        ]);
    }

    /**
     * 解除
     */
    public function unsubscribe($token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->first();

        if (!$subscriber) {
            return view('newsletter.unsubscribe', [
                'success' => false,
                'message' => '無効なトークンです。',
            ]);
        }

        if ($subscriber->isUnsubscribed()) {
            return view('newsletter.unsubscribe', [
                'success' => true,
                'message' => '既に解除済みです。',
            ]);
        }

        $subscriber->unsubscribed_at = now();
        $subscriber->save();

        return view('newsletter.unsubscribe', [
            'success' => true,
            'message' => 'メールマガジンの配信を解除しました。',
        ]);
    }
}
