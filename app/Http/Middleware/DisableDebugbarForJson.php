<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableDebugbarForJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // JSONレスポンスの場合はDebugbarを無効化
        if ($request->wantsJson() || $request->expectsJson() || $request->isJson() || $request->header('Accept') === 'application/json') {
            if (class_exists('\Barryvdh\Debugbar\Facades\Debugbar')) {
                \Barryvdh\Debugbar\Facades\Debugbar::disable();
            }
        }

        $response = $next($request);

        // レスポンスがJSONの場合はDebugbarを無効化
        if ($response->headers->get('Content-Type') && str_contains($response->headers->get('Content-Type'), 'application/json')) {
            if (class_exists('\Barryvdh\Debugbar\Facades\Debugbar')) {
                \Barryvdh\Debugbar\Facades\Debugbar::disable();
            }
        }

        return $response;
    }
}






