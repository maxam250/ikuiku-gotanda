<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Member;

class PasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('member');
    }

    public function showChangePasswordForm()
    {

        return view('member.passwords.change');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $member = Member::find($password);

        $member->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('member.password')->with('success', 'パスワードが変更されました。');
    }
}
