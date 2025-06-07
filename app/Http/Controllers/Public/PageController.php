<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

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

    public function pricing()
    {
        return Inertia::render('Public/Pages/Pricing');
    }
}
