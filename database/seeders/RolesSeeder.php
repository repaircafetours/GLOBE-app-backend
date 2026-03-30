<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("roles")->insertOrIgnore([
            ["id" => 1, "name" => "Administrateur"],
            ["id" => 2, "name" => "Intendance"],
            ["id" => 3, "name" => "Opérationel"],
            ["id" => 4, "name" => "Réparateur"],
        ]);
    }
}
