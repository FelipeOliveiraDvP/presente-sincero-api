<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'role_id' => (string) Uuid::uuid4(),
            'name'    => 'Administrador'
        ]);

        DB::table('roles')->insert([
            'role_id' => (string) Uuid::uuid4(),
            'name'    => 'Cliente'
        ]);
    }
}
