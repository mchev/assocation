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

        $equipments = $this->getFilteredEquipments($request, $locationPreferences);

        // Handle full page load vs. infinite scroll request
        if (!request()->header('X-Inertia')) {
            // Full page load - fetch all pages up to current
            $currentPage = $request->input('page', 1);
            $perPage = 10;
            $allResults = collect();

            for ($page = 1; $page <= $currentPage; $page++) {
                $pageResults = $this->getFilteredEquipments($request, $locationPreferences, $page, $perPage);
                $allResults = $allResults->concat($pageResults->items());
            }

            $equipments = new \Illuminate\Pagination\LengthAwarePaginator(
                $allResults,
                $this->getFilteredEquipments($request, $locationPreferences)->total(),
                $perPage,
                $currentPage
            );
        } else {
            // Infinite scroll request - use Inertia::merge for automatic merging
            $currentPage = $request->input('page', 1);
            if ($currentPage > 1) {
                $equipments = $this->getFilteredEquipments($request, $locationPreferences, $currentPage, 10);
                return Inertia::merge(['equipments' => $equipments]);
            }
        }

        return Inertia::render('Public/Home', [
            'equipments' => $equipments,
            'filters' => [
                'search' => $request->search,
                'categories' => $request->categories,
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

    private function getFilteredEquipments(Request $request, $locationPreferences, $page = null, $perPage = 10)
    {
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
        if ($request->filled('categories')) {
            $categories = Category::whereIn('id', $request->categories)->with('children')->get();
            $query->where(function ($q) use ($categories) {
                $q->whereIn('category_id', $categories->pluck('id'))
                    ->orWhereIn('category_id', $categories->pluck('children')->flatten()->pluck('id'));
            });
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

        if ($page) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }

        return $query->paginate($perPage);
    }
}
