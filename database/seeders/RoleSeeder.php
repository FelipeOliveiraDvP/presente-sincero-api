<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::insert([
            'id' => Str::uuid(),
            'identifier' => 'admin',
            'name' => 'Administrador',
        ]);

        Role::insert([
            'id' => Str::uuid(),
            'identifier' => 'seller',
            'name' => 'Vendedor',
        ]);

        Role::insert([
            'id' => Str::uuid(),
            'identifier' => 'customer',
            'name' => 'Cliente',
        ]);
    }
}
