<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HoneypotMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ハニーポットフィールドがPOSTリクエストに含まれているかチェック
        if ($request->isMethod('post')) {
            // ハニーポットフィールドに値が入力されている場合はスパムと判定
            if ($request->filled('website') || $request->filled('url') || $request->filled('phone_number')) {
                \Log::warning('Honeypot triggered', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'honeypot_fields' => [
                        'website' => $request->input('website'),
                        'url' => $request->input('url'),
                        'phone_number' => $request->input('phone_number'),
                    ]
                ]);

                // スパムの場合は403エラーを返す
                abort(403, 'Spam detected');
            }

            // 送信速度チェック（フォーム表示から送信まで最低2秒必要）
            $formTime = $request->input('form_time');
            if ($formTime && (time() - $formTime) < 2) {
                \Log::warning('Form submitted too quickly', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'time_taken' => time() - $formTime
                ]);

                abort(403, 'Form submitted too quickly');
            }
        }

        return $next($request);
    }
}
