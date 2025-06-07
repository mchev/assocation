<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Organization;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users_count' => User::count(),
                'organizations_count' => Organization::count(),
                'equipment_count' => Equipment::count(),
            ],
            'recent_users' => User::with('organization')
                ->latest()
                ->take(5)
                ->get(),
            'recent_organizations' => Organization::latest()
                ->take(5)
                ->get(),
        ]);
    }
}
