# Phase 3 : Fixtures et Données de Test

Cette phase vise à peupler la base de données avec des données cohérentes pour le développement et les tests. Nous utiliserons `Zenstruck Foundry` (déjà installé) et `Doctrine Fixtures Bundle`.

## 3.1 Installation des dépendances manquantes
- Installer `doctrine/doctrine-fixtures-bundle` (nécessaire pour la commande `doctrine:fixtures:load`).

## 3.2 Création des Factories (Foundry)
Nous allons créer des "usines" pour générer facilement nos entités avec des données réalistes (via Faker).

1.  **CountryFactory** : Création de pays statiques (France, USA, UK, etc.).
2.  **DocumentTypeFactory** : Types de documents standards (CV, Lettre de motivation, Contrat).
3.  **UserFactory** :
    - 1 Admin (email: `admin@docgen.com`, pass: `password`).
    - 1 User standard (email: `user@docgen.com`, pass: `password`).
    - 10 utilisateurs aléatoires.
4.  **TemplateFactory** : Quelques templates d'exemple liés aux types de documents.
5.  **DocumentFactory** : Documents générés par les utilisateurs.
6.  **UsageQuotaFactory** : Quotas mensuels associés aux utilisateurs.

## 3.3 Création du Seeder (AppFixtures)
- Mise à jour de `src/DataFixtures/AppFixtures.php` pour utiliser les factories.
- Orchestration de l'ordre de chargement (Pays/Types -> Users -> Templates -> Documents).

## 3.4 Chargement des données
- Exécution de la commande de chargement des fixtures pour réinitialiser et peupler la base de données.
