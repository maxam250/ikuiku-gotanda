<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MemberPoint;
use App\Models\Member;
use Faker\Factory as Faker;
use Carbon\Carbon;

class MemberPointsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ja_JP');
        $members = Member::all();

        foreach ($members as $member) {
            // 各メンバーに3-10個のポイント履歴を作成
            $pointCount = $faker->numberBetween(3, 10);

            for ($i = 0; $i < $pointCount; $i++) {
                $date = Carbon::now()->subDays($faker->numberBetween(0, 365));
                $point = $faker->numberBetween(100, 1000);
                $status = $faker->randomElement(['add', 'del']);

                MemberPoint::create([
                    'member_id' => $member->id,
                    'point' => $point,
                    'status' => $status,
                    'memo' => $faker->text(100),
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }
}
