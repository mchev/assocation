<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Organization;
use App\Models\Equipment;
use App\Http\Requests\Reservation\StoreRequest;
use App\Http\Requests\Reservation\UpdateRequest;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReservationController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->authorizeResource(Reservation::class);
        $this->reservationService = $reservationService;
    }

    public function index()
    {
        $organization = auth()->user()->organization;
        $reservations = $organization->reservations()
            ->with(['equipment', 'user'])
            ->latest()
            ->paginate(10);

        return Inertia::render('App/Reservations/Index', [
            'organization' => $organization,
            'reservations' => $reservations,
            'can' => [
                'create' => auth()->user()->can('create', [Reservation::class, $organization]),
            ],
        ]);
    }

    public function create(Equipment $equipment)
    {
        $organization = auth()->user()->organization;
        return Inertia::render('App/Reservations/Create', [
            'organization' => $organization,
            'equipment' => $equipment,
        ]);
    }

    public function store(StoreRequest $request)
    {
        try {
            $organization = auth()->user()->organization;
            $equipment = Equipment::findOrFail($request->equipment_id);
            $reservation = $this->reservationService->createReservation(
                array_merge($request->validated(), ['user_id' => auth()->id()]),
                $equipment
            );

            return redirect()
                ->route('app.reservations.show', $reservation)
                ->with('success', 'Reservation created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['dates' => $e->getMessage()]);
        }
    }

    public function show(Reservation $reservation)
    {
        $organization = auth()->user()->organization;
        $reservation->load(['equipment', 'user']);

        return Inertia::render('App/Reservations/Show', [
            'organization' => $organization,
            'reservation' => $reservation,
            'can' => [
                'update' => auth()->user()->can('update', $reservation),
                'delete' => auth()->user()->can('delete', $reservation),
            ],
        ]);
    }

    public function edit(Reservation $reservation)
    {
        $organization = auth()->user()->organization;
        return Inertia::render('App/Reservations/Edit', [
            'organization' => $organization,
            'reservation' => $reservation,
        ]);
    }

    public function update(UpdateRequest $request, Reservation $reservation)
    {
        try {
            $this->reservationService->updateReservation($reservation, $request->validated());

            return redirect()
                ->route('app.reservations.show', $reservation)
                ->with('success', 'Reservation updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['dates' => $e->getMessage()]);
        }
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()
            ->route('app.reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }

    public function approve(Request $request, Reservation $reservation)
    {
        try {
            $this->reservationService->approveReservation($reservation);

            return redirect()
                ->route('app.reservations.show', $reservation)
                ->with('success', 'Reservation approved successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }

    public function reject(Request $request, Reservation $reservation)
    {
        try {
            $this->reservationService->rejectReservation($reservation);

            return redirect()
                ->route('app.reservations.show', $reservation)
                ->with('success', 'Reservation rejected successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        try {
            $request->validate([
                'cancellation_reason' => 'required|string|max:1000',
            ]);

            $this->reservationService->cancelReservation($reservation, $request->cancellation_reason);

            return redirect()
                ->route('app.reservations.show', $reservation)
                ->with('success', 'Reservation cancelled successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }
} 