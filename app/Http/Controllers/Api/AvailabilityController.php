<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Availability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AvailabilityController extends Controller
{
    public function getMonthlyAvailability(Equipment $equipment, Request $request): JsonResponse
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $availabilities = $equipment->availabilities()
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->mapWithKeys(function ($availability) {
                return [$availability->date->format('Y-m-d') => $availability->is_available];
            });

        // Générer toutes les dates du mois
        $dates = collect();
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $dates[$dateString] = $availabilities->get($dateString, true); // Par défaut, disponible
            $currentDate->addDay();
        }

        return response()->json([
            'month' => $month,
            'year' => $year,
            'availabilities' => $dates
        ]);
    }

    public function updateAvailability(Equipment $equipment, Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'is_available' => 'required|boolean',
            'notes' => 'nullable|string'
        ]);

        $availability = $equipment->availabilities()
            ->updateOrCreate(
                ['date' => $request->date],
                [
                    'is_available' => $request->is_available,
                    'notes' => $request->notes
                ]
            );

        return response()->json($availability);
    }
}
