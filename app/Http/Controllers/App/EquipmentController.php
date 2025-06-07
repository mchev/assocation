<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $organization = auth()->user()->organization;
        
        if (!$organization) {
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
            $query->where('category', $request->category);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('availability')) {
            $query->where('is_available', $request->availability === 'available');
        }

        $equipments = $query->paginate(12);

        return Inertia::render('App/Organizations/Equipments/Index', [
            'organization' => $organization,
            'equipment' => $equipments,
            'filters' => $request->only(['search', 'category', 'condition', 'availability']),
            'can' => [
                'create' => true, // You might want to add proper authorization here
                'update' => true, // You might want to add proper authorization here
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('App/Organizations/Equipments/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'depot_id' => 'required|exists:depots,id',
            'is_available' => 'boolean',
        ]);

        $equipment = auth()->user()->organization?->equipments()->create($validated);

        return redirect()->route('app.organizations.equipments.index')
            ->with('success', 'Equipment created successfully.');
    }

    public function edit(Equipment $equipment)
    {
        $this->authorize('update', $equipment);

        return Inertia::render('App/Equipment/Edit', [
            'equipment' => $equipment
        ]);
    }

    public function update(Request $request, Equipment $equipment)
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

        return redirect()->route('app.equipment.index')
            ->with('success', 'Equipment updated successfully.');
    }

    public function destroy(Equipment $equipment)
    {
        $this->authorize('delete', $equipment);

        $equipment->delete();

        return redirect()->route('app.equipment.index')
            ->with('success', 'Equipment deleted successfully.');
    }
} 