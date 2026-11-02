<?php

namespace App\Http\Controllers\Member\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\MemberLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::guard('member')->check()) {
            return redirect()->route('member.dashboard');
        }

        return view('member.auth.login'); //auth.loginから変更してみた
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(MemberLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // redirect パラメータがある場合は同一オリジンのURLに限りリダイレクト
        if ($request->filled('redirect')) {
            $redirectUrl = $request->input('redirect');
            $appUrl = parse_url(config('app.url'), PHP_URL_HOST);
            $targetHost = parse_url($redirectUrl, PHP_URL_HOST);
            if ($targetHost === null || $targetHost === $appUrl) {
                return redirect()->to($redirectUrl);
            }
        }

        // それ以外の場合はダッシュボードにリダイレクト
        return redirect()->intended(route('member.dashboard'));
    }

    /**
     * GET /member/logout（ブックマーク等）— POST 用フォームを表示
     */
    public function confirmLogout(Request $request): View
    {
        return view('member.auth.logout-confirm');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('member')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
