<?php

namespace App\Http\Controllers\App;

use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
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

    public function create(Request $request): Response
    {
        $cart = session()->get('cart');
        dd($cart);

        // TODO: Create reservation

        // redirect to reservation show
        return redirect()->route('app.organizations.reservations.in.show', $reservation);

    }
}
