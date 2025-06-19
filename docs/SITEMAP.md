# Système de Sitemap

## Vue d'ensemble

Le système de sitemap de l'application Assocation est conçu pour générer automatiquement des sitemaps XML optimisés pour le SEO, incluant toutes les pages statiques et le contenu dynamique (équipements). Le système est configurable, performant et extensible.

## Architecture

### Composants principaux

- **Configuration** : `config/sitemap.php` - Configuration centralisée
- **Service** : `app/Services/SitemapService.php` - Logique métier
- **Contrôleur** : `app/Http/Controllers/PageController.php` - Endpoints HTTP
- **Commande** : `app/Console/Commands/GenerateSitemap.php` - Génération manuelle
- **Job** : `app/Jobs/RegenerateSitemap.php` - Régénération automatique
- **Observer** : `app/Observers/EquipmentObserver.php` - Déclenchement automatique

## Configuration

### Fichier de configuration

Le fichier `config/sitemap.php` contient toute la configuration du système :

```php
return [
    // Pages statiques
    'static_pages' => [
        [
            'route' => 'home',
            'priority' => '1.0',
            'changefreq' => 'daily',
        ],
        // ...
    ],

    // Contenu dynamique
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

    // Cache
    'cache' => [
        'enabled' => env('SITEMAP_CACHE_ENABLED', true),
        'duration' => env('SITEMAP_CACHE_DURATION', 24), // heures
        'key' => 'sitemap_content',
        'generated_at_key' => 'sitemap_generated_at',
    ],

    // Performance
    'performance' => [
        'max_urls_per_sitemap' => 50000,
        'chunk_size' => 1000,
        'timeout' => 300, // secondes
    ],
];
```

### Variables d'environnement

```env
# .env
SITEMAP_CACHE_ENABLED=true
SITEMAP_CACHE_DURATION=24
SITEMAP_AUTO_REGENERATION=true
```

## Endpoints disponibles

### Sitemap principal
```
GET /sitemap.xml
```
- Inclut toutes les pages statiques et le contenu dynamique
- Utilise le cache pour de meilleures performances
- Headers de cache appropriés

### Sitemap index
```
GET /sitemap-index.xml
```
- Index de tous les sitemaps disponibles
- Utile pour les gros sites (>50,000 URLs)

### Sitemap statique
```
GET /sitemap-static.xml
```
- Pages statiques uniquement
- Pas de cache (génération à la demande)

### Sitemap dynamique
```
GET /sitemap-{type}-{page}.xml
```
- Contenu dynamique paginé
- Exemple : `/sitemap-equipment-1.xml`

## Utilisation

### Génération manuelle

#### Commande Artisan
```bash
# Sitemap principal
php artisan sitemap:generate

# Sitemap statique
php artisan sitemap:generate --type=static

# Sitemap dynamique (équipements, page 1)
php artisan sitemap:generate --type=dynamic --dynamic-type=equipment --page=1

# Sitemap index
php artisan sitemap:generate --type=index

# Forcer la régénération
php artisan sitemap:generate --force
```

#### Programmatique
```php
use App\Services\SitemapService;

$sitemapService = app(SitemapService::class);

// Générer le sitemap principal
$content = $sitemapService->generateMainSitemap();

// Générer le sitemap statique
$content = $sitemapService->generateStaticSitemap();

// Générer le sitemap dynamique
$content = $sitemapService->generateDynamicSitemap('equipment', 1);

// Vérifier le cache
if ($sitemapService->isCached()) {
    $content = $sitemapService->getCachedSitemap();
}

// Vider le cache
$sitemapService->clearCache();
```

### Régénération automatique

Le sitemap se régénère automatiquement quand :
- Un équipement est créé/modifié/supprimé
- Un équipement devient actif/disponible
- L'équipement est restauré (soft delete)

Le délai de régénération est configurable (défaut : 5 minutes).

## Extension du système

### Ajouter une nouvelle page statique

1. Ajouter la route dans `routes/web.php`
2. Ajouter la configuration dans `config/sitemap.php` :

