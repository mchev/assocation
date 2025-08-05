<?php

namespace App\Actions\Reservations;

use App\Enums\ReservationStatus;
use App\Models\Organization;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class CreateManualReservation
{
    public function handle(array $data, Organization $organization): Reservation
    {
        return DB::transaction(function () use ($data, $organization) {
            // Préparer les notes avec le titre et le destinataire
            $notes = [];

            if (! empty($data['title'])) {
                $notes[] = 'Titre: '.$data['title'];
            }

            if (! empty($data['recipient'])) {
                $notes[] = 'Destinataire: '.$data['recipient'];
            }

            if (! empty($data['notes'])) {
                $notes[] = $data['notes'];
            }

            $fullNotes = implode("\n\n", $notes);

            // Créer la réservation principale
            $reservation = new Reservation([
                'from_organization_id' => $organization->id,
                'to_organization_id' => $organization->id, // Même organisation pour les réservations manuelles
                'user_id' => auth()->id(),
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'notes' => $fullNotes ?: null,
                'status' => ReservationStatus::CONFIRMED, // Directement confirmée pour les réservations manuelles
            ]);

            $reservation->save();

            // Ajouter les équipements sélectionnés
            foreach ($data['items'] as $item) {
                $equipment = $organization->equipments()->findOrFail($item['equipment_id']);

                $reservation->items()->create([
                    'equipment_id' => $equipment->id,
                    'depot_id' => $item['depot_id'] ?? $equipment->depot_id,
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                    'price' => 0, // Gratuit pour les réservations manuelles
                    'status' => \App\Enums\ReservationItemStatus::PICKED_UP, // Directement récupéré
                ]);
            }

            // Calculer les totaux (sera 0 pour les réservations manuelles)
            $reservation->calculateTotals();

            return $reservation;
        });
    }
}
