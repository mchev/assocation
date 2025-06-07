<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        return Inertia::render('Discover');
    }

    /**
     * Affiche la page "Comment ça marche"
     */
    public function howItWorks(): Response
    {
        return Inertia::render('HowItWorks');
    }
} 