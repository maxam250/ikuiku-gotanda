<?php

namespace App\Services\Fanclub;

use App\Services\ImageCompressor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class FanclubContentMediaHelper
{
    public static function normalizeItemValue(array $item, mixed $imageFile, mixed $videoFile = null, string $diskDirectory = 'fanclub-content'): array
    {
        $value = $item['value'] ?? [];
        if (!is_array($value)) {
            $value = ['body' => (string) $value];
        }

        // 削除チェックボックス処理
        if (!empty($item['remove_image'])) {
            unset($value['path'], $value['url'], $value['name']);
        }
        if (!empty($item['remove_video'])) {
            unset($value['video_path'], $value['video_url'], $value['video_name']);
        }

        if ($imageFile instanceof UploadedFile) {
            $storedPath = $imageFile->store($diskDirectory, 'public');
            $fullPath = storage_path('app/public/' . $storedPath);
            ImageCompressor::compressIfNeeded($fullPath);
            $value['path'] = $storedPath;
            $value['url'] = Storage::disk('public')->url($storedPath);
            $value['name'] = $imageFile->getClientOriginalName();
        }

        if ($videoFile instanceof UploadedFile) {
            $storedPath = $videoFile->store($diskDirectory . '/video', 'public');
            $value['video_path'] = $storedPath;
            $value['video_url'] = Storage::disk('public')->url($storedPath);
            $value['video_name'] = $videoFile->getClientOriginalName();
        }

        return $value;
    }

    public static function storeTeaserImage(UploadedFile $file): string
    {
        $storedPath = $file->store('fanclub-content/teaser', 'public');
        $fullPath = storage_path('app/public/' . $storedPath);
        ImageCompressor::compressIfNeeded($fullPath);

        return $storedPath;
    }
}
