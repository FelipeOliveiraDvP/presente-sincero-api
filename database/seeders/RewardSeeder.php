<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 20; $i++) {
            DB::table('rewards')->insert([
                'contest_id'    => $i,
                'number'        => '0155',
                'description'   => 'Prêmio de R$ 500,00',
            ]);

            DB::table('rewards')->insert([
                'contest_id'    => $i,
                'number'        => '0602',
                'description'   => 'Prêmio de R$ 250,00',
            ]);

            DB::table('rewards')->insert([
                'contest_id'    => $i,
                'number'        => '0789',
                'description'   => 'Prêmio de R$ 100,00',
            ]);
        }
    }
}
