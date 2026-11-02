<?php

namespace App\Policies;

use App\Models\FanclubContent;
use App\Models\FanclubMembership;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class FanclubContentPolicy
{
    public function manage(Authenticatable $user, FanclubContent $content): bool
    {
        if (Auth::guard('admin')->check()) {
            return true;
        }

        return (int)$content->cast_id === (int)$user->id;
    }

    public function view(Authenticatable $user, FanclubContent $content): bool
    {
        if (!$content->is_paid) {
            return true;
        }

        if ((int)$content->cast_id === (int)$user->id) {
            return true;
        }

        $membership = FanclubMembership::query()
            ->where('member_id', $user->id)
            ->whereHas('fanclub', function ($query) use ($content) {
                $query->where('cast_id', $content->cast_id);
            })
            ->where('status', 'active')
            ->first();

        return $membership !== null;
    }
}

