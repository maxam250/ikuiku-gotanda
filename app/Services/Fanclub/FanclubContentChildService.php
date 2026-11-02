<?php

namespace App\Services\Fanclub;

use App\Models\FanclubContent;
use App\Models\FanclubContentChild;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FanclubContentChildService
{
    public function create(FanclubContent $parent, array $attributes, array $items, array $itemFiles = [], ?UploadedFile $teaserImage = null): FanclubContentChild
    {
        return DB::transaction(function () use ($parent, $attributes, $items, $itemFiles, $teaserImage) {
            $child = FanclubContentChild::create([
                'fanclub_content_id' => $parent->id,
                'title' => $attributes['title'] ?? null,
                'description' => $attributes['description'] ?? null,
                'sort' => (int) ($attributes['sort'] ?? 0),
                'scheduled_at' => $attributes['scheduled_at'] ?? null,
                'published_at' => $attributes['published_at'] ?? null,
                'teaser_enabled' => (bool) ($attributes['teaser_enabled'] ?? false),
                'teaser_style' => $attributes['teaser_style'] ?? 'blur',
                'teaser_image_path' => null,
                'teaser_reveal_at' => $attributes['teaser_reveal_at'] ?? null,
                'teaser_promo_note' => $attributes['teaser_promo_note'] ?? null,
            ]);

            if ($teaserImage) {
                $child->update([
                    'teaser_image_path' => FanclubContentMediaHelper::storeTeaserImage($teaserImage),
                ]);
            }

            foreach ($items as $index => $item) {
                $value = FanclubContentMediaHelper::normalizeItemValue(
                    $item,
                    $itemFiles[$index]['image_file'] ?? null,
                    $itemFiles[$index]['video_file'] ?? null
                );
                $child->items()->create([
                    'type' => $item['type'],
                    'value' => $value,
                    'sort' => $item['sort'] ?? $index,
                ]);
            }

            return $child->load('items');
        });
    }

    public function update(FanclubContentChild $child, array $attributes, array $items, array $itemFiles = [], ?UploadedFile $teaserImage = null, bool $removeTeaserImage = false): FanclubContentChild
    {
        return DB::transaction(function () use ($child, $attributes, $items, $itemFiles, $teaserImage, $removeTeaserImage) {
            $data = [
                'title' => $attributes['title'] ?? null,
                'description' => $attributes['description'] ?? null,
                'sort' => (int) ($attributes['sort'] ?? $child->sort),
                'scheduled_at' => $attributes['scheduled_at'] ?? null,
                'published_at' => $attributes['published_at'] ?? null,
                'teaser_enabled' => (bool) ($attributes['teaser_enabled'] ?? false),
                'teaser_style' => $attributes['teaser_style'] ?? 'blur',
                'teaser_reveal_at' => $attributes['teaser_reveal_at'] ?? null,
                'teaser_promo_note' => $attributes['teaser_promo_note'] ?? null,
            ];

            // ティザー画像の古いファイルを削除
            if ($removeTeaserImage) {
                $this->deleteFile($child->teaser_image_path);
                $data['teaser_image_path'] = null;
            } elseif ($teaserImage) {
                $this->deleteFile($child->teaser_image_path);
                $data['teaser_image_path'] = FanclubContentMediaHelper::storeTeaserImage($teaserImage);
            } elseif (array_key_exists('teaser_image_path', $attributes)) {
                $data['teaser_image_path'] = $attributes['teaser_image_path'];
            }

            $child->update($data);

            // 古いアイテムのファイルを収集してから削除
            $child->load('items');
            $newImagePaths = collect($items)->map(fn($i) => $i['value']['path'] ?? null)->filter()->values()->toArray();
            $newVideoPaths = collect($items)->map(fn($i) => $i['value']['video_path'] ?? null)->filter()->values()->toArray();
            foreach ($child->items as $oldItem) {
                $oldValue = $oldItem->value ?? [];
                if (!empty($oldValue['path']) && !in_array($oldValue['path'], $newImagePaths)) {
                    $this->deleteFile($oldValue['path']);
                }
                if (!empty($oldValue['video_path']) && !in_array($oldValue['video_path'], $newVideoPaths)) {
                    $this->deleteFile($oldValue['video_path']);
                }
            }

            $child->items()->delete();

            foreach ($items as $index => $item) {
                $imageFile = $itemFiles[$index]['image_file'] ?? null;
                $videoFile = $itemFiles[$index]['video_file'] ?? null;
                $value = FanclubContentMediaHelper::normalizeItemValue(
                    $item,
                    $imageFile instanceof UploadedFile ? $imageFile : null,
                    $videoFile instanceof UploadedFile ? $videoFile : null
                );
                $child->items()->create([
                    'type' => $item['type'],
                    'value' => $value,
                    'sort' => $item['sort'] ?? $index,
                ]);
            }

            return $child->load('items');
        });
    }

    public function deleteWithFiles(FanclubContentChild $child): void
    {
        $child->load('items');

        // アイテムの画像・動画ファイルを削除
        foreach ($child->items as $item) {
            $value = $item->value ?? [];
            if (!empty($value['path'])) {
                $this->deleteFile($value['path']);
            }
            if (!empty($value['video_path'])) {
                $this->deleteFile($value['video_path']);
            }
        }

        // ティザー画像を削除
        $this->deleteFile($child->teaser_image_path);

        $child->delete();
    }

    private function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
