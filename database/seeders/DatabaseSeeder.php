<?php

namespace Database\Seeders;

use App\Enums\ReservationItemStatus;
use App\Enums\ReservationStatus;
use App\Models\Category;
use App\Models\Depot;
use App\Models\Equipment;
use App\Models\Organization;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Création des catégories
        $categories = Category::factory(5)->create();

        // Création de l'organisation de Martin (Pégase)
        $pegase = Organization::factory()->create([
            'name' => 'Pégase',
            'description' => 'Organisation de Martin',
            'email' => 'contact@pegase.io',
            'website' => 'https://pegase.io',
        ]);

        // Création de Martin comme admin
        $martin = User::factory()->create([
            'name' => 'Martin',
            'email' => 'martin@pegase.io',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'current_organization_id' => $pegase->id,
        ]);

        // Attacher Martin à son organisation
        $martin->organizations()->attach($pegase->id, ['role' => 'admin']);

        // Création des dépôts pour Pégase
        $pegaseDepots = Depot::factory(3)->create([
            'organization_id' => $pegase->id,
        ]);

        // Création des équipements pour Pégase (répartis dans les catégories)
        foreach ($categories as $category) {
            Equipment::factory(4)->create([
                'organization_id' => $pegase->id,
                'category_id' => $category->id,
                'depot_id' => $pegaseDepots->random()->id,
            ]);
        }

        // Création des autres organisations
        $organizations = Organization::factory(5)->create()->each(function ($org) use ($categories) {
            // Création des dépôts
            $depots = Depot::factory(2)->create([
                'organization_id' => $org->id,
            ]);

            // Création des équipements (répartis dans les catégories)
            foreach ($categories as $category) {
                Equipment::factory(3)->create([
                    'organization_id' => $org->id,
                    'category_id' => $category->id,
                    'depot_id' => $depots->random()->id,
                ]);
            }
        });

        // Création des réservations
        $organizations->each(function ($org) use ($pegase) {
            // Réservations où Pégase prête
            Reservation::factory(3)
                ->sequence(
                    ['status' => ReservationStatus::PENDING],
                    ['status' => ReservationStatus::CONFIRMED],
                    ['status' => ReservationStatus::COMPLETED]
                )
                ->create([
                    'from_organization_id' => $pegase->id,
                    'to_organization_id' => $org->id,
                ])
                ->each(function ($reservation) {
                    // Statut des items cohérent avec la réservation
                    $itemStatus = match ($reservation->status) {
                        ReservationStatus::PENDING => ReservationItemStatus::PENDING,
                        ReservationStatus::CONFIRMED => ReservationItemStatus::PICKED_UP,
                        ReservationStatus::COMPLETED => ReservationItemStatus::RETURNED,
                        default => ReservationItemStatus::PENDING,
                    };

                    // 2-4 items par réservation
                    $equipment = Equipment::where('organization_id', $reservation->from_organization_id)
                        ->inRandomOrder()
                        ->take(fake()->numberBetween(2, 4))
                        ->get();

                    foreach ($equipment as $item) {
                        ReservationItem::factory()
                            ->create([
                                'reservation_id' => $reservation->id,
                                'equipment_id' => $item->id,
                                'depot_id' => $item->depot_id,
                                'status' => $itemStatus,
                                'price' => $item->rental_price,
                            ]);
                    }
                });

            // Réservations où Pégase emprunte
            Reservation::factory(3)
                ->sequence(
                    ['status' => ReservationStatus::PENDING],
                    ['status' => ReservationStatus::CONFIRMED],
                    ['status' => ReservationStatus::COMPLETED]
                )
                ->create([
                    'from_organization_id' => $org->id,
                    'to_organization_id' => $pegase->id,
                ])
                ->each(function ($reservation) {
                    // Statut des items cohérent avec la réservation
                    $itemStatus = match ($reservation->status) {
                        ReservationStatus::PENDING => ReservationItemStatus::PENDING,
                        ReservationStatus::CONFIRMED => ReservationItemStatus::PICKED_UP,
                        ReservationStatus::COMPLETED => ReservationItemStatus::RETURNED,
                        default => ReservationItemStatus::PENDING,
                    };

                    // 2-4 items par réservation
                    $equipment = Equipment::where('organization_id', $reservation->from_organization_id)
                        ->inRandomOrder()
                        ->take(fake()->numberBetween(2, 4))
                        ->get();

                    foreach ($equipment as $item) {
                        ReservationItem::factory()
                            ->create([
                                'reservation_id' => $reservation->id,
                                'equipment_id' => $item->id,
                                'depot_id' => $item->depot_id,
                                'status' => $itemStatus,
                                'price' => $item->rental_price,
                            ]);
                    }
                });
        });
    }
}
