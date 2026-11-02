<?php

namespace App\Http\Controllers\Api\Fanclub;

use App\Http\Controllers\Controller;
use App\Http\Resources\FanclubContentResource;
use App\Models\Fanclub;
use App\Models\FanclubMembership;
use Illuminate\Support\Facades\Auth;

class ContentController extends Controller
{
    public function index(Fanclub $fanclub)
    {
        $member = Auth::guard('member')->user();

        $membership = FanclubMembership::query()
            ->where('fanclub_id', $fanclub->id)
            ->when($member, fn ($q) => $q->where('member_id', $member->id))
            ->first();

        $canViewPaid = $membership && $membership->status === 'active';

        $contents = $fanclub->contents()
            ->with([
                'category',
                'children' => function ($q) {
                    $q->visible()->orderBy('sort')->orderBy('id')->with('items');
                },
            ])
            ->visible()
            ->when(!$canViewPaid, fn ($q) => $q->where('is_paid', false))
            ->latest()
            ->paginate(20);

        return FanclubContentResource::collection($contents);
    }
}
