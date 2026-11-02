<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FanclubContentChildResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fanclub_content_id' => $this->fanclub_content_id,
            'title' => $this->title,
            'description' => $this->description,
            'sort' => (int) $this->sort,
            'scheduled_at' => optional($this->scheduled_at)?->toIso8601String(),
            'published_at' => optional($this->published_at)?->toIso8601String(),
            'teaser_enabled' => (bool) $this->teaser_enabled,
            'teaser_style' => $this->teaser_style,
            'teaser_image_url' => $this->teaser_image_path ? $this->teaserImageUrl() : null,
            'teaser_reveal_at' => optional($this->teaser_reveal_at)?->toIso8601String(),
            'teaser_promo_note' => $this->teaser_promo_note,
            'teaser_blocks_main' => $this->teaserBlocksMain(),
            'items' => FanclubContentItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
