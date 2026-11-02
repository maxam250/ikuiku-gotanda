<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberRegistration;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\VerifyMemberEmail;

class ResendVerificationController extends Controller
{
    //
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:member_registrations,email',
        ]);

        $temp = MemberRegistration::where('email', $request->email)->first();

        if (!$temp) {
            return back()->withErrors(['email' => '該当する仮登録が見つかりませんでした。']);
        }

        $url = URL::temporarySignedRoute(
            'member.verify.email',
            now()->addMinutes(60),
            ['id' => $temp->id]
        );

        Mail::to($temp->email)->send(new VerifyMemberEmail($url));

        return back()->with('message', '確認メールを再送しました。メールをご確認ください。');
    }    
}
