<?php

namespace App\Http\Controllers\Item\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ItemLoginRequest;
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
        if (Auth::guard('item')->check()) {
            return redirect()->route('item.dashboard');
        }

        return view('item.auth.login'); //auth.loginから変更してみた
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(ItemLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('item.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('item')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
