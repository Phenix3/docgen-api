# Phase 8 : Gestion des Quotas et Limitations

Actuellement, le système suit l'utilisation des quotas (`UsageQuota`) mais ne bloque pas la création de documents lorsque la limite est atteinte. Cette phase vise à rendre ces limites effectives pour monétiser l'API (modèle Freemium).

## Objectifs
-   Empêcher la création de documents si l'utilisateur a atteint son quota mensuel.
-   Centraliser la logique de vérification et d'incrémentation des quotas.
-   Gérer proprement les retours d'erreur (Code 422 avec message explicite).

## Étapes d'implémentation

### 1. Création du `QuotaService`
Ce service sera responsable de toute la logique liée aux limites :
-   `checkQuota(User $user): bool` : Vérifie si l'utilisateur peut encore générer des documents.
-   `incrementQuota(User $user)` : Incrémente le compteur après génération réussie.
-   `getRemainingQuota(User $user): int` : (Optionnel) Pour l'affichage frontend.

### 2. Validation Personnalisée (Constraint Validator)
Pour intégrer proprement la vérification dans le cycle de vie API Platform, nous allons créer une contrainte de validation personnalisée `#[CheckQuota]` sur l'entité `Document`.
-   Si le quota est dépassé, l'API retournera une erreur de validation standard (422 Unprocessable Entity) avec le message : *"Vous avez atteint votre limite de documents pour ce mois (3/3). Passez au plan Pro pour illimité."*

### 3. Mise à jour du Handler Asynchrone
Le `GenerateDocumentMessageHandler` incrémente actuellement le quota manuellement. Nous allons le refactoriser pour utiliser le `QuotaService` afin de garantir la cohérence (ex: gestion du changement de mois).

### 4. Tests des Limitations
-   Création d'un test fonctionnel `QuotaTest`.
-   Scénario :
    1.  Créer un utilisateur avec abonnement `FREE`.
    2.  Générer 3 documents (Succès).
    3.  Tenter de générer un 4ème document (Échec attendu 422).
    4.  Vérifier le message d'erreur.
