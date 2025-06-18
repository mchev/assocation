<?php

namespace App\Actions\Reservations;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Notifications\ConfirmedReservationNotification;

class ConfirmReservation
{
    public function handle(Reservation $reservation)
    {
        $reservation->update([
            'status' => ReservationStatus::CONFIRMED,
        ]);

        foreach ($reservation->borrowerOrganization->users as $user) {
            $user->notify(new ConfirmedReservationNotification($reservation));
        }

        return $reservation;
    }
}
