<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gallery;
use App\Models\GalleryImage;
use Faker\Factory as Faker;

class GalleriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        // メンバーごとにギャラリーを作成（ID: 1-10）
        for ($memberId = 1; $memberId <= 10; $memberId++) {
            // 各メンバーに1-3個のギャラリーを作成
            $galleryCount = rand(1, 3);

            for ($i = 0; $i < $galleryCount; $i++) {
                $gallery = Gallery::create([
                    'member_id' => $memberId,
                    'item_id' => null,
                    'active' => 1,
                    'title' => $faker->sentence(3),
                    'description' => $faker->paragraph(2),
                    'sort' => $i + 1,
                    'flag' => 0,
                ]);

                // 各ギャラリーに2-5枚の画像を追加
                $imageCount = rand(2, 5);
                for ($j = 0; $j < $imageCount; $j++) {
                    GalleryImage::create([
                        'gallery_id' => $gallery->id,
                        'filename' => 'dummy/gallery/image' . rand(1, 10) . '.jpg',
                        'caption' => $faker->sentence(),
                        'sort' => $j + 1,
                    ]);
                }
            }
        }
    }
}
