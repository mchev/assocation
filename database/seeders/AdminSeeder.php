<?php

namespace Database\Seeders;

use App\Models\Depot;
use App\Models\Equipment;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the admin user and his organization.
     */
    public function run(): void
    {
        // Création de Martin comme admin
        $martin = User::factory()->create([
            'name' => 'Martin',
            'email' => 'martin@pegase.io',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Création de l'organisation de Martin (Pégase)
        $pegase = Organization::factory()->create([
            'name' => 'Pégase',
            'description' => 'Organisation de Martin',
            'email' => 'martin@pegase.io',
            'website' => 'https://pegase.io',
            'owner_id' => $martin->id,
        ]);

        // Mettre à jour l'organisation courante de Martin
        $martin->update(['current_organization_id' => $pegase->id]);

        // Attacher Martin à son organisation
        $martin->organizations()->attach($pegase->id, ['role' => 'admin']);

        // Création des dépôts pour Pégase
        $pegaseDepots = Depot::factory(1)->create([
            'organization_id' => $pegase->id,
        ]);

        // // Création des équipements pour Pégase (répartis dans les catégories)
        // foreach (Category::all() as $category) {
        //     Equipment::factory(4)->create([
        //         'organization_id' => $pegase->id,
        //         'category_id' => $category->id,
        //         'depot_id' => $pegaseDepots->random()->id,
        //     ]);
        // }
    }
}
