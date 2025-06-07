<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Organization;
use App\Http\Requests\Equipment\StoreRequest;
use App\Http\Requests\Equipment\UpdateRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EquipmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Equipment::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization)
    {
        $equipment = $organization->equipment()
            ->withCount('reservations')
            ->paginate(12);

        return Inertia::render('Organizations/Equipment/Index', [
            'organization' => $organization,
            'equipment' => $equipment,
            'can' => [
                'create' => auth()->user()->can('create', [Equipment::class, $organization]),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organization $organization)
    {
        return Inertia::render('Organizations/Equipment/Create', [
            'organization' => $organization,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request, Organization $organization)
    {
        $equipment = $organization->equipment()->create($request->validated());

        return redirect()
            ->route('organizations.equipment.show', [$organization, $equipment])
            ->with('success', 'Équipement ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization, Equipment $equipment)
    {
        $equipment->load(['reservations' => function ($query) {
            $query->latest()->take(5);
        }]);

        return Inertia::render('Organizations/Equipment/Show', [
            'organization' => $organization,
            'equipment' => $equipment,
            'can' => [
                'update' => auth()->check() ? auth()->user()->can('update', $equipment) : false,
                'delete' => auth()->check() ? auth()->user()->can('delete', $equipment) : false,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization, Equipment $equipment)
    {
        return Inertia::render('Organizations/Equipment/Edit', [
            'organization' => $organization,
            'equipment' => $equipment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Organization $organization, Equipment $equipment)
    {
        $equipment->update($request->validated());

        return redirect()
            ->route('organizations.equipment.show', [$organization, $equipment])
            ->with('success', 'Équipement mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization, Equipment $equipment)
    {
        $equipment->delete();

        return redirect()
            ->route('organizations.equipment.index', $organization)
            ->with('success', 'Équipement supprimé avec succès.');
    }
}
