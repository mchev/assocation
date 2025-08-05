# Infinite Load Implementation avec WhenVisible d'Inertia.js v2.0

## Vue d'ensemble

Cette implémentation utilise le composant `WhenVisible` d'Inertia.js v2.0 pour créer un infinite load on scroll pour la liste des équipements, basée sur l'exemple de [Bilal Haidar sur dev.to](https://dev.to/bhaidar/implementing-infinite-scrolling-with-laravel-inertiajs-v20-and-vue-3-3il).

## Fonctionnalités

- **Infinite Load Automatique** : Chargement automatique des nouveaux éléments quand l'utilisateur atteint le bas de la liste
- **WhenVisible Component** : Utilisation du composant officiel d'Inertia.js v2.0 pour la détection de visibilité
- **Fallback Manuel** : Bouton "Charger plus" en cas de problème avec la détection automatique
- **Animations** : Transitions fluides pour les nouveaux éléments qui apparaissent
- **États de Chargement** : Indicateurs visuels pendant le chargement
- **Gestion Full Page Load** : Support des rechargements de page avec maintien de la position de scroll
- **Fusion Automatique** : Les nouveaux éléments s'ajoutent automatiquement à la liste existante

## Architecture

### Backend (Laravel)

**HomeController.php**
- Gestion intelligente des requêtes full page vs infinite scroll
- Méthode `getAllPagesUpTo()` pour charger toutes les pages précédentes lors d'un rechargement
- Méthode `getFilteredEquipments()` optimisée pour la pagination
- Code optimisé et nettoyé

### Frontend (Vue.js)

**Home.vue**
- Page simplifiée car `WhenVisible` gère automatiquement le chargement
- Passage direct des props d'équipements

**ResultsSection.vue**
- Utilisation du composant `WhenVisible` d'Inertia.js v2.0
- État local réactif pour la fusion des données
- Computed properties optimisées
- Debug logging conditionnel (développement uniquement)

## Configuration

### WhenVisible Component

```vue
<WhenVisible
  v-if="hasMorePages"
  :params="{
    data: {
      page: localEquipments.current_page + 1,
    },
    preserveState: true,
    preserveScroll: true,
    only: ['equipments'],
  }"
>
  <template #default="{ loading }">
    <!-- Contenu du bouton/indicateur -->
  </template>
</WhenVisible>
```

### Backend Pagination Handling

```php
// Handle full page load vs. infinite scroll request
if (!request()->header('X-Inertia')) {
    // Full page load - fetch all pages up to current
    $currentPage = $request->input('page', 1);
    $equipments = $this->getAllPagesUpTo($request, $locationPreferences, $currentPage);
}
```

### Frontend Data Merging

```javascript
// Watch for new equipment data and merge
watch(() => props.equipments, (newEquipments) => {
  if (newEquipments?.data && newEquipments.current_page > 1) {
    localEquipments.value.data.push(...newEquipments.data);
    localEquipments.value.current_page = newEquipments.current_page;
    localEquipments.value.has_more = newEquipments.has_more;
    localEquipments.value.next_page_url = newEquipments.next_page_url;
  }
}, { deep: true });
```

## Avantages

1. **Performance** : Chargement à la demande, pas de surcharge initiale
2. **UX** : Expérience fluide sans rechargement de page
3. **Accessibilité** : Fallback manuel disponible
4. **Maintenabilité** : Code modulaire et réutilisable
5. **SEO Friendly** : Support des rechargements de page
6. **Intégration Native** : Utilisation des composants officiels d'Inertia.js v2.0
7. **Code Optimisé** : Structure propre et maintenable

## Dependencies

- `@inertiajs/vue3` : Pour le composant WhenVisible et la navigation
- `lucide-vue-next` : Pour les icônes

## Tests

Pour tester l'implémentation :

1. Aller sur la page d'accueil (`/`)
2. Faire défiler jusqu'en bas de la liste d'équipements
3. Vérifier que de nouveaux éléments se chargent automatiquement
4. Vérifier les indicateurs de chargement
5. Tester le bouton de chargement manuel si nécessaire
6. Recharger la page et vérifier que la position de scroll est maintenue
7. Vérifier que les nouveaux éléments s'ajoutent à la liste existante

## Optimisations Apportées

- **Backend** : Méthodes extraites pour une meilleure lisibilité
- **Frontend** : Computed properties optimisées
- **Debug** : Logs conditionnels (développement uniquement)
- **Template** : Structure simplifiée et nettoyée
- **Performance** : Fusion efficace des données côté frontend

## Références

- [Article original de Bilal Haidar](https://dev.to/bhaidar/implementing-infinite-scrolling-with-laravel-inertiajs-v20-and-vue-3-3il)
- [Documentation Inertia.js](https://inertiajs.com/load-when-visible) 