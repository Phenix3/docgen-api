# Phase 7 : Finalisation et Vérification

C'est la dernière étape du développement backend. Nous allons passer en revue l'ensemble du projet pour nous assurer qu'il est propre, documenté et prêt à être déployé ou connecté à un frontend.

## Objectifs
-   Vérifier la qualité du code (Linting, Analyse statique).
-   Vérifier la cohérence de la base de données.
-   Valider l'ensemble des fonctionnalités via les tests.
-   Générer la documentation de l'API (OpenAPI/Swagger).

## Étapes d'implémentation

### 1. Analyse Statique et Linting
-   Vérification de la configuration des services (`lint:container`).
-   Vérification des templates Twig (`lint:twig`).
-   Validation du schéma de base de données (`doctrine:schema:validate`).

### 2. Tests de Régression
-   Exécution de tous les tests unitaires et fonctionnels (`phpunit`).
-   Vérification que les nouvelles fonctionnalités (Quotas, Admin) n'ont rien cassé.

### 3. Documentation API
-   Export de la spécification OpenAPI au format JSON (`openapi.json`).
-   Vérification que tous les endpoints sont bien documentés.

### 4. Nettoyage
-   Suppression des fichiers temporaires ou inutiles.
-   Vérification des fichiers `.gitignore`.

### 5. Démarrage Final
-   Démarrage du serveur et du worker de messagerie pour une démonstration finale si nécessaire.
