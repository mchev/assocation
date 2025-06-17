<?php

namespace App\Http\Controllers\App;

use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;
use Inertia\Response;

class ReservationInController extends Controller
{
    public function index(Request $request): Response
    {
        $organization = $request->user()->currentOrganization;

        $this->authorize('viewAny', Reservation::class);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', new Enum(ReservationStatus::class)],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'sort' => ['nullable', 'string', 'in:start_date,end_date,status,created_at'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $query = $organization->borrowedReservations()
            ->with(['lenderOrganization', 'items.equipment', 'items.depot', 'user']);

        if ($filters['search'] ?? null) {
            $query->where(function ($query) use ($filters) {
                $query->whereHas('lenderOrganization', function ($query) use ($filters) {
                    $query->where('name', 'like', "%{$filters['search']}%");
                })->orWhereHas('user', function ($query) use ($filters) {
                    $query->where('name', 'like', "%{$filters['search']}%");
                });
            });
        }

        if ($filters['status'] ?? null) {
            $query->where('status', $filters['status']);
        }

        if ($filters['start_date'] ?? null) {
            $query->where('start_date', '>=', $filters['start_date']);
        }

        if ($filters['end_date'] ?? null) {
            $query->where('end_date', '<=', $filters['end_date']);
        }

        $sort = $filters['sort'] ?? 'created_at';
        $direction = $filters['direction'] ?? 'desc';
        $query->orderBy($sort, $direction);

        $reservations = $query->paginate()->through(function ($reservation) {
            $reservation->status_label = $reservation->status->label();
            $reservation->status_color = $reservation->status->color();

            return $reservation;
        })->withQueryString();

        return Inertia::render('App/Organizations/Reservations/In/Index', [
            'organization' => $organization,
            'reservations' => $reservations,
            'filters' => $filters,
            'statuses' => collect(ReservationStatus::cases())->map(fn ($status) => [
                'value' => $status->value,
                'label' => $status->label(),
            ]),
            'can' => [
                'create' => $request->user()->can('create', [Reservation::class, $organization]),
                'viewAny' => $request->user()->can('viewAny', Reservation::class),
            ],
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $cart = session()->get('cart');
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

                foreach ($items as $item) {

                    $equipment = Equipment::find($item['equipment_id']);
                    $days = abs(Carbon::parse($item['rental_start'])->diffInDays(Carbon::parse($item['rental_end'])) + 1);

                    $reservation->items()->create([
                        'equipment_id' => $item['equipment_id'],
                        'depot_id' => $equipment->depot_id,
                        'quantity' => $item['quantity'],
                        'price' => $equipment->rental_price,
                        'total_price' => $equipment->rental_price * $days * $item['quantity'],
                        'deposit' => $equipment->deposit_amount * $item['quantity'],
                    ]);
                }

            }
        }

        // Notify organizations
        // Send an email to the borrower

        // Forget the cart
        session()->forget('cart');

        return redirect()->route('app.organizations.reservations.in.index');

    }

    public function edit(Reservation $reservation): Response
    {
        $reservation->load('items.equipment', 'fromOrganization');

        return Inertia::render('App/Organizations/Reservations/In/Edit', [
            'reservation' => [
                'id' => $reservation->id,
                'start_date' => $reservation->start_date->locale('fr')->isoFormat('D MMMM YYYY'),
                'end_date' => $reservation->end_date->locale('fr')->isoFormat('D MMMM YYYY'),
                'status' => $reservation->status->value,
                'status_label' => $reservation->status->label(),
                'status_color' => $reservation->status->color(),
                'total_price' => $reservation->total_price,
                'from_organization' => [
                    'id' => $reservation->fromOrganization->id,
                    'name' => $reservation->fromOrganization->name,
                ],
                'items' => $reservation->items,
            ],
        ]);
    }
}
