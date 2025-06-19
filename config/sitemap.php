<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sitemap Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour la génération des sitemaps XML
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Static Pages Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration des pages statiques à inclure dans le sitemap
    |
    */
    'static_pages' => [
        [
            'route' => 'home',
            'priority' => '1.0',
            'changefreq' => 'daily',
        ],
        [
            'route' => 'discover',
            'priority' => '0.9',
            'changefreq' => 'weekly',
        ],
        [
            'route' => 'how-it-works',
            'priority' => '0.8',
            'changefreq' => 'monthly',
        ],
        [
            'route' => 'faq',
            'priority' => '0.7',
            'changefreq' => 'monthly',
        ],
        [
            'route' => 'terms',
            'priority' => '0.3',
            'changefreq' => 'yearly',
        ],
        [
            'route' => 'conditions',
            'priority' => '0.3',
            'changefreq' => 'yearly',
        ],
        [
            'route' => 'privacy',
            'priority' => '0.3',
            'changefreq' => 'yearly',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dynamic Content Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration du contenu dynamique (équipements, etc.)
    |
    */
    'dynamic_content' => [
        'equipment' => [
            'model' => \App\Models\Equipment::class,
            'route' => 'equipments.show',
            'priority' => '0.8',
            'changefreq' => 'weekly',
            'conditions' => [
                'is_active' => true,
                'is_available' => true,
            ],
            'order_by' => 'updated_at',
            'order_direction' => 'desc',
            'select_fields' => ['id', 'updated_at'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration du cache pour les sitemaps
    |
    */
    'cache' => [
        'enabled' => env('SITEMAP_CACHE_ENABLED', true),
        'duration' => env('SITEMAP_CACHE_DURATION', 24), // heures
        'key' => 'sitemap_content',
        'generated_at_key' => 'sitemap_generated_at',
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration des performances et limites
    |
    */
    'performance' => [
        'max_urls_per_sitemap' => 50000,
        'chunk_size' => 1000, // Nombre d'équipements traités par lot
        'timeout' => 300, // Timeout en secondes pour la génération
    ],

    /*
    |--------------------------------------------------------------------------
    | XML Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration de la génération XML
    |
    */
    'xml' => [
        'version' => '1.0',
        'encoding' => 'UTF-8',
        'namespace' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
        'indent' => '  ',
    ],

    /*
    |--------------------------------------------------------------------------
    | Headers Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration des headers HTTP
    |
    */
    'headers' => [
        'content_type' => 'application/xml; charset=utf-8',
        'cache_control' => 'public, max-age=3600',
        'x_sitemap_cached' => true,
        'x_sitemap_generated_at' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-regeneration Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration de la régénération automatique
    |
    */
    'auto_regeneration' => [
        'enabled' => env('SITEMAP_AUTO_REGENERATION', true),
        'delay_minutes' => 5, // Délai avant régénération après modification
        'queue' => 'sitemap',
    ],
];
