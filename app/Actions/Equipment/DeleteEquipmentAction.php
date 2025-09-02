<?php

namespace App\Actions\Equipment;

use App\Enums\ReservationStatus;
use App\Models\Equipment;
use App\Models\ReservationItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteEquipmentAction
{
    public function execute(Equipment $equipment): bool
    {
        return DB::transaction(function () use ($equipment) {
            // 1. Check if equipment has active or future confirmed reservations
            $this->checkReservationConstraints($equipment);

            // 2. Remove equipment from future pending reservations only
            $this->removeFromFuturePendingReservations($equipment);

            // 3. Delete associated images (files will be deleted automatically via model observer)
            $this->deleteAssociatedImages($equipment);

            // 4. Soft delete the equipment
            $equipment->delete();

            return true;
        });
    }

    /**
     * Check if equipment has active or future confirmed reservations and throw exception if so
     */
    protected function checkReservationConstraints(Equipment $equipment): void
    {
        // Check for active reservations (currently ongoing)
        $activeReservations = $equipment->reservations()
            ->whereIn('reservations.status', [ReservationStatus::CONFIRMED, ReservationStatus::PENDING])
            ->where('reservations.start_date', '<=', now())
            ->where('reservations.end_date', '>=', now())
            ->exists();

        if ($activeReservations) {
            throw new \Exception('Ce matériel est actuellement en cours de location et ne peut être supprimé.');
        }

        // Check for future confirmed reservations
        $futureConfirmedReservations = $equipment->reservations()
            ->where('reservations.status', ReservationStatus::CONFIRMED)
            ->where('reservations.start_date', '>', now())
            ->exists();

        if ($futureConfirmedReservations) {
            throw new \Exception('Ce matériel a des réservations confirmées à venir et ne peut être supprimé.');
        }
    }

    /**
     * Remove equipment from future pending reservations only
     */
    protected function removeFromFuturePendingReservations(Equipment $equipment): void
    {
        // Get all reservation items for this equipment in future PENDING reservations only
        $futurePendingReservationItems = ReservationItem::where('equipment_id', $equipment->id)
            ->whereHas('reservation', function ($query) {
                $query->where('start_date', '>', now())
                    ->where('status', ReservationStatus::PENDING);
            })
            ->get();

        foreach ($futurePendingReservationItems as $item) {
            // Delete the reservation item
            $item->delete();

            // Recalculate reservation totals
            $item->reservation->calculateTotals();

            // If this was the last item in the reservation, cancel the reservation
            if ($item->reservation->items()->count() === 0) {
                $item->reservation->update([
                    'status' => ReservationStatus::CANCELLED,
                    'notes' => ($item->reservation->notes ?? '')."\n\nRéservation annulée automatiquement : équipement supprimé.",
                ]);
            }
        }
    }

    /**
     * Delete all associated images
     */
    protected function deleteAssociatedImages(Equipment $equipment): void
    {
        // The EquipmentImage model has a booted() method that automatically
        // deletes the file from storage when the model is deleted
        $equipment->images()->delete();
    }
}
