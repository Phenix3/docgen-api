# Phase 6 : Tests Automatisés et Qualité

Cette phase vise à mettre en place une suite de tests robuste pour garantir la stabilité de l'application. Nous allons utiliser PHPUnit et Foundry pour créer des tests d'intégration et fonctionnels.

## 6.1 Configuration de l'environnement de test
* Installation des dépendances de test (`phpunit/phpunit`, `symfony/test-pack` si nécessaire).
* Configuration du fichier `phpunit.xml.dist` (il semble manquant, je vais le créer).
* Création de la base de données de test (`.env.test`).

## 6.2 Tests Unitaires (Logique Métier)
Tester les services isolés qui contiennent de la logique métier importante.
* **Service** : `DocumentContentGeneratorTest`
  * Vérifier que les placeholders sont bien remplacés.
  * Vérifier le comportement avec une structure JSON invalide.

## 6.3 Tests d'Intégration (API Platform)
Tester les endpoints API pour s'assurer qu'ils répondent correctement (Code HTTP, Structure JSON).
* **Authentification** :
  * `AuthenticationTest` : Login réussi, Login échoué, Accès sans token.
  * `RegistrationTest` : Création de compte réussie, Validation des données.
* **Documents** :
  * `DocumentResourceTest` :
    * CRUD complet (Create, Read, Update, Delete).
    * Vérification des permissions (un utilisateur ne voit que ses documents).
    * Vérification du déclenchement automatique de la génération (statut change).

## 6.4 Exécution et Rapport
* Lancer la suite complète de tests.
* Corriger les éventuels bugs découverts.

Cette phase transformera nos "vérifications manuelles" en une assurance qualité pérenne.
