<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate {--force : Force regeneration even if cache exists} {--type=main : Type of sitemap to generate (main, static, dynamic)} {--dynamic-type=equipment : Dynamic content type} {--page=1 : Page number for dynamic sitemaps}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and cache the sitemap for better performance';

    /**
     * Execute the console command.
     */
    public function handle(SitemapService $sitemapService)
    {
        $type = $this->option('type');
        $force = $this->option('force');

        $this->info("Generating {$type} sitemap...");

        // Vérifier le cache pour le sitemap principal
        if ($type === 'main' && ! $force && $sitemapService->isCached()) {
            $this->info('Sitemap already cached. Use --force to regenerate.');

            return 0;
        }

        try {
            $content = match ($type) {
                'main' => $sitemapService->generateMainSitemap(),
                'static' => $sitemapService->generateStaticSitemap(),
                'dynamic' => $sitemapService->generateDynamicSitemap(
                    $this->option('dynamic-type'),
                    (int) $this->option('page')
                ),
                'index' => $sitemapService->generateSitemapIndex(),
                default => throw new \InvalidArgumentException("Type '{$type}' not supported")
            };

            // Mettre en cache seulement pour le sitemap principal
            if ($type === 'main') {
                $sitemapService->cacheSitemap($content);
            }

            // Sauvegarde dans un fichier statique (optionnel)
            $filename = "sitemap-{$type}.xml";
            if (Storage::disk('public')->exists($filename)) {
                Storage::disk('public')->delete($filename);
            }
            Storage::disk('public')->put($filename, $content);

            $this->info("Sitemap '{$type}' generated successfully.");
            $this->info('Static file saved to: '.Storage::disk('public')->path($filename));

            if ($type === 'main') {
                $this->info('Cached for '.config('sitemap.cache.duration').' hours.');
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('Failed to generate sitemap: '.$e->getMessage());

            return 1;
        }
    }
}
