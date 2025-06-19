<?php

namespace App\Jobs;

use App\Services\SitemapService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RegenerateSitemap implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue(config('sitemap.auto_regeneration.queue', 'sitemap'));
    }

    /**
     * Execute the job.
     */
    public function handle(SitemapService $sitemapService): void
    {
        if (! config('sitemap.auto_regeneration.enabled', true)) {
            return;
        }

        try {
            $content = $sitemapService->generateMainSitemap();
            $sitemapService->cacheSitemap($content);

            Log::info('Sitemap regenerated successfully via job.');

        } catch (\Exception $e) {
            Log::error('Failed to regenerate sitemap via job: '.$e->getMessage());
            throw $e;
        }
    }
}
