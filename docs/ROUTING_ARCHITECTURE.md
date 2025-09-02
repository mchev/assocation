# Architecture des Routes - Association

## Principe de base

Le projet utilise une architecture de routes **sans organisation dans l'URL** pour les ressources liées à l'organisation courante de l'utilisateur.

## Structure des routes

### ✅ Routes avec organisation dans l'URL
Ces routes gèrent les organisations elles-mêmes :
```
app/organizations/{organization}
app/organizations/{organization}/edit
app/organizations/switch/{organization}
```

### ✅ Routes sans organisation dans l'URL
Ces routes utilisent l'organisation courante de l'utilisateur connecté :
```
app/equipments/{equipment}
app/organizations/depots/{depot}
app/reservations/in/{reservation}
app/reservations/out/{reservation}
```

## Logique de sécurité

### Organisation courante
- L'organisation est déterminée par `$request->user()->currentOrganization`
- L'utilisateur ne peut accéder qu'aux ressources de son organisation courante
- Les politiques (Policies) vérifient l'appartenance à l'organisation

### Exemple de contrôleur
```php
public function index(Request $request)
{
    $user = $request->user();
    $organization = $user->currentOrganization;
    
    if (!$organization) {
        return redirect()->route('app.organizations.create')
            ->with('error', 'Vous devez créer une organisation.');
    }
    
    // Utiliser $organization pour les requêtes
    $equipments = $organization->equipments();
}
```

## Avantages de cette approche

1. **Simplicité** : URLs plus courtes et plus lisibles
2. **Sécurité** : L'organisation est déterminée par l'utilisateur connecté
3. **Cohérence** : Même pattern pour équipements, dépôts, réservations
4. **Maintenance** : Moins de paramètres à gérer dans les routes

## Redirections

Toutes les redirections vers les routes d'équipement doivent être faites **sans** passer l'organisation :

```php
// ✅ Correct
return redirect()->route('app.organizations.equipments.index');

// ❌ Incorrect
return redirect()->route('app.organizations.equipments.index', $organization);
```

## Tests

Les tests utilisent cette architecture et passent tous avec succès.
