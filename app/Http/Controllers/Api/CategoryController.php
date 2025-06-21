<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $categories = Category::select('id', 'name')
            ->orderBy('name')
            ->whereNull('parent_id')
            ->with('children')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
                orWhereHas('children', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
            })
            ->get();

        return response()->json($categories);
    }

    public function tree()
    {
        $categories = Category::whereNull('parent_id')->with('children')->get();

        return response()->json($categories);
    }
}
