<?php

namespace App\Actions\Reservations;

use App\Enums\ReservationStatus;
use App\Models\Equipment;
use App\Models\Organization;
use App\Models\Reservation;
use App\Notifications\NewReservationForBorrowerNotification;
use App\Notifications\NewReservationForLenderNotification;
use Illuminate\Http\Request;

class CreateReservationFromCart
{
    public function handle(Request $request): Reservation
    {
        $cart = $request->session()->get('cart');
        $user = $request->user();

        // Group each equipment from the cart by organization
        $listByOrganization = [];

        foreach ($cart as $item) {
            $organizationId = Equipment::find($item['equipment_id'])->organization_id;
            $listByOrganization[$organizationId][] = $item;
        }

        // Get items by organization
        foreach ($listByOrganization as $organizationId => $items) {

            // Group items by start and end date
            $itemsByDate = collect($items)->groupBy('rental_start', 'rental_end');

            foreach ($itemsByDate as $date => $items) {

                $startDate = $items->first()['rental_start'];
                $endDate = $items->last()['rental_end'];

                $reservation = $user->reservations()->create([
                    'from_organization_id' => $organizationId,
                    'to_organization_id' => $user->currentOrganization->id,
                    'status' => ReservationStatus::PENDING,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);

                $days = $reservation->duration;

                foreach ($items as $item) {

                    $equipment = Equipment::find($item['equipment_id']);

                    $reservation->items()->create([
                        'equipment_id' => $item['equipment_id'],
                        'depot_id' => $equipment->depot_id,
                        'quantity' => $item['quantity'],
                        'price' => $equipment->rental_price,
                        'total_price' => $equipment->rental_price * $days * $item['quantity'],
                        'deposit' => $equipment->deposit_amount * $item['quantity'],
                    ]);
                }

                $reservation->calculateTotals();

            }
        }

        // Notify borrower
        foreach ($reservation->borrowerOrganization->users as $user) {
            $user->notify(new NewReservationForBorrowerNotification($reservation));
        }

        // Notify lender
        foreach ($reservation->lenderOrganization->users as $user) {
            $user->notify(new NewReservationForLenderNotification($reservation));
        }

        // Forget the cart
        $request->session()->forget('cart');

        return $reservation;
    }
}
