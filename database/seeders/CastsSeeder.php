<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CastsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('casts')->insert([
            ['cast_name' => 'John Doe', 'cast_shoes' => 'Lead Actor', 'cast_age' => 35],
            ['cast_name' => 'Jane Smith', 'cast_shoes' => 'Supporting Actor', 'cast_age' => 28],
            ['cast_name' => 'Paul Brown', 'cast_shoes' => 'Director', 'cast_age' => 45],
        ]);
    }
}
