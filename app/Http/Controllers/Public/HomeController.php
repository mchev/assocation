<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::query()
            ->select([
                'id',
                'name',
                'description',
                'rental_price',
                'is_available',
                'images',
                'category_id',
                'organization_id',
                'depot_id',
            ])
            ->with([
                'category:id,name',
                'organization:id,name',
                'depot:id,city'
            ])
            ->where('is_available', true);

        // Apply filters
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('location')) {
            // Add location-based filtering logic here
        }

        if ($request->filled('min_price')) {
            $query->where('rental_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('rental_price', '<=', $request->max_price);
        }

        $equipment = $query->paginate(12);

        // Get stats for filters
        $stats = [
            'min_price' => Equipment::min('rental_price') ?? 0,
            'max_price' => Equipment::max('rental_price') ?? 1000,
            'categories' => Category::select(['id', 'name', 'slug'])->get(),
        ];

        return Inertia::render('Public/Home', [
            'equipment' => $equipment,
            'filters' => $request->only(['search', 'category', 'location', 'radius', 'min_price', 'max_price', 'start_date', 'end_date']),
            'stats' => $stats,
        ]);
    }
} 