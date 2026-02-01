# Phase 4 : Implémentation de la Logique de Génération de Documents

Cette phase se concentre sur le cœur de l'application : transformer des données utilisateur et un modèle (Template) en un document PDF final, de manière asynchrone.

La lib wkhtmltopdf est deja installe sur la machine local et les paths sont deja dans des vars dans .env.local

## 4.1 Service de Génération de Contenu (`DocumentContentGenerator`)

Création d'un service responsable de la fusion des données.

* **Responsabilité** : Prendre le contenu structuré du `Template` et les `inputData` du `Document`, et remplacer les variables (ex: `{{ name }}`) par leurs valeurs.

* **Logique** : Utilisation de Twig ou d'un simple moteur de remplacement de chaînes pour commencer.

## 4.2 Service de Génération PDF (`PdfGeneratorService`)

Configuration et encapsulation de `KnpSnappyBundle`.

* **Configuration** : Vérifier le chemin `WKHTMLTOPDF_PATH` dans le `.env` (il pointe actuellement vers `/usr/local/bin/...`, à adapter pour Windows ou simuler pour le dev).

* **Service** : Une classe wrapper qui prend du HTML et retourne le contenu binaire PDF ou le sauvegarde sur le disque.

## 4.3 Système de Messagerie Asynchrone

Utilisation de Symfony Messenger pour ne pas bloquer l'API lors de la génération.

* **Message** : `GenerateDocumentMessage` (DTO contenant l'ID du document).

* **Handler** : `GenerateDocumentMessageHandler`.

  * Récupère le Document.

  * Change le statut à `GENERATING`.

  * Appelle `DocumentContentGenerator` -> `generatedContent`.

  * Appelle `PdfGeneratorService` -> fichier PDF.

  * Sauvegarde le chemin du fichier (`pdfPath`) et passe le statut à `COMPLETED`.

  * Gestion des erreurs : Statut `FAILED` et message d'erreur.

## 4.4 Déclenchement Automatique (API Platform)

Intégration transparente avec l'API.

* **State Processor** : Création de `DocumentPostProcessor` (décorant le processeur par défaut).

  * Après la persistance réussie d'un nouveau Document, dispatcher le `GenerateDocumentMessage`.

## 4.5 Configuration Windows

* **Note** : Sur Windows, `wkhtmltopdf` doit être installé manuellement. Je vais configurer le `.env` pour pointer vers un chemin Windows typique ou laisser un commentaire explicite, et potentiellement créer un "Mock" pour le développement si le binaire est absent.

## 4.6 Verification

* Création d'un test fonctionnel ou d'un script pour simuler la génération complète.

