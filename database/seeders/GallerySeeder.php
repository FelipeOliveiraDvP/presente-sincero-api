<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 20; $i++) {
            DB::table('gallery')->insert([
                'contest_id'    => $i,
                'image_path'    => 'https://picsum.photos/600/600?random=1',
                'thumbnail'     => true,
            ]);

            DB::table('gallery')->insert([
                'contest_id'    => $i,
                'image_path'    => 'https://picsum.photos/600/600?random=2',
            ]);

            DB::table('gallery')->insert([
                'contest_id'    => $i,
                'image_path'    => 'https://picsum.photos/600/600?random=3',
            ]);
        }
    }
}
