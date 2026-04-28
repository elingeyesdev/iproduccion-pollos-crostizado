<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Role::firstOrCreate(['name' => 'administrador']);
        Role::firstOrCreate(['name' => 'venta']);
        Role::firstOrCreate(['name' => 'cocina']);
    }
}
