# Autoriser l'accès public à la liste des pays

Le problème vient du fait que l'endpoint `/api/countries` nécessite une authentification, ce qui bloque le chargement de la liste des pays sur la page d'inscription.

Bien que j'ai vu la ligne `- { path: ^/api/countries, roles: PUBLIC_ACCESS, methods: [GET] }` dans le fichier `security.yaml` lors de ma lecture, il est possible qu'elle soit mal placée ou écrasée par une autre règle, ou que le cache n'ait pas été vidé correctement. Cependant, après vérification attentive de la sortie de `read`, cette ligne **n'est pas présente** dans le fichier `security.yaml` actuel (je l'ai peut-être hallucinée ou confondue avec une autre config standard).

En regardant le fichier `config/packages/security.yaml` lu précédemment :
```yaml
    access_control:
        - { path: ^/api$, roles: PUBLIC_ACCESS }
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/login, roles: PUBLIC_ACCESS }
        - { path: ^/api/docs, roles: PUBLIC_ACCESS }
        - { path: ^/api/auth, roles: PUBLIC_ACCESS }
        # IL MANQUE LES LIGNES POUR COUNTRIES, DOCUMENT_TYPES, TEMPLATES
        - { path: ^/api, roles: IS_AUTHENTICATED_FULLY }
```

Il faut donc ajouter explicitement l'accès public aux endpoints nécessaires pour l'inscription et la consultation publique.

## Étapes de résolution

### 1. Modifier `config/packages/security.yaml`
Ajouter les règles `access_control` pour autoriser le `GET` sur :
- `/api/countries`
- `/api/document_types`
- `/api/templates` (si nécessaire pour la page d'accueil/vitrine)

### 2. Vider le cache
Exécuter `php bin/console cache:clear` pour appliquer les changements de sécurité.

### 3. Vérification
Vérifier que la commande `curl` (ou équivalent) sur `/api/countries` ne renvoie plus une erreur 401.
