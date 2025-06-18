<?php

namespace App\Jobs;

use App\Actions\Reservations\AutomaticalyCancelReservation;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CancelExpiredReservations implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Get all pending reservations and filter by deadline in memory
            Reservation::where('status', ReservationStatus::PENDING)
                ->get() // Load all informations to pass to the action and the notification
                ->filter(function (Reservation $reservation) {
                    return $reservation->deadline->isPast();
                })
                ->each(function (Reservation $reservation) {
                    (new AutomaticalyCancelReservation)->handle($reservation);
                });
        } catch (Exception $e) {
            Log::error('CancelExpiredReservations job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
