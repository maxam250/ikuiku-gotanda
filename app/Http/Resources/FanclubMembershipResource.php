<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FanclubMembershipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fanclub_id' => $this->fanclub_id,
            'member_id' => $this->member_id,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'started_at' => optional($this->started_at)?->toIso8601String(),
            'ended_at' => optional($this->ended_at)?->toIso8601String(),
            'last_paid_at' => optional($this->last_paid_at)?->toIso8601String(),
            'next_billing_at' => optional($this->next_billing_at)?->toIso8601String(),
        ];
    }
}
