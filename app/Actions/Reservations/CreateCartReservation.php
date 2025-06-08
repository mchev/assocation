<?php

namespace App\Actions\Reservations;

use App\Enums\ReservationStatus;
use App\Models\Organization;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class CreateCartReservation
{
    public function handle(array $cartItems, Organization $fromOrganization, Organization $toOrganization): Reservation
    {
        return DB::transaction(function () use ($cartItems, $fromOrganization, $toOrganization) {
            // Créer la réservation principale
            $reservation = new Reservation([
                'from_organization_id' => $fromOrganization->id,
                'to_organization_id' => $toOrganization->id,
                'user_id' => auth()->id(),
                'start_date' => collect($cartItems)->min('start_date'),
                'end_date' => collect($cartItems)->max('end_date'),
                'status' => ReservationStatus::PENDING,
            ]);

            $reservation->save();

            // Ajouter les items du panier
            foreach ($cartItems as $item) {
                $equipment = $fromOrganization->equipments()->findOrFail($item['equipment_id']);

                $reservation->items()->create([
                    'equipment_id' => $equipment->id,
                    'depot_id' => $equipment->depot_id,
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                    'price' => $equipment->rental_price,
                    'total_price' => $equipment->rental_price * $item['quantity'],
                ]);
            }

            // Calculer les totaux
            $reservation->calculateTotals();

            return $reservation;
        });
    }
}
