<?php

namespace App\Http\Controllers\App;

use App\Actions\Equipment\DeleteEquipmentAction;
use App\Actions\Equipment\GenerateEquipmentImageFromAiAction;
use App\Actions\Equipment\StoreEquipmentAction;
use App\Actions\Equipment\UpdateEquipmentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\StoreRequest;
use App\Http\Requests\Equipment\UpdateRequest;
use App\Models\Equipment;
use Illuminate\Http\JsonResponse;
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
            'depots' => $organization->depots()->orderBy('name')->get(['id', 'name', 'city']),
        ]);
    }

    public function suggestImages(Request $request, GenerateEquipmentImageFromAiAction $action): JsonResponse
    {
        $this->authorize('create', [Equipment::class, $request->user()->currentOrganization]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'brand' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ]);

        try {
            $urls = $action->suggestImageUrls($validated);
        } catch (\Throwable $e) {
            return response()->json(['urls' => [], 'error' => $e->getMessage()], 422);
        }

        return response()->json(['urls' => $urls]);
    }

    public function store(StoreRequest $request, StoreEquipmentAction $storeEquipmentAction)
    {
        $organization = $request->user()->currentOrganization;
        $this->authorize('create', [Equipment::class, $organization]);

        $validated = $request->validated();
        $findImageFromWeb = filter_var($validated['find_image_from_web'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $suggestedImageUrls = $validated['suggested_image_urls'] ?? [];
        unset($validated['find_image_from_web'], $validated['suggested_image_urls']);
        $validated['organization_id'] = $organization->id;
        $images = $request->file('images', []);

        $equipment = $storeEquipmentAction->execute($validated, $images);

        $generateAction = app(GenerateEquipmentImageFromAiAction::class);

        if (! empty($suggestedImageUrls)) {
            foreach (array_slice($suggestedImageUrls, 0, 3) as $url) {
                if (is_string($url) && str_starts_with($url, 'http')) {
                    try {
                        $generateAction->attachImageFromUrl($equipment, $url);
                    } catch (\Throwable) {
                        // Skip failed URL, continue with others
                    }
                }
            }
        } elseif ($findImageFromWeb) {
            try {
                $generateAction->execute($equipment);
            } catch (\Throwable $e) {
                return redirect()
                    ->route('app.organizations.equipments.index')
                    ->with('success', 'L\'équipement a été ajouté avec succès.')
                    ->with('warning', 'La recherche automatique de photo a échoué : '.$e->getMessage());
            }
        }

        $message = (! empty($suggestedImageUrls) || $findImageFromWeb)
            ? 'L\'équipement a été ajouté avec succès. Les photos ont été enregistrées.'
            : 'L\'équipement a été ajouté avec succès.';

        return redirect()
            ->route('app.organizations.equipments.index')
            ->with('success', $message);
    }

    public function edit(Request $request, Equipment $equipment)
    {
        $this->authorize('update', $equipment);
        $user = $request->user();

        $equipment->load(['category', 'depot', 'images']);

        // Get all organizations the user belongs to
        $organizations = $user->organizations()->orderBy('name')->get(['organizations.id', 'organizations.name']);

        // Get all depots from all organizations the user belongs to
        $depots = \App\Models\Depot::whereIn('organization_id', $organizations->pluck('id'))
            ->orderBy('name')
            ->get(['id', 'name', 'city', 'organization_id']);

        return Inertia::render('App/Organizations/Equipments/Edit', [
            'equipment' => $equipment,
            'organization' => $equipment->organization,
            'organizations' => $organizations,
            'depots' => $depots,
        ]);
    }

    public function update(UpdateRequest $request, Equipment $equipment)
    {
        $this->authorize('update', $equipment);

        $validated = $request->validated();
        $images = $request->file('images', []);

        app(UpdateEquipmentAction::class)->execute($equipment, $validated, $images);

        return redirect()->route('app.organizations.equipments.index')
            ->with('success', 'Matériel modifié avec succès.');
    }

    public function destroy(Equipment $equipment)
    {
        $this->authorize('delete', $equipment);

        try {
            app(DeleteEquipmentAction::class)->execute($equipment);

            return redirect()->route('app.organizations.equipments.index')
                ->with('success', 'Matériel supprimé avec succès.');
        } catch (\Exception $e) {
            // Return to the edit page with the error message so user can see the context
            return redirect()->route('app.organizations.equipments.edit', $equipment)
                ->with('error', $e->getMessage());
        }
    }
}
