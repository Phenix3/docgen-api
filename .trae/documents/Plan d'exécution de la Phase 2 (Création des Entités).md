Nous passons maintenant à la **Phase 2 : Création des Entités**. Cette phase consiste à implémenter le modèle de données de l'application.

### Phase 2 : Création des Entités

#### TASK 2.1 : Créer les Énumérations
1.  **Action** : Créer les classes Enum PHP 8.1+ pour :
    *   `src/Enum/DocumentStatus.php` (Statuts des documents)
    *   `src/Enum/SubscriptionType.php` (Types d'abonnement)
    *   `src/Enum/ToneLevel.php` (Niveaux de tonalité)
2.  **Validation** : Vérification du conteneur.

#### TASK 2.2 : Créer l'entité Country
1.  **Action** : Créer l'entité `src/Entity/Country.php` et son repository `src/Repository/CountryRepository.php`.
2.  **Validation** : Vérification du conteneur.

#### TASK 2.3 : Créer l'entité DocumentType
1.  **Action** : Créer l'entité `src/Entity/DocumentType.php` et son repository `src/Repository/DocumentTypeRepository.php`.
2.  **Validation** : Vérification du conteneur.

#### TASK 2.4 : Créer l'entité User
1.  **Action** : Créer l'entité `src/Entity/User.php` (avec gestion des rôles, abonnements) et son repository `src/Repository/UserRepository.php`.
2.  **Validation** : Vérification du conteneur.

#### TASK 2.5 : Créer l'entité Template
1.  **Action** : Créer l'entité `src/Entity/Template.php` (modèles de documents) et son repository `src/Repository/TemplateRepository.php`.
2.  **Validation** : Vérification du conteneur.

#### TASK 2.6 : Créer l'entité Document
1.  **Action** : Créer l'entité principale `src/Entity/Document.php` et son repository `src/Repository/DocumentRepository.php`.
2.  **Validation** : Vérification du conteneur.

#### TASK 2.7 : Créer l'entité UsageQuota
1.  **Action** : Créer l'entité `src/Entity/UsageQuota.php` pour gérer les limites d'utilisation et son repository `src/Repository/UsageQuotaRepository.php`.
2.  **Validation** : Vérification du conteneur.

#### TASK 2.8 : Créer les migrations et la base de données
1.  **Action** : Générer la migration Doctrine (`make:migration`).
2.  **Action** : Appliquer la migration (`doctrine:migrations:migrate`).
3.  **Validation** : Valider le schéma de la base de données (`doctrine:schema:validate`).

Une fois ce plan confirmé, je commencerai par la **TASK 2.1**.