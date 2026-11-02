<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FanclubContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cast_id' => $this->cast_id,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ]),
            'title' => $this->title,
            'description' => $this->description,
            'is_paid' => (bool)$this->is_paid,
            'scheduled_at' => optional($this->scheduled_at)?->toIso8601String(),
            'next_release_at' => $this->next_release_at,
            'published_at' => optional($this->published_at)?->toIso8601String(),
            'children' => FanclubContentChildResource::collection($this->whenLoaded('children')),
            'children_count' => $this->when(isset($this->children_count), (int) $this->children_count),
        ];
    }
}
