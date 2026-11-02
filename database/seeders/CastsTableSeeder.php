<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CastsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // サンプルデータを挿入
        DB::table('casts')->insert([
            ['cast_name' => 'あい', 'cast_height' => '181', 'cast_age' => 18],
            ['cast_name' => 'うえお', 'cast_height' => '182', 'cast_age' => 22],
            ['cast_name' => 'かきく', 'cast_height' => '183', 'cast_age' => 16],
            ['cast_name' => 'けこ', 'cast_height' => '148', 'cast_age' => 25],
        ]);
    }
}
