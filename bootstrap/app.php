<?php


use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
// use Illuminate\Auth\Middleware\RedirectIfAuthenticated;//追加
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('kanrigamen')->as('admin.')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->prefix('item')->as('item.')
                ->group(base_path('routes/item.php'));

            Route::middleware('web')
            ->group(base_path('routes/member.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('guest', RedirectIfAuthenticated::class);

        // Cloudflare 経由のアクセスで、訪問者の本当の IP・https を受け取る
        // IP 範囲は https://www.cloudflare.com/ips/ の公開値（2026-11-02 時点）
        $middleware->trustProxies(
            at: [
                // IPv4
                '173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22', '103.31.4.0/22',
                '141.101.64.0/18', '108.162.192.0/18', '190.93.240.0/20', '188.114.96.0/20',
                '197.234.240.0/22', '198.41.128.0/17', '162.158.0.0/15', '104.16.0.0/13',
                '104.24.0.0/14', '172.64.0.0/13', '131.0.72.0/22',
                // IPv6
                '2400:cb00::/32', '2606:4700::/32', '2803:f800::/32', '2405:b500::/32',
                '2405:8100::/32', '2a06:98c0::/29', '2c0f:f248::/32',
            ],
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO,
        );

        // Standards modeを確保するミドルウェアを追加（最初に実行）
        $middleware->prependToGroup('web', \App\Http\Middleware\EnsureStandardsMode::class);

        // JSONレスポンスからDebugbarを除外するミドルウェアを追加
        $middleware->append(\App\Http\Middleware\DisableDebugbarForJson::class);

        // セキュリティヘッダーを設定するミドルウェアを追加
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // 簡易アクセスログ（フロントページのUU/PV記録・cron無しで自動削除）
        // 一時停止中：サーバーDB負荷軽減のため。後日 LockerStudio 導入予定
        // $middleware->appendToGroup('web', \App\Http\Middleware\LogPageView::class);

        // ミドルウェアエイリアスを登録
        $middleware->alias([
            'super_admin'                => \App\Http\Middleware\CheckSuperAdmin::class,
            'registration.rate.limit'    => \App\Http\Middleware\RegistrationRateLimit::class,
            'honeypot'                   => \App\Http\Middleware\HoneypotMiddleware::class,
            'fanclub.member'             => \App\Http\Middleware\EnsureActiveFanclubMembership::class,
        ]);

        // 未認証ユーザーのリダイレクト設定
        $middleware->redirectGuestsTo(function (Request $request) {
            // URLパスを取得
            $path = $request->path();

            // 各セクションに応じたリダイレクト先を返す
            if (str_starts_with($path, 'kanrigamen')) {
                return route('admin.login');
            }
            if (str_starts_with($path, 'member')) {
                return route('member.login');
            }
            if (str_starts_with($path, 'item')) {
                return route('item.login');
            }

            // フロントページへのアクセスの場合は何もしない
            if ($path === '/') {
                return null;
            }

            return route('login');
        });

        // ログイン済みユーザーのリダイレクト設定
        // $middleware->appendToGroup('web', RedirectIfAuthenticated::class);
    })


    ->withExceptions(function (Exceptions $exceptions) {
        //


    // 419 を捕捉
    $exceptions->render(function (HttpException $e, $request) {
        if ($e->getStatusCode() === 419) {
            // JSONリクエストの場合はJSONレスポンスを返す
            if ($request->wantsJson() || $request->expectsJson() || $request->isJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'セッションが切れました。再度ログインしてください。',
                    'error_code' => 419
                ], 419, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }

            // すでにログイン済みなら元URLをリロード
            if (Auth::guard('admin')->check() ||
                Auth::guard('member')->check() ||
                Auth::guard('item')->check()) {
                return redirect()->to($request->fullUrl());
            }

            // パスに応じてログインページへ
            $path = $request->path();
            if (str_starts_with($path, 'kanrigamen'))  return redirect()->route('admin.login');
            if (str_starts_with($path, 'member')) return redirect()->route('member.login');
            if (str_starts_with($path, 'item'))   return redirect()->route('item.login');

            return redirect()->route('login');
        }
    });
    })->create();
