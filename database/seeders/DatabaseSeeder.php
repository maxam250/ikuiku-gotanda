<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminsTableSeeder::class,
            MembersTableSeeder::class,
            ItemsTableSeeder::class,
            MemberPointsTableSeeder::class,
            GalleriesTableSeeder::class,
            ReservationHistorySeeder::class,
        ]);
    }
}
