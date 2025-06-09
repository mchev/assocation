<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = Equipment::with(['category', 'organization', 'depot'])
            ->where('is_available', true)
            ->where('is_rentable', true)
            ->paginate(12);

        return Inertia::render('Public/Equipments/Index', [
            'equipment' => $equipment,
        ]);
    }

    public function show(Request $request, Equipment $equipment)
    {
        $equipment->load(['category', 'depot', 'images']);

        return Inertia::render('Public/Equipments/Show', [
            'equipment' => $equipment,
        ]);
    }
}
