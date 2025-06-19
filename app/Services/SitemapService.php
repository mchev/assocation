<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class SitemapService
{
    /**
     * Génère le contenu XML du sitemap principal
     */
    public function generateMainSitemap(): string
    {
        $staticPages = $this->getStaticPages();
        $dynamicPages = $this->getDynamicPages();

        $allPages = array_merge($staticPages, $dynamicPages);

        return $this->generateXml($allPages);
    }

    /**
     * Génère le contenu XML pour les pages statiques uniquement
     */
    public function generateStaticSitemap(): string
    {
        $staticPages = $this->getStaticPages();

        return $this->generateXml($staticPages);
    }

    /**
     * Génère le contenu XML pour le contenu dynamique avec pagination
     */
    public function generateDynamicSitemap(string $type, int $page = 1): string
    {
        $config = config("sitemap.dynamic_content.{$type}");

        if (! $config) {
            throw new \InvalidArgumentException("Type de contenu dynamique '{$type}' non configuré");
        }

        $perPage = config('sitemap.performance.max_urls_per_sitemap');
        $offset = ($page - 1) * $perPage;

        $model = $config['model'];
        $query = $model::select($config['select_fields']);

        // Appliquer les conditions
        foreach ($config['conditions'] as $field => $value) {
            $query->where($field, $value);
        }

        // Appliquer l'ordre
        $query->orderBy($config['order_by'], $config['order_direction']);

        // Pagination
        $items = $query->offset($offset)->limit($perPage)->get();

        $pages = $items->map(function ($item) use ($config) {
            return [
                'url' => route($config['route'], $item),
                'priority' => $config['priority'],
                'changefreq' => $config['changefreq'],
                'lastmod' => $item->{$config['order_by']}->toISOString(),
            ];
        });

        return $this->generateXml($pages->toArray());
    }

    /**
     * Génère un sitemap index
     */
    public function generateSitemapIndex(): string
    {
        $xmlLines = [
            '<?xml version="'.config('sitemap.xml.version').'" encoding="'.config('sitemap.xml.encoding').'"?>',
            '<sitemapindex xmlns="'.config('sitemap.xml.namespace').'">',
        ];

        // Sitemap pour les pages statiques
        $xmlLines[] = $this->generateSitemapIndexEntry(route('sitemap.static'));

        // Sitemaps pour le contenu dynamique
        foreach (config('sitemap.dynamic_content') as $type => $config) {
            $count = $this->getDynamicContentCount($type);
            $sitemapCount = ceil($count / config('sitemap.performance.max_urls_per_sitemap'));

            for ($i = 1; $i <= $sitemapCount; $i++) {
                $xmlLines[] = $this->generateSitemapIndexEntry(
                    route('sitemap.dynamic', ['type' => $type, 'page' => $i])
                );
            }
        }

        $xmlLines[] = '</sitemapindex>';

        return implode("\n", $xmlLines);
    }

    /**
     * Récupère les pages statiques depuis la configuration
     */
    private function getStaticPages(): array
    {
        $pages = [];

        foreach (config('sitemap.static_pages') as $page) {
            $pages[] = [
                'url' => route($page['route']),
                'priority' => $page['priority'],
                'changefreq' => $page['changefreq'],
                'lastmod' => now()->toISOString(),
            ];
        }

        return $pages;
    }

    /**
     * Récupère le contenu dynamique depuis la configuration
     */
    private function getDynamicPages(): array
    {
        $pages = [];

        foreach (config('sitemap.dynamic_content') as $type => $config) {
            $model = $config['model'];
            $query = $model::select($config['select_fields']);

            // Appliquer les conditions
            foreach ($config['conditions'] as $field => $value) {
                $query->where($field, $value);
            }

            // Appliquer l'ordre
            $query->orderBy($config['order_by'], $config['order_direction']);

            $items = $query->get();

            foreach ($items as $item) {
                $pages[] = [
                    'url' => route($config['route'], $item),
                    'priority' => $config['priority'],
                    'changefreq' => $config['changefreq'],
                    'lastmod' => $item->{$config['order_by']}->toISOString(),
                ];
            }
        }

        return $pages;
    }

    /**
     * Génère le XML à partir d'un tableau de pages
     */
    private function generateXml(array $pages): string
    {
        $xmlLines = [
            '<?xml version="'.config('sitemap.xml.version').'" encoding="'.config('sitemap.xml.encoding').'"?>',
            '<urlset xmlns="'.config('sitemap.xml.namespace').'">',
        ];

        $indent = config('sitemap.xml.indent');

        foreach ($pages as $page) {
            $xmlLines[] = $indent.'<url>';
            $xmlLines[] = $indent.$indent.'<loc>'.htmlspecialchars($page['url']).'</loc>';
            $xmlLines[] = $indent.$indent.'<lastmod>'.$page['lastmod'].'</lastmod>';
            $xmlLines[] = $indent.$indent.'<changefreq>'.$page['changefreq'].'</changefreq>';
            $xmlLines[] = $indent.$indent.'<priority>'.$page['priority'].'</priority>';
            $xmlLines[] = $indent.'</url>';
        }

        $xmlLines[] = '</urlset>';

        return implode("\n", $xmlLines);
    }

    /**
     * Génère une entrée pour le sitemap index
     */
    private function generateSitemapIndexEntry(string $url): string
    {
        $indent = config('sitemap.xml.indent');

        return implode("\n", [
            $indent.'<sitemap>',
            $indent.$indent.'<loc>'.htmlspecialchars($url).'</loc>',
            $indent.$indent.'<lastmod>'.now()->toISOString().'</lastmod>',
            $indent.'</sitemap>',
        ]);
    }

    /**
     * Récupère le nombre d'éléments pour un type de contenu dynamique
     */
    private function getDynamicContentCount(string $type): int
    {
        $config = config("sitemap.dynamic_content.{$type}");

        if (! $config) {
            return 0;
        }

        $model = $config['model'];
        $query = $model::query();

        // Appliquer les conditions
        foreach ($config['conditions'] as $field => $value) {
            $query->where($field, $value);
        }

        return $query->count();
    }

    /**
     * Vérifie si le sitemap est en cache
     */
    public function isCached(): bool
    {
        if (! config('sitemap.cache.enabled')) {
            return false;
        }

        return Cache::has(config('sitemap.cache.key'));
    }

    /**
     * Récupère le sitemap depuis le cache
     */
    public function getCachedSitemap(): ?string
    {
        if (! $this->isCached()) {
            return null;
        }

        return Cache::get(config('sitemap.cache.key'));
    }

    /**
     * Met le sitemap en cache
     */
    public function cacheSitemap(string $content): void
    {
        if (! config('sitemap.cache.enabled')) {
            return;
        }

        $duration = config('sitemap.cache.duration');

        Cache::put(
            config('sitemap.cache.key'),
            $content,
            now()->addHours($duration)
        );

        Cache::put(
            config('sitemap.cache.generated_at_key'),
            now(),
            now()->addHours($duration)
        );
    }

    /**
     * Vide le cache du sitemap
     */
    public function clearCache(): void
    {
        Cache::forget(config('sitemap.cache.key'));
        Cache::forget(config('sitemap.cache.generated_at_key'));
    }

    /**
     * Récupère la date de génération du cache
     */
    public function getCacheGeneratedAt(): ?string
    {
        $timestamp = Cache::get(config('sitemap.cache.generated_at_key'));

        return $timestamp?->toISOString();
    }
}
