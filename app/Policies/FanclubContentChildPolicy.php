<?php

namespace App\Policies;

use App\Models\FanclubContentChild;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class FanclubContentChildPolicy
{
    public function manage(Authenticatable $user, FanclubContentChild $child): bool
    {
        if (Auth::guard('admin')->check()) {
            return true;
        }

        return (int) $child->parent?->cast_id === (int) $user->id;
    }
}
