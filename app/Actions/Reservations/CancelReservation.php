<?php

namespace App\Actions\Reservations;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Notifications\CanceledReservationNotification;
use Exception;
use Illuminate\Support\Facades\Log;

class CancelReservation
{
    public function handle(Reservation $reservation)
    {

        try {
            $reservation->update([
                'status' => ReservationStatus::CANCELLED,
            ]);

            foreach ($reservation->borrowerOrganization->users as $user) {
                $user->notify(new CanceledReservationNotification($reservation, 'borrower'));
            }

            foreach ($reservation->lenderOrganization->users as $user) {
                $user->notify(new CanceledReservationNotification($reservation, 'lender'));
            }
        } catch (Exception $e) {
            Log::error('CancelReservation action failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
