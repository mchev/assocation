<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $ids = $request->input('ids', []);

        $query = Organization::select('id', 'name');

        if (! empty($ids)) {
            return response()->json(['data' => $query->whereIn('id', $ids)->get()], 200);
        }

        $organizations = $query->when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })
            ->orderBy('name')
            ->paginate(10);

        return response()->json($organizations, 200);
    }
}
