<?php

namespace App\Actions\Reservations;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Notifications\CompletedReservationNotification;
use Exception;
use Illuminate\Support\Facades\Log;

class CompleteReservation
{
    public function handle(Reservation $reservation)
    {
        try {
            $reservation->update([
                'status' => ReservationStatus::COMPLETED,
            ]);

            foreach ($reservation->borrowerOrganization->users as $user) {
                $user->notify(new CompletedReservationNotification($reservation));
            }
        } catch (Exception $e) {
            Log::error('CompleteReservation action failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
