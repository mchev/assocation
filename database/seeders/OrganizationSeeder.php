<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Depot;
use App\Models\Equipment;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Seed fake organizations with their owners and equipment.
     */
    public function run(): void
    {
        // Création des organisations avec leurs propriétaires
        for ($i = 0; $i < 5; $i++) {
            // Créer un utilisateur propriétaire
            $owner = User::factory()->create();

            // Créer l'organisation avec le propriétaire
            $organization = Organization::factory()->create([
                'owner_id' => $owner->id,
            ]);

            // Mettre à jour l'organisation courante du propriétaire
            $owner->update(['current_organization_id' => $organization->id]);

            // Attacher le propriétaire à son organisation
            $owner->organizations()->attach($organization->id, ['role' => 'admin']);

            // Création des dépôts
            $depots = Depot::factory(2)->create([
                'organization_id' => $organization->id,
            ]);

            // Création des équipements (répartis dans les catégories)
            foreach (Category::all() as $category) {
                Equipment::factory(3)->create([
                    'organization_id' => $organization->id,
                    'category_id' => $category->id,
                    'depot_id' => $depots->random()->id,
                ]);
            }
        }
    }
}
