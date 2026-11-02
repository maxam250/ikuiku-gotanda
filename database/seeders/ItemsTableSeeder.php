<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use Faker\Factory as Faker;

class ItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        // 基本キャスト（3名）
        $items = [
            [
                'item_name' => '椎名 さくら',
                'item_active' => 1,
                'item_comment' => "清楚で可愛らしい雰囲気の癒し系キャスト。\n身長：158cm\n趣味：お菓子作り、カフェ巡り\n特技：ピアノ、フラワーアレンジメント\n\n明るく優しい性格で、初めての方でもリラックスしてお過ごしいただけます。",
                'item_sort' => 1,
            ],
            [
                'item_name' => '白石 みお',
                'item_active' => 1,
                'item_comment' => "知的で落ち着いた雰囲気の大人系キャスト。\n身長：165cm\n趣味：読書、アロマテラピー\n特技：英会話、ヨガ\n\nしっとりとした癒しの時間をご提供いたします。",
                'item_sort' => 2,
            ],
            [
                'item_name' => '星野 ゆめ',
                'item_active' => 1,
                'item_comment' => "元気いっぱいの笑顔が魅力的なアイドル系キャスト。\n身長：156cm\n趣味：ダンス、コスプレ\n特技：歌、メイク\n\n楽しい会話と笑顔で、素敵なひとときをお約束します。",
                'item_sort' => 3,
            ],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }

        // ダミーデータ（7件）
        $lastNames = ['桜井', '藤原', '成瀬', '葵', '小鳥遊', '月島', '綾瀬'];
        $firstNames = ['ゆい', 'れい', 'みく', 'かれん', 'ひな', 'あおい', 'ことは'];
        $personalities = ['お姉さん系', '妹系', 'クール系', '癒し系', 'モデル系', '清楚系', 'アイドル系'];
        $hobbies = [
            ['音楽鑑賞', 'ショッピング'],
            ['料理', 'ガーデニング'],
            ['写真', 'アート'],
            ['旅行', 'カフェ巡り'],
            ['ファッション', 'ネイル'],
            ['映画鑑賞', 'スイーツ作り'],
            ['ダンス', 'カラオケ']
        ];
        $skills = [
            ['ピアノ', 'マッサージ'],
            ['お菓子作り', 'アロマ'],
            ['イラスト', 'メイク'],
            ['占い', 'ストレッチ'],
            ['ファッションコーディネート', '語学'],
            ['フラワーアレンジメント', 'テーブルコーディネート'],
            ['歌唱', 'ダンス']
        ];
        $heights = [158, 162, 156, 167, 170, 155, 160];

        for ($i = 0; $i < 7; $i++) {
            $hobby = $hobbies[$i];
            $skill = $skills[$i];

            Item::create([
                'item_name' => $lastNames[$i] . ' ' . $firstNames[$i],
                'item_active' => 1,
                'item_comment' => sprintf(
                    "%s担当の%s。\n\n身長：%dcm\n趣味：%s、%s\n特技：%s、%s\n\n%s",
                    $personalities[$i],
                    $faker->realText(20),
                    $heights[$i],
                    $hobby[0],
                    $hobby[1],
                    $skill[0],
                    $skill[1],
                    $faker->realText(100)
                ),
                'item_sort' => $i + 4,
            ]);
        }
    }
}
