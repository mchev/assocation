<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Equipment;
use App\Models\Organization;
use App\Models\Depot;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Martin',
            'email' => 'martin@pegase.io',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Create test user
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create categories
        $this->call(CategorySeeder::class);

        // Create additional organizations with their depots and equipment
        Organization::factory(10)->create()->each(function ($organization) {
            // Create 1-3 depots for each organization
            Depot::factory(rand(1, 3))->create([
                'organization_id' => $organization->id
            ])->each(function ($depot) {
                // Create 5-15 equipment items for each depot
                Equipment::factory(rand(5, 15))->create([
                    'depot_id' => $depot->id,
                    'organization_id' => $depot->organization_id
                ]);
            });
        });
    }
}
