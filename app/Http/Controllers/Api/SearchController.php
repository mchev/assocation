<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function suggestions(Request $request)
    {
        $query = $request->input('query');
        $perPage = 5;

        Log::info('Search request', [
            'query' => $query,
            'page' => $request->input('page', 1)
        ]);

        if (empty($query)) {
            return response()->json([
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => $perPage,
                'total' => 0
            ]);
        }

        $equipment = Equipment::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->with('category:id,name,slug')
            ->paginate($perPage);

        $response = response()->json($equipment);
        
        Log::info('Search response', [
            'total' => $equipment->total(),
            'current_page' => $equipment->currentPage(),
            'last_page' => $equipment->lastPage(),
            'per_page' => $equipment->perPage(),
            'items_count' => count($equipment->items())
        ]);

        return $response;
    }
} 