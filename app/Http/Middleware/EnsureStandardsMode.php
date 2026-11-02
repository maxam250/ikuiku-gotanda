<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStandardsMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 出力バッファをクリーンにする（BOMや余分な空白を除去）
        if (ob_get_level() > 0) {
            ob_clean();
        }

        $response = $next($request);

        // HTMLレスポンスの場合、先頭のBOMや空白を除去
        if ($response->headers->get('Content-Type') &&
            str_contains($response->headers->get('Content-Type'), 'text/html')) {

            $content = $response->getContent();

            // BOMを除去
            if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
                $content = substr($content, 3);
            }

            // DOCTYPE宣言の前の空白や改行を除去
            $content = preg_replace('/^[\s\n\r]*/', '', $content);

            // DOCTYPE宣言が最初に来ることを確認
            if (!str_starts_with($content, '<!DOCTYPE')) {
                // DOCTYPE宣言が見つからない場合、先頭に追加
                if (str_starts_with($content, '<html')) {
                    $content = '<!DOCTYPE html>' . "\n" . $content;
                }
            }

            $response->setContent($content);
        }

        return $response;
    }
}


