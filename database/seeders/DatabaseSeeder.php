<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ImageSeeder::class,
            ContestSeeder::class,
            ContactSeeder::class,
            GallerySeeder::class,
            NumbersSeeder::class,
            RewardSeeder::class,
        ]);
    }
}
