<?php

namespace App\Http\Controllers\App;

use App\Actions\Equipment\StoreEquipmentAction;
use App\Actions\Equipment\UpdateEquipmentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\StoreRequest;
use App\Http\Requests\Equipment\UpdateRequest;
use App\Models\Category;
use App\Models\Equipment;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $organization = $user->currentOrganization;

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
            case 'depot':
                $query->join('depots', 'equipments.depot_id', '=', 'depots.id')
                    ->orderBy('depots.name', $direction)
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
        ]);
    }

    public function create(Request $request)
    {
        $organization = $request->user()->currentOrganization;
        $this->authorize('create', [Equipment::class, $organization]);

        return Inertia::render('App/Organizations/Equipments/Create', [
            'organization' => $organization,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'depots' => $organization->depots()->orderBy('name')->get(['id', 'name', 'city']),
        ]);
    }

    public function store(StoreRequest $request, StoreEquipmentAction $storeEquipmentAction)
    {
        $organization = $request->user()->currentOrganization;
        $this->authorize('create', [Equipment::class, $organization]);

        $validated = $request->validated();
        $validated['organization_id'] = $organization->id;
        $images = $request->file('images', []);

        $equipment = $storeEquipmentAction->execute($validated, $images);

        return redirect()
            ->route('app.organizations.equipments.index', $organization)
            ->with('success', 'L\'équipement a été ajouté avec succès.');
    }

    public function edit(Request $request, Equipment $equipment)
    {
        $this->authorize('update', $equipment);
        $organization = $request->user()->currentOrganization;

        $equipment->load(['category', 'depot', 'images']);

        return Inertia::render('App/Organizations/Equipments/Edit', [
            'equipment' => $equipment,
            'organization' => $organization,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'depots' => $organization->depots()->orderBy('name')->get(['id', 'name', 'city']),
        ]);
    }

    public function update(UpdateRequest $request, Organization $organization, Equipment $equipment)
    {
        $this->authorize('update', $equipment);

        $validated = $request->validated();
        $images = $request->file('images', []);

        app(UpdateEquipmentAction::class)->execute($equipment, $validated, $images);

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
