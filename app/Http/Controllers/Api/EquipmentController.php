<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function reservationsDatesByMonth(Equipment $equipment, Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $reservations = $equipment->reservationsDatesByMonth($request->start, $request->end);

        return response()->json($reservations);
    }

    public function availableQuantity(Equipment $equipment, Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $availableQuantity = $equipment->availableQuantity($request->start, $request->end);

        return response()->json($availableQuantity);
    }
}
