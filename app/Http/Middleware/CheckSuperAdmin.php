<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 管理者としてログインしているかチェック
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'ログインが必要です。');
        }

        // スーパー管理者かどうかチェック
        if (!Auth::guard('admin')->user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard')->with('error', 'この機能にアクセスする権限がありません。');
        }

        return $next($request);
    }
}
