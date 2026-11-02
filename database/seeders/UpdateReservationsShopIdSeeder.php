<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateReservationsShopIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存の予約データにshop_idを設定
        \DB::table('reservations')
            ->whereNull('shop_id')
            ->orWhere('shop_id', 0)
            ->update(['shop_id' => 3]);
            
        echo "Updated reservations with shop_id = 3\n";
    }
}
