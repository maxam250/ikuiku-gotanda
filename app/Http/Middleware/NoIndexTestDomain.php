<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NoIndexTestDomain
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if (str_starts_with($request->getHost(), 'test.')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }
        return $response;
    }
}
