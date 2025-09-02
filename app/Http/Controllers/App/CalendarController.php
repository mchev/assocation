<?php

namespace App\Http\Controllers\App;

use App\Actions\Reservations\CreateManualReservation;
use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\ManualReservationRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $organization = $user->currentOrganization;

        if (!$organization) {
            return redirect()->route('app.organizations.create')
                ->with('error', 'Vous devez créer une organisation avant de pouvoir accéder au calendrier.');
        }

        // Get only outgoing reservations (where we are lending equipment)
        $reservations = $organization->lentReservations()
            ->whereIn('status', [ReservationStatus::PENDING, ReservationStatus::CONFIRMED, ReservationStatus::COMPLETED])
            ->with([
                'items.equipment',
                'borrowerOrganization',
                'user',
            ])
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'title' => $reservation->items->map(fn ($item) => $item->equipment?->name ?? 'Équipement supprimé')->join(', '),
                    'start' => $reservation->start_date,
                    'end' => $reservation->end_date,
                    'status' => [
                        'value' => $reservation->status->value,
                        'label' => $reservation->status->label(),
                        'color' => $reservation->status->color(),
                    ],
                    'borrower' => [
                        'name' => $reservation->borrowerOrganization?->name ?? 'Organisation supprimée',
                        'email' => $reservation->borrowerOrganization?->email,
                        'phone' => $reservation->borrowerOrganization?->phone,
                        'contact' => [
                            'name' => $reservation->user?->name ?? 'Utilisateur supprimé',
                            'email' => $reservation->user?->email,
                            'phone' => $reservation->user?->phone,
                        ],
                    ],
                    'total' => $reservation->formatted_total,
                    'items' => $reservation->items->map(fn ($item) => [
                        'equipment' => $item->equipment?->name ?? 'Équipement supprimé',
                        'quantity' => $item->quantity,
                        'status' => [
                            'value' => $item->status->value,
                            'label' => $item->status->label(),
                            'color' => $item->status->color(),
                        ],
                        'price' => $item->formatted_price,
                    ]),
                ];
            });

        // Get available equipments for manual reservations
        $equipments = $organization->equipments()
            ->where('is_available', true)
            ->orderBy('name')
            ->get(['id', 'name', 'is_available']);

        return Inertia::render('App/Organizations/Calendar/Index', [
            'reservations' => $reservations,
            'equipments' => $equipments,
        ]);
    }

    public function storeManualReservation(ManualReservationRequest $request)
    {
        $organization = $request->user()->currentOrganization;

        if (!$organization) {
            return redirect()->route('app.organizations.create')
                ->with('error', 'Vous devez créer une organisation avant de pouvoir créer des réservations.');
        }

        $validated = $request->validated();

        $reservation = app(CreateManualReservation::class)->handle($validated, $organization);

        return redirect()->back()->with('success', 'Réservation manuelle créée avec succès.');
    }
}
