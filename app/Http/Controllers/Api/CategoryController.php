<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return response()->json($categories);
    }

    public function tree()
    {
        $categories = Category::whereNull('parent_id')->with('children')->get();

        return response()->json($categories);
    }
}
