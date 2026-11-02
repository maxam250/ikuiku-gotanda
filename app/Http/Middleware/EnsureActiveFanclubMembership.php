<?php

namespace App\Http\Middleware;

use App\Models\FanclubMembership;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveFanclubMembership
{
    /**
     * ファンクラブIDをルートパラメータ {fanclub} から取得し、
     * 有効な会員（active かつ 停止期間外）のみ通過させる。
     */
    public function handle(Request $request, Closure $next): Response
    {
        $member = Auth::guard('member')->user();
        if (!$member) {
            return redirect()->route('member.login');
        }

        $fanclub = $request->route('fanclub');
        $fanclubId = is_object($fanclub) ? $fanclub->id : (int) $fanclub;

        $membership = FanclubMembership::where('fanclub_id', $fanclubId)
            ->where('member_id', $member->id)
            ->first();

        if (!$membership || $membership->status !== 'active') {
            abort(403, 'ファンクラブ会員のみアクセスできます。');
        }

        if ($membership->isSuspended()) {
            abort(403, 'アカウントは ' . $membership->suspended_until->format('Y年m月d日') . ' まで利用停止中です。');
        }

        return $next($request);
    }
}