```php
'static_pages' => [
    // ... pages existantes
    [
        'route' => 'nouvelle-page',
        'priority' => '0.6',
        'changefreq' => 'monthly',
    ],
],
```

### Ajouter un nouveau type de contenu dynamique

1. Ajouter la configuration dans `config/sitemap.php` :

```php
'dynamic_content' => [
    'equipment' => [ /* config existante */ ],
    'categories' => [
        'model' => \App\Models\Category::class,
        'route' => 'categories.show',
        'priority' => '0.7',
        'changefreq' => 'weekly',
        'conditions' => ['is_active' => true],
        'order_by' => 'updated_at',
        'order_direction' => 'desc',
        'select_fields' => ['id', 'updated_at'],
    ],
],
```

2. Créer un observer si nécessaire pour la régénération automatique

### Personnaliser la génération XML

Modifier la configuration XML dans `config/sitemap.php` :

```php
'xml' => [
    'version' => '1.0',
    'encoding' => 'UTF-8',
    'namespace' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
    'indent' => '  ',
],
```

## Performance et optimisation

### Cache
- **Durée** : 24 heures par défaut (configurable)
- **Clés** : `sitemap_content` et `sitemap_generated_at`
- **Désactivation** : `SITEMAP_CACHE_ENABLED=false`

### Pagination
- **Limite** : 50,000 URLs par sitemap
- **Automatique** : pour le contenu dynamique
- **Index** : génération automatique des sitemaps multiples

### Optimisations
- **Requêtes optimisées** : sélection des champs nécessaires uniquement
- **Conditions** : filtrage au niveau base de données
- **Ordre** : tri par `updated_at` pour les équipements

## Monitoring et debugging

### Headers de réponse
```
X-Sitemap-Cached: true/false
X-Sitemap-Generated-At: 2024-01-01T12:00:00.000Z
```

### Logs
Les jobs de régénération automatique génèrent des logs :
```
[INFO] Sitemap regenerated successfully via job.
[ERROR] Failed to regenerate sitemap via job: [message]
```

### Tests
```bash
# Exécuter les tests du sitemap
php artisan test --filter=SitemapTest
```

## Intégration SEO

### robots.txt
Le fichier `public/robots.txt` référence le sitemap :
```
Sitemap: https://assocation.fr/sitemap.xml
```

### Soumission aux moteurs de recherche
- **Google Search Console** : Ajouter le sitemap
- **Bing Webmaster Tools** : Ajouter le sitemap
- **Autres** : Soumettre l'URL du sitemap

## Dépannage

### Problèmes courants

#### Sitemap vide
- Vérifier que les équipements sont actifs et disponibles
- Vérifier la configuration des conditions
- Consulter les logs d'erreur

#### Cache non fonctionnel
- Vérifier la configuration du cache
- Vider le cache manuellement : `php artisan cache:clear`
- Vérifier les permissions de stockage

#### Performance lente
- Réduire la durée du cache
- Augmenter la pagination
- Optimiser les requêtes de base de données

#### Régénération automatique non fonctionnelle
- Vérifier que les jobs sont traités
- Vérifier la configuration de la queue
- Consulter les logs des jobs

### Commandes utiles

```bash
# Vider le cache du sitemap
php artisan sitemap:generate --force

# Vérifier la queue
php artisan queue:work

# Consulter les logs
tail -f storage/logs/laravel.log
```

## Maintenance

### Tâches régulières
- **Surveillance** : Vérifier la génération automatique
- **Performance** : Monitorer les temps de génération
- **SEO** : Vérifier l'indexation des nouvelles pages

### Mise à jour
- **Configuration** : Adapter selon les besoins SEO
- **Contenu** : Ajouter de nouveaux types de contenu
- **Performance** : Optimiser selon la croissance

## Support

Pour toute question ou problème :
1. Consulter cette documentation
2. Vérifier les logs d'erreur
3. Tester avec les commandes Artisan
4. Consulter les tests pour des exemples d'utilisation 