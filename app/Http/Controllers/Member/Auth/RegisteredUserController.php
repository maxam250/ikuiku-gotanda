<?php

namespace App\Http\Controllers\Member\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('member.auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'loginid' => ['required', 'string', 'max:255','unique:members,loginid'],
            // 'birth' => ['required', 'date', 'before:'.Carbon::now()->subYears(18)->format('Y-m-d')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // **　暗号化されたメールアドレスとの重複チェック
        // 入力されたメールアドレスを暗号化
        $inputEmail = $request->input('email');

        // データベース内のメールアドレスと比較
        $isDuplicate = Member::all()->contains(function ($member) use ($inputEmail) {
            return $inputEmail === Crypt::decryptString($member->email);
        });

        if ($isDuplicate) {
            return back()->withErrors(['email' => 'このメールアドレスは既に使用されています。']);
        }


        $member = Member::create([
            'name' => $request->name,
            'email' => Crypt::encryptString($request->email),
            'loginid' => $request->loginid,
            // 'birth' => $request->birth,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($member));

        Auth::guard('member')->login($member);


        // Auth::login($member);

        return redirect(route('member.dashboard', absolute: false));

        // return redirect(RouteServiceProvider::MEMBER_HOME);
    }
}
