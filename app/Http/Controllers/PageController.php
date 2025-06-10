<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    /**
     * Affiche la page d'accueil
     */
    public function home(): Response
    {
        return Inertia::render('Welcome');
    }

    /**
     * Affiche la page de découverte de la plateforme
     */
    public function discover(): Response
    {
        $popularCategories = Category::withCount('equipment')
            ->orderByDesc('equipment_count')
            ->take(4)
            ->get()
            ->map(fn ($category) => [
                'name' => $category->name,
                'count' => $category->equipment_count,
                'description' => $category->description,
            ]);

        return Inertia::render('Discover', [
            'popularCategories' => $popularCategories,
        ]);
    }

    /**
     * Affiche la page "Comment ça marche"
     */
    public function howItWorks(): Response
    {
        return Inertia::render('HowItWorks');
    }


    public function privacy()
    {
        return Inertia::render('Privacy');
    }

    public function terms()
    {
        return Inertia::render('Terms');
    }

    public function conditions()
    {
        return Inertia::render('Conditions');
    }
}
