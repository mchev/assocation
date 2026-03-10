<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Equipment\GenerateEquipmentImageFromAiAction;
use App\Actions\Equipment\UpdateEquipmentAction;
use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class EquipmentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Equipment::query()
            ->with(['organization', 'category', 'images'])
            ->withCount('images')
            ->orderBy('name');

        if ($request->boolean('without_photos')) {
            $query->has('images', '=', 0);
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        $equipments = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Equipments/Index', [
            'equipments' => $equipments,
            'filters' => $request->only(['without_photos', 'search']),
        ]);
    }

    public function show(Equipment $equipment): Response
    {
        $equipment->load(['organization', 'category', 'depot', 'images']);

        return Inertia::render('Admin/Equipments/Show', [
            'equipment' => $equipment,
        ]);
    }

    public function storeImage(Request $request, Equipment $equipment): RedirectResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:10240'],
        ]);

        $data = $equipment->only([
            'name', 'brand', 'description', 'category_id', 'organization_id', 'condition',
            'quantity', 'depot_id', 'purchase_price', 'rental_price', 'deposit_amount',
            'is_available', 'requires_deposit', 'is_rentable',
            'last_maintenance_date', 'next_maintenance_date',
        ]);

        $action = app(UpdateEquipmentAction::class);
        $action->execute($equipment, $data, [$request->file('image')]);

        return back()->with('success', 'Photo ajoutée.');
    }

    public function generateImage(Equipment $equipment, GenerateEquipmentImageFromAiAction $action): RedirectResponse
    {
        try {
            $result = $action->execute($equipment);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'generate' => 'Impossible de générer l\'image : '.$e->getMessage(),
            ]);
        }

        return back()
            ->with('success', 'Photo trouvée sur le web et ajoutée.')
            ->with('generateSteps', $result['steps']);
    }

    public function destroyImage(Equipment $equipment, int $image): RedirectResponse
    {
        $imageModel = $equipment->images()->findOrFail($image);
        $imageModel->delete();

        return back()->with('success', 'Photo supprimée.');
    }
}
