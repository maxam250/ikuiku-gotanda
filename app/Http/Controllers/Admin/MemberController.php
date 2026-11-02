<?php

namespace App\Http\Controllers\Admin;

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
    public function index(Request $request)
    {
        $query = Member::with('memberPoints');
        
        // 個別検索条件の処理
        if ($request->filled('member_id')) {
            $query->where('id', 'like', '%' . $request->input('member_id') . '%');
        }
        
        if ($request->filled('member_no')) {
            $query->where('member_no', 'like', '%' . $request->input('member_no') . '%');
        }
        
        // まずは暗号化されていないフィールドで絞り込み
        $members = $query->orderBy('id', 'desc')->get();
        
        // 暗号化されたフィールドでの検索（名前、メール、電話番号）
        if ($request->filled('name') || $request->filled('email') || $request->filled('tel')) {
            $filteredMembers = collect();
            
            foreach ($members as $member) {
                $match = true;
                
                // 名前検索
                if ($request->filled('name')) {
                    $nameMatch = false;
                    try {
                        $decryptedName = Crypt::decryptString($member->getRawOriginal('name'));
                        if (strpos($decryptedName, $request->input('name')) !== false) {
                            $nameMatch = true;
                        }
                    } catch (\Exception $e) {
                        // 暗号化されていない場合
                        $rawName = $member->getRawOriginal('name');
                        if ($rawName && strpos($rawName, $request->input('name')) !== false) {
                            $nameMatch = true;
                        }
                    }
                    if (!$nameMatch) {
                        $match = false;
                    }
                }
                
                // メール検索
                if ($match && $request->filled('email')) {
                    $emailMatch = false;
                    try {
                        $decryptedEmail = Crypt::decryptString($member->getRawOriginal('email'));
                        if (strpos($decryptedEmail, $request->input('email')) !== false) {
                            $emailMatch = true;
                        }
                    } catch (\Exception $e) {
                        // 暗号化されていない場合
                        if (strpos($member->email, $request->input('email')) !== false) {
                            $emailMatch = true;
                        }
                    }
                    if (!$emailMatch) {
                        $match = false;
                    }
                }
                
                // 電話番号検索
                if ($match && $request->filled('tel')) {
                    $telMatch = false;
                    try {
                        $decryptedTel = Crypt::decryptString($member->getRawOriginal('tel'));
                        if (strpos($decryptedTel, $request->input('tel')) !== false) {
                            $telMatch = true;
                        }
                    } catch (\Exception $e) {
                        // 暗号化されていない場合
                        $rawTel = $member->getRawOriginal('tel');
                        if ($rawTel && strpos($rawTel, $request->input('tel')) !== false) {
                            $telMatch = true;
                        }
                    }
                    if (!$telMatch) {
                        $match = false;
                    }
                }
                
                if ($match) {
                    $filteredMembers->push($member);
                }
            }
            
            $members = $filteredMembers;
        }
        
        // ページネーション処理
        $perPage = 50;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedMembers = $members->slice($offset, $perPage);
        
        $members = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedMembers,
            $members->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        
        // 各メンバーごとのポイントの計算　Memberモデルにアクセサで設定
        foreach ($members as $member) {
            try {
                $member->email = Crypt::decryptString($member->getRawOriginal('email'));
            } catch (\Exception $e) {
                \Log::warning('Failed to decrypt email for member ID: ' . $member->id . ' - ' . $e->getMessage());
                $member->email = $member->getRawOriginal('email') ?: '(復号化失敗)';
            }
        }
        
        $items = Item::where('item_active', '1' )->orderBy('id', 'desc')->get();

        return view('admin.members.index', compact('members', 'items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        // 生年月日の西暦 18歳以上
        $start_year = Carbon::now()->year - 82;
        $end_year =  Carbon::now()->year - 18;


        return view('admin.members.create', compact('start_year', 'end_year'));
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
            'loginid' => 'required|regex:/^[a-zA-Z0-9_]+$/|max:255|unique:members,loginid',
            'tel.*' => 'required|numeric|digits_between:3,4',
            'tel' => 'required|array|size:3',
            'gender' => 'nullable|integer',
            'line' => 'nullable|string|max:255',
            'sns' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'kind' => 'required|integer',
            'password' => 'required|string|min:8',
            'memo' => 'nullable|string',
            'memo_share' => 'nullable|string',
        ]);

        // **　暗号化されたメールアドレスとの重複チェック
        // 入力されたメールアドレスを暗号化
        $inputEmail = $request->input('email');

        // データベース内のメールアドレスと比較
        $isDuplicate = Member::all()->contains(function ($member) use ($inputEmail) {
            try {
                return $inputEmail === Crypt::decryptString($member->getRawOriginal('email'));
            } catch (\Exception $e) {
                \Log::warning('Failed to decrypt email for duplicate check - Member ID: ' . $member->id);
                return false;
            }
        });

        if ($isDuplicate) {
            return back()->withErrors(['email' => 'このメールアドレスは既に使用されています。']);
        }

        // loginidの重複チェック
        $existingLoginId = Member::where('loginid', $request->loginid)->first();
        if ($existingLoginId) {
            return back()->withErrors(['loginid' => 'このログインIDは既に使用されています。'])->withInput();
        }

        // 必要なデータのみを抽出
        $memberData = [
            'name' => $request->name,
            'nickname' => $request->nickname,
            'member_no' => $request->member_no,
            'email' => Crypt::encryptString($request->email),
            'tel' => implode('-', array_filter($request->tel)),
            'gender' => $request->gender,
            'line' => $request->line,
            'sns' => $request->sns,
            'url' => $request->url,
            'kind' => $request->kind,
            'loginid' => $request->loginid,
            'password' => Hash::make($request->password),
            'memo' => $request->memo,
            'memo_share' => $request->memo_share,
        ];

        $member = Member::create($memberData);

        return redirect()->route('admin.members.index')->with('success', '会員を登録しました');
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
        try {
            $decryptedEmail = Crypt::decryptString($member->getRawOriginal('email'));
        } catch (\Exception $e) {
            \Log::warning('Failed to decrypt email for member ID: ' . $member->id . ' - ' . $e->getMessage());
            $decryptedEmail = $member->getRawOriginal('email') ?: '';
        }

        return view('admin.members.edit', compact('member', 'items', 'start_year', 'end_year', 'decryptedEmail'));
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
            // 'birth_year' => 'required|integer',
            // 'birth_month' => 'required|integer',
            // 'birth_day' => 'required|integer',
            'gender' => 'nullable|integer',
            'line' => 'nullable|string|max:255',
            'sns' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'kind' => 'required|integer',
            'loginid' => 'required|regex:/^[a-zA-Z0-9_]+$/|max:255|unique:members,loginid,' . $member->id,
            'password' => 'nullable|string|min:8',
        ]);


        // 入力されたメールアドレス
        $inputEmail = $request->input('email');

        // 自分以外の会員で同じメールが存在するかチェック
        $isDuplicate = Member::where('id', '!=', $member->id)->get()->contains(function ($m) use ($inputEmail) {
            try {
                return $inputEmail === \Illuminate\Support\Facades\Crypt::decryptString($m->getRawOriginal('email'));
            } catch (\Exception $e) {
                \Log::warning('Failed to decrypt email for duplicate check - Member ID: ' . $m->id);
                return false;
            }
        });

        if ($isDuplicate) {
            return back()->withErrors(['email' => 'このメールアドレスは既に使用されています。'])->withInput();
        }

        // メールアドレスの暗号化
        $encryptedEmail = Crypt::encryptString($request->email);

        // パスワードのハッシュ化（nullでない場合のみ）
        $hashedPassword = $request->filled('password') ? Hash::make($request->password) : $member->password;


        // telを結合
        $fullTel = implode('-', array_filter($request->tel));
        // 生年月日を生成
        // $birth = Carbon::createFromDate(
        //     $request->input('birth_year'),
        //     $request->input('birth_month'),
        //     $request->input('birth_day')
        // )->format('Y-m-d');


        // 更新
        $member->update([
            'kind' => $request->kind,
            'name' => $request->name,
            'nickname' => $request->nickname,
            'member_no' => $request->member_no,
            'loginid' => $request->loginid,
            'email' => $encryptedEmail,
            'gender' => $request->gender,
            'tel' => $fullTel,
            // 'birth' => $birth,
            'line' => $request->line,
            'sns' => $request->sns,
            'url' => $request->url,
            'memo' => $request->memo,
            'memo_share' => $request->memo_share,
            'password' => $hashedPassword,
        ]);

        return redirect()->route('admin.members.edit', ['member' => $member])->with('success', '会員情報が更新されました');
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

        return redirect()->route('admin.members.index')->with('success', '会員が削除されました');
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
