<?php

namespace App\Actions\Reservations;

use App\Enums\ReservationStatus;
use App\Models\Organization;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class CreateCalendarReservation
{
    public function handle(array $data, Organization $fromOrganization): Reservation
    {
        return DB::transaction(function () use ($data, $fromOrganization) {
            // Créer la réservation principale
            $reservation = new Reservation([
                'from_organization_id' => $fromOrganization->id,
                'to_organization_id' => $data['to_organization_id'],
                'user_id' => auth()->id(),
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'notes' => $data['notes'] ?? null,
                'status' => ReservationStatus::PENDING,
            ]);

            $reservation->save();

            // Ajouter les équipements sélectionnés
            foreach ($data['items'] as $item) {
                $equipment = $fromOrganization->equipments()->findOrFail($item['equipment_id']);

                $reservation->items()->create([
                    'equipment_id' => $equipment->id,
                    'depot_id' => $item['depot_id'] ?? $equipment->depot_id,
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                    'price' => $item['price'] ?? $equipment->rental_price,
                ]);
            }

            // Calculer les totaux
            $reservation->calculateTotals();

            return $reservation;
        });
    }
}
