<?php

namespace App\Actions\Reservations;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Notifications\RejectedReservationNotification;
use Exception;
use Illuminate\Support\Facades\Log;

class RejectReservation
{
    public function handle(Reservation $reservation)
    {
        try {
            $reservation->update([
                'status' => ReservationStatus::REJECTED,
            ]);

            foreach ($reservation->borrowerOrganization->users as $user) {
                $user->notify(new RejectedReservationNotification($reservation));
            }
        } catch (Exception $e) {
            Log::error('RejectReservation action failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
