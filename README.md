# matos.live

![Laravel](https://img.shields.io/badge/Laravel-12.x-red?logo=laravel)
![Vue 3](https://img.shields.io/badge/Vue-3.x-42b883?logo=vue.js)
![Inertia.js](https://img.shields.io/badge/Inertia.js-2.x-4B5563?logo=inertia)
![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-4.x-38bdf8?logo=tailwindcss)
![shadcn-vue](https://img.shields.io/badge/shadcn--vue-%23f5f5f5?logo=vue.js)

**Le matériel de vos événements, en un clic.**

**matos.live** est une marketplace collaborative de matériel événementiel pour associations et particuliers. Partagez, louez, facilitez vos événements — avec une intégration HelloAsso pour la synchronisation des organisations.

## 🚀 Fonctionnalités principales

- **Gestion d'organisations** : Créez, éditez, et gérez plusieurs associations ou clubs.
- **Inventaire de matériel** : Ajoutez, catégorisez, et gérez le matériel (son, lumière, vidéo, etc.) avec photos, quantités, dépôts, maintenance, etc.
- **Réservations avancées** :
  - Prêt et emprunt de matériel entre organisations
  - Gestion des statuts (en attente, confirmé, annulé, etc.)
  - Calcul automatique des prix, dépôts, remises
  - Notifications et suivi
- **Calendrier** : Visualisation des disponibilités et réservations (FullCalendar intégré)
- **Synchronisation HelloAsso** :
  - Authentification OAuth HelloAsso
  - Synchronisation automatique des organisations et membres
  - Commande artisan pour synchronisation manuelle
- **Gestion des membres** : Invitations, rôles (admin, membre), gestion des accès
- **Système de dépôt** : Gestion multi-dépôts pour le stockage du matériel
- **Sitemap SEO** : Génération automatique et optimisée pour le référencement
- **API REST** : Endpoints pour équipements, organisations, catégories, etc.
- **Expérience utilisateur moderne** :
  - UI avec shadcn-vue, Tailwind, Lucide icons
  - Vue 3 Composition API, ````<script setup>````
  - Responsive, dark mode, accessibilité

## 🛠️ Stack technique

- **Backend** : Laravel 12+, PHP 8.4+
- **Frontend** : Vue 3, Inertia.js, shadcn-vue, Tailwind CSS
- **Auth** : Laravel Socialite (Google, HelloAsso)
- **Tests** : PestPHP, PHPUnit
- **CI/CD** : GitHub Actions

## 📦 Installation & Démarrage rapide

### Prérequis
- PHP 8.4+
- Node.js 20+
- Composer
- Base de données (MySQL, SQLite, etc.)

### Installation
```bash
# Cloner le repo
 git clone https://github.com/votre-org/matos.git
 cd matos

# Installer les dépendances PHP
 composer install

# Installer les dépendances JS
 npm install

# Copier l'exemple d'environnement
 cp .env.example .env

# Générer la clé d'application
 php artisan key:generate

# Configurer la base de données dans .env

# Lancer les migrations et seeders
 php artisan migrate --seed

# Lancer le serveur de dev
 npm run dev
 php artisan serve
```

## 📚 Documentation
- [docs/README.md](docs/README.md) — Documentation technique
- [docs/SITEMAP.md](docs/SITEMAP.md) — Système de sitemap SEO

## 🧑‍💻 Contribution
Les contributions sont les bienvenues !

1. Forkez le repo
2. Créez une branche (`git checkout -b feature/ma-feature`)
3. Commitez vos changements (`git commit -am 'feat: nouvelle fonctionnalité'`)
4. Poussez la branche (`git push origin feature/ma-feature`)
5. Ouvrez une Pull Request

Voir [docs/README.md](docs/README.md) pour les conventions et la structure de documentation.

## 📝 Licence
MIT

## 💬 Support
Pour toute question, ouvrez une issue ou consultez la documentation dans le dossier `docs/`.

---

> Plateforme propulsée par Laravel, Inertia, Vue 3, Tailwind, shadcn-vue, Lucide icons.

