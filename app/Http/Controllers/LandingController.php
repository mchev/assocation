<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $cacheKey = 'equipment_search_'.md5(json_encode($request->all()));

        $equipment = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request) {
            $query = Equipment::with([
                'organization' => function ($query) {
                    $query->select('id', 'name');
                },
                'depot' => function ($query) {
                    $query->select('id', 'name', 'city', 'latitude', 'longitude');
                },
                'reservations' => function ($query) use ($request) {
                    $query->where('status', 'approved')
                        ->when($request->has(['start_date', 'end_date']), function ($query) use ($request) {
                            $query->where(function ($q) use ($request) {
                                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                                    ->orWhere(function ($q) use ($request) {
                                        $q->where('start_date', '<=', $request->start_date)
                                            ->where('end_date', '>=', $request->end_date);
                                    });
                            });
                        });
                },
                'category' => function ($query) {
                    $query->select('id', 'name');
                },
            ])
                ->select('id', 'name', 'description', 'category_id', 'is_available', 'rental_price', 'depot_id', 'organization_id')
                ->whereNotNull('organization_id');

            // Filtrage par recherche textuelle (nom ou description)
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filtrage par localisation (ville du dépôt) avec rayon
            if ($request->has('location') && ! empty($request->input('location'))) {
                $location = $request->input('location');
                $radius = $request->input('radius', 10);

                // Si la location est une chaîne de caractères (ville), on cherche par nom de ville
                if (is_string($location)) {
                    $query->whereHas('depot', function ($q) use ($location) {
                        $q->where('city', 'like', "%{$location}%");
                    });
                }
                // Si la location est un tableau avec lat/lng, on utilise la formule de distance
                elseif (is_array($location) && isset($location['lat']) && isset($location['lng'])) {
                    $query->whereHas('depot', function ($q) use ($location, $radius) {
                        $q->whereRaw('
                            (6371 * acos(
                                cos(radians(?)) * 
                                cos(radians(latitude)) * 
                                cos(radians(longitude) - radians(?)) + 
                                sin(radians(?)) * 
                                sin(radians(latitude))
                            )) <= ?
                        ', [$location['lat'], $location['lng'], $location['lat'], $radius]);
                    });
                }
            }

            // Filtrage par catégorie
            if ($request->has('category')) {
                $query->where('category_id', $request->input('category'));
            }

            // Filtrage par disponibilité
            if ($request->has('available')) {
                $query->where('is_available', true);
            }

            // Filtrage par prix
            if ($request->has('min_price')) {
                $query->where('rental_price', '>=', $request->input('min_price'));
            }
            if ($request->has('max_price')) {
                $query->where('rental_price', '<=', $request->input('max_price'));
            }

            // Tri par défaut par disponibilité puis par distance
            $query->orderBy('is_available', 'desc');
            if ($request->has('location') && ! empty($request->input('location'))) {
                $location = $request->input('location');
                if (is_array($location) && isset($location['lat']) && isset($location['lng'])) {
                    $query->orderByRaw('
                        (6371 * acos(
                            cos(radians(?)) * 
                            cos(radians(depots.latitude)) * 
                            cos(radians(depots.longitude) - radians(?)) + 
                            sin(radians(?)) * 
                            sin(radians(depots.latitude))
                        ))
                    ', [$location['lat'], $location['lng'], $location['lat']]);
                }
            }

            return $query->paginate(12)->withQueryString();
        });

        // Récupérer les statistiques pour les filtres
        $stats = Cache::remember('equipment_stats', now()->addHours(1), function () {
            $categories = \App\Models\Category::whereIn('id', Equipment::distinct()->pluck('category_id')->filter())->get(['id', 'name']);

            return [
                'min_price' => Equipment::min('rental_price'),
                'max_price' => Equipment::max('rental_price'),
                'categories' => $categories,
            ];
        });

        return Inertia::render('Landing', [
            'equipment' => $equipment,
            'filters' => $request->only(['search', 'location', 'radius', 'category', 'available', 'min_price', 'max_price', 'start_date', 'end_date']),
            'stats' => $stats,
        ]);
    }
}
