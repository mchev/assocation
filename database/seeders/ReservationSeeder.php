<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\Organization;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Enums\ReservationStatus;
use App\Enums\ReservationItemStatus;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Seed reservations between organizations.
     */
    public function run(): void
    {
        $pegase = Organization::where('name', 'Pégase')->first();
        $organizations = Organization::where('id', '!=', $pegase->id)->get();

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
                        $quantity = fake()->numberBetween(1, 3);
                        $price = (int) (str_replace(',', '.', $item->rental_price) * 100);

                        ReservationItem::factory()->create([
                            'reservation_id' => $reservation->id,
                            'equipment_id' => $item->id,
                            'depot_id' => $item->depot_id,
                            'status' => $itemStatus,
                            'quantity' => $quantity,
                            'price' => $price,
                            'total_price' => $price * $quantity,
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
                        $quantity = fake()->numberBetween(1, 3);
                        $price = (int) (str_replace(',', '.', $item->rental_price) * 100);

                        ReservationItem::factory()->create([
                            'reservation_id' => $reservation->id,
                            'equipment_id' => $item->id,
                            'depot_id' => $item->depot_id,
                            'status' => $itemStatus,
                            'quantity' => $quantity,
                            'price' => $price,
                            'total_price' => $price * $quantity,
                        ]);
                    }
                });
        });
    }
} 