<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $organization = $user->organization;

        return Inertia::render('App/Dashboard', [
            'user' => $user,
            'organization' => $organization,
            'stats' => [
                'equipment_count' => $organization?->equipment()->count(),
                'reservations_count' => $organization?->reservations()->count(),
                'depots_count' => $organization?->depots()->count(),
            ]
        ]);
    }
} 