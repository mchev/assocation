# Intégration HelloAsso - Organisations

Cette documentation décrit l'intégration avec l'API HelloAsso pour récupérer et synchroniser les organisations des utilisateurs.

## Vue d'ensemble

L'intégration HelloAsso permet de :
- Récupérer les informations des organisations auxquelles appartient un utilisateur
- Synchroniser automatiquement ces organisations avec le système local
- Maintenir une cohérence entre les données HelloAsso et locales

## Configuration

### 1. Configuration des services

Dans `config/services.php`, assurez-vous d'avoir la configuration HelloAsso :

```php
'helloasso' => [
    'client_id' => env('HELLOASSO_CLIENT_ID'),
    'client_secret' => env('HELLOASSO_CLIENT_SECRET'),
    'redirect' => env('HELLOASSO_REDIRECT_URI'),
],
```

### 2. Variables d'environnement

Ajoutez ces variables dans votre fichier `.env` :

```env
HELLOASSO_CLIENT_ID=your_client_id
HELLOASSO_CLIENT_SECRET=your_client_secret
HELLOASSO_REDIRECT_URI=https://your-domain.com/auth/helloasso/callback
```

### 3. Migration de base de données

Exécutez la migration pour ajouter les champs HelloAsso aux organisations :

```bash
php artisan migrate
```

## Fonctionnalités

### Synchronisation automatique

Lors de la connexion via HelloAsso, les organisations de l'utilisateur sont automatiquement synchronisées :

1. Récupération des organisations via l'API HelloAsso
2. Création ou mise à jour des organisations locales
3. Association de l'utilisateur aux organisations
4. Définition de l'organisation principale

### Champs synchronisés

Les champs suivants sont synchronisés depuis HelloAsso :

- `helloasso_id` : Identifiant unique de l'organisation HelloAsso
- `helloasso_name` : Nom de l'organisation HelloAsso
- `helloasso_slug` : Slug de l'organisation HelloAsso
- `name` : Nom de l'organisation
- `description` : Description
- `email` : Email de contact
- `phone` : Téléphone
- `address` : Adresse
- `city` : Ville
- `postal_code` : Code postal
- `website` : Site web

## API HelloAsso

### Endpoint utilisé

- **URL** : `https://api.helloasso.com/v5/users/me/organizations`
- **Méthode** : GET
- **Authentification** : Bearer Token

### Scopes requis

Les scopes suivants sont nécessaires :
- `openid`
- `profile`
- `email`
- `organizations`

## Utilisation

### Connexion via HelloAsso

```php
// Redirection vers HelloAsso
Route::get('/auth/helloasso/redirect', [HelloassoController::class, 'redirect']);

// Callback après authentification
Route::get('/auth/helloasso/callback', [HelloassoController::class, 'callback']);
```

### Synchronisation manuelle

Utilisez la commande Artisan pour synchroniser manuellement :

```bash
php artisan helloasso:sync-organizations user@example.com
```

### Service de synchronisation

```php
use App\Services\HelloAssoOrganizationService;

$service = app(HelloAssoOrganizationService::class);
$service->syncOrganizations($user, $helloassoOrganizations);
```

## Modèles et relations

### User

Le modèle `User` a les champs HelloAsso suivants :
- `helloasso_id`
- `helloasso_token`
- `helloasso_refresh_token`

### Organization

Le modèle `Organization` a les champs HelloAsso suivants :
- `helloasso_id`
- `helloasso_name`
- `helloasso_slug`

## Tests

Exécutez les tests pour vérifier l'intégration :

```bash
php artisan test tests/Feature/HelloAssoOrganizationSyncTest.php
```

## Gestion des erreurs

### Erreurs API

- Les erreurs de l'API HelloAsso sont loggées
- La synchronisation continue même si certaines organisations échouent
- Les tokens expirés sont gérés automatiquement

### Fallback

Si aucune organisation n'est trouvée, une organisation par défaut est créée pour l'utilisateur.

## Sécurité

- Les tokens HelloAsso sont stockés de manière sécurisée
- Les appels API utilisent HTTPS
- Les erreurs sensibles ne sont pas exposées aux utilisateurs

## Maintenance

### Nettoyage des tokens expirés

Créez une tâche planifiée pour nettoyer les tokens expirés :

```php
// Dans App\Console\Kernel
$schedule->call(function () {
    User::where('helloasso_token', '!=', null)
        ->where('updated_at', '<', now()->subDays(30))
        ->update(['helloasso_token' => null]);
})->daily();
```

### Monitoring

Surveillez les logs pour détecter les problèmes de synchronisation :

```bash
tail -f storage/logs/laravel.log | grep "HelloAsso"
```

## Support

Pour toute question ou problème avec l'intégration HelloAsso, consultez :
- [Documentation HelloAsso](https://dev.helloasso.com/docs/introduction-%C3%A0-lapi-de-helloasso)
- Les logs de l'application
- Les tests d'intégration 