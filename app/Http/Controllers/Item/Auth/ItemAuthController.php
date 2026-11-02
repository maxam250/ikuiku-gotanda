<?php

namespace App\Http\Controllers\Item\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('item.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('item')->attempt($credentials)) {
            return redirect()->intended('item/dashboard');
        }

        return redirect()->back()->withErrors(['email' => 'IDが間違っています']);
    }

    public function logout(Request $request)
    {
        Auth::guard('item')->logout();
        return redirect('/item/login');
    }
}
