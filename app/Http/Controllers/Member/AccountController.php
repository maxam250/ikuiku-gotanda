<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use Illuminate\Validation\Rules\Password;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $memberId = Auth::id();
        $member = Member::findOrFail($memberId);

        return view('member.account', compact('member'));

    }

    // パスワード変更フォーム表示
    public function password()
    {

        return view('member.account.password');
    }

    // パスワード更新処理
    public function passwordUpdate(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'current_password:member'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.current_password' => '現在のパスワードが正しくありません。',
            'password.required' => '新しいパスワードを入力してください。',
            'password.confirmed' => 'パスワードが一致しません。',
        ]);

        $member = Auth::guard('member')->user();

        if ($member->id != $id) {
            return back()->with('error', '不正なアクセスです。');
        }

        $member->password = Hash::make($request->password);
        $member->save();

        return redirect()->route('member.account.password')
            ->with('status', 'パスワードを更新しました。');
    }



    // アカウントフォーム表示
    public function account()
    {
        $memberId = Auth::id();
        $member = Member::findOrFail($memberId);

        $member->email = Crypt::decryptString($member->email);


        return view('member.account', compact('member'));
    }


    // アカウント更新処理
    public function accountUpdate(Request $request, $id)
    {

        $member = Auth::guard('member')->user();
        //　バリデータ
        $request->validate([
            'nickname' => 'nullable|string|max:255',
            'loginid' => 'required|regex:/^[a-zA-Z0-9_]+$/|max:255|unique:members,loginid,' . $member->id,
            'email' => 'required|email|unique:members,email,' . $member->id,
            'tel.*' => 'required|numeric|digits_between:3,4',
            'tel' => 'required|array|size:3',
        ], [
            'loginid.required' => 'ログインIDは必須です。',
            'loginid.unique' => 'このログインIDは既に使用されています。',
        ]);

        // **　暗号化されたメールアドレスとの重複チェック
        // 入力されたメールアドレスを暗号化
        $inputEmail = $request->input('email');

        // データベース内のメールアドレスと比較（自分以外）
        $isDuplicate = Member::where('id', '!=', $member->id)
            ->get()
            ->contains(function ($otherMember) use ($inputEmail) {
                return $inputEmail === Crypt::decryptString($otherMember->email);
            });

        if ($isDuplicate) {
            return back()->withErrors(['email' => 'このメールアドレスは既に使用されています。']);
        }

        // メールアドレスの暗号化
        $encryptedEmail = Crypt::encryptString($request->email);

        $fullTel = implode('-', array_filter($request->tel));

        if ($member->id != $id) {
            return back()->with('error', '不正なアクセスです。');
        }

        // 更新
        $member->update([
            'nickname' => $request->nickname,
            'loginid' => $request->loginid,
            'email' => $encryptedEmail,
            'tel' => $fullTel,
            'memo_share' => $request->memo_share,
        ]);


        return redirect()->route('member.account')
            ->with('status', 'アカウント情報を更新しました。');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $member = Member::findOrFail($id);

        return view('member.edit', compact('member'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //　バリデータ
        $request->validate([
        'name' => 'required|string|max:255',
        ]);

        // 項目の取得
        $member = Member::findOrFail($id);

        // 名前を更新
        $member->update([
            'name' => $request->name,
        ]);

        // リダイレクト（成功メッセージ付き）
        return redirect()->route('member.account.index', $member->id)->with('success', '名前が更新されました。');

   }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
