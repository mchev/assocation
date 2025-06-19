<?php

namespace App\Observers;

use App\Jobs\RegenerateSitemap;
use App\Models\Equipment;

class EquipmentObserver
{
    /**
     * Handle the Equipment "created" event.
     */
    public function created(Equipment $equipment): void
    {
        if ($equipment->is_active && $equipment->is_available) {
            $this->scheduleSitemapRegeneration();
        }
    }

    /**
     * Handle the Equipment "updated" event.
     */
    public function updated(Equipment $equipment): void
    {
        // Régénérer si l'équipement est devenu actif/disponible ou si ses métadonnées ont changé
        if ($equipment->is_active && $equipment->is_available) {
            $this->scheduleSitemapRegeneration();
        }
    }

    /**
     * Handle the Equipment "deleted" event.
     */
    public function deleted(Equipment $equipment): void
    {
        if ($equipment->is_active && $equipment->is_available) {
            $this->scheduleSitemapRegeneration();
        }
    }

    /**
     * Handle the Equipment "restored" event.
     */
    public function restored(Equipment $equipment): void
    {
        if ($equipment->is_active && $equipment->is_available) {
            $this->scheduleSitemapRegeneration();
        }
    }

    /**
     * Schedule sitemap regeneration with debouncing
     */
    private function scheduleSitemapRegeneration(): void
    {
        if (! config('sitemap.auto_regeneration.enabled', true)) {
            return;
        }

        // Utiliser un job différé pour éviter les régénérations multiples
        RegenerateSitemap::dispatch()->delay(
            now()->addMinutes(config('sitemap.auto_regeneration.delay_minutes', 5))
        );
    }
}
