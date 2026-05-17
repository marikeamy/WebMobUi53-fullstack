# HEIG-VD DévProdMéd Course - Mini-projet

Ce dépôt contient le mini-projet à réaliser dans le cadre du cours
_"[Développement de produit média (DévProdMéd)](https://github.com/heig-vd-devprodmed-course/heig-vd-devprodmed-course)"_
enseigné à la
[Haute Ecole d'Ingénierie et de Gestion du Canton de Vaud (HEIG-VD)](https://heig-vd.ch),
Suisse.

## Objectif du mini-projet

L'objectif de ce mini-projet est de créer un réseau social simple en utilisant le
framework [Laravel](https://laravel.com/) et Vue 3. Ce projet permettra de mettre en pratique les concepts
appris dans le cours.

---

## Architecture frontend

### Choix techniques

| Technologie | Rôle |
|---|---|
| Vue 3 (Composition API) -> Framework frontend principal 
| Vue Router 4 -> Navigation côté client dans chaque app Vue 
| Chart.js + vue-chartjs -> Visualisation des résultats sous forme de graphiques 
| Tailwind CSS 4 -> Styles utilitaires, responsive/mobile first
| Vite + laravel-vite-plugin -> Bundler et HMR en développement

### Plusieurs applications Vue distinctes

Le frontend est découpé en trois applications Vue indépendantes, chacune avec son propre entrypoint Vite :

| Entrypoint | Page Blade | URL | Rôle |
|---|---|---|---|
| `poll-dashboard.js` | `polls/dashboard.blade.php` | `/polls/dashboard` | Dashboard du créateur (liste, création, modification) |
| `poll-vote.js` | `polls/vote.blade.php` | `/vote/{token}` | Vote et consultation via lien partagé |
| `poll-results.js` | `polls/results.blade.php` | `/results/{id}` | Résultats détaillés pour le créateur |

Chaque entrypoint importe `bootstrap.js` (configuration CSRF + URL de base de l'API), puis monte son app Vue avec Vue Router.

### Pattern général

```
Vue Component
  → Composable (usePollStore / useFetchApi)
    → API JSON versionnée (/api/v1/...)
      → Contrôleur Laravel
        → Modèle Eloquent
```

Les composables isolent toute la logique d'appel API. Les composants Vue ne font que réagir à l'état réactif (`ref`, `computed`) et émettre des événements.

### Authentification

L'authentification repose sur le cookie de session Laravel (Sanctum SPA). Voir [`README_FRONT.md`](README_FRONT.md) pour les détails d'intégration (CSRF, middleware stateful, etc.).

## Pré-requis

Afin de lancer ce projet, une stack compatible avec Laravel, est requise.

Voici les pré-requis nécessaires :

- PHP >= 8.0.
- Composer.
- Node.js et npm.
- Une base de données (MySQL, PostgreSQL, SQLite, etc.).
- Un serveur web (Apache, Nginx, etc.).

[Laravel Herd](https://helm.sh/docs/charts/laravel/) est recommandé pour une installation facile de Laravel et de ses dépendances.

## Développement local

Pour développer et tester le mini-projet en local, voici les étapes à suivre :

1. Forker ce dépôt

2. Installer les dépendances avec npm et Composer :

    ```bash
    npm install && npm run build

    composer install
    ```

3. Copier le fichier `.env.example` en `.env`.
4. Modifier les variables d'environnement si nécessaire (optionnel).
5. Générer la clé d'application Laravel :

    ```bash
    php artisan key:generate
    ```

6. Créer le lien symbolique pour les fichiers téléversés :

    ```bash
    php artisan storage:link
    ```

7. Créer la base de données et exécuter les migrations :

    ```bash
    php artisan migrate
    ```

    S'il est nécessaire de réinitialiser la base de données, utiliser la commande `php artisan migrate:reset` puis `php artisan migrate` à nouveau.

8. Optionnel : en mode développement, il est possible de peupler la base de données avec des données fictives :

    ```bash
    php artisan db:seed
    ```

9. Démarrer le serveur de développement Laravel :

    ```bash
    composer run dev
    ```

L'application sera accessible à l'adresse <http://localhost:8000>.
