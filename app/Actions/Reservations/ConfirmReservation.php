<?php

namespace App\Actions\Reservations;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Notifications\ConfirmedReservationNotification;
use Exception;
use Illuminate\Support\Facades\Log;

class ConfirmReservation
{
    public function handle(Reservation $reservation)
    {
        try {
            $reservation->update([
                'status' => ReservationStatus::CONFIRMED,
            ]);

            foreach ($reservation->borrowerOrganization->users as $user) {
                $user->notify(new ConfirmedReservationNotification($reservation));
            }

            return $reservation;
        } catch (Exception $e) {
            Log::error('ConfirmReservation action failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
