<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Equipment;
use App\Services\LocationPreferencesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __construct(
        private readonly LocationPreferencesService $locationService
    ) {}

    public function index(Request $request)
    {
        // Handle location preferences
        $locationPreferences = $this->locationService->updateLocationPreferences($request)
            ?? $this->locationService->getLocationPreferences($request);

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

        // Apply category filter
        if ($request->filled('category') && $request->category !== 'all') {
            $category = Category::find($request->category);
            if ($category) {
                $query->where(function ($q) use ($category) {
                    $q->where('category_id', $category->id)
                        ->orWhereIn('category_id', $category->children()->pluck('id'))
                        ->orWhereIn('category_id', $category->parent()->pluck('id'));
                });
            }
        }

        // Apply location filter if preferences exist
        if ($locationPreferences && isset($locationPreferences['coordinates'], $locationPreferences['radius'])) {
            $location = $locationPreferences['coordinates'];
            $query->whereHas('depot', function ($query) use ($location, $locationPreferences) {
                $query->whereRaw('ST_Distance_Sphere(
                    POINT(depots.longitude, depots.latitude),
                    POINT(?, ?)
                ) <= ?', [
                    $location['lng'],
                    $location['lat'],
                    $locationPreferences['radius'] * 1000, // Convert km to meters
                ]);
            });
        }

        // Organization filter
        $query->when($request->filled('organizations'), function ($q) use ($request) {
            $q->whereIn('organization_id', $request->organizations);
        });

        return Inertia::render('Public/Home', [
            'equipments' => $query->paginate(12),
            'filters' => [
                'search' => $request->search,
                'category' => $request->category,
                'organizations' => $request->organizations,
                'coordinates' => $locationPreferences['coordinates'] ?? null,
                'radius' => $locationPreferences['radius'] ?? null,
                'city' => $locationPreferences['city'] ?? null,
                'postcode' => $locationPreferences['postcode'] ?? null,
            ],
            'stats' => [
                'categories' => Cache::remember('categories.tree', 3600, function () {
                    return Category::with(['children' => function ($query) {
                        $query->select('id', 'name', 'parent_id');
                    }])
                        ->whereNull('parent_id')
                        ->select('id', 'name')
                        ->get();
                }),
            ],
        ]);
    }
}
