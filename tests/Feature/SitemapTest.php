<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Depot;
use App\Models\Equipment;
use App\Models\Organization;
use App\Services\SitemapService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_returns_valid_xml()
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');

        $xml = $response->getContent();

        // Vérifier que c'est du XML valide
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $xml);
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $xml);
        $this->assertStringContainsString('</urlset>', $xml);
    }

    public function test_sitemap_includes_static_pages()
    {
        $response = $this->get('/sitemap.xml');
        $xml = $response->getContent();

        // Vérifier que les pages statiques sont présentes
        $this->assertStringContainsString(route('home'), $xml);
        $this->assertStringContainsString(route('discover'), $xml);
        $this->assertStringContainsString(route('how-it-works'), $xml);
        $this->assertStringContainsString(route('faq'), $xml);
        $this->assertStringContainsString(route('terms'), $xml);
        $this->assertStringContainsString(route('conditions'), $xml);
        $this->assertStringContainsString(route('privacy'), $xml);
    }

    public function test_sitemap_includes_active_equipment()
    {
        // Créer des données de test
        $organization = Organization::factory()->create();
        $depot = Depot::factory()->create(['organization_id' => $organization->id]);
        $category = Category::factory()->create();

        // Créer des équipements actifs
        $activeEquipment = Equipment::factory()->create([
            'organization_id' => $organization->id,
            'depot_id' => $depot->id,
            'category_id' => $category->id,
            'is_active' => true,
            'is_available' => true,
        ]);

        // Créer un équipement inactif (ne doit pas apparaître)
        $inactiveEquipment = Equipment::factory()->create([
            'organization_id' => $organization->id,
            'depot_id' => $depot->id,
            'category_id' => $category->id,
            'is_active' => false,
            'is_available' => true,
        ]);

        $response = $this->get('/sitemap.xml');
        $xml = $response->getContent();

        // Vérifier que l'équipement actif est présent
        $this->assertStringContainsString(route('equipments.show', $activeEquipment), $xml);

        // Vérifier que l'équipement inactif n'est pas présent
        $this->assertStringNotContainsString(route('equipments.show', $inactiveEquipment), $xml);
    }

    public function test_sitemap_uses_cache()
    {
        // Vider le cache
        $sitemapService = app(SitemapService::class);
        $sitemapService->clearCache();

        // Première requête (génère le cache)
        $response1 = $this->get('/sitemap.xml');
        $this->assertEquals('false', $response1->headers->get('X-Sitemap-Cached'));

        // Deuxième requête (utilise le cache)
        $response2 = $this->get('/sitemap.xml');
        $this->assertEquals('true', $response2->headers->get('X-Sitemap-Cached'));
    }

    public function test_sitemap_has_correct_priority_and_changefreq()
    {
        $response = $this->get('/sitemap.xml');
        $xml = $response->getContent();

        // Vérifier les priorités et fréquences de changement
        $this->assertStringContainsString('<priority>1.0</priority>', $xml); // Home page
        $this->assertStringContainsString('<priority>0.9</priority>', $xml); // Discover
        $this->assertStringContainsString('<priority>0.8</priority>', $xml); // How it works
        $this->assertStringContainsString('<priority>0.7</priority>', $xml); // FAQ
        $this->assertStringContainsString('<priority>0.3</priority>', $xml); // Legal pages

        $this->assertStringContainsString('<changefreq>daily</changefreq>', $xml);
        $this->assertStringContainsString('<changefreq>weekly</changefreq>', $xml);
        $this->assertStringContainsString('<changefreq>monthly</changefreq>', $xml);
        $this->assertStringContainsString('<changefreq>yearly</changefreq>', $xml);
    }

    public function test_sitemap_has_valid_lastmod_dates()
    {
        $response = $this->get('/sitemap.xml');
        $xml = $response->getContent();

        // Vérifier que les dates lastmod sont au format ISO (plus flexible)
        $this->assertMatchesRegularExpression('/<lastmod>\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d+Z<\/lastmod>/', $xml);
    }

    public function test_sitemap_static_endpoint()
    {
        $response = $this->get('/sitemap-static.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');

        $xml = $response->getContent();

        // Vérifier que seules les pages statiques sont présentes
        $this->assertStringContainsString(route('home'), $xml);
        $this->assertStringNotContainsString('equipments.show', $xml);
    }

    public function test_sitemap_dynamic_endpoint()
    {
        // Créer des données de test
        $organization = Organization::factory()->create();
        $depot = Depot::factory()->create(['organization_id' => $organization->id]);
        $category = Category::factory()->create();

        Equipment::factory()->create([
            'organization_id' => $organization->id,
            'depot_id' => $depot->id,
            'category_id' => $category->id,
            'is_active' => true,
            'is_available' => true,
        ]);

        $response = $this->get('/sitemap-equipment-1.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');

        $xml = $response->getContent();

        // Vérifier que seuls les équipements sont présents (chercher le pattern de route)
        $this->assertMatchesRegularExpression('/equipments\/\d+/', $xml);
    }

    public function test_sitemap_service_methods()
    {
        $sitemapService = app(SitemapService::class);

        // Test de génération du sitemap principal
        $mainSitemap = $sitemapService->generateMainSitemap();
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $mainSitemap);
        $this->assertStringContainsString('<urlset', $mainSitemap);

        // Test de génération du sitemap statique
        $staticSitemap = $sitemapService->generateStaticSitemap();
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $staticSitemap);
        $this->assertStringContainsString(route('home'), $staticSitemap);

        // Test de génération du sitemap index
        $indexSitemap = $sitemapService->generateSitemapIndex();
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $indexSitemap);
        $this->assertStringContainsString('<sitemapindex', $indexSitemap);
    }

    public function test_sitemap_handles_large_number_of_equipment()
    {
        // Créer beaucoup d'équipements pour tester les performances
        $organization = Organization::factory()->create();
        $depot = Depot::factory()->create(['organization_id' => $organization->id]);
        $category = Category::factory()->create();

        // Créer 100 équipements actifs
        Equipment::factory()->count(100)->create([
            'organization_id' => $organization->id,
            'depot_id' => $depot->id,
            'category_id' => $category->id,
            'is_active' => true,
            'is_available' => true,
        ]);

        $startTime = microtime(true);
        $response = $this->get('/sitemap.xml');
        $endTime = microtime(true);

        $response->assertStatus(200);

        // Le sitemap doit être généré en moins de 2 secondes
        $this->assertLessThan(2.0, $endTime - $startTime);

        $xml = $response->getContent();

        // Compter le nombre d'URLs d'équipements en cherchant le pattern de route
        $equipmentUrlPattern = '/equipments\/\d+/';
        $equipmentUrlCount = preg_match_all($equipmentUrlPattern, $xml);

        // Vérifier qu'il y a exactement 100 URLs d'équipements
        $this->assertEquals(100, $equipmentUrlCount,
            "Expected 100 equipment URLs, but found {$equipmentUrlCount}. ".
            'XML contains: '.substr($xml, 0, 500).'...'
        );

        // Vérifier aussi qu'il y a les pages statiques (7 pages)
        $staticPageCount = 0;
        $staticPages = ['home', 'discover', 'how-it-works', 'faq', 'terms', 'conditions', 'privacy'];
        foreach ($staticPages as $page) {
            if (str_contains($xml, route($page))) {
                $staticPageCount++;
            }
        }

        $this->assertEquals(7, $staticPageCount,
            "Expected 7 static pages, but found {$staticPageCount}"
        );

        // Vérifier le nombre total d'URLs (pages statiques + équipements)
        $totalUrlCount = substr_count($xml, '<url>');
        $this->assertEquals(107, $totalUrlCount,
            "Expected 107 total URLs (7 static + 100 equipment), but found {$totalUrlCount}"
        );
    }
}
