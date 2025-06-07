<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Inertia\Inertia;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = Equipment::with(['category', 'organization', 'depot'])
            ->where('is_available', true)
            ->where('is_rentable', true)
            ->paginate(12);

        return Inertia::render('Public/Equipments/Index', [
            'equipment' => $equipment
        ]);
    }

    public function show(Request $request, Equipment $equipment)
    {
        $equipment->load(['category', 'depot']);

        return Inertia::render('Public/Equipments/Show', [
            'equipment' => $equipment,
        ]);
    }
} 