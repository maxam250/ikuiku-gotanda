<?php

namespace App\Http\Middleware;

use App\Models\AccessLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * フロントページの簡易アクセスログを記録するミドルウェア。
 *
 * - GETかつHTML(200)のフロントページのみを対象に1PV=1行を記録
 * - UUは1stパーティCookie(vlog_id)で判定
 * - cronを使わず、アクセスを契機に「1日1回だけ」古い行を自動削除
 */
class LogPageView
{
    /**
     * 一時停止中（サーバーDB負荷軽減）。
     * 後日 LockerStudio 導入まで access_logs への記録を行わない。
     */
    private const ENABLED = false;

    /** UU判定用Cookie名 */
    private const COOKIE_NAME = 'vlog_id';

    /** Cookie保持期間（分）＝約400日 */
    private const COOKIE_MINUTES = 60 * 24 * 400;

    public function handle(Request $request, Closure $next): Response
    {
        if (! self::ENABLED) {
            return $next($request);
        }

        $response = $next($request);

        // 記録対象で、まだ訪問者Cookieが無ければ発行する
        if ($this->shouldLog($request, $response) && ! $request->cookie(self::COOKIE_NAME)) {
            $visitorId = Str::random(32);
            // terminate() 側で同じIDを使えるように控える
            $request->attributes->set('_vlog_new_id', $visitorId);
            Cookie::queue(self::COOKIE_NAME, $visitorId, self::COOKIE_MINUTES);
        }

        return $response;
    }

    /**
     * レスポンス送出後に実行され、ユーザー体感速度に影響を与えずに記録する。
     */
    public function terminate(Request $request, Response $response): void
    {
        if (! self::ENABLED) {
            return;
        }

        try {
            if (! $this->shouldLog($request, $response)) {
                return;
            }

            $visitorId = $request->cookie(self::COOKIE_NAME)
                ?? $request->attributes->get('_vlog_new_id')
                ?? Str::random(32);

            $referer = $request->headers->get('referer');
            $refererHost = null;
            if ($referer) {
                $refererHost = parse_url($referer, PHP_URL_HOST) ?: null;
                // 自サイト内の遷移はリファラ集計から除外する
                if ($refererHost && strcasecmp($refererHost, $request->getHost()) === 0) {
                    $refererHost = null;
                }
            }

            DB::table('access_logs')->insert([
                'visitor_id'   => substr((string) $visitorId, 0, 32),
                'path'         => Str::limit($request->path(), 190, ''),
                'referer'      => $referer ? Str::limit($referer, 250, '') : null,
                'referer_host' => $refererHost ? Str::limit($refererHost, 190, '') : null,
                'created_at'   => now(),
            ]);

            $this->cleanupIfDue();
        } catch (\Throwable $e) {
            // ログ記録の失敗は本処理へ影響させない
        }
    }

    /**
     * 記録対象のリクエストかどうか判定する。
     */
    private function shouldLog(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }
        if ($request->ajax() || $request->pjax() || $request->wantsJson() || $request->isJson()) {
            return false;
        }
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        // HTMLレスポンスのみ
        $contentType = $response->headers->get('Content-Type');
        if ($contentType && ! Str::contains($contentType, 'text/html')) {
            return false;
        }

        $path = $request->path();

        // 管理系・API・ヘルスチェック等は除外
        $excluded = ['kanrigamen', 'item', 'member', 'api', 'up', 'livewire', 'storage', 'sanctum'];
        foreach ($excluded as $prefix) {
            if ($path === $prefix || Str::startsWith($path, $prefix . '/')) {
                return false;
            }
        }

        // 拡張子付き（静的アセット）は除外
        if (preg_match('/\.(css|js|png|jpe?g|gif|webp|svg|ico|woff2?|ttf|map|txt|xml|json|php)$/i', $path)) {
            return false;
        }

        // 簡易ボット判定（UUを人ベースに保つため）
        $ua = (string) $request->userAgent();
        if ($ua === '' || preg_match('/bot|crawl|spider|slurp|bing|google|yahoo|baidu|yandex|duckduck|facebookexternalhit|embedly|monitor|uptime|curl|wget|python-requests|axios|headless|lighthouse/i', $ua)) {
            return false;
        }

        return true;
    }

    /**
     * cronを使わず、1日1回だけ古い行をまとめて削除する。
     * アクセスを契機に発火し、キャッシュで重複実行を防ぐ。
     */
    private function cleanupIfDue(): void
    {
        $today = now()->toDateString();

        if (Cache::get('access_logs_last_cleanup') === $today) {
            return;
        }

        // 同時アクセス時の多重実行を防止（取得できたプロセスのみ実行）
        $lock = Cache::lock('access_logs_cleanup_lock', 600);
        if (! $lock->get()) {
            return;
        }

        try {
            // 先にフラグを立て、他リクエストの再実行を止める
            Cache::put('access_logs_last_cleanup', $today, now()->addDay());

            $threshold = now()->subDays(AccessLog::RETENTION_DAYS);

            // 1リクエストあたりの負荷を抑えるためチャンク削除
            for ($i = 0; $i < 30; $i++) {
                $deleted = DB::table('access_logs')
                    ->where('created_at', '<', $threshold)
                    ->limit(1000)
                    ->delete();

                if ($deleted < 1000) {
                    break;
                }
            }
        } finally {
            $lock->release();
        }
    }
}
