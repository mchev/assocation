<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function about()
    {
        return Inertia::render('Public/Pages/About');
    }

    public function contact()
    {
        return Inertia::render('Public/Pages/Contact');
    }
}