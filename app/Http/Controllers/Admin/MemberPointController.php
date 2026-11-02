<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberPointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Member::with('memberPoints');
        
        // 検索パラメータを保存
        $searchParams = $request->only(['name', 'tel', 'member_id', 'member_no']);
        
        // 個別検索条件の処理
        if ($request->filled('member_id')) {
            $query->where('id', 'like', '%' . $request->input('member_id') . '%');
        }
        
        if ($request->filled('member_no')) {
            $query->where('member_no', 'like', '%' . $request->input('member_no') . '%');
        }
        
        // まずは暗号化されていないフィールドで絞り込み
        $members = $query->orderBy('id', 'desc')->get();
        
        // 暗号化されたフィールドでの検索（名前、電話番号）
        if ($request->filled('name') || $request->filled('tel')) {
            $filteredMembers = collect();
            
            foreach ($members as $member) {
                $match = true;
                
                // 名前検索
                if ($request->filled('name')) {
                    $nameMatch = false;
                    try {
                        $decryptedName = \Illuminate\Support\Facades\Crypt::decryptString($member->getRawOriginal('name'));
                        if (strpos($decryptedName, $request->input('name')) !== false) {
                            $nameMatch = true;
                        }
                    } catch (\Exception $e) {
                        // 暗号化されていない場合
                        if (strpos($member->name, $request->input('name')) !== false) {
                            $nameMatch = true;
                        }
                    }
                    if (!$nameMatch) {
                        $match = false;
                    }
                }
                
                // 電話番号検索
                if ($match && $request->filled('tel')) {
                    $telMatch = false;
                    try {
                        $decryptedTel = \Illuminate\Support\Facades\Crypt::decryptString($member->getRawOriginal('tel'));
                        if (strpos($decryptedTel, $request->input('tel')) !== false) {
                            $telMatch = true;
                        }
                    } catch (\Exception $e) {
                        // 暗号化されていない場合
                        if (strpos($member->tel, $request->input('tel')) !== false) {
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
        $perPage = 30;
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
        
        // 各メンバーごとのポイントの計算
        foreach ($members as $member) {
            $member->point = $member->memberPoints->sum(function ($point) {
                return $point->status === 'add' ? $point->point : -$point->point;
            });
        }
        
        $member_lists = Member::where('id', '>', 0)->orderBy('id', 'desc')->get();

        return view('admin.member_points.index', compact('members', 'member_lists', 'searchParams'));
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
    public function store(Request $request, Member $member)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
            'status' => 'required|in:add,del',
            'reason' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $member) {
            $points = $request->points;
            $status = $request->status;
            $memo = $request->reason;

            // ポイント履歴作成
            MemberPoint::create([
                'member_id' => $member->id,
                'point' => $points,
                'status' => $status,
                'memo' => $memo,
            ]);

            // 合計ポイント再計算
            $total = $member->memberPoints()->sum(DB::raw("CASE WHEN status = 'add' THEN point ELSE -point END"));
            $member->point = $total;
            $member->save();
        });

        return redirect()->route('admin.member_points.edit', $member->id)->with('success', 'ポイントを更新しました。');
    }

    /**
     * Display the specified resource.
     */
    public function show(MemberPoint $member_point)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        // 履歴を20件でページネーション
        $pointHistories = $member->memberPoints()->orderBy('created_at', 'desc')->paginate(20);

        // 合計ポイントを計算
        $total = $member->memberPoints->sum(function ($point) {
            return $point->status === 'add' ? $point->point : -$point->point;
        });

        // 合計ポイントをmembersテーブルに保存
        $member->point = $total;
        $member->save();

        return view('admin.member_points.edit', compact('member', 'pointHistories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $historyId)
    {
        $request->validate([
            'memo' => 'nullable|string|max:255',
        ]);
        $history = MemberPoint::findOrFail($historyId);
        $history->memo = $request->memo;
        $history->save();
        return back()->with('success', 'メモを更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($historyId)
    {
        $history = MemberPoint::findOrFail($historyId);
        $member = $history->member;
        // 巻き戻し
        if ($history->status === 'add') {
            $member->point -= $history->point;
        } else {
            $member->point += $history->point;
        }
        $member->save();
        $history->delete();
        return back()->with('success', '履歴を削除し、ポイントを元に戻しました。');
    }

    public function suggest(Request $request)
    {
        $keyword = $request->input('q');

        if (!$keyword) {
            return response()->json([]);
        }

        $members = Member::where('name', 'like', "%{$keyword}%")
            ->orWhere('tel', 'like', "%{$keyword}%")
            ->orWhere('id', 'like', "%{$keyword}%")
            ->orWhere('member_no', 'like', "%{$keyword}%")
            ->limit(10)
            ->get(['id', 'name', 'tel', 'member_no']);

        return response()->json($members);
    }


    // public function suggest(Request $request)
    // {
    //     $keyword = $request->input('q');

    //     if (!$keyword) {
    //         return response()->json([]);
    //     }

    //     $results = Member::where('name', 'like', "%{$keyword}%")
    //         ->orWhere('email', 'like', "%{$keyword}%")
    //         ->orWhere('tel', 'like', "%{$keyword}%")
    //         ->limit(10)
    //         ->get(['id', 'name', 'email', 'tel']);

    //     return response()->json($results);
    // }


}
