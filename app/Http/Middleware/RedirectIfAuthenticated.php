<?php

namespace App\Http\Middleware;

// use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // URLパスを取得
                $path = $request->path();

                // デバッグ情報をログに出力
                // \Log::info('RedirectIfAuthenticated Debug', [
                //     'guard' => $guard,
                //     'path' => $path,
                //     'user_id' => Auth::guard($guard)->id(),
                //     'user_type' => get_class(Auth::guard($guard)->user())
                // ]);

                // 各セクションに応じたリダイレクト先を返す
                if ($guard === 'admin' || str_starts_with($path, 'kanrigamen')) {
                    return redirect()->route('admin.dashboard');
                }
                if ($guard === 'member' || str_starts_with($path, 'member')) {
                    // 会員ログイン後はダッシュボードにリダイレクト
                    return redirect()->route('member.dashboard');
                }
                if ($guard === 'item' || str_starts_with($path, 'item')) {
                    return redirect()->route('item.dashboard');
                }

                // フロントページへのアクセスの場合
                if ($path === '/') {
                    return $next($request);
                }

                return redirect('/');
            }
        }

        return $next($request);
    }
}
