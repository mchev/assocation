<?php

namespace App\Actions\Reservations;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Notifications\RejectedReservationNotification;

class RejectReservation
{
    public function handle(Reservation $reservation)
    {
        $reservation->update([
            'status' => ReservationStatus::REJECTED,
        ]);

        foreach ($reservation->borrowerOrganization->users as $user) {
            $user->notify(new RejectedReservationNotification($reservation));
        }
    }
}
