<?php

namespace App\Services;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * アップロード済み画像を 80 KB 以内に収めるユーティリティ。
 *
 * 処理フロー:
 *  1. ファイルサイズが上限以下ならそのまま終了
 *  2. 長辺が 800 px を超える場合はアスペクト比を保って縮小
 *  3. JPEG 品質 78 で再エンコードして上書き保存
 */
class ImageCompressor
{
    /** 許容する最大ファイルサイズ (KB) */
    public const MAX_KB = 80;

    /** 最大辺長 (px) */
    public const MAX_DIMENSION = 800;

    /** JPEG 品質 */
    public const QUALITY = 78;

    /**
     * 必要に応じて画像を圧縮・縮小する。
     *
     * @param string $absolutePath 保存済み画像の絶対パス
     */
    public static function compressIfNeeded(string $absolutePath): void
    {
        if (!file_exists($absolutePath)) {
            return;
        }

        if (filesize($absolutePath) <= self::MAX_KB * 1024) {
            return;
        }

        try {
            $manager = new ImageManager(new Driver());
            $img = $manager->read($absolutePath);

            // 長辺が上限を超えていればアスペクト比を保って縮小
            if ($img->width() > self::MAX_DIMENSION || $img->height() > self::MAX_DIMENSION) {
                if ($img->width() >= $img->height()) {
                    $img->scale(width: self::MAX_DIMENSION);
                } else {
                    $img->scale(height: self::MAX_DIMENSION);
                }
            }

            // JPEG で再エンコードして上書き保存
            $img->toJpeg(self::QUALITY)->save($absolutePath);
        } catch (\Throwable $e) {
            \Log::warning('ImageCompressor: 圧縮に失敗しました', [
                'path'  => $absolutePath,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
