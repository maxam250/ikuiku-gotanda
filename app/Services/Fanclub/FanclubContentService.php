<?php

namespace App\Services\Fanclub;

use App\Models\FanclubContent;
use App\Models\FanclubContentChild;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class FanclubContentService
{
    public function create(Item $cast, array $attributes): FanclubContent
    {
        return DB::transaction(function () use ($cast, $attributes) {
            return FanclubContent::create([
                'cast_id' => $cast->id,
                'category_id' => $attributes['category_id'] ?? null,
                'title' => $attributes['title'],
                'description' => $attributes['description'] ?? null,
                'scheduled_at' => $attributes['scheduled_at'] ?? null,
                'next_release_at' => self::normalizeNextReleaseMemo($attributes['next_release_at'] ?? null),
                'published_at' => $attributes['published_at'] ?? null,
            ]);
        });
    }

    public function update(FanclubContent $content, array $attributes): FanclubContent
    {
        return DB::transaction(function () use ($content, $attributes) {
            $content->update([
                'category_id' => $attributes['category_id'] ?? null,
                'title' => $attributes['title'],
                'description' => $attributes['description'] ?? null,
                'scheduled_at' => $attributes['scheduled_at'] ?? null,
                'next_release_at' => self::normalizeNextReleaseMemo($attributes['next_release_at'] ?? null),
                'published_at' => $attributes['published_at'] ?? null,
            ]);

            return $content;
        });
    }

    private static function normalizeNextReleaseMemo(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (! is_string($value)) {
            return null;
        }
        $t = trim($value);

        return $t === '' ? null : $t;
    }

    public function publishDueContents(): int
    {
        $a = FanclubContent::query()
            ->whereNull('published_at')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->update(['published_at' => now()]);

        $b = FanclubContentChild::query()
            ->whereNull('published_at')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->update(['published_at' => now()]);

        return (int) $a + (int) $b;
    }
}

