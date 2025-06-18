<?php

namespace App\Actions\Reservations;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Notifications\CompletedReservationNotification;

class CompleteReservation
{
    public function handle(Reservation $reservation)
    {
        $reservation->update([
            'status' => ReservationStatus::COMPLETED,
        ]);

        $this->notify($reservation);
    }

    public function notify(Reservation $reservation)
    {
        foreach ($reservation->borrowerOrganization->users as $user) {
            $user->notify(new CompletedReservationNotification($reservation));
        }
    }
}
