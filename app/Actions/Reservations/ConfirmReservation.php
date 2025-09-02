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
            $reservation->status = ReservationStatus::CONFIRMED;
            $reservation->save();

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
