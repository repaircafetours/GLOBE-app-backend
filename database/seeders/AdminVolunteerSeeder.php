<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminVolunteerSeeder extends Seeder
{
    /**
     * Creates or updates the default administrator volunteer.
     *
     * - On a fresh install: creates the volunteer with id = 1.
     * - On an existing database with a volunteer at id 1: updates its
     *   fields to make it the admin (login, password, idHumHub).
     *
     * Credentials: login = "admin" / password = "password"
     */
    public function run(): void
    {
        // Upsert basé sur l'id pour garantir id = 1 sur une install vierge
        // et mettre à jour le volunteer existant le cas échéant.
        DB::table("volunteers")->upsert(
            [
                "id" => 1,
                "idHumHub" => 0,
                "login" => "admin",
                "password" => Hash::make("password"),
                "extra_attributes" => "{}",
            ],
            ["id"],
            ["idHumHub", "login", "password", "extra_attributes"],
        );

        DB::table("volunteer_roles")->insertOrIgnore([
            "volunteer_id" => 1,
            "role_id" => 1, // Administrateur
        ]);
    }
}
