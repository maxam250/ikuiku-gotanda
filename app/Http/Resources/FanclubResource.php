<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FanclubResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cast_id' => $this->cast_id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => (int)$this->price,
            'status' => $this->status,
            'cast' => $this->whenLoaded('cast', fn () => [
                'id' => $this->cast?->id,
                'name' => $this->cast?->name,
            ]),
            'membership' => $this->whenLoaded('memberships', function () {
                $membership = $this->memberships->first();
                return $membership ? new FanclubMembershipResource($membership) : null;
            }),
        ];
    }
}
