<?php

namespace App\Policies;

use App\Models\Fanclub;
use Illuminate\Contracts\Auth\Authenticatable;

class FanclubPolicy
{
    public function update(Authenticatable $user, Fanclub $fanclub): bool
    {
        return (int)$fanclub->cast_id === (int)$user->id;
    }

    public function viewMembers(Authenticatable $user, Fanclub $fanclub): bool
    {
        return (int)$fanclub->cast_id === (int)$user->id;
    }

    public function adminManage(Authenticatable $user): bool
    {
        return str_contains(get_class($user), '\\Admin');
    }
}

