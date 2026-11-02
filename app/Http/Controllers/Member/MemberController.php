<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\MemberUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use App\Models\Member;
use App\Models\History;



class MemberController extends Controller
{
    /**
     * Display the member's member form.
     */
    public function edit(Request $request): View
    {
        return view('member.edit', [
            'member' => $request->member(),
        ]);
    }

    /**
     * Update the member's member information.
     */
    public function update(memberUpdateRequest $request): RedirectResponse
    {
        $request->member()->fill($request->validated());

        if ($request->member()->isDirty('email')) {
            $request->member()->email_verified_at = null;
        }

        $request->member()->save();

        return Redirect::route('member.edit')->with('status', 'member-updated');
    }

    /**
     * Delete the member's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('memberDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $member = $request->member();

        Auth::logout();

        $member->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }


    /**
     * アカウント
     * @return account
     */
    public function account(Request $request): View
    {
        return view('member.account', [
            'member' => $request->member(),
        ]);
    }



}
