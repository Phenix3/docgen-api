# Correctif pour la page d'accueil Symfony

Le problème vient du fait que la route "catch-all" (fourre-tout) configurée pour l'application React ne capture pas correctement la racine `/` avec la priorité actuelle, laissant Symfony afficher sa page d'accueil par défaut.

## Étapes de résolution

### 1. Modifier le contrôleur SPA
Nous allons modifier `src/Controller/SpaController.php` pour définir explicitement deux routes :
1. Une route spécifique pour la racine `/` avec une priorité standard.
2. Une route "catch-all" pour les autres URLs (comme `/login`, `/dashboard`, etc.) avec une priorité basse.

Cela garantira que Symfony route correctement la page d'accueil vers votre application React.

### 2. Vérification
Nous nettoierons le cache pour nous assurer que les nouvelles routes sont prises en compte immédiatement.
