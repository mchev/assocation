<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EquipmentController extends Controller
{
    public function index(Request $request, Organization $organization)
    {
        $user = $request->user();

        if (! $organization) {
            return redirect()->route('app.organizations.create')
                ->with('error', 'Vous devez créer une organisation avant de pouvoir gérer du matériel.');
        }

        $query = $organization->equipments()
            ->with(['category', 'depot']);

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('availability')) {
            $query->where('is_available', $request->availability === 'available');
        }

        // Apply sorting
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        // Handle special sorting cases
        switch ($sort) {
            case 'category':
                $query->join('categories', 'equipments.category_id', '=', 'categories.id')
                    ->orderBy('categories.name', $direction)
                    ->select('equipments.*');
                break;
            default:
                $query->orderBy($sort, $direction);
        }

        $equipments = $query->paginate(12);

        // Get all categories
        $categories = \App\Models\Category::orderBy('name')->get(['id', 'name']);

        return Inertia::render('App/Organizations/Equipments/Index', [
            'organization' => $organization,
            'equipments' => $equipments,
            'allCategories' => $categories,
            'filters' => $request->only(['search', 'category', 'condition', 'availability', 'sort', 'direction']),
            'can' => [
                'create' => $user->can('create', [Equipment::class, $organization]),
                'update' => $user->can('update', $organization->equipments()->first() ?: new Equipment(['organization_id' => $organization->id])),
                'delete' => $user->can('delete', $organization->equipments()->first() ?: new Equipment(['organization_id' => $organization->id])),
            ],
        ]);
    }

    public function show(Request $request, Organization $organization, Equipment $equipment)
    {
        $this->authorize('view', $equipment);
        $user = $request->user();

        // Get current rental and upcoming rentals
        $currentDate = now();
        $currentRental = $equipment->reservations()
            ->with('borrowerOrganization:id,name')
            ->where('reservations.start_date', '<=', $currentDate)
            ->where('reservations.end_date', '>=', $currentDate)
            ->where('reservations.status', 'approved')
            ->first();

        $upcomingRentals = $equipment->reservations()
            ->with('borrowerOrganization:id,name')
            ->where('reservations.start_date', '>', $currentDate)
            ->where('reservations.status', 'approved')
            ->orderBy('reservations.start_date')
            ->take(5)
            ->get();

        return Inertia::render('App/Organizations/Equipments/Show', [
            'organization' => $organization,
            'equipment' => array_merge($equipment->load('category', 'depot')->toArray(), [
                'current_rental' => $currentRental ? [
                    'renter' => [
                        'name' => $currentRental->borrowerOrganization->name,
                    ],
                    'start_date' => $currentRental->start_date->format('d/m/Y'),
                    'end_date' => $currentRental->end_date->format('d/m/Y'),
                ] : null,
                'upcoming_rentals' => $upcomingRentals->map(function ($rental) {
                    return [
                        'renter' => [
                            'name' => $rental->borrowerOrganization->name,
                        ],
                        'start_date' => $rental->start_date->format('d/m/Y'),
                        'end_date' => $rental->end_date->format('d/m/Y'),
                    ];
                }),
            ]),
            'can' => [
                'update' => $user->can('update', $equipment),
                'delete' => $user->can('delete', $equipment),
            ],
        ]);
    }

    public function create(Request $request, Organization $organization)
    {
        $this->authorize('create', [Equipment::class, $organization]);

        return Inertia::render('App/Organizations/Equipments/Create', [
            'organization' => $organization,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'depots' => $organization->depots()->orderBy('name')->get(['id', 'name', 'city']),
        ]);
    }

    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'depot_id' => 'required|exists:depots,id',
            'is_available' => 'boolean',
            'purchase_price' => 'required|numeric',
            'rental_price' => 'required|numeric',
            'deposit_amount' => 'required|numeric',
            'is_rentable' => 'boolean',
            'requires_deposit' => 'boolean',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date',
        ]);

        $organization->equipments()->create($validated);

        return redirect()->route('app.organizations.equipments.index', $organization)
            ->with('success', 'Matériel créé avec succès.');
    }

    public function edit(Organization $organization, Equipment $equipment)
    {
        $this->authorize('update', $equipment);

        return Inertia::render('App/Organizations/Equipments/Edit', [
            'equipment' => $equipment,
            'organization' => $organization,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'depots' => $organization->depots()->orderBy('name')->get(['id', 'name', 'city']),
        ]);
    }

    public function update(Request $request, Organization $organization, Equipment $equipment)
    {
        $this->authorize('update', $equipment);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'depot_id' => 'required|exists:depots,id',
            'is_available' => 'boolean',
        ]);

        $equipment->update($validated);

        return redirect()->route('app.organizations.equipments.index', $organization)
            ->with('success', 'Matériel modifié avec succès.');
    }

    public function destroy(Organization $organization, Equipment $equipment)
    {
        $this->authorize('delete', $equipment);

        // TODO: Check if the equipment is rented and alert users; improve this
        $rented = $equipment->reservations()->where('end_date', '>=', now())->exists();
        if ($rented) {
            return redirect()->route('app.organizations.equipments.index', $organization)
                ->with('error', 'Ce matériel est actuellement loué et ne peut être supprimé.');
        }

        $equipment->delete();

        return redirect()->route('app.organizations.equipments.index', $organization)
            ->with('success', 'Matériel supprimé avec succès.');
    }
}
