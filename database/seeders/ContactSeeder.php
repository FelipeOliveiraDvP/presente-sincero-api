<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 20; $i++) {
            $contact_ends = $i < 10 ? "0{$i}" : $i;

            DB::table('contacts')->insert([
                'contest_id'    => $i,
                'name'          => 'Contato Exemplo',
                'value'         => "119999955{$contact_ends}"
            ]);
        }
    }
}
