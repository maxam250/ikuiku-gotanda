<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // X-Content-Type-Options: MIMEタイプのスニッフィングを防ぐ
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // X-Frame-Options: クリックジャッキング攻撃を防ぐ
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // X-XSS-Protection: ブラウザのXSSフィルターを有効化（レガシーブラウザ用）
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer-Policy: リファラー情報の送信を制御
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions-Policy: ブラウザ機能の使用を制限
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // HTTPS接続の場合のみHSTSを設定
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Content-Security-Policy: XSS攻撃を防ぐ（必要に応じて調整）
        // 注意: 既存のスクリプトやスタイルに影響を与える可能性があるため、慎重に設定
        // 開発環境ではCSPを緩和するか、環境変数で無効化可能
        // 一時的にCSPを無効化して問題を特定する場合は、.envに DISABLE_CSP=true を設定
        // デフォルトでは開発環境（APP_DEBUG=true）ではCSPを無効化
        if ((config('app.debug') && env('DISABLE_CSP', true)) || env('DISABLE_CSP', false)) {
            // 開発環境でCSPを無効化する場合
            // CSPヘッダーを設定しない
        } else {
            // 開発環境用のViteサーバー接続を許可
            $viteDevServer = config('app.debug') ? ' http://localhost:5173 ws://localhost:5173' : '';

            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
                   "https://platform.twitter.com " .
                   "https://cdn.jsdelivr.net " .
                   "https://code.jquery.com " .
                   "https://www.googletagmanager.com " .
                   "https://www.google-analytics.com " .
                   "https://cdn.tiny.cloud " .
                   $viteDevServer . "; " .
                   "style-src 'self' 'unsafe-inline' " .
                   "https://fonts.googleapis.com " .
                   "https://fonts.bunny.net " .
                   "https://cdn.jsdelivr.net " .
                   "https://cdnjs.cloudflare.com " .
                   "https://maxst.icons8.com " .
                   "https://cdn.tiny.cloud" .
                   $viteDevServer . "; " .
                   "font-src 'self' data: " .
                   "https://fonts.gstatic.com " .
                   "https://fonts.bunny.net " .
                   "https://cdnjs.cloudflare.com " .
                   "https://maxst.icons8.com " .
                   "https://cdn.tiny.cloud; " .
                   "img-src 'self' data: https: http: blob:; " .
                   "connect-src 'self' " .
                   "https://api.twitter.com " .
                   "https://www.google-analytics.com " .
                   "https://www.googletagmanager.com " .
                   "https://cdn.tiny.cloud " .
                   "https://cdn.jsdelivr.net" .
                   $viteDevServer . "; " .
                   "frame-src 'self' " .
                   "https://platform.twitter.com " .
                   "https://snapwidget.com " .
                   "https://cdn.tiny.cloud " .
                   "https://qzin.jp " .
                   "https://www.cityheaven.net " .
                   "https://cityheaven.net " .
                   "https://blogparts.cityheaven.net " .
                   "https://fujoho.jp " .
                   "https://fuzoku.jp " .
                   "https://ranking-deli.jp " .
                   "https://fuzokudx.com; " .
                   "worker-src 'self' blob:; " .
                   "manifest-src 'self'; " .
                   "object-src 'none'; " .
                   "base-uri 'self'; " .
                   "form-action 'self'; " .
                   "frame-ancestors 'self';";

            // HTTPS接続の場合のみupgrade-insecure-requestsを追加
            // HTTP接続の場合は追加しない（ローカル開発環境での問題を避けるため）
            if ($request->secure()) {
                $csp .= " upgrade-insecure-requests;";
            }

            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }
}

