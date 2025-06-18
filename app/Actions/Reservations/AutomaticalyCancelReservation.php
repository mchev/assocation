<?php

namespace App\Actions\Reservations;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Notifications\ReservationCancelledAutomaticallyNotification;
use Exception;
use Illuminate\Support\Facades\Log;

class AutomaticalyCancelReservation
{
    public function handle(Reservation $reservation)
    {
        try {

            $reservation->update([
                'status' => ReservationStatus::CANCELLED_AUTOMATICALLY,
            ]);

            foreach ($reservation->borrowerOrganization->users as $user) {
                $user->notify(new ReservationCancelledAutomaticallyNotification($reservation));
            }

        } catch (Exception $e) {
            Log::error('AutomaticalyCancelReservation action failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
