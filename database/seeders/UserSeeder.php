<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name'      => 'PS Admin',
            'whatsapp'  => App::environment('local') ? '11964513763' : '15997237158',
            'email'     => App::environment('local') ? 'admin@email.com' : 'admin@presentesincero.com',
            'password'  => Hash::make('asdf1234'),
            'role_id'   => 1,
        ]);

        DB::table('users')->insert([
            'name'      => 'Cliente de teste',
            'whatsapp'  => App::environment('local') ? '11999994444' : '11999995555',
            'email'     => App::environment('local') ? 'cliente@email.com' : 'cliente@presentesincero.com',
            'password'  => Hash::make('asdf1234'),
            'role_id'   => 2,
            'avatar'    => 'https://picsum.photos/600/600?random=3'
        ]);
    }
}
