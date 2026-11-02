<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\PointHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        $members = $query->orderBy('name')->paginate(10);

        return view('admin.points.index', compact('members'));
    }

    public function edit(Member $member)
    {
        $pointHistories = PointHistory::with('admin')
            ->where('member_id', $member->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.points.edit', compact('member', 'pointHistories'));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
            'operation' => 'required|in:add,subtract',
            'reason' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $member) {
            $points = $request->points;
            if ($request->operation === 'subtract') {
                if ($member->points < $points) {
                    throw new \Exception('ポイントが不足しています。');
                }
                $points = -$points;
            }

            $member->points += $points;
            $member->save();

            PointHistory::create([
                'member_id' => $member->id,
                'admin_id' => auth()->id(),
                'points' => abs($points),
                'operation' => $request->operation,
                'reason' => $request->reason,
            ]);
        });

        return redirect()
            ->route('admin.member_points.edit', $member)
            ->with('success', 'ポイントを更新しました。');
    }

    public function suggest(Request $request)
    {
        $query = $request->get('query');
        
        $members = Member::where('name', 'like', '%' . $query . '%')
            ->select('id', 'name', 'phone', 'points')
            ->limit(10)
            ->get();

        return response()->json($members);
    }
} 