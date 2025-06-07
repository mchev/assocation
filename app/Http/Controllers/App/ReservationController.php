<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Organization;
use App\Models\Reservation;
use App\Models\ReservationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->user()->organization;

        $reservations = $organization->allReservations()
            ->with(['fromOrganization', 'toOrganization', 'items.equipment', 'items.depot'])
            ->latest()
            ->paginate();

        return Inertia::render('App/Reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    public function create(Request $request)
    {
        $organization = $request->user()->organization;
        
        return Inertia::render('App/Reservations/Create', [
            'organizations' => Organization::where('id', '!=', $organization->id)->get(),
            'equipment' => Equipment::with('depot')->get(),
            'discountTypes' => Reservation::discountTypes(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'to_organization_id' => ['required', 'exists:organizations,id'],
            'start_date' => ['required', 'date', 'after:now'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.equipment_id' => ['required', 'exists:equipment,id'],
            'items.*.depot_id' => ['required', 'exists:depots,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'integer', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
            'discount_type' => ['nullable', 'string', 'in:' . implode(',', array_keys(Reservation::discountTypes()))],
            'discount_value' => ['nullable', 'required_with:discount_type', 'integer', 'min:0'],
            'discount_reason' => ['nullable', 'string'],
        ]);

        $organization = $request->user()->organization;

        try {
            DB::beginTransaction();

            $reservation = Reservation::create([
                'from_organization_id' => $organization->id,
                'to_organization_id' => $validated['to_organization_id'],
                'created_by_user_id' => $request->user()->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'notes' => $validated['notes'],
            ]);

            foreach ($validated['items'] as $item) {
                $reservation->items()->create([
                    'equipment_id' => $item['equipment_id'],
                    'depot_id' => $item['depot_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // Appliquer la remise si elle existe
            if (isset($validated['discount_type'])) {
                $reservation->applyDiscount(
                    $validated['discount_type'],
                    $validated['discount_value'],
                    $validated['discount_reason'] ?? null
                );
            }

            DB::commit();

            return redirect()->route('app.reservations.show', $reservation)
                ->with('success', 'Réservation créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la création de la réservation.');
        }
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['fromOrganization', 'toOrganization', 'items.equipment', 'items.depot', 'createdByUser']);

        return Inertia::render('App/Reservations/Show', [
            'reservation' => $reservation,
            'discountTypes' => Reservation::discountTypes(),
        ]);
    }

    public function applyDiscount(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'discount_type' => ['required', 'string', 'in:' . implode(',', array_keys(Reservation::discountTypes()))],
            'discount_value' => ['required', 'integer', 'min:0'],
            'discount_reason' => ['nullable', 'string'],
        ]);

        try {
            $reservation->applyDiscount(
                $validated['discount_type'],
                $validated['discount_value'],
                $validated['discount_reason'] ?? null
            );

            return back()->with('success', 'Remise appliquée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de l\'application de la remise.');
        }
    }

    public function removeDiscount(Reservation $reservation)
    {
        try {
            $reservation->removeDiscount();
            return back()->with('success', 'Remise supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la suppression de la remise.');
        }
    }

    public function confirm(Reservation $reservation)
    {
        if (!$reservation->canBeConfirmed()) {
            return back()->with('error', 'Cette réservation ne peut pas être confirmée.');
        }

        $reservation->update(['status' => Reservation::STATUS_CONFIRMED]);

        return back()->with('success', 'Réservation confirmée avec succès.');
    }

    public function reject(Reservation $reservation)
    {
        if (!$reservation->canBeRejected()) {
            return back()->with('error', 'Cette réservation ne peut pas être refusée.');
        }

        $reservation->update(['status' => Reservation::STATUS_REJECTED]);

        return back()->with('success', 'Réservation refusée.');
    }

    public function cancel(Reservation $reservation)
    {
        if (!$reservation->canBeCancelled()) {
            return back()->with('error', 'Cette réservation ne peut pas être annulée.');
        }

        $reservation->update(['status' => Reservation::STATUS_CANCELLED]);

        return back()->with('success', 'Réservation annulée.');
    }

    public function complete(Reservation $reservation)
    {
        if (!$reservation->canBeCompleted()) {
            return back()->with('error', 'Cette réservation ne peut pas être terminée.');
        }

        $reservation->update(['status' => Reservation::STATUS_COMPLETED]);

        return back()->with('success', 'Réservation terminée.');
    }

    public function pickupItem(ReservationItem $item)
    {
        if (!$item->canBePickedUp()) {
            return back()->with('error', 'Cet équipement ne peut pas être récupéré.');
        }

        $item->update([
            'status' => ReservationItem::STATUS_PICKED_UP,
            'picked_up_at' => now(),
        ]);

        return back()->with('success', 'Équipement marqué comme récupéré.');
    }

    public function returnItem(ReservationItem $item)
    {
        if (!$item->canBeReturned()) {
            return back()->with('error', 'Cet équipement ne peut pas être retourné.');
        }

        $item->update([
            'status' => ReservationItem::STATUS_RETURNED,
            'returned_at' => now(),
        ]);

        return back()->with('success', 'Équipement marqué comme retourné.');
    }
} 