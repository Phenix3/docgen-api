# Phase 7 : Interface d'Administration (Back-Office)

Actuellement, la gestion des modèles (Templates), des types de documents et des pays se fait via l'API ou directement en base de données. Pour rendre l'application exploitable, nous allons mettre en place une interface d'administration visuelle.

## Objectifs
-   Permettre aux administrateurs de gérer les **Templates**, **Pays**, et **Types de Documents** sans coder.
-   Permettre la modération des **Utilisateurs** et la visualisation des **Documents** générés.
-   Sécuriser l'accès à cette interface (réservé aux `ROLE_ADMIN`).

## Étapes d'implémentation

### 1. Installation de EasyAdmin
-   Installation du bundle `easycorp/easyadmin-bundle`.
-   Vérification de la compatibilité avec l'AssetMapper ou WebpackEncore (Symfony 7 utilise AssetMapper par défaut).

### 2. Création du Dashboard
-   Génération du `DashboardController` (`/admin`).
-   Configuration du menu principal avec des liens vers les différentes entités.
-   Restriction d'accès : Sécurisation de la route `/admin` pour requérir le rôle `ROLE_ADMIN`.

### 3. Création des CRUD Controllers
Nous allons générer des interfaces de gestion (Create, Read, Update, Delete) pour chaque entité clé :

*   **Template** :
    *   Champs : Titre, Description, Type de Document, Pays.
    *   **Spécifique** : Éditeur de texte riche (ou code) pour le champ `content` (le modèle Twig).
*   **DocumentType** : Gestion des catégories (ex: "Juridique", "Administratif").
*   **Country** : Gestion des pays supportés.
*   **User** :
    *   Visualisation des inscrits.
    *   Modification des rôles (ex: promouvoir un Admin).
*   **Document** :
    *   Vue "Lecture seule" pour voir les documents générés par les utilisateurs (à des fins de support/debug).

### 4. Tests et Validation
-   Vérification que l'interface est inaccessible pour un utilisateur anonyme ou standard (`ROLE_USER`).
-   Test de création d'un Template complet via l'interface.
