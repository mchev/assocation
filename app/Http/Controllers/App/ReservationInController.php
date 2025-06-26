<?php

namespace App\Http\Controllers\App;

use App\Actions\Reservations\CancelReservation;
use App\Actions\Reservations\CreateReservationFromCart;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReservationInController extends Controller
{
    public function index(Request $request): Response
    {
        $organization = $request->user()->currentOrganization;
        $this->authorize('viewAny', Reservation::class);

        $reservations = $organization->borrowedReservations()
            ->with(['lenderOrganization', 'items.equipment', 'items.depot:city', 'user'])
            ->orderBy('start_date', 'desc')
            ->paginate()->through(function ($reservation) {
                $reservation->status_label = $reservation->status->label();
                $reservation->status_color = $reservation->status->color();

                return $reservation;
            })->withQueryString();

        return Inertia::render('App/Organizations/Reservations/In/Index', [
            'organization' => $organization,
            'reservations' => $reservations,
            'can' => [
                'create' => $request->user()->can('create', [Reservation::class, $organization]),
                'viewAny' => $request->user()->can('viewAny', Reservation::class),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'message' => 'required|string|max:65535',
        ]);

        $reservation = (new CreateReservationFromCart)->handle($request);

        return redirect()->route('app.organizations.reservations.in.edit', $reservation)
            ->with('success', 'Réservation créée avec succès');
    }

    public function edit(Reservation $reservation): Response
    {
        $reservation->load('items.equipment', 'fromOrganization', 'user');

        return Inertia::render('App/Organizations/Reservations/In/Edit', [
            'reservation' => [
                'id' => $reservation->id,
                'start_date' => $reservation->start_date->locale('fr')->isoFormat('D MMMM YYYY'),
                'end_date' => $reservation->end_date->locale('fr')->isoFormat('D MMMM YYYY'),
                'status' => $reservation->status->value,
                'status_label' => $reservation->status->label(),
                'status_color' => $reservation->status->color(),
                'total' => $reservation->total,
                'created_at' => $reservation->created_at->locale('fr')->isoFormat('D MMMM YYYY HH:mm'),
                'updated_at' => $reservation->updated_at->locale('fr')->isoFormat('D MMMM YYYY HH:mm'),
                'duration' => $reservation->duration,
                'notes' => $reservation->notes,
                'from_organization' => [
                    'id' => $reservation->fromOrganization->id,
                    'name' => $reservation->fromOrganization->name,
                ],
                'to_organization' => [
                    'id' => $reservation->toOrganization->id,
                    'name' => $reservation->toOrganization->name,
                ],
                'items' => $reservation->items->map(function ($item) {
                    $item->city = $item->equipment->depot->city;

                    return $item;
                }),
                'user' => [
                    'id' => $reservation->user->id,
                    'name' => $reservation->user->name,
                    'email' => $reservation->user->email,
                    'phone' => $reservation->user->phone,
                ],
            ],
        ]);
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $this->authorize('delete', $reservation);

        (new CancelReservation)->handle($reservation);

        return redirect()->route('app.organizations.reservations.in.index')
            ->with('success', 'Réservation annulée avec succès');
    }
}
