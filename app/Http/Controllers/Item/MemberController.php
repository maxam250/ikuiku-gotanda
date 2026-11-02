<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Member;
use App\Models\MemberPoint;
use App\Models\Item;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use DateTime;
use DateInterval;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::with('memberPoints')->orderBy('id', 'desc')->get();
        // 各メンバーごとのポイントの計算　Memberモデルにアクセサで設定

        foreach ($members as $member) {
            // `email`プロパティに復号した値を直接上書き
            $member->email = Crypt::decryptString($member->email);
        }
        $items = Item::where('item_active', '1' )->orderBy('id', 'desc')->get();


        //お気に入り favo
        //ブロック block

        return view('item.members.index', compact('members', 'items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        // 生年月日の西暦 18歳以上
        $start_year = Carbon::now()->year - 82;
        $end_year =  Carbon::now()->year - 18;


        return view('item.members.create', compact('start_year', 'end_year'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'member_no' => 'nullable|string|unique:members,member_no',
            'email' => 'required|email',
            'tel.*' => 'required|numeric|digits_between:3,4',
            'tel' => 'required|array|size:3',
            'birth_year' => 'required|integer',
            'birth_month' => 'required|integer',
            'birth_day' => 'required|integer',
            'gender' => 'nullable|integer',
            'line' => 'nullable|string|max:255',
            'sns' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'kind' => 'required|integer',
            'password' => 'required|string|min:8',
            // その他の必要なバリデーションルール
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



        $memberData = $request->all();

        // telを結合
        // $telParts = $memberData['tel'];
        // $fullTel = implode('-', array_filter($telParts)); // 空の要素を除外
        // $memberData['tel'] = $fullTel;



        // セッションに保存する際は、結合した値を保存
        // $request->session()->put('member_form_data', array_merge($memberData->all(), ['tel' => $fullTel]));

        // メールアドレスの暗号化
        $memberData['email'] = Crypt::encryptString($memberData['email']);

        // パスワードのハッシュ化（nullでない場合のみ）
        $memberData['password'] =  Hash::make($memberData['password']) ;


        // telを結合
        $memberData['tel'] = implode('-', array_filter($memberData['tel']));
        // 生年月日を生成
        $memberData['birth'] = Carbon::createFromDate(
            $request->input('birth_year'),
            $request->input('birth_month'),
            $request->input('birth_day')
        )->format('Y-m-d');



        $members = Member::create($memberData);


        return redirect()->route('item.members.index')->with('予約を登録しました');



    }

    /**
     * Display the specified resource.
     */
    public function show($memberId)
    {

        $members = Member::with('memberPoints')->orderBy('id', 'desc')->get();
        // 各メンバーごとのポイントの計算　Memberモデルにアクセサで設定

        $items = Item::where('item_active', '1' )->orderBy('id', 'desc')->get();


        // 指定したメンバーのポイント履歴を取得
        $member = Member::with('memberPoints')->findOrFail($memberId);

        return view('member_points.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $member)
    {
        //
        $member = Member::findOrFail($member);
        // 各メンバーごとのポイントの計算　Memberモデルにアクセサで設定
        $items = Item::where('item_active', '1' )->orderBy('id', 'desc')->get();

        // 生年月日の西暦 18歳以上
        $start_year = Carbon::now()->year - 82;
        $end_year =  Carbon::now()->year - 18;

        // 暗号化されたemailを復号
        $decryptedEmail = Crypt::decryptString($member->email);

        return view('item.members.edit', compact('member', 'items', 'start_year', 'end_year', 'decryptedEmail'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'member_no' => 'nullable|string|unique:members,member_no,' . $member->id,
            'email' => 'required|email',
            'tel.*' => 'required|numeric|digits_between:3,4',
            'tel' => 'required|array|size:3',
            'birth_year' => 'required|integer',
            'birth_month' => 'required|integer',
            'birth_day' => 'required|integer',
            'gender' => 'nullable|integer',
            'line' => 'nullable|string|max:255',
            'sns' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'kind' => 'required|integer',
            'password' => 'nullable|string|min:8',
        ]);

        // メールアドレスの暗号化
        $encryptedEmail = Crypt::encryptString($request->email);

        // パスワードのハッシュ化（nullでない場合のみ）
        $hashedPassword = $request->filled('password') ? Hash::make($request->password) : $member->password;


        // telを結合
        $fullTel = implode('-', array_filter($request->tel));
        // 生年月日を生成
        $birth = Carbon::createFromDate(
            $request->input('birth_year'),
            $request->input('birth_month'),
            $request->input('birth_day')
        )->format('Y-m-d');


        // 更新
        $member->update([
            'kind' => $request->kind,
            'name' => $request->name,
            'nickname' => $request->nickname,
            'member_no' => $request->member_no,
            'email' => $encryptedEmail,
            'gender' => $request->gender,
            'tel' => $fullTel,
            'birth' => $birth,
            'line' => $request->line,
            'sns' => $request->sns,
            'url' => $request->url,
            'memo' => $request->memo,
            'memo_share' => $request->memo_share,
            'password' => $hashedPassword,
        ]);

        return redirect()->route('item.members.edit', ['member' => $member])->with('success', '会員情報が更新されました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $member)
    {
        //
        $members = Member::findOrFail($member);

        // データの削除
        $members->delete();

        return redirect()->route('item.members.index')->with('success', '会員が削除されました');
    }



    public function updateFlag(Request $request)
    {
        $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'flag' => 'required|boolean'
        ]);
        $member = Member::findOrFail($request->member_id);
        $member->check = $request->flag;
        $success = $member->save();

        return response()->json(['success' => $success]);
    }

}
