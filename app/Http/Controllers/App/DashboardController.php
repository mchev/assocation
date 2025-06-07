<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $organization = auth()->user()->currentOrganization;

        return Inertia::render('App/Dashboard', [
            'stats' => [
                'equipment_count' => $organization->equipments()->count(),
                'total_borrowed_count' => $organization->borrowedReservations()->count(),
                'total_lent_count' => $organization->lentReservations()->count(),
                'depots_count' => $organization->depots()->count(),
            ],
            'lentReservations' => $organization->lentReservations()
                ->with(['borrowerOrganization', 'items' => function($query) {
                    $query->select('id', 'reservation_id', 'equipment_id', 'quantity', 'status')
                        ->with(['equipment:id,name']);
                }])
                ->select(['id', 'start_date', 'end_date', 'subtotal', 'total', 'discount_amount', 'discount_type', 'to_organization_id', 'status'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($reservation) {
                    return array_merge($reservation->toArray(), [
                        'status_color' => $reservation->status->color(),
                        'status_label' => $reservation->status->label(),
                        'items' => $reservation->items->map(function ($item) {
                            return array_merge($item->toArray(), [
                                'status_color' => $item->status->color(),
                                'status_label' => $item->status->label(),
                            ]);
                        }),
                    ]);
                }),
            'borrowedReservations' => $organization->borrowedReservations()
                ->with(['lenderOrganization', 'items' => function($query) {
                    $query->select('id', 'reservation_id', 'equipment_id', 'quantity', 'status')
                        ->with(['equipment:id,name']);
                }])
                ->select(['id', 'start_date', 'end_date', 'subtotal', 'total', 'discount_amount', 'discount_type', 'from_organization_id', 'status'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($reservation) {
                    return array_merge($reservation->toArray(), [
                        'status_color' => $reservation->status->color(),
                        'status_label' => $reservation->status->label(),
                        'items' => $reservation->items->map(function ($item) {
                            return array_merge($item->toArray(), [
                                'status_color' => $item->status->color(),
                                'status_label' => $item->status->label(),
                            ]);
                        }),
                    ]);
                }),
            'recentEquipment' => $organization->equipments()
                ->with(['depot'])
                ->latest()
                ->take(5)
                ->get(),
            'depots' => $organization->depots()
                ->withCount(['equipments'])
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
} 