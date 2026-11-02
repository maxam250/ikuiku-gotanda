<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Faker\Factory as Faker;

class MembersTableSeeder extends Seeder
{
    private function generateRomanizedId($length = 6)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $id = '';
        for ($i = 0; $i < $length; $i++) {
            $id .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $id;
    }

    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        // 管理者アカウント
        Member::create([
            'name' => '管理者',
            'nickname' => 'admin',
            'member_no' => 'A001',
            'email' => Crypt::encryptString('admin@example.com'),
            'tel' => '090-0000-0000',
            'gender' => 1,
            'line' => 'admin_line',
            'sns' => 'admin_sns',
            'url' => 'https://example.com',
            'kind' => 1,
            'loginid' => 'admin',
            'password' => Hash::make('password'),
            'memo' => '管理者アカウント',
            'memo_share' => '共有メモ',
        ]);

        // ダミーデータ（9件）
        $lastNames = ['佐藤', '鈴木', '高橋', '田中', '伊藤', '渡辺', '山本', '中村', '小林'];
        $firstNames = ['翔太', '健一', '大輔', '直樹', '拓也', '誠', '隆', '優', '剛'];
        $nicknames = ['社長', '部長', '課長', '主任', '係長', '専務', '常務', '取締役', 'マネージャー'];
        $jobs = ['会社経営', 'IT企業', '金融', '不動産', '商社', 'メーカー', 'コンサル', '広告代理店', '建設'];

        for ($i = 0; $i < 9; $i++) {
            $userId = $this->generateRomanizedId();
            $email = $userId . '@example.com';
            $encryptedEmail = Crypt::encryptString($email);

            Member::create([
                'name' => $lastNames[$i] . ' ' . $firstNames[$i],
                'nickname' => $nicknames[$i] . 'さん',
                'member_no' => 'M' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'email' => $encryptedEmail,
                'tel' => '0' . $faker->numberBetween(7, 9) . '0-' . $faker->randomNumber(4, true) . '-' . $faker->randomNumber(4, true),
                'gender' => 1, // 男性
                'line' => $userId . '_line',
                'sns' => '@' . $userId . '_' . $faker->randomNumber(4, true),
                'url' => 'https://instagram.com/' . $userId,
                'kind' => $faker->numberBetween(0, 2),
                'loginid' => $userId,
                'password' => Hash::make('password'),
                'memo' => $jobs[$i] . "関連。" . $faker->realText(80),
                'memo_share' => $faker->realText(100),
            ]);
        }
    }
}
