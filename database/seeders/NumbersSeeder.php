<?php

namespace Database\Seeders;

use App\Enums\NumberStatus;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NumbersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 20; $i++) {
            $start = 0;
            $quantity = 1000;

            $numbers = [];

            for ($j = $start; $j <= $quantity; $j++) {
                $number_length = strlen((string) $quantity);

                $number = json_encode([
                    'number'     => str_pad($j, $number_length, '0', STR_PAD_LEFT),
                    'status'     => NumberStatus::FREE,
                    'customer_id' => null
                ]);

                $numbers[] = $number;
            }

            DB::table('numbers')->insert([
                'contest_id'    => $i,
                'price'         => $faker->randomFloat(0, 5.00, 200.00),
                'start'         => $start,
                'quantity'      => $quantity,
                'per_customer'  => 10,
                'numbers'       => json_encode($numbers)
            ]);
        }
    }
}
