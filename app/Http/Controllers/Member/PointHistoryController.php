<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MemberPoint;

class PointHistoryController extends Controller
{
    /**
     * ポイント履歴を表示
     */
    public function index()
    {
        $memberId = Auth::id();
        
        // ポイント履歴を取得（新しい順）
        $pointHistory = MemberPoint::where('member_id', $memberId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('member.point_history', compact('pointHistory'));
    }
} 