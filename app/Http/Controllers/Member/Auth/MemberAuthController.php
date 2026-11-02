<?php

namespace App\Http\Controllers\Member\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;

class MemberAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('member.auth.login');
    }

    public function login(Request $request)
    {

        
        $credentials = $request->only('email', 'password');

        if (Auth::guard('member')->attempt($credentials)) {
            return redirect()->intended('member/dashboard');
        }

        return redirect()->back()->withErrors(['email' => 'IDが間違っています']);
    }

    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
        return redirect('/member/login');
    }
}
