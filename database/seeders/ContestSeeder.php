<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContestSeeder extends Seeder
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
            $title = "Sorteio de exemplo #{$i}";
            $contest_interval = $i + 30;

            DB::table('contests')->insert([
                'title'             => $title,
                'slug'              => Str::slug($title),
                'short_description' => $faker->sentence(12),
                'full_description'  => $faker->paragraphs(2, true),
                'start_date'        => Carbon::now()->addDays($i)->toDateTimeString(),
                'days_to_end'       => $contest_interval,
                'contest_date'      => Carbon::now()->addDays($contest_interval)->toDateTimeString(),
            ]);
        }
    }
}
