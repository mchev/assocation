<?php

namespace App\Http\Controllers\App;

use App\Actions\Reservations\CancelReservation;
use App\Actions\Reservations\ConfirmReservation;
use App\Actions\Reservations\CreateCalendarReservation;
use App\Actions\Reservations\RejectReservation;
use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservationItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ReservationOutController extends Controller
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

        $query = $organization->lentReservations()
            ->with(['borrowerOrganization', 'items.equipment', 'items.depot', 'user']);

        if ($filters['search'] ?? null) {
            $query->where(function ($query) use ($filters) {
                $query->whereHas('borrowerOrganization', function ($query) use ($filters) {
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

        return Inertia::render('App/Organizations/Reservations/Out/Index', [
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
        $organization = $request->user()->currentOrganization;

        $this->authorize('create', [Reservation::class, $organization]);

        return Inertia::render('App/Organizations/Reservations/Out/Create', [
            'organization' => $organization,
            'start_date' => $request->date('start_date'),
            'end_date' => $request->date('end_date'),
        ]);
    }

    public function store(Request $request, CreateCalendarReservation $action): RedirectResponse
    {
        $organization = $request->user()->currentOrganization;

        $this->authorize('create', [Reservation::class, $organization]);

        try {
            $validated = $request->validate([
                'to_organization_id' => ['required', 'exists:organizations,id'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
                'notes' => ['nullable', 'string'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.equipment_id' => ['required', 'exists:equipments,id'],
                'items.*.depot_id' => ['required', 'exists:depots,id'],
                'items.*.quantity' => ['required', 'integer', 'min:1'],
                'items.*.price' => ['nullable', 'numeric', 'min:0'],
                'items.*.notes' => ['nullable', 'string', 'max:1000'],
            ]);

            $reservation = DB::transaction(function () use ($action, $validated, $organization) {
                return $action->handle($validated, $organization);
            });

            return redirect()
                ->route('app.organizations.reservations.out.show', $reservation)
                ->with('success', 'Réservation créée avec succès.');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            report($e);

            return back()->withErrors(['error' => 'Une erreur est survenue lors de la création de la réservation.']);
        }
    }

    public function edit(Request $request, Reservation $reservation): Response
    {
        $this->authorize('view', $reservation);

        $reservation->load(['borrowerOrganization', 'items.equipment', 'items.depot', 'user']);

        return Inertia::render('App/Organizations/Reservations/Out/Edit', [
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
                'deadline' => $reservation->deadline,
                'deadline_for_human' => $reservation->deadline_for_human,
                'from_organization' => [
                    'id' => $reservation->fromOrganization->id,
                    'name' => $reservation->fromOrganization->name,
                ],
                'to_organization' => [
                    'id' => $reservation->borrowerOrganization->id,
                    'name' => $reservation->borrowerOrganization->name,
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

    public function applyDiscount(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        try {
            $validated = $request->validate([
                'discount_type' => ['required', 'string', 'in:'.implode(',', array_keys(Reservation::discountTypes()))],
                'discount_value' => ['required', 'numeric', 'min:0'],
                'discount_reason' => ['nullable', 'string', 'max:1000'],
            ]);

            DB::transaction(function () use ($reservation, $validated) {
                $reservation->applyDiscount(
                    $validated['discount_type'],
                    $validated['discount_value'],
                    $validated['discount_reason'] ?? null
                );
            });

            return back()->with('success', 'Remise appliquée avec succès.');
        } catch (\Exception $e) {
            report($e);

            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'application de la remise.']);
        }
    }

    public function removeDiscount(Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        try {
            DB::transaction(fn () => $reservation->removeDiscount());

            return back()->with('success', 'Remise supprimée avec succès.');
        } catch (\Exception $e) {
            report($e);

            return back()->withErrors(['error' => 'Une erreur est survenue lors de la suppression de la remise.']);
        }
    }

    public function confirm(Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        if (! $reservation->canBeConfirmed()) {
            return back()->withErrors(['error' => 'Cette réservation ne peut pas être confirmée.']);
        }

        $reservation = (new ConfirmReservation)->handle($reservation);

        return back()->with('success', 'Réservation confirmée avec succès.');
    }

    public function reject(Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        if (! $reservation->canBeRejected()) {
            return back()->withErrors(['error' => 'Cette réservation ne peut pas être refusée.']);
        }

        $reservation = (new RejectReservation)->handle($reservation);

        return back()->with('success', 'Réservation refusée.');
    }

    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        if (! $reservation->canBeCancelled()) {
            return back()->withErrors(['error' => 'Cette réservation ne peut pas être annulée.']);
        }

        $reservation = (new CancelReservation)->handle($reservation);

        return back()->with('success', 'Réservation annulée.');
    }

    public function complete(Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        if (! $reservation->canBeCompleted()) {
            return back()->withErrors(['error' => 'Cette réservation ne peut pas être terminée.']);
        }

        try {
            DB::transaction(function () use ($reservation) {
                $reservation->update(['status' => ReservationStatus::COMPLETED]);
            });

            return back()->with('success', 'Réservation terminée.');
        } catch (\Exception $e) {
            report($e);

            return back()->withErrors(['error' => 'Une erreur est survenue lors de la complétion de la réservation.']);
        }
    }

    public function pickupItem(Reservation $reservation, ReservationItem $item): RedirectResponse
    {
        $this->authorize('update', $reservation);

        if (! $item->canBePickedUp()) {
            return back()->withErrors(['error' => 'Cet équipement ne peut pas être récupéré.']);
        }

        try {
            DB::transaction(function () use ($item) {
                $item->update([
                    'status' => ReservationItem::STATUS_PICKED_UP,
                    'picked_up_at' => now(),
                ]);
            });

            return back()->with('success', 'Équipement marqué comme récupéré.');
        } catch (\Exception $e) {
            report($e);

            return back()->withErrors(['error' => 'Une erreur est survenue lors du marquage de l\'équipement comme récupéré.']);
        }
    }

    public function returnItem(Reservation $reservation, ReservationItem $item): RedirectResponse
    {
        $this->authorize('update', $reservation);

        if (! $item->canBeReturned()) {
            return back()->withErrors(['error' => 'Cet équipement ne peut pas être retourné.']);
        }

        try {
            DB::transaction(function () use ($item) {
                $item->update([
                    'status' => ReservationItem::STATUS_RETURNED,
                    'returned_at' => now(),
                ]);
            });

            return back()->with('success', 'Équipement marqué comme retourné.');
        } catch (\Exception $e) {
            report($e);

            return back()->withErrors(['error' => 'Une erreur est survenue lors du marquage de l\'équipement comme retourné.']);
        }
    }
}
