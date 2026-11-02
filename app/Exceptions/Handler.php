<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        // JSONリクエストの場合はJSONレスポンスを返す
        if ($request->wantsJson() || $request->expectsJson() || $request->isJson() || $request->header('Accept') === 'application/json') {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'リソースが見つかりません',
                    'error_code' => 404
                ], 404, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }
            
            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                    'error_code' => 422
                ], 422, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }
            
            if ($e instanceof TokenMismatchException || ($e instanceof HttpException && $e->getStatusCode() === 419)) {
                return response()->json([
                    'success' => false,
                    'message' => 'セッションが切れました。再度ログインしてください。',
                    'error_code' => 419
                ], 419, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }
            
            if ($e instanceof HttpException) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'エラーが発生しました',
                    'error_code' => $e->getStatusCode()
                ], $e->getStatusCode(), ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            }
        }
        
        if (
            $e instanceof TokenMismatchException ||
            ($e instanceof HttpException && $e->getStatusCode() === 419)
        ) {
            // すでにどれかでログイン中なら現在のURLをそのまま再表示
            if (auth('admin')->check() || auth('member')->check() || auth('item')->check()) {
                return redirect()->to($request->fullUrl());
            }

            // 未ログインならパスやルート名に応じてそれぞれのログインページへ
            return redirect()->to($this->loginRouteFor($request))
                ->withErrors(['session' => 'セッションが切れました。再度ログインしてください。']);
        }

        return parent::render($request, $e);
    }

    /**
     * アクセスURLから適切なログインページのURLを返す
     */
    protected function loginRouteFor($request): string
    {
        // ルート名で判定（推奨）
        if ($request->routeIs('admin.*'))  return route('admin.login');
        if ($request->routeIs('member.*')) return route('member.login');
        if ($request->routeIs('item.*'))   return route('item.login');

        // ルート名が無い場合はパスでフォールバック
        $path = $request->path();
        if (str_starts_with($path, 'kanrigamen'))  return route('admin.login');
        if (str_starts_with($path, 'member')) return route('member.login');
        if (str_starts_with($path, 'item'))   return route('item.login');

        // どれにも当てはまらなければ共通ログインへ
        return route('login');
    }
}
