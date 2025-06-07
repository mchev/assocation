<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function isEquipmentAvailable(Equipment $equipment, Carbon $startDate, Carbon $endDate, ?Reservation $excludeReservation = null)
    {
        $query = $equipment->reservations()
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
            })
            ->where('status', '!=', 'cancelled');

        if ($excludeReservation) {
            $query->where('id', '!=', $excludeReservation->id);
        }

        return ! $query->exists();
    }

    public function calculateTotalPrice(Equipment $equipment, Carbon $startDate, Carbon $endDate)
    {
        if (! $equipment->rental_price) {
            return 0;
        }

        $days = $startDate->diffInDays($endDate) + 1;

        return $equipment->rental_price * $days;
    }

    public function createReservation(array $data, Equipment $equipment)
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        if (! $this->isEquipmentAvailable($equipment, $startDate, $endDate)) {
            throw new \Exception('L\'équipement n\'est pas disponible pour ces dates.');
        }

        $totalPrice = $this->calculateTotalPrice($equipment, $startDate, $endDate);

        return Reservation::create([
            ...$data,
            'equipment_id' => $equipment->id,
            'organization_id' => $equipment->organization_id,
            'total_price' => $totalPrice,
            'deposit_amount' => $equipment->requires_deposit ? $equipment->deposit_amount : 0,
            'status' => 'pending',
        ]);
    }

    public function updateReservation(Reservation $reservation, array $data)
    {
        if (isset($data['start_date']) && isset($data['end_date'])) {
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);

            if (! $this->isEquipmentAvailable($reservation->equipment, $startDate, $endDate, $reservation)) {
                throw new \Exception('L\'équipement n\'est pas disponible pour ces dates.');
            }

            $data['total_price'] = $this->calculateTotalPrice($reservation->equipment, $startDate, $endDate);
        }

        $reservation->update($data);

        return $reservation;
    }

    public function approveReservation(Reservation $reservation)
    {
        if ($reservation->status !== 'pending') {
            throw new \Exception('Seules les réservations en attente peuvent être approuvées.');
        }

        $reservation->update(['status' => 'approved']);

        return $reservation;
    }

    public function rejectReservation(Reservation $reservation)
    {
        if ($reservation->status !== 'pending') {
            throw new \Exception('Seules les réservations en attente peuvent être rejetées.');
        }

        $reservation->update(['status' => 'rejected']);

        return $reservation;
    }

    public function cancelReservation(Reservation $reservation, string $reason)
    {
        if (! in_array($reservation->status, ['pending', 'approved'])) {
            throw new \Exception('Seules les réservations en attente ou approuvées peuvent être annulées.');
        }

        $reservation->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ]);

        return $reservation;
    }

    public function createMultipleReservations(array $items, string $contactName, string $contactEmail, string $contactPhone)
    {
        return DB::transaction(function () use ($items, $contactName, $contactEmail, $contactPhone) {
            $reservations = [];

            foreach ($items as $item) {
                $equipment = Equipment::findOrFail($item['equipment_id']);

                // Check availability
                $this->checkAvailability($equipment, $item['start_date'], $item['end_date'], $item['quantity']);

                // Calculate total price
                $startDate = Carbon::parse($item['start_date']);
                $endDate = Carbon::parse($item['end_date']);
                $days = $startDate->diffInDays($endDate) + 1;
                $totalPrice = $equipment->rental_price * $days * $item['quantity'];
                $totalDeposit = $equipment->deposit_amount * $item['quantity'];

                // Create reservation
                $reservation = Reservation::create([
                    'equipment_id' => $equipment->id,
                    'start_date' => $item['start_date'],
                    'end_date' => $item['end_date'],
                    'quantity' => $item['quantity'],
                    'total_price' => $totalPrice,
                    'deposit_amount' => $totalDeposit,
                    'status' => 'pending',
                    'notes' => $item['notes'] ?? null,
                    'contact_name' => $contactName,
                    'contact_email' => $contactEmail,
                    'contact_phone' => $contactPhone,
                ]);

                $reservations[] = $reservation;
            }

            return $reservations;
        });
    }

    protected function checkAvailability(Equipment $equipment, string $startDate, string $endDate, int $quantity)
    {
        $existingReservations = Reservation::where('equipment_id', $equipment->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->sum('quantity');

        $availableQuantity = $equipment->quantity - $existingReservations;

        if ($availableQuantity < $quantity) {
            throw new \Exception("Not enough equipment available for the selected dates. Only {$availableQuantity} items available.");
        }
    }
}
