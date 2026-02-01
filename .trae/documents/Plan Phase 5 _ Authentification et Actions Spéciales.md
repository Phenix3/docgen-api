# Phase 5 : Authentification et Points de Terminaison Spéciaux

Cette phase vise à compléter l'API avec des fonctionnalités d'authentification et des actions personnalisées qui ne rentrent pas dans le cadre CRUD standard d'API Platform.

## 5.1 Inscription Utilisateur (Registration)
Création d'un contrôleur dédié pour gérer l'inscription, car API Platform gère par défaut les ressources mais l'inscription nécessite souvent une logique spécifique (validation, hashage, envoi d'email de bienvenue).

* **Endpoint** : `POST /api/auth/register`
* **Controller** : `RegistrationController`
* **Logique** :
  * Validation des données (email, mot de passe, nom).
  * Vérification de l'unicité de l'email.
  * Hashage du mot de passe.
  * Création de l'utilisateur avec un rôle par défaut (`ROLE_USER`) et un type d'abonnement (`FREE`).
  * (Optionnel) Envoi d'un email de confirmation (simulé pour l'instant).

## 5.2 Informations Utilisateur Courant (Me)
Permettre à l'utilisateur connecté de récupérer ses propres informations facilement sans connaître son ID.

* **Endpoint** : `GET /api/me`
* **Controller** : `MeController`
* **Logique** : Retourner l'objet `User` de l'utilisateur authentifié via le token JWT.

## 5.3 Téléchargement de Documents
Création d'un endpoint sécurisé pour télécharger les fichiers PDF générés, en vérifiant que l'utilisateur est bien le propriétaire.

* **Endpoint** : `GET /api/documents/{id}/download`
* **Controller** : `DownloadDocumentController`
* **Logique** :
  * Récupérer le document.
  * Vérifier les droits (Voter/Security).
  * Servir le fichier via `BinaryFileResponse`.

## 5.4 Tests et Vérification
* Vérification manuelle via script ou curl des nouveaux endpoints.
* S'assurer que les tokens JWT fonctionnent correctement avec ces nouvelles routes.

Cette phase finalisera l'aspect "Utilisateur" de l'API.
