<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\SitemapService;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
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

    /**
     * Affiche la page FAQ
     */
    public function faq(): Response
    {
        return Inertia::render('Faq');
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

    /**
     * Génère le sitemap XML principal
     */
    public function sitemap(SitemapService $sitemapService)
    {
        // Vérifier le cache
        if ($sitemapService->isCached()) {
            return $this->createSitemapResponse(
                $sitemapService->getCachedSitemap(),
                true,
                $sitemapService->getCacheGeneratedAt()
            );
        }

        // Générer le sitemap
        $content = $sitemapService->generateMainSitemap();

        // Mettre en cache
        $sitemapService->cacheSitemap($content);

        return $this->createSitemapResponse($content, false);
    }

    /**
     * Génère un sitemap index
     */
    public function sitemapIndex(SitemapService $sitemapService)
    {
        $content = $sitemapService->generateSitemapIndex();

        return $this->createSitemapResponse($content, false);
    }

    /**
     * Génère un sitemap pour les pages statiques
     */
    public function sitemapStatic(SitemapService $sitemapService)
    {
        $content = $sitemapService->generateStaticSitemap();

        return $this->createSitemapResponse($content, false);
    }

    /**
     * Génère un sitemap pour le contenu dynamique
     */
    public function sitemapDynamic(SitemapService $sitemapService, string $type, int $page = 1)
    {
        $content = $sitemapService->generateDynamicSitemap($type, $page);

        return $this->createSitemapResponse($content, false);
    }

    /**
     * Crée une réponse HTTP pour le sitemap
     */
    private function createSitemapResponse(string $content, bool $cached = false, ?string $generatedAt = null): \Illuminate\Http\Response
    {
        $headers = [
            'Content-Type' => config('sitemap.headers.content_type'),
            'Cache-Control' => config('sitemap.headers.cache_control'),
        ];

        if (config('sitemap.headers.x_sitemap_cached')) {
            $headers['X-Sitemap-Cached'] = $cached ? 'true' : 'false';
        }

        if (config('sitemap.headers.x_sitemap_generated_at')) {
            $headers['X-Sitemap-Generated-At'] = $generatedAt ?? now()->toISOString();
        }

        return response($content, 200, $headers);
    }
}
