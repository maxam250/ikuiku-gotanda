<?php

namespace App\Http\Middleware;

use App\Rules\RecaptchaRule;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReCaptchaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (! RecaptchaRule::shouldVerify()) {
            return $next($request);
        }

        $token = $request->input('g-recaptcha-response');
        
        if (!$token) {
            return back()->withErrors(['recaptcha' => 'reCAPTCHAの検証に失敗しました。']);
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('app.recaptcha.secret_key'),
            'response' => $token,
            'remoteip' => $request->ip(),
        ]);

        $result = $response->json();

        if (!$result['success'] || $result['score'] < 0.5) {
            return back()->withErrors(['recaptcha' => 'reCAPTCHAの検証に失敗しました。']);
        }

        return $next($request);
    }
} 