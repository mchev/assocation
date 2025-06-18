<?php

namespace App\Actions\Reservations;

use App\Enums\ReservationStatus;
use App\Models\Reservation;

class CancelReservation
{
    public function handle(Reservation $reservation)
    {
        $reservation->update([
            'status' => ReservationStatus::CANCELLED,
        ]);

        foreach ($reservation->borrowerOrganization->users as $user) {
            $user->notify(new CanceledReservationNotification($reservation, 'borrower'));
        }

        foreach ($reservation->lenderOrganization->users as $user) {
            $user->notify(new CanceledReservationNotification($reservation, 'lender'));
        }
    }
}
