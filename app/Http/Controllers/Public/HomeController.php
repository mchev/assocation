<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {

        if ($request->user()) {

            // Get the cached user preferences
            $userPreferences = Cache::get('user_preferences_'.$request->user()->id);

            // Compare the user preferences with the request
            if ($request->filled('coordinates') && $request->filled('radius') && $request->filled('city') && $request->filled('postcode')) {
                if (! $userPreferences || $userPreferences['coordinates'] !== $request->coordinates || $userPreferences['radius'] !== $request->radius || $userPreferences['city'] !== $request->city || $userPreferences['postcode'] !== $request->postcode) {
                    // Update the user preferences
                    $request->user()->update([
                        'preferences' => $request->only(['coordinates', 'radius', 'city', 'postcode']),
                    ]);
                    // Store the user preferences in the cache
                    Cache::put('user_preferences_'.$request->user()->id, $request->only(['coordinates', 'radius', 'city', 'postcode']), 60 * 60 * 24 * 30);
                    $userPreferences = $request->only(['coordinates', 'radius', 'city', 'postcode']);
                }
            }

        } else {
            $userPreferences = $request->only(['coordinates', 'radius', 'city', 'postcode']);
        }

        $query = Equipment::query()
            ->select([
                'id',
                'name',
                'description',
                'rental_price',
                'is_available',
                'category_id',
                'organization_id',
                'depot_id',
            ])
            ->with([
                'category:id,name',
                'organization:id,name',
                'depot:id,city',
                'images',
            ])
            ->orderByDesc('updated_at')
            ->where('is_available', true);

        // Apply filters
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('category')) {
            if ($request->category !== 'all') {
                $query->where('category_id', $request->category);
            }
        }

        if ($userPreferences && $userPreferences['coordinates'] && $userPreferences['radius']) {
            $location = $userPreferences['coordinates'];
            $query->whereHas('depot', function ($query) use ($location, $userPreferences) {
                $query->whereRaw('ST_Distance_Sphere(
                    POINT(depots.longitude, depots.latitude),
                    POINT(?, ?)
                ) <= ?', [
                    $location['lng'],
                    $location['lat'],
                    $userPreferences['radius'] * 1000, // Convert km to meters
                ]);
            });
        }

        // Availability
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDoesntHave('reservations', function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
            });
        }

        $equipments = $query->paginate(12);

        // Get stats for filters
        $stats = [
            'categories' => Category::select(['id', 'name', 'slug'])->orderBy('name')->get(),
        ];

        return Inertia::render('Public/Home', [
            'equipments' => $equipments,
            'filters' => [
                'search' => $request->search,
                'category' => $request->category,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'coordinates' => $userPreferences['coordinates'] ?? null,
                'radius' => $userPreferences['radius'] ?? null,
                'city' => $userPreferences['city'] ?? null,
                'postcode' => $userPreferences['postcode'] ?? null,
            ],
            'stats' => $stats,
        ]);
    }
}
