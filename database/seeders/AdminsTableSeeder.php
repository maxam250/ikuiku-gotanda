<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminsTableSeeder extends Seeder
{
    public function run(): void
    {
        // 既存データを削除し、必ず1件だけadminを作成
        DB::table('admins')->truncate();
        DB::table('admins')->insert([
            'name' => 'admin',
            'login_name' => 'admin',
            'email' => 'kagawa@1creation.co.jp',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
