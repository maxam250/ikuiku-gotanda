<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;


class LoginController extends Controller
{
    //
    public function showLoginForm()
    {
        return view('item.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('item')->attempt($credentials)) {
            return redirect()->route('item.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        Auth::guard('item')->logout();
        return redirect()->route('item.login');
    }
}
