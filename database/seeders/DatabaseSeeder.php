<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,    // Création des catégories d'équipement
            AdminSeeder::class,       // Création de Martin et Pégase
            // OrganizationSeeder::class, // Création des autres organisations
            // ReservationSeeder::class,  // Création des réservations
        ]);
    }
}
