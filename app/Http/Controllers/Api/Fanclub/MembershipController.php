<?php

namespace App\Http\Controllers\Api\Fanclub;

use App\Http\Controllers\Controller;
use App\Http\Resources\FanclubMembershipResource;
use App\Models\Fanclub;
use App\Models\FanclubMembership;
use App\Services\Fanclub\FanclubMembershipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    public function __construct(
        private readonly FanclubMembershipService $membershipService
    ) {
    }

    public function subscribe(Request $request, Fanclub $fanclub)
    {
        $member = Auth::guard('member')->user();
        $validated = $request->validate([
            'payment_method' => ['nullable', 'in:manual,bank_transfer,card,points'],
            'source_id' => ['nullable', 'string', 'max:255'],
            'card_id' => ['nullable', 'string', 'max:255'],
            'plan_variation_id' => ['nullable', 'string', 'max:255'],
        ]);

        $membership = $this->membershipService->subscribe($fanclub, $member, $validated);

        return (new FanclubMembershipResource($membership))
            ->response()
            ->setStatusCode(201);
    }

    public function updateStatus(Request $request, Fanclub $fanclub)
    {
        $member = Auth::guard('member')->user();
        $validated = $request->validate([
            'status' => ['required', 'in:active,paused,canceled'],
        ]);

        $membership = FanclubMembership::query()
            ->where('fanclub_id', $fanclub->id)
            ->where('member_id', $member->id)
            ->firstOrFail();

        $membership = $this->membershipService->changeStatus($membership, $validated['status']);

        return new FanclubMembershipResource($membership);
    }
}
