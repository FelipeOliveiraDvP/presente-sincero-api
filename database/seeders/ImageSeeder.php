<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('images')->insert([
            'title' => 'Imagem exemplo',
            'path'  => 'https://picsum.photos/600/600?random=1'
        ]);

        DB::table('images')->insert([
            'title' => 'Imagem exemplo',
            'path'  => 'https://picsum.photos/600/600?random=2'
        ]);

        DB::table('images')->insert([
            'title' => 'Imagem exemplo',
            'path'  => 'https://picsum.photos/600/600?random=3'
        ]);
    }
}
