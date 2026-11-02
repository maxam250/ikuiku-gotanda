<?php

namespace App\Http\Controllers\Api\Fanclub;

use App\Http\Controllers\Controller;
use App\Http\Resources\FanclubResource;
use App\Models\Fanclub;
use Illuminate\Support\Facades\Auth;

class FanclubController extends Controller
{
    public function index()
    {
        $member = Auth::guard('member')->user();

        $fanclubs = Fanclub::query()
            ->with([
                'cast',
                'memberships' => function ($query) use ($member) {
                    if ($member) {
                        $query->where('member_id', $member->id);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                },
            ])
            ->where('status', '!=', 'canceled')
            ->latest()
            ->paginate(24);

        return FanclubResource::collection($fanclubs);
    }

    public function show(Fanclub $fanclub)
    {
        $member = Auth::guard('member')->user();

        $fanclub->load([
            'cast',
            'memberships' => function ($query) use ($member) {
                if ($member) {
                    $query->where('member_id', $member->id);
                } else {
                    $query->whereRaw('1 = 0');
                }
            },
        ]);

        return new FanclubResource($fanclub);
    }
}
