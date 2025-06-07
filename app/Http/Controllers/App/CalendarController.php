<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $organization = $user->currentOrganization;
        $reservations = $organization->reservations;

        return Inertia::render('App/Organizations/Calendar/Index', [
            'reservations' => $reservations,
        ]);
    }
}
