# DOCGEN AFRICA - GUIDE D'EXÉCUTION AGENT IA
# Backend Symfony 7 + API Platform 4
# Version: 1.0.0
# Date: Février 2026

---

## MÉTADONNÉES DU PROJET

```yaml
project_name: docgen-africa-api
description: API RESTful pour génération de documents administratifs africains
framework: Symfony 7.2
api_framework: API Platform 4.0
php_version: "8.3"
database: PostgreSQL 16
cache: Redis 7
```

---

## INSTRUCTIONS POUR L'AGENT IA

Ce document contient des instructions séquentielles pour construire l'application.
- Exécuter chaque TASK dans l'ordre
- Ne pas passer à la TASK suivante avant validation
- Chaque TASK contient: objectif, commandes, fichiers à créer, validation
- Les blocs de code sont complets et prêts à l'emploi

---

# PHASE 1: INITIALISATION DU PROJET

## TASK 1.1: Créer le projet Symfony

### Objectif
Initialiser un nouveau projet Symfony avec API Platform

### Commandes à exécuter

```bash
# Créer le répertoire et initialiser le projet
composer create-project symfony/skeleton docgen-africa-api "7.2.*"
cd docgen-africa-api

# Installer API Platform et les dépendances core
composer require api
composer require symfony/orm-pack
composer require symfony/uid
composer require ramsey/uuid-doctrine

# Installer les outils d'authentification
composer require lexik/jwt-authentication-bundle
composer require gesdinet/jwt-refresh-token-bundle

# Installer les utilitaires
composer require symfony/messenger
composer require symfony/mailer
composer require nelmio/cors-bundle
composer require stof/doctrine-extensions-bundle

# Installer les outils d'export
composer require knplabs/knp-snappy-bundle
composer require phpoffice/phpword

# Installer les outils de développement
composer require --dev symfony/maker-bundle
composer require --dev symfony/test-pack
composer require --dev dama/doctrine-test-bundle
composer require --dev zenstruck/foundry
composer require --dev phpstan/phpstan
composer require --dev friendsofphp/php-cs-fixer
```

### Validation
```bash
# Vérifier que Symfony fonctionne
php bin/console about
# Doit afficher les informations Symfony sans erreur
```

---

## TASK 1.2: Configurer l'environnement

### Objectif
Configurer les variables d'environnement et la base de données

### Fichier à créer: `.env.local`

```bash
cat > .env.local << 'EOF'
###> doctrine/doctrine-bundle ###
DATABASE_URL="postgresql://docgen:docgen_password@127.0.0.1:5432/docgen_db?serverVersion=16&charset=utf8"
###< doctrine/doctrine-bundle ###

###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=docgen_jwt_passphrase_change_in_production
###< lexik/jwt-authentication-bundle ###

###> symfony/messenger ###
MESSENGER_TRANSPORT_DSN=redis://localhost:6379/messages
###< symfony/messenger ###

###> nelmio/cors-bundle ###
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
###< nelmio/cors-bundle ###

###> app ###
ANTHROPIC_API_KEY=your_anthropic_api_key_here
###< app ###
EOF
```

### Validation
```bash
php bin/console debug:container --env-vars
# Doit lister les variables d'environnement configurées
```

---

## TASK 1.3: Configurer API Platform

### Objectif
Configurer API Platform avec les options optimales

### Fichier à créer: `config/packages/api_platform.yaml`

```bash
cat > config/packages/api_platform.yaml << 'EOF'
api_platform:
    title: 'DocGen Africa API'
    description: 'API pour la génération de documents administratifs africains'
    version: '1.0.0'
    
    formats:
        jsonld: ['application/ld+json']
        json: ['application/json']
    
    docs_formats:
        jsonld: ['application/ld+json']
        jsonopenapi: ['application/vnd.openapi+json']
        html: ['text/html']
    
    defaults:
        stateless: true
        cache_headers:
            vary: ['Content-Type', 'Authorization', 'Accept-Language']
        extra_properties:
            standard_put: true
        pagination_items_per_page: 15
        pagination_client_items_per_page: true
        pagination_maximum_items_per_page: 100
    
    swagger:
        versions: [3]
        api_keys:
            JWT:
                name: Authorization
                type: header
    
    mapping:
        paths: ['%kernel.project_dir%/src/Entity']
    
    patch_formats:
        json: ['application/merge-patch+json']
EOF
```

### Validation
```bash
php bin/console debug:config api_platform
# Doit afficher la configuration API Platform
```

---

## TASK 1.4: Configurer Doctrine

### Objectif
Configurer Doctrine ORM avec PostgreSQL et les types UUID

### Fichier à créer: `config/packages/doctrine.yaml`

```bash
cat > config/packages/doctrine.yaml << 'EOF'
doctrine:
    dbal:
        url: '%env(resolve:DATABASE_URL)%'
        profiling_collect_backtrace: '%kernel.debug%'
        use_savepoints: true
        types:
            uuid: Ramsey\Uuid\Doctrine\UuidType
    orm:
        auto_generate_proxy_classes: true
        enable_lazy_ghost_objects: true
        report_fields_where_declared: true
        validate_xml_mapping: true
        naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
        auto_mapping: true
        mappings:
            App:
                type: attribute
                is_bundle: false
                dir: '%kernel.project_dir%/src/Entity'
                prefix: 'App\Entity'
                alias: App
        controller_resolver:
            auto_mapping: true

when@prod:
    doctrine:
        orm:
            auto_generate_proxy_classes: false
            proxy_dir: '%kernel.build_dir%/doctrine/orm/Proxies'
            query_cache_driver:
                type: pool
                pool: doctrine.system_cache_pool
            result_cache_driver:
                type: pool
                pool: doctrine.result_cache_pool
EOF
```

### Validation
```bash
php bin/console doctrine:database:create --if-not-exists
# Doit créer la base de données sans erreur
```

---

## TASK 1.5: Configurer JWT Authentication

### Objectif
Générer les clés JWT et configurer l'authentification

### Commandes à exécuter

```bash
# Créer le répertoire pour les clés JWT
mkdir -p config/jwt

# Générer les clés
php bin/console lexik:jwt:generate-keypair

# Définir les permissions
chmod 644 config/jwt/public.pem
chmod 600 config/jwt/private.pem
```

### Fichier à créer: `config/packages/lexik_jwt_authentication.yaml`

```bash
cat > config/packages/lexik_jwt_authentication.yaml << 'EOF'
lexik_jwt_authentication:
    secret_key: '%env(resolve:JWT_SECRET_KEY)%'
    public_key: '%env(resolve:JWT_PUBLIC_KEY)%'
    pass_phrase: '%env(JWT_PASSPHRASE)%'
    token_ttl: 3600
    user_identity_field: email
    
    token_extractors:
        authorization_header:
            enabled: true
            prefix: Bearer
            name: Authorization
EOF
```

### Validation
```bash
php bin/console lexik:jwt:check-config
# Doit confirmer que la configuration JWT est correcte
```

---

## TASK 1.6: Configurer la Sécurité

### Objectif
Configurer le système de sécurité Symfony avec JWT

### Fichier à créer: `config/packages/security.yaml`

```bash
cat > config/packages/security.yaml << 'EOF'
security:
    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
    
    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email
    
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        
        login:
            pattern: ^/api/auth/login
            stateless: true
            json_login:
                check_path: /api/auth/login
                username_path: email
                password_path: password
                success_handler: lexik_jwt_authentication.handler.authentication_success
                failure_handler: lexik_jwt_authentication.handler.authentication_failure
        
        api:
            pattern: ^/api
            stateless: true
            jwt: ~
    
    access_control:
        - { path: ^/api/docs, roles: PUBLIC_ACCESS }
        - { path: ^/api/auth, roles: PUBLIC_ACCESS }
        - { path: ^/api/countries, roles: PUBLIC_ACCESS, methods: [GET] }
        - { path: ^/api/document_types, roles: PUBLIC_ACCESS, methods: [GET] }
        - { path: ^/api/templates, roles: PUBLIC_ACCESS, methods: [GET] }
        - { path: ^/api, roles: IS_AUTHENTICATED_FULLY }
EOF
```

### Validation
```bash
php bin/console debug:config security
# Doit afficher la configuration sécurité sans erreur
```

---

## TASK 1.7: Configurer CORS

### Objectif
Configurer les headers CORS pour l'API

### Fichier à créer: `config/packages/nelmio_cors.yaml`

```bash
cat > config/packages/nelmio_cors.yaml << 'EOF'
nelmio_cors:
    defaults:
        origin_regex: true
        allow_origin: ['%env(CORS_ALLOW_ORIGIN)%']
        allow_methods: ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE']
        allow_headers: ['Content-Type', 'Authorization', 'Accept', 'Accept-Language']
        expose_headers: ['Link', 'X-Total-Count']
        max_age: 3600
    paths:
        '^/api/':
            allow_origin: ['%env(CORS_ALLOW_ORIGIN)%']
            allow_headers: ['Content-Type', 'Authorization', 'Accept']
            allow_methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']
            max_age: 3600
EOF
```

### Validation
```bash
php bin/console debug:config nelmio_cors
# Doit afficher la configuration CORS
```

---

## TASK 1.8: Configurer Messenger

### Objectif
Configurer Symfony Messenger pour les tâches asynchrones

### Fichier à créer: `config/packages/messenger.yaml`

```bash
cat > config/packages/messenger.yaml << 'EOF'
framework:
    messenger:
        failure_transport: failed
        
        transports:
            async:
                dsn: '%env(MESSENGER_TRANSPORT_DSN)%'
                options:
                    stream: 'messages'
                    group: 'docgen'
                    consumer: 'consumer'
                retry_strategy:
                    max_retries: 3
                    delay: 1000
                    multiplier: 2
                    max_delay: 60000
            
            failed:
                dsn: 'doctrine://default?queue_name=failed'
        
        routing:
            'App\Message\GenerateDocumentMessage': async
            'App\Message\ExportDocumentMessage': async

when@test:
    framework:
        messenger:
            transports:
                async: 'in-memory://'
EOF
```

### Validation
```bash
php bin/console debug:messenger
# Doit lister les transports configurés
```

---

# PHASE 2: CRÉATION DES ENTITÉS

## TASK 2.1: Créer les Énumérations

### Objectif
Créer les énumérations PHP 8.1+ pour les statuts et types

### Fichier à créer: `src/Enum/DocumentStatus.php`

```bash
mkdir -p src/Enum

cat > src/Enum/DocumentStatus.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Enum;

enum DocumentStatus: string
{
    case DRAFT = 'draft';
    case GENERATING = 'generating';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Brouillon',
            self::GENERATING => 'Génération en cours',
            self::COMPLETED => 'Terminé',
            self::FAILED => 'Échec',
        };
    }

    public function isEditable(): bool
    {
        return $this === self::DRAFT || $this === self::FAILED;
    }
}
EOF
```

### Fichier à créer: `src/Enum/SubscriptionType.php`

```bash
cat > src/Enum/SubscriptionType.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Enum;

enum SubscriptionType: string
{
    case FREE = 'free';
    case CITIZEN = 'citizen';
    case PRO = 'pro';
    case ENTERPRISE = 'enterprise';

    public function monthlyLimit(): int
    {
        return match($this) {
            self::FREE => 3,
            self::CITIZEN => 15,
            self::PRO => 999999,
            self::ENTERPRISE => 999999,
        };
    }

    public function canExportDocx(): bool
    {
        return $this !== self::FREE;
    }

    public function label(): string
    {
        return match($this) {
            self::FREE => 'Gratuit',
            self::CITIZEN => 'Citoyen',
            self::PRO => 'Professionnel',
            self::ENTERPRISE => 'Entreprise',
        };
    }
}
EOF
```

### Fichier à créer: `src/Enum/ToneLevel.php`

```bash
cat > src/Enum/ToneLevel.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Enum;

enum ToneLevel: string
{
    case VERY_FORMAL = 'very_formal';
    case FORMAL = 'formal';
    case SIMPLE = 'simple';

    public function label(): string
    {
        return match($this) {
            self::VERY_FORMAL => 'Très formel',
            self::FORMAL => 'Formel standard',
            self::SIMPLE => 'Simple',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::VERY_FORMAL => 'Pour les administrations officielles et ministères',
            self::FORMAL => 'Pour les entreprises et services publics',
            self::SIMPLE => 'Pour les communications courantes',
        };
    }
}
EOF
```

### Validation
```bash
php bin/console lint:container
# Ne doit pas afficher d'erreur
```

---

## TASK 2.2: Créer l'entité Country

### Objectif
Créer l'entité Country pour les pays supportés

### Fichier à créer: `src/Entity/Country.php`

```bash
mkdir -p src/Entity

cat > src/Entity/Country.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\Table(name: 'country')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
    ],
    normalizationContext: ['groups' => ['country:read']],
    order: ['name' => 'ASC'],
    paginationEnabled: false
)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['country:read', 'user:read', 'template:read', 'document:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(['country:read', 'user:read', 'template:read', 'document:read'])]
    private string $name;

    #[ORM\Column(length: 2)]
    #[Assert\NotBlank]
    #[Assert\Length(exactly: 2)]
    #[Groups(['country:read', 'user:read', 'template:read'])]
    private string $codeIso;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank]
    #[Groups(['country:read'])]
    private string $language = 'fr';

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Groups(['country:read'])]
    private string $dateFormat = 'd/m/Y';

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank]
    #[Groups(['country:read'])]
    private string $currency = 'XOF';

    #[ORM\Column]
    #[Groups(['country:read'])]
    private bool $isActive = true;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getCodeIso(): string
    {
        return $this->codeIso;
    }

    public function setCodeIso(string $codeIso): static
    {
        $this->codeIso = strtoupper($codeIso);
        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;
        return $this;
    }

    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    public function setDateFormat(string $dateFormat): static
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
EOF
```

### Fichier à créer: `src/Repository/CountryRepository.php`

```bash
mkdir -p src/Repository

cat > src/Repository/CountryRepository.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Country>
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    /**
     * @return Country[]
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCodeIso(string $codeIso): ?Country
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.codeIso = :code')
            ->setParameter('code', strtoupper($codeIso))
            ->getQuery()
            ->getOneOrNullResult();
    }
}
EOF
```

### Validation
```bash
php bin/console lint:container
# Ne doit pas afficher d'erreur
```

---

## TASK 2.3: Créer l'entité DocumentType

### Objectif
Créer l'entité pour les types de documents

### Fichier à créer: `src/Entity/DocumentType.php`

```bash
cat > src/Entity/DocumentType.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\DocumentTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DocumentTypeRepository::class)]
#[ORM\Table(name: 'document_type')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
    ],
    normalizationContext: ['groups' => ['document_type:read']],
    order: ['sortOrder' => 'ASC', 'name' => 'ASC'],
    paginationEnabled: false
)]
class DocumentType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['document_type:read', 'template:read', 'document:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[a-z0-9\-]+$/')]
    #[Groups(['document_type:read', 'template:read'])]
    private string $slug;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(['document_type:read', 'template:read', 'document:read'])]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['document_type:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Groups(['document_type:read'])]
    private string $category;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['document_type:read'])]
    private ?string $icon = null;

    #[ORM\Column]
    #[Groups(['document_type:read'])]
    private bool $isActive = true;

    #[ORM\Column]
    #[Groups(['document_type:read'])]
    private int $sortOrder = 0;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
EOF
```

### Fichier à créer: `src/Repository/DocumentTypeRepository.php`

```bash
cat > src/Repository/DocumentTypeRepository.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DocumentType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentType>
 */
class DocumentTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentType::class);
    }

    /**
     * @return DocumentType[]
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('dt')
            ->andWhere('dt.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('dt.sortOrder', 'ASC')
            ->addOrderBy('dt.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBySlug(string $slug): ?DocumentType
    {
        return $this->createQueryBuilder('dt')
            ->andWhere('dt.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return DocumentType[]
     */
    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('dt')
            ->andWhere('dt.category = :category')
            ->andWhere('dt.isActive = :active')
            ->setParameter('category', $category)
            ->setParameter('active', true)
            ->orderBy('dt.sortOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
EOF
```

### Validation
```bash
php bin/console lint:container
```

---

## TASK 2.4: Créer l'entité User

### Objectif
Créer l'entité User avec authentification et abonnement

### Fichier à créer: `src/Entity/User.php`

```bash
cat > src/Entity/User.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use App\Enum\SubscriptionType;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé')]
#[UniqueEntity(fields: ['phone'], message: 'Ce numéro de téléphone est déjà utilisé')]
#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_USER') and object == user",
            securityMessage: "Vous ne pouvez accéder qu'à votre propre profil"
        ),
        new Put(
            security: "is_granted('ROLE_USER') and object == user",
            securityMessage: "Vous ne pouvez modifier que votre propre profil"
        ),
    ],
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['user:read', 'document:read'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read', 'user:write'])]
    private Country $country;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['user:read', 'user:write', 'document:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true, nullable: true)]
    #[Assert\Email(message: 'Email invalide')]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 20, unique: true, nullable: true)]
    #[Assert\Regex(
        pattern: '/^\+?[0-9]{8,15}$/',
        message: 'Numéro de téléphone invalide'
    )]
    #[Groups(['user:read', 'user:write'])]
    private ?string $phone = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\Column(type: 'string', enumType: SubscriptionType::class)]
    #[Groups(['user:read'])]
    private SubscriptionType $subscriptionType = SubscriptionType::FREE;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $subscriptionEndsAt = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private bool $isGuest = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $emailVerifiedAt = null;

    /** @var Collection<int, Document> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Document::class, orphanRemoval: true)]
    private Collection $documents;

    /** @var Collection<int, UsageQuota> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UsageQuota::class, orphanRemoval: true)]
    private Collection $usageQuotas;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->documents = new ArrayCollection();
        $this->usageQuotas = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Effacer les données sensibles temporaires si nécessaire
    }

    public function getSubscriptionType(): SubscriptionType
    {
        return $this->subscriptionType;
    }

    public function setSubscriptionType(SubscriptionType $subscriptionType): static
    {
        $this->subscriptionType = $subscriptionType;
        return $this;
    }

    public function getSubscriptionEndsAt(): ?\DateTimeImmutable
    {
        return $this->subscriptionEndsAt;
    }

    public function setSubscriptionEndsAt(?\DateTimeImmutable $subscriptionEndsAt): static
    {
        $this->subscriptionEndsAt = $subscriptionEndsAt;
        return $this;
    }

    public function isGuest(): bool
    {
        return $this->isGuest;
    }

    public function setIsGuest(bool $isGuest): static
    {
        $this->isGuest = $isGuest;
        return $this;
    }

    public function getEmailVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }

    public function setEmailVerifiedAt(?\DateTimeImmutable $emailVerifiedAt): static
    {
        $this->emailVerifiedAt = $emailVerifiedAt;
        return $this;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setUser($this);
        }
        return $this;
    }

    public function removeDocument(Document $document): static
    {
        $this->documents->removeElement($document);
        return $this;
    }

    /**
     * @return Collection<int, UsageQuota>
     */
    public function getUsageQuotas(): Collection
    {
        return $this->usageQuotas;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getMonthlyLimit(): int
    {
        return $this->subscriptionType->monthlyLimit();
    }

    public function canExportDocx(): bool
    {
        return $this->subscriptionType->canExportDocx();
    }

    public function hasActiveSubscription(): bool
    {
        if ($this->subscriptionType === SubscriptionType::FREE) {
            return true;
        }
        
        return $this->subscriptionEndsAt !== null && $this->subscriptionEndsAt > new \DateTimeImmutable();
    }
}
EOF
```

### Fichier à créer: `src/Repository/UserRepository.php`

```bash
cat > src/Repository/UserRepository.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByPhone(string $phone): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.phone = :phone')
            ->setParameter('phone', $phone)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findById(UuidInterface $id): ?User
    {
        return $this->find($id);
    }
}
EOF
```

### Validation
```bash
php bin/console lint:container
```

---

## TASK 2.5: Créer l'entité Template

### Objectif
Créer l'entité Template pour les modèles de documents

### Fichier à créer: `src/Entity/Template.php`

```bash
cat > src/Entity/Template.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Enum\ToneLevel;
use App\Repository\TemplateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
#[ORM\Table(name: 'template')]
#[ORM\Index(columns: ['country_id', 'document_type_id'], name: 'idx_template_country_type')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
    ],
    normalizationContext: ['groups' => ['template:read']],
    order: ['documentType.sortOrder' => 'ASC'],
    paginationEnabled: false
)]
#[ApiFilter(SearchFilter::class, properties: [
    'country.id' => 'exact',
    'country.codeIso' => 'exact',
    'documentType.id' => 'exact',
    'documentType.slug' => 'exact',
    'toneLevel' => 'exact',
    'isActive' => 'exact',
])]
class Template
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['template:read', 'document:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: DocumentType::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['template:read', 'document:read'])]
    private DocumentType $documentType;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['template:read', 'document:read'])]
    private Country $country;

    #[ORM\Column(type: 'string', enumType: ToneLevel::class)]
    #[Groups(['template:read', 'document:read'])]
    private ToneLevel $toneLevel = ToneLevel::FORMAL;

    #[ORM\Column]
    #[Groups(['template:read'])]
    private int $version = 1;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $systemPrompt;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $userPromptTemplate;

    #[ORM\Column(type: 'json')]
    #[Groups(['template:read'])]
    private array $fieldsSchema = [];

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $exampleOutput = null;

    #[ORM\Column]
    #[Groups(['template:read'])]
    private bool $isActive = true;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocumentType(): DocumentType
    {
        return $this->documentType;
    }

    public function setDocumentType(DocumentType $documentType): static
    {
        $this->documentType = $documentType;
        return $this;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getToneLevel(): ToneLevel
    {
        return $this->toneLevel;
    }

    public function setToneLevel(ToneLevel $toneLevel): static
    {
        $this->toneLevel = $toneLevel;
        return $this;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): static
    {
        $this->version = $version;
        return $this;
    }

    public function getSystemPrompt(): string
    {
        return $this->systemPrompt;
    }

    public function setSystemPrompt(string $systemPrompt): static
    {
        $this->systemPrompt = $systemPrompt;
        return $this;
    }

    public function getUserPromptTemplate(): string
    {
        return $this->userPromptTemplate;
    }

    public function setUserPromptTemplate(string $userPromptTemplate): static
    {
        $this->userPromptTemplate = $userPromptTemplate;
        return $this;
    }

    public function getFieldsSchema(): array
    {
        return $this->fieldsSchema;
    }

    public function setFieldsSchema(array $fieldsSchema): static
    {
        $this->fieldsSchema = $fieldsSchema;
        return $this;
    }

    public function getExampleOutput(): ?string
    {
        return $this->exampleOutput;
    }

    public function setExampleOutput(?string $exampleOutput): static
    {
        $this->exampleOutput = $exampleOutput;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
EOF
```

### Fichier à créer: `src/Repository/TemplateRepository.php`

```bash
cat > src/Repository/TemplateRepository.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Country;
use App\Entity\DocumentType;
use App\Entity\Template;
use App\Enum\ToneLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Template>
 */
class TemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Template::class);
    }

    /**
     * @return Template[]
     */
    public function findByCountry(Country $country): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.country = :country')
            ->andWhere('t.isActive = :active')
            ->setParameter('country', $country)
            ->setParameter('active', true)
            ->leftJoin('t.documentType', 'dt')
            ->addSelect('dt')
            ->orderBy('dt.sortOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByCountryAndType(
        Country $country,
        DocumentType $documentType,
        ToneLevel $toneLevel = ToneLevel::FORMAL
    ): ?Template {
        return $this->createQueryBuilder('t')
            ->andWhere('t.country = :country')
            ->andWhere('t.documentType = :type')
            ->andWhere('t.toneLevel = :tone')
            ->andWhere('t.isActive = :active')
            ->setParameter('country', $country)
            ->setParameter('type', $documentType)
            ->setParameter('tone', $toneLevel)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Template[]
     */
    public function findAllActiveWithRelations(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.isActive = :active')
            ->setParameter('active', true)
            ->leftJoin('t.country', 'c')
            ->addSelect('c')
            ->leftJoin('t.documentType', 'dt')
            ->addSelect('dt')
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('dt.sortOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
EOF
```

### Validation
```bash
php bin/console lint:container
```

---

## TASK 2.6: Créer l'entité Document

### Objectif
Créer l'entité principale Document avec API Platform

### Fichier à créer: `src/Entity/Document.php`

```bash
cat > src/Entity/Document.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Dto\GenerateDocumentInput;
use App\Enum\DocumentStatus;
use App\Repository\DocumentRepository;
use App\State\Processor\DocumentPersistProcessor;
use App\State\Processor\GenerateDocumentProcessor;
use App\State\Provider\UserDocumentsProvider;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\Table(name: 'document')]
#[ORM\Index(columns: ['user_id', 'status'], name: 'idx_document_user_status')]
#[ORM\Index(columns: ['created_at'], name: 'idx_document_created')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            provider: UserDocumentsProvider::class,
            paginationItemsPerPage: 15
        ),
        new Get(
            security: "is_granted('DOCUMENT_VIEW', object)",
            securityMessage: "Vous ne pouvez pas accéder à ce document"
        ),
        new Post(
            security: "is_granted('ROLE_USER')",
            processor: DocumentPersistProcessor::class,
            validationContext: ['groups' => ['Default', 'document:create']]
        ),
        new Put(
            security: "is_granted('DOCUMENT_EDIT', object)",
            securityMessage: "Vous ne pouvez pas modifier ce document",
            processor: DocumentPersistProcessor::class
        ),
        new Delete(
            security: "is_granted('DOCUMENT_DELETE', object)",
            securityMessage: "Vous ne pouvez pas supprimer ce document"
        ),
        new Post(
            uriTemplate: '/documents/{id}/generate',
            input: GenerateDocumentInput::class,
            security: "is_granted('DOCUMENT_EDIT', object)",
            securityMessage: "Vous ne pouvez pas générer ce document",
            processor: GenerateDocumentProcessor::class,
            name: 'generate',
            openapiContext: [
                'summary' => 'Génère le contenu du document via IA',
                'description' => 'Lance la génération asynchrone du document. Le statut passe à "generating".',
            ]
        ),
    ],
    normalizationContext: ['groups' => ['document:read']],
    denormalizationContext: ['groups' => ['document:write']],
    order: ['createdAt' => 'DESC']
)]
#[ApiFilter(SearchFilter::class, properties: [
    'status' => 'exact',
    'title' => 'partial',
    'template.documentType.slug' => 'exact',
])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'generatedAt'])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'title', 'generatedAt'])]
class Document
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['document:read'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['document:read'])]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Template::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le template est requis', groups: ['document:create'])]
    #[Groups(['document:read', 'document:write'])]
    private Template $template;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est requis')]
    #[Assert\Length(max: 255, maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères')]
    #[Groups(['document:read', 'document:write'])]
    private string $title;

    #[ORM\Column(type: 'json')]
    #[Assert\NotNull(message: 'Les données sont requises')]
    #[Groups(['document:read', 'document:write'])]
    private array $inputData = [];

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['document:read'])]
    private ?string $generatedContent = null;

    #[ORM\Column(type: 'string', enumType: DocumentStatus::class)]
    #[Groups(['document:read'])]
    private DocumentStatus $status = DocumentStatus::DRAFT;

    #[ORM\Column(nullable: true)]
    private ?int $tokensUsed = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pdfPath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $docxPath = null;

    #[ORM\Column]
    #[Groups(['document:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['document:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['document:read'])]
    private ?\DateTimeImmutable $generatedAt = null;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getTemplate(): Template
    {
        return $this->template;
    }

    public function setTemplate(Template $template): static
    {
        $this->template = $template;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getInputData(): array
    {
        return $this->inputData;
    }

    public function setInputData(array $inputData): static
    {
        $this->inputData = $inputData;
        return $this;
    }

    public function getGeneratedContent(): ?string
    {
        return $this->generatedContent;
    }

    public function setGeneratedContent(?string $generatedContent): static
    {
        $this->generatedContent = $generatedContent;
        return $this;
    }

    public function getStatus(): DocumentStatus
    {
        return $this->status;
    }

    public function setStatus(DocumentStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getTokensUsed(): ?int
    {
        return $this->tokensUsed;
    }

    public function setTokensUsed(?int $tokensUsed): static
    {
        $this->tokensUsed = $tokensUsed;
        return $this;
    }

    public function getPdfPath(): ?string
    {
        return $this->pdfPath;
    }

    public function setPdfPath(?string $pdfPath): static
    {
        $this->pdfPath = $pdfPath;
        return $this;
    }

    public function getDocxPath(): ?string
    {
        return $this->docxPath;
    }

    public function setDocxPath(?string $docxPath): static
    {
        $this->docxPath = $docxPath;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getGeneratedAt(): ?\DateTimeImmutable
    {
        return $this->generatedAt;
    }

    public function setGeneratedAt(?\DateTimeImmutable $generatedAt): static
    {
        $this->generatedAt = $generatedAt;
        return $this;
    }

    public function isEditable(): bool
    {
        return $this->status->isEditable();
    }

    public function isCompleted(): bool
    {
        return $this->status === DocumentStatus::COMPLETED;
    }

    #[Groups(['document:read'])]
    public function getStatusLabel(): string
    {
        return $this->status->label();
    }
}
EOF
```

### Fichier à créer: `src/Repository/DocumentRepository.php`

```bash
cat > src/Repository/DocumentRepository.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Document;
use App\Entity\User;
use App\Enum\DocumentStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends ServiceEntityRepository<Document>
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function save(Document $document, bool $flush = true): void
    {
        $this->getEntityManager()->persist($document);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Document $document, bool $flush = true): void
    {
        $this->getEntityManager()->remove($document);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findById(UuidInterface $id): ?Document
    {
        return $this->find($id);
    }

    public function createQueryBuilderForUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.user = :user')
            ->setParameter('user', $user)
            ->leftJoin('d.template', 't')
            ->addSelect('t')
            ->leftJoin('t.documentType', 'dt')
            ->addSelect('dt')
            ->leftJoin('t.country', 'c')
            ->addSelect('c')
            ->orderBy('d.createdAt', 'DESC');
    }

    /**
     * @return Document[]
     */
    public function findByUser(User $user, int $limit = 50): array
    {
        return $this->createQueryBuilderForUser($user)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Document[]
     */
    public function findByUserAndStatus(User $user, DocumentStatus $status): array
    {
        return $this->createQueryBuilderForUser($user)
            ->andWhere('d.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    public function countByUserAndPeriod(User $user, string $period): int
    {
        $startDate = new \DateTimeImmutable($period . '-01');
        $endDate = $startDate->modify('last day of this month')->setTime(23, 59, 59);

        return (int) $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->andWhere('d.user = :user')
            ->andWhere('d.createdAt >= :start')
            ->andWhere('d.createdAt <= :end')
            ->andWhere('d.status = :status')
            ->setParameter('user', $user)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('status', DocumentStatus::COMPLETED)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
EOF
```

### Validation
```bash
php bin/console lint:container
```

---

## TASK 2.7: Créer l'entité UsageQuota

### Objectif
Créer l'entité pour gérer les quotas d'utilisation

### Fichier à créer: `src/Entity/UsageQuota.php`

```bash
cat > src/Entity/UsageQuota.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UsageQuotaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UsageQuotaRepository::class)]
#[ORM\Table(name: 'usage_quota')]
#[ORM\UniqueConstraint(name: 'unique_user_period', columns: ['user_id', 'period'])]
#[ORM\Index(columns: ['user_id', 'period'], name: 'idx_usage_quota_user_period')]
class UsageQuota
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'usageQuotas')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(length: 7)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^\d{4}-\d{2}$/', message: 'Format invalide (YYYY-MM)')]
    private string $period;

    #[ORM\Column]
    private int $documentsGenerated = 0;

    #[ORM\Column]
    private int $documentsLimit;

    #[ORM\Column]
    private int $tokensUsed = 0;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        $this->documentsLimit = $user->getMonthlyLimit();
        return $this;
    }

    public function getPeriod(): string
    {
        return $this->period;
    }

    public function setPeriod(string $period): static
    {
        $this->period = $period;
        return $this;
    }

    public function getDocumentsGenerated(): int
    {
        return $this->documentsGenerated;
    }

    public function setDocumentsGenerated(int $documentsGenerated): static
    {
        $this->documentsGenerated = $documentsGenerated;
        return $this;
    }

    public function incrementDocumentsGenerated(): static
    {
        $this->documentsGenerated++;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getDocumentsLimit(): int
    {
        return $this->documentsLimit;
    }

    public function setDocumentsLimit(int $documentsLimit): static
    {
        $this->documentsLimit = $documentsLimit;
        return $this;
    }

    public function getTokensUsed(): int
    {
        return $this->tokensUsed;
    }

    public function setTokensUsed(int $tokensUsed): static
    {
        $this->tokensUsed = $tokensUsed;
        return $this;
    }

    public function addTokensUsed(int $tokens): static
    {
        $this->tokensUsed += $tokens;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getRemainingDocuments(): int
    {
        return max(0, $this->documentsLimit - $this->documentsGenerated);
    }

    public function hasReachedLimit(): bool
    {
        return $this->documentsGenerated >= $this->documentsLimit;
    }

    public function getUsagePercentage(): float
    {
        if ($this->documentsLimit === 0) {
            return 100.0;
        }
        return min(100.0, ($this->documentsGenerated / $this->documentsLimit) * 100);
    }
}
EOF
```

### Fichier à créer: `src/Repository/UsageQuotaRepository.php`

```bash
cat > src/Repository/UsageQuotaRepository.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UsageQuota;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UsageQuota>
 */
class UsageQuotaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsageQuota::class);
    }

    public function save(UsageQuota $quota, bool $flush = true): void
    {
        $this->getEntityManager()->persist($quota);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOrCreateForCurrentPeriod(User $user): UsageQuota
    {
        $period = (new \DateTimeImmutable())->format('Y-m');
        
        $quota = $this->findOneBy([
            'user' => $user,
            'period' => $period,
        ]);

        if ($quota === null) {
            $quota = new UsageQuota();
            $quota->setUser($user);
            $quota->setPeriod($period);
            $this->save($quota);
        }

        return $quota;
    }

    public function findByUserAndPeriod(User $user, string $period): ?UsageQuota
    {
        return $this->findOneBy([
            'user' => $user,
            'period' => $period,
        ]);
    }

    /**
     * @return UsageQuota[]
     */
    public function findByUser(User $user, int $months = 6): array
    {
        $startPeriod = (new \DateTimeImmutable())
            ->modify("-{$months} months")
            ->format('Y-m');

        return $this->createQueryBuilder('q')
            ->andWhere('q.user = :user')
            ->andWhere('q.period >= :start')
            ->setParameter('user', $user)
            ->setParameter('start', $startPeriod)
            ->orderBy('q.period', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
EOF
```

### Validation
```bash
php bin/console lint:container
```

---

## TASK 2.8: Créer les migrations et la base de données

### Objectif
Générer et exécuter les migrations Doctrine

### Commandes à exécuter

```bash
# Générer la migration
php bin/console make:migration

# Exécuter la migration
php bin/console doctrine:migrations:migrate --no-interaction

# Vérifier le schéma
php bin/console doctrine:schema:validate
```

### Validation
```bash
php bin/console doctrine:schema:validate
# Doit afficher "OK" pour le mapping et le schéma
```

---

# PHASE 3: CRÉATION DES SERVICES ET LOGIQUE MÉTIER

## TASK 3.1: Créer les DTOs

### Objectif
Créer les Data Transfer Objects pour les opérations API

### Fichier à créer: `src/Dto/GenerateDocumentInput.php`

```bash
mkdir -p src/Dto

cat > src/Dto/GenerateDocumentInput.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class GenerateDocumentInput
{
    #[Assert\Type('bool')]
    public bool $regenerate = false;
}
EOF
```

### Fichier à créer: `src/Dto/RegisterUserInput.php`

```bash
cat > src/Dto/RegisterUserInput.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class RegisterUserInput
{
    #[Assert\NotBlank(message: 'L\'email est requis')]
    #[Assert\Email(message: 'Email invalide')]
    public string $email;

    #[Assert\NotBlank(message: 'Le mot de passe est requis')]
    #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères')]
    public string $password;

    #[Assert\Length(max: 255)]
    public ?string $name = null;

    #[Assert\Regex(pattern: '/^\+?[0-9]{8,15}$/', message: 'Numéro de téléphone invalide')]
    public ?string $phone = null;

    #[Assert\NotBlank(message: 'Le pays est requis')]
    #[Assert\Positive]
    public int $countryId;
}
EOF
```

### Validation
```bash
php bin/console lint:container
```

---

## TASK 3.2: Créer les Exceptions personnalisées

### Objectif
Créer les exceptions métier de l'application

### Fichier à créer: `src/Exception/QuotaExceededException.php`

```bash
mkdir -p src/Exception

cat > src/Exception/QuotaExceededException.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class QuotaExceededException extends HttpException
{
    public function __construct(
        string $message = 'Vous avez atteint votre limite mensuelle de documents',
        ?\Throwable $previous = null
    ) {
        parent::__construct(429, $message, $previous);
    }
}
EOF
```

### Fichier à créer: `src/Exception/AIGenerationException.php`

```bash
cat > src/Exception/AIGenerationException.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AIGenerationException extends HttpException
{
    public function __construct(
        string $message = 'Erreur lors de la génération du document',
        ?\Throwable $previous = null
    ) {
        parent::__construct(503, $message, $previous);
    }
}
EOF
```

### Fichier à créer: `src/Exception/DocumentNotEditableException.php`

```bash
cat > src/Exception/DocumentNotEditableException.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class DocumentNotEditableException extends HttpException
{
    public function __construct(
        string $message = 'Ce document ne peut pas être modifié',
        ?\Throwable $previous = null
    ) {
        parent::__construct(400, $message, $previous);
    }
}
EOF
```

### Validation
```bash
php bin/console lint:container
```

---

## TASK 3.3: Créer le Service IA

### Objectif
Créer le service d'intégration avec l'API Claude

### Fichier à créer: `src/Service/AI/AIResponse.php`

```bash
mkdir -p src/Service/AI

cat > src/Service/AI/AIResponse.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Service\AI;

final readonly class AIResponse
{
    public function __construct(
        public string $content,
        public int $tokensUsed,
        public int $durationMs,
        public bool $success = true,
        public ?string $error = null
    ) {}

    public static function failure(string $error, int $durationMs = 0): self
    {
        return new self(
            content: '',
            tokensUsed: 0,
            durationMs: $durationMs,
            success: false,
            error: $error
        );
    }
}
EOF
```

### Fichier à créer: `src/Service/AI/AIServiceInterface.php`

```bash
cat > src/Service/AI/AIServiceInterface.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Service\AI;

interface AIServiceInterface
{
    public function generate(string $systemPrompt, string $userPrompt): AIResponse;
}
EOF
```

### Fichier à créer: `src/Service/AI/ClaudeService.php`

```bash
cat > src/Service/AI/ClaudeService.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Service\AI;

use App\Exception\AIGenerationException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ClaudeService implements AIServiceInterface
{
    private const API_URL = 'https://api.anthropic.com/v1/messages';
    private const MODEL = 'claude-sonnet-4-20250514';
    private const MAX_TOKENS = 1024;
    private const TIMEOUT = 60;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly string $apiKey
    ) {}

    public function generate(string $systemPrompt, string $userPrompt): AIResponse
    {
        $startTime = microtime(true);

        try {
            $this->logger->info('Starting AI generation', [
                'system_prompt_length' => strlen($systemPrompt),
                'user_prompt_length' => strlen($userPrompt),
            ]);

            $response = $this->httpClient->request('POST', self::API_URL, [
                'headers' => [
                    'x-api-key' => $this->apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json',
                ],
                'timeout' => self::TIMEOUT,
                'json' => [
                    'model' => self::MODEL,
                    'max_tokens' => self::MAX_TOKENS,
                    'system' => $systemPrompt,
                    'messages' => [
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $data = $response->toArray();
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            if ($statusCode !== 200) {
                throw new \RuntimeException(
                    $data['error']['message'] ?? 'Unknown API error'
                );
            }

            $content = $data['content'][0]['text'] ?? '';
            $tokensUsed = ($data['usage']['input_tokens'] ?? 0) + ($data['usage']['output_tokens'] ?? 0);

            $this->logger->info('AI generation completed', [
                'tokens_used' => $tokensUsed,
                'duration_ms' => $durationMs,
                'content_length' => strlen($content),
            ]);

            return new AIResponse(
                content: $content,
                tokensUsed: $tokensUsed,
                durationMs: $durationMs
            );

        } catch (\Throwable $e) {
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);
            
            $this->logger->error('AI generation failed', [
                'error' => $e->getMessage(),
                'duration_ms' => $durationMs,
            ]);

            throw new AIGenerationException(
                'Erreur lors de la génération: ' . $e->getMessage(),
                $e
            );
        }
    }
}
EOF
```

### Fichier à créer: `src/Service/AI/PromptBuilder.php`

```bash
cat > src/Service/AI/PromptBuilder.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Service\AI;

final class PromptBuilder
{
    /**
     * Construit le prompt final en remplaçant les variables
     *
     * @param string $template Template avec placeholders {variable}
     * @param array<string, mixed> $data Données à injecter
     * @return string Prompt final
     * @throws \InvalidArgumentException Si une variable requise est manquante
     */
    public function build(string $template, array $data): string
    {
        $prompt = $template;
        
        // Remplacer les variables
        foreach ($data as $key => $value) {
            $placeholder = '{' . $key . '}';
            $stringValue = $this->valueToString($value);
            $prompt = str_replace($placeholder, $stringValue, $prompt);
        }

        // Vérifier qu'il ne reste pas de placeholders non remplacés
        if (preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $prompt, $matches)) {
            $missingVars = array_unique($matches[1]);
            throw new \InvalidArgumentException(
                sprintf('Variables manquantes dans le prompt: %s', implode(', ', $missingVars))
            );
        }

        return $prompt;
    }

    /**
     * Valide que les données contiennent tous les champs requis
     *
     * @param array<string, array{required?: bool, type?: string}> $schema
     * @param array<string, mixed> $data
     * @return array<string, string> Erreurs de validation
     */
    public function validate(array $schema, array $data): array
    {
        $errors = [];

        foreach ($schema as $field => $rules) {
            $isRequired = $rules['required'] ?? false;
            $type = $rules['type'] ?? 'string';

            if ($isRequired && (!isset($data[$field]) || $data[$field] === '')) {
                $errors[$field] = sprintf('Le champ "%s" est requis', $field);
                continue;
            }

            if (isset($data[$field]) && $data[$field] !== '') {
                if (!$this->validateType($data[$field], $type)) {
                    $errors[$field] = sprintf('Le champ "%s" doit être de type %s', $field, $type);
                }
            }
        }

        return $errors;
    }

    private function valueToString(mixed $value): string
    {
        if (is_array($value)) {
            return implode(', ', array_map(fn($v) => $this->valueToString($v), $value));
        }
        
        if ($value instanceof \DateTimeInterface) {
            return $value->format('d/m/Y');
        }

        if (is_bool($value)) {
            return $value ? 'Oui' : 'Non';
        }

        return (string) $value;
    }

    private function validateType(mixed $value, string $type): bool
    {
        return match ($type) {
            'string' => is_string($value),
            'int', 'integer' => is_int($value) || (is_string($value) && ctype_digit($value)),
            'email' => is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'date' => $value instanceof \DateTimeInterface || (is_string($value) && strtotime($value) !== false),
            'bool', 'boolean' => is_bool($value),
            'array' => is_array($value),
            default => true,
        };
    }
}
EOF
```

### Configuration du service

### Fichier à modifier/créer: `config/services.yaml`

```bash
cat > config/services.yaml << 'EOF'
parameters:
    anthropic_api_key: '%env(ANTHROPIC_API_KEY)%'

services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\:
        resource: '../src/'
        exclude:
            - '../src/DependencyInjection/'
            - '../src/Entity/'
            - '../src/Kernel.php'

    # Service IA
    App\Service\AI\ClaudeService:
        arguments:
            $apiKey: '%anthropic_api_key%'

    App\Service\AI\AIServiceInterface:
        alias: App\Service\AI\ClaudeService
EOF
```

### Validation
```bash
php bin/console lint:container
php bin/console debug:autowiring AIServiceInterface
# Doit afficher le service ClaudeService
```

---

## TASK 3.4: Créer le Service de Gestion des Quotas

### Objectif
Créer le service pour gérer les quotas d'utilisation

### Fichier à créer: `src/Service/Document/QuotaManager.php`

```bash
mkdir -p src/Service/Document

cat > src/Service/Document/QuotaManager.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Service\Document;

use App\Entity\User;
use App\Entity\UsageQuota;
use App\Repository\UsageQuotaRepository;
use Psr\Log\LoggerInterface;

final class QuotaManager
{
    public function __construct(
        private readonly UsageQuotaRepository $quotaRepository,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Vérifie si l'utilisateur peut générer un nouveau document
     */
    public function canGenerate(User $user): bool
    {
        $quota = $this->quotaRepository->findOrCreateForCurrentPeriod($user);
        
        $canGenerate = !$quota->hasReachedLimit();
        
        $this->logger->debug('Checking quota', [
            'user_id' => $user->getId()->toString(),
            'generated' => $quota->getDocumentsGenerated(),
            'limit' => $quota->getDocumentsLimit(),
            'can_generate' => $canGenerate,
        ]);

        return $canGenerate;
    }

    /**
     * Incrémente le compteur de documents générés
     */
    public function incrementUsage(User $user, int $tokensUsed = 0): UsageQuota
    {
        $quota = $this->quotaRepository->findOrCreateForCurrentPeriod($user);
        
        $quota->incrementDocumentsGenerated();
        
        if ($tokensUsed > 0) {
            $quota->addTokensUsed($tokensUsed);
        }
        
        $this->quotaRepository->save($quota);

        $this->logger->info('Usage incremented', [
            'user_id' => $user->getId()->toString(),
            'new_count' => $quota->getDocumentsGenerated(),
            'tokens_used' => $tokensUsed,
        ]);

        return $quota;
    }

    /**
     * Récupère les informations de quota actuelles
     */
    public function getCurrentQuota(User $user): UsageQuota
    {
        return $this->quotaRepository->findOrCreateForCurrentPeriod($user);
    }

    /**
     * Récupère le nombre de documents restants
     */
    public function getRemainingDocuments(User $user): int
    {
        $quota = $this->quotaRepository->findOrCreateForCurrentPeriod($user);
        return $quota->getRemainingDocuments();
    }

    /**
     * Vérifie si l'utilisateur est proche de sa limite
     */
    public function isNearLimit(User $user, int $threshold = 80): bool
    {
        $quota = $this->quotaRepository->findOrCreateForCurrentPeriod($user);
        return $quota->getUsagePercentage() >= $threshold;
    }
}
EOF
```

### Validation
```bash
php bin/console lint:container
```

---

## TASK 3.5: Créer le Service de Génération de Documents

### Objectif
Créer le service principal de génération de documents

### Fichier à créer: `src/Service/Document/DocumentGenerator.php`

```bash
cat > src/Service/Document/DocumentGenerator.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Service\Document;

use App\Entity\Document;
use App\Enum\DocumentStatus;
use App\Exception\AIGenerationException;
use App\Exception\DocumentNotEditableException;
use App\Exception\QuotaExceededException;
use App\Message\DocumentGeneratedEvent;
use App\Repository\DocumentRepository;
use App\Service\AI\AIServiceInterface;
use App\Service\AI\PromptBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class DocumentGenerator
{
    public function __construct(
        private readonly AIServiceInterface $aiService,
        private readonly PromptBuilder $promptBuilder,
        private readonly QuotaManager $quotaManager,
        private readonly DocumentRepository $documentRepository,
        private readonly MessageBusInterface $eventBus,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Génère le contenu d'un document via l'IA
     *
     * @throws QuotaExceededException
     * @throws DocumentNotEditableException
     * @throws AIGenerationException
     */
    public function generate(Document $document, bool $regenerate = false): Document
    {
        $user = $document->getUser();
        
        $this->logger->info('Starting document generation', [
            'document_id' => $document->getId()->toString(),
            'user_id' => $user->getId()->toString(),
            'regenerate' => $regenerate,
        ]);

        // Vérifier que le document peut être généré
        if (!$regenerate && !$document->isEditable()) {
            throw new DocumentNotEditableException(
                'Ce document a déjà été généré. Utilisez regenerate=true pour le regénérer.'
            );
        }

        // Vérifier le quota
        if (!$this->quotaManager->canGenerate($user)) {
            throw new QuotaExceededException(
                sprintf(
                    'Vous avez atteint votre limite mensuelle de %d documents',
                    $user->getMonthlyLimit()
                )
            );
        }

        // Mettre le statut en "generating"
        $document->setStatus(DocumentStatus::GENERATING);
        $this->documentRepository->save($document);

        try {
            $template = $document->getTemplate();
            
            // Construire les prompts
            $systemPrompt = $template->getSystemPrompt();
            $userPrompt = $this->promptBuilder->build(
                $template->getUserPromptTemplate(),
                $document->getInputData()
            );

            $this->logger->debug('Prompts built', [
                'document_id' => $document->getId()->toString(),
                'system_prompt_length' => strlen($systemPrompt),
                'user_prompt_length' => strlen($userPrompt),
            ]);

            // Appeler l'IA
            $response = $this->aiService->generate($systemPrompt, $userPrompt);

            // Mettre à jour le document
            $document->setGeneratedContent($response->content);
            $document->setTokensUsed($response->tokensUsed);
            $document->setStatus(DocumentStatus::COMPLETED);
            $document->setGeneratedAt(new \DateTimeImmutable());
            
            $this->documentRepository->save($document);

            // Incrémenter le quota
            $this->quotaManager->incrementUsage($user, $response->tokensUsed);

            // Dispatcher l'événement
            $this->eventBus->dispatch(new DocumentGeneratedEvent(
                $document->getId()->toString()
            ));

            $this->logger->info('Document generated successfully', [
                'document_id' => $document->getId()->toString(),
                'tokens_used' => $response->tokensUsed,
                'duration_ms' => $response->durationMs,
            ]);

        } catch (\Throwable $e) {
            $document->setStatus(DocumentStatus::FAILED);
            $this->documentRepository->save($document);

            $this->logger->error('Document generation failed', [
                'document_id' => $document->getId()->toString(),
                'error' => $e->getMessage(),
            ]);

            if ($e instanceof AIGenerationException) {
                throw $e;
            }

            throw new AIGenerationException(
                'La génération du document a échoué: ' . $e->getMessage(),
                $e
            );
        }

        return $document;
    }
}
EOF
```

### Validation
```bash
php bin/console lint:container
```

---

## TASK 3.6: Créer les Messages et Handlers

### Objectif
Créer les messages Symfony Messenger pour le traitement asynchrone

### Fichier à créer: `src/Message/GenerateDocumentMessage.php`

```bash
mkdir -p src/Message
mkdir -p src/MessageHandler

cat > src/Message/GenerateDocumentMessage.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Message;

final readonly class GenerateDocumentMessage
{
    public function __construct(
        public string $documentId,
        public bool $regenerate = false
    ) {}
}
EOF
```

### Fichier à créer: `src/Message/DocumentGeneratedEvent.php`

```bash
cat > src/Message/DocumentGeneratedEvent.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Message;

final readonly class DocumentGeneratedEvent
{
    public function __construct(
        public string $documentId
    ) {}
}
EOF
```

### Fichier à créer: `src/Message/ExportDocumentMessage.php`

```bash
cat > src/Message/ExportDocumentMessage.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Message;

final readonly class ExportDocumentMessage
{
    public function __construct(
        public string $documentId,
        public string $format = 'pdf'
    ) {}
}
EOF
```

### Fichier à créer: `src/MessageHandler/GenerateDocumentHandler.php`

```bash
cat > src/MessageHandler/GenerateDocumentHandler.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\GenerateDocumentMessage;
use App\Repository\DocumentRepository;
use App\Service\Document\DocumentGenerator;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GenerateDocumentHandler
{
    public function __construct(
        private readonly DocumentRepository $documentRepository,
        private readonly DocumentGenerator $documentGenerator,
        private readonly LoggerInterface $logger
    ) {}

    public function __invoke(GenerateDocumentMessage $message): void
    {
        $this->logger->info('Processing GenerateDocumentMessage', [
            'document_id' => $message->documentId,
            'regenerate' => $message->regenerate,
        ]);

        $documentId = Uuid::fromString($message->documentId);
        $document = $this->documentRepository->findById($documentId);

        if ($document === null) {
            $this->logger->warning('Document not found', [
                'document_id' => $message->documentId,
            ]);
            return;
        }

        try {
            $this->documentGenerator->generate($document, $message->regenerate);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to generate document', [
                'document_id' => $message->documentId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
EOF
```

### Validation
```bash
php bin/console lint:container
php bin/console debug:messenger
# Doit lister GenerateDocumentMessage avec le transport async
```

---

## TASK 3.7: Créer les State Providers et Processors

### Objectif
Créer les State Providers et Processors pour API Platform

### Fichier à créer: `src/State/Provider/UserDocumentsProvider.php`

```bash
mkdir -p src/State/Provider
mkdir -p src/State/Processor

cat > src/State/Provider/UserDocumentsProvider.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use App\Repository\DocumentRepository;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProviderInterface<iterable>
 */
final class UserDocumentsProvider implements ProviderInterface
{
    public function __construct(
        private readonly DocumentRepository $documentRepository,
        private readonly Security $security,
        private readonly Pagination $pagination
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        /** @var User|null $user */
        $user = $this->security->getUser();
        
        if ($user === null) {
            return [];
        }

        $queryBuilder = $this->documentRepository->createQueryBuilderForUser($user);

        return $queryBuilder->getQuery()->getResult();
    }
}
EOF
```

### Fichier à créer: `src/State/Processor/DocumentPersistProcessor.php`

```bash
cat > src/State/Processor/DocumentPersistProcessor.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Document;
use App\Entity\User;
use App\Repository\DocumentRepository;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProcessorInterface<Document, Document>
 */
final class DocumentPersistProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly DocumentRepository $documentRepository,
        private readonly Security $security
    ) {}

    /**
     * @param Document $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Document
    {
        /** @var User $user */
        $user = $this->security->getUser();

        // Si c'est une création, assigner l'utilisateur
        if (!isset($context['previous_data'])) {
            $data->setUser($user);
        }

        $this->documentRepository->save($data);

        return $data;
    }
}
EOF
```

### Fichier à créer: `src/State/Processor/GenerateDocumentProcessor.php`

```bash
cat > src/State/Processor/GenerateDocumentProcessor.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\GenerateDocumentInput;
use App\Entity\Document;
use App\Enum\DocumentStatus;
use App\Exception\QuotaExceededException;
use App\Message\GenerateDocumentMessage;
use App\Repository\DocumentRepository;
use App\Service\Document\QuotaManager;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProcessorInterface<GenerateDocumentInput, Document>
 */
final class GenerateDocumentProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly DocumentRepository $documentRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly QuotaManager $quotaManager
    ) {}

    /**
     * @param GenerateDocumentInput $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Document
    {
        /** @var Document $document */
        $document = $context['previous_data'];
        $user = $document->getUser();

        // Vérifier le quota
        if (!$this->quotaManager->canGenerate($user)) {
            throw new QuotaExceededException(
                sprintf(
                    'Vous avez atteint votre limite mensuelle de %d documents',
                    $user->getMonthlyLimit()
                )
            );
        }

        // Mettre à jour le statut
        $document->setStatus(DocumentStatus::GENERATING);
        $this->documentRepository->save($document);

        // Dispatcher le message pour traitement asynchrone
        $this->messageBus->dispatch(new GenerateDocumentMessage(
            documentId: $document->getId()->toString(),
            regenerate: $data->regenerate
        ));

        return $document;
    }
}
EOF
```

### Validation
```bash
php bin/console lint:container
php bin/console debug:container DocumentPersistProcessor
# Doit afficher les informations du service
```

---

## TASK 3.8: Créer le Security Voter

### Objectif
Créer le voter pour les autorisations sur les documents

### Fichier à créer: `src/Security/Voter/DocumentVoter.php`

```bash
mkdir -p src/Security/Voter

cat > src/Security/Voter/DocumentVoter.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Document;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Document>
 */
class DocumentVoter extends Voter
{
    public const VIEW = 'DOCUMENT_VIEW';
    public const EDIT = 'DOCUMENT_EDIT';
    public const DELETE = 'DOCUMENT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE], true)
            && $subject instanceof Document;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Document $document */
        $document = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($document, $user),
            self::EDIT => $this->canEdit($document, $user),
            self::DELETE => $this->canDelete($document, $user),
            default => false,
        };
    }

    private function canView(Document $document, User $user): bool
    {
        return $this->isOwner($document, $user);
    }

    private function canEdit(Document $document, User $user): bool
    {
        return $this->isOwner($document, $user) && $document->isEditable();
    }

    private function canDelete(Document $document, User $user): bool
    {
        return $this->isOwner($document, $user);
    }

    private function isOwner(Document $document, User $user): bool
    {
        return $document->getUser()->getId()->equals($user->getId());
    }
}
EOF
```

### Validation
```bash
php bin/console lint:container
php bin/console debug:container DocumentVoter
```

---

# PHASE 4: CONTROLLERS ET ROUTES

## TASK 4.1: Créer le Controller d'Authentification

### Objectif
Créer les endpoints d'authentification

### Fichier à créer: `src/Controller/AuthController.php`

```bash
mkdir -p src/Controller

cat > src/Controller/AuthController.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\RegisterUserInput;
use App\Entity\User;
use App\Repository\CountryRepository;
use App\Repository\UserRepository;
use App\Service\Document\QuotaManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
        private readonly CountryRepository $countryRepository,
        private readonly UserRepository $userRepository
    ) {}

    #[Route('/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            /** @var RegisterUserInput $input */
            $input = $this->serializer->deserialize(
                $request->getContent(),
                RegisterUserInput::class,
                'json'
            );
        } catch (\Throwable $e) {
            return $this->json([
                'message' => 'Données invalides',
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        // Valider l'input
        $errors = $this->validator->validate($input);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json([
                'message' => 'Validation failed',
                'errors' => $errorMessages,
            ], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier si l'email existe déjà
        if ($this->userRepository->findByEmail($input->email) !== null) {
            return $this->json([
                'message' => 'Cet email est déjà utilisé',
            ], Response::HTTP_CONFLICT);
        }

        // Récupérer le pays
        $country = $this->countryRepository->find($input->countryId);
        if ($country === null) {
            return $this->json([
                'message' => 'Pays invalide',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Créer l'utilisateur
        $user = new User();
        $user->setEmail($input->email);
        $user->setName($input->name);
        $user->setPhone($input->phone);
        $user->setCountry($country);
        $user->setPassword($this->passwordHasher->hashPassword($user, $input->password));

        $this->em->persist($user);
        $this->em->flush();

        return $this->json([
            'message' => 'Compte créé avec succès',
            'user' => [
                'id' => $user->getId()->toString(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
            ],
        ], Response::HTTP_CREATED);
    }

    #[Route('/me', name: 'api_auth_me', methods: ['GET'])]
    public function me(QuotaManager $quotaManager): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if ($user === null) {
            return $this->json([
                'message' => 'Non authentifié',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $quota = $quotaManager->getCurrentQuota($user);

        return $this->json([
            'id' => $user->getId()->toString(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'phone' => $user->getPhone(),
            'country' => [
                'id' => $user->getCountry()->getId(),
                'name' => $user->getCountry()->getName(),
                'code' => $user->getCountry()->getCodeIso(),
            ],
            'subscription' => [
                'type' => $user->getSubscriptionType()->value,
                'label' => $user->getSubscriptionType()->label(),
                'endsAt' => $user->getSubscriptionEndsAt()?->format('c'),
            ],
            'quota' => [
                'used' => $quota->getDocumentsGenerated(),
                'limit' => $quota->getDocumentsLimit(),
                'remaining' => $quota->getRemainingDocuments(),
                'period' => $quota->getPeriod(),
            ],
            'createdAt' => $user->getCreatedAt()->format('c'),
        ]);
    }
}
EOF
```

### Fichier à créer: Configuration des routes

```bash
cat > config/routes/auth.yaml << 'EOF'
api_auth_login:
    path: /api/auth/login
    methods: [POST]
EOF
```

### Validation
```bash
php bin/console debug:router | grep auth
# Doit afficher les routes d'authentification
```

---

## TASK 4.2: Créer le Controller d'Export

### Objectif
Créer les endpoints d'export PDF et DOCX

### Fichier à créer: `src/Controller/DocumentExportController.php`

```bash
cat > src/Controller/DocumentExportController.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Document;
use App\Entity\User;
use App\Service\Export\DocxExporter;
use App\Service\Export\PdfExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/documents/{id}/export')]
class DocumentExportController extends AbstractController
{
    #[Route('/pdf', name: 'api_document_export_pdf', methods: ['GET'])]
    #[IsGranted('DOCUMENT_VIEW', subject: 'document')]
    public function pdf(Document $document, PdfExporter $exporter): Response
    {
        if (!$document->isCompleted()) {
            return $this->json([
                'message' => 'Le document n\'a pas encore été généré',
            ], Response::HTTP_BAD_REQUEST);
        }

        $content = $exporter->generate($document);
        $filename = $this->sanitizeFilename($document->getTitle()) . '.pdf';

        return new Response($content, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            'Content-Length' => strlen($content),
        ]);
    }

    #[Route('/docx', name: 'api_document_export_docx', methods: ['GET'])]
    #[IsGranted('DOCUMENT_VIEW', subject: 'document')]
    public function docx(Document $document, DocxExporter $exporter): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->canExportDocx()) {
            return $this->json([
                'message' => 'L\'export DOCX nécessite un abonnement premium',
            ], Response::HTTP_FORBIDDEN);
        }

        if (!$document->isCompleted()) {
            return $this->json([
                'message' => 'Le document n\'a pas encore été généré',
            ], Response::HTTP_BAD_REQUEST);
        }

        $content = $exporter->generate($document);
        $filename = $this->sanitizeFilename($document->getTitle()) . '.docx';

        return new Response($content, Response::HTTP_OK, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            'Content-Length' => strlen($content),
        ]);
    }

    private function sanitizeFilename(string $filename): string
    {
        // Supprimer les caractères non autorisés
        $filename = preg_replace('/[^a-zA-Z0-9àâäéèêëïîôùûüç\-_\s]/u', '', $filename);
        // Remplacer les espaces par des underscores
        $filename = preg_replace('/\s+/', '_', $filename);
        // Limiter la longueur
        return substr($filename, 0, 100);
    }
}
EOF
```

### Validation
```bash
php bin/console debug:router | grep export
# Doit afficher les routes d'export
```

---

## TASK 4.3: Créer les Services d'Export

### Objectif
Créer les services pour générer les fichiers PDF et DOCX

### Fichier à créer: `src/Service/Export/PdfExporter.php`

```bash
mkdir -p src/Service/Export
mkdir -p templates/document

cat > src/Service/Export/PdfExporter.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Service\Export;

use App\Entity\Document;
use Knp\Snappy\Pdf;
use Twig\Environment;

final class PdfExporter
{
    public function __construct(
        private readonly Pdf $pdf,
        private readonly Environment $twig
    ) {}

    public function generate(Document $document): string
    {
        $html = $this->twig->render('document/pdf.html.twig', [
            'document' => $document,
            'content' => $document->getGeneratedContent(),
            'country' => $document->getTemplate()->getCountry(),
            'documentType' => $document->getTemplate()->getDocumentType(),
        ]);

        return $this->pdf->getOutputFromHtml($html, [
            'page-size' => 'A4',
            'margin-top' => '20mm',
            'margin-bottom' => '20mm',
            'margin-left' => '20mm',
            'margin-right' => '20mm',
            'encoding' => 'UTF-8',
        ]);
    }
}
EOF
```

### Fichier à créer: `src/Service/Export/DocxExporter.php`

```bash
cat > src/Service/Export/DocxExporter.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Service\Export;

use App\Entity\Document;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;

final class DocxExporter
{
    public function __construct()
    {
        Settings::setOutputEscapingEnabled(true);
    }

    public function generate(Document $document): string
    {
        $phpWord = new PhpWord();

        // Définir les styles
        $phpWord->addFontStyle('titleStyle', [
            'bold' => true,
            'size' => 14,
            'name' => 'Arial',
        ]);
        
        $phpWord->addFontStyle('bodyStyle', [
            'size' => 12,
            'name' => 'Arial',
        ]);

        $phpWord->addParagraphStyle('bodyParagraph', [
            'lineHeight' => 1.5,
            'spaceBefore' => 0,
            'spaceAfter' => 200,
        ]);

        // Créer la section principale
        $section = $phpWord->addSection([
            'marginTop' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
            'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
            'marginLeft' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
            'marginRight' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
        ]);

        // Ajouter le titre
        $section->addText($document->getTitle(), 'titleStyle');
        $section->addTextBreak();

        // Ajouter le contenu
        $content = $document->getGeneratedContent();
        $paragraphs = explode("\n\n", $content);

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if ($paragraph !== '') {
                $lines = explode("\n", $paragraph);
                $textRun = $section->addTextRun('bodyParagraph');
                foreach ($lines as $i => $line) {
                    $textRun->addText($line, 'bodyStyle');
                    if ($i < count($lines) - 1) {
                        $textRun->addTextBreak();
                    }
                }
            }
        }

        // Générer le fichier en mémoire
        $tempFile = tempnam(sys_get_temp_dir(), 'docx_');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        $content = file_get_contents($tempFile);
        unlink($tempFile);

        return $content;
    }
}
EOF
```

### Fichier à créer: `templates/document/pdf.html.twig`

```bash
cat > templates/document/pdf.html.twig << 'EOF'
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ document.title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
        }
        .header {
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }
        .header h1 {
            font-size: 16pt;
            color: #1a5276;
            margin-bottom: 5px;
        }
        .header .meta {
            font-size: 10pt;
            color: #666;
        }
        .content {
            white-space: pre-wrap;
            text-align: justify;
        }
        .content p {
            margin-bottom: 12pt;
        }
        .footer {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ document.title }}</h1>
        <div class="meta">
            {{ documentType.name }} - {{ country.name }}
            <br>
            Généré le {{ document.generatedAt|date('d/m/Y à H:i') }}
        </div>
    </div>
    
    <div class="content">
        {{ content|nl2br }}
    </div>
    
    <div class="footer">
        Document généré par DocGen Africa
    </div>
</body>
</html>
EOF
```

### Configuration Snappy

```bash
cat > config/packages/knp_snappy.yaml << 'EOF'
knp_snappy:
    pdf:
        enabled: true
        binary: /usr/bin/wkhtmltopdf
        options:
            page-size: A4
            encoding: UTF-8
            margin-top: 20mm
            margin-bottom: 20mm
            margin-left: 20mm
            margin-right: 20mm
EOF
```

### Validation
```bash
php bin/console lint:container
php bin/console lint:twig templates/
```

---

# PHASE 5: FIXTURES ET DONNÉES DE TEST

## TASK 5.1: Créer les DataFixtures

### Objectif
Créer les données de test pour le développement

### Fichier à créer: `src/DataFixtures/CountryFixtures.php`

```bash
mkdir -p src/DataFixtures

cat > src/DataFixtures/CountryFixtures.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    public const SENEGAL_REFERENCE = 'country-senegal';
    public const COTE_IVOIRE_REFERENCE = 'country-cote-ivoire';
    public const CAMEROUN_REFERENCE = 'country-cameroun';

    public function load(ObjectManager $manager): void
    {
        $countries = [
            [
                'name' => 'Sénégal',
                'codeIso' => 'SN',
                'language' => 'fr',
                'dateFormat' => 'd/m/Y',
                'currency' => 'XOF',
                'reference' => self::SENEGAL_REFERENCE,
            ],
            [
                'name' => 'Côte d\'Ivoire',
                'codeIso' => 'CI',
                'language' => 'fr',
                'dateFormat' => 'd/m/Y',
                'currency' => 'XOF',
                'reference' => self::COTE_IVOIRE_REFERENCE,
            ],
            [
                'name' => 'Cameroun',
                'codeIso' => 'CM',
                'language' => 'fr',
                'dateFormat' => 'd/m/Y',
                'currency' => 'XAF',
                'reference' => self::CAMEROUN_REFERENCE,
            ],
        ];

        foreach ($countries as $data) {
            $country = new Country();
            $country->setName($data['name']);
            $country->setCodeIso($data['codeIso']);
            $country->setLanguage($data['language']);
            $country->setDateFormat($data['dateFormat']);
            $country->setCurrency($data['currency']);
            $country->setIsActive(true);

            $manager->persist($country);
            $this->addReference($data['reference'], $country);
        }

        $manager->flush();
    }
}
EOF
```

### Fichier à créer: `src/DataFixtures/DocumentTypeFixtures.php`

```bash
cat > src/DataFixtures/DocumentTypeFixtures.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\DocumentType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DocumentTypeFixtures extends Fixture
{
    public const DEMANDE_EMPLOI = 'doctype-demande-emploi';
    public const LETTRE_MOTIVATION = 'doctype-lettre-motivation';
    public const DEMANDE_STAGE = 'doctype-demande-stage';
    public const ATTESTATION_HONNEUR = 'doctype-attestation-honneur';
    public const LETTRE_PLAINTE = 'doctype-lettre-plainte';

    public function load(ObjectManager $manager): void
    {
        $documentTypes = [
            [
                'slug' => 'demande-emploi',
                'name' => 'Demande d\'emploi',
                'description' => 'Lettre pour postuler à une offre d\'emploi',
                'category' => 'emploi',
                'icon' => 'briefcase',
                'sortOrder' => 1,
                'reference' => self::DEMANDE_EMPLOI,
            ],
            [
                'slug' => 'lettre-motivation',
                'name' => 'Lettre de motivation',
                'description' => 'Lettre accompagnant un CV pour une candidature',
                'category' => 'emploi',
                'icon' => 'file-text',
                'sortOrder' => 2,
                'reference' => self::LETTRE_MOTIVATION,
            ],
            [
                'slug' => 'demande-stage',
                'name' => 'Demande de stage',
                'description' => 'Lettre pour demander un stage en entreprise',
                'category' => 'emploi',
                'icon' => 'graduation-cap',
                'sortOrder' => 3,
                'reference' => self::DEMANDE_STAGE,
            ],
            [
                'slug' => 'attestation-honneur',
                'name' => 'Attestation sur l\'honneur',
                'description' => 'Déclaration officielle sur l\'honneur',
                'category' => 'administratif',
                'icon' => 'shield-check',
                'sortOrder' => 4,
                'reference' => self::ATTESTATION_HONNEUR,
            ],
            [
                'slug' => 'lettre-plainte',
                'name' => 'Lettre de plainte',
                'description' => 'Réclamation officielle auprès d\'une administration',
                'category' => 'administratif',
                'icon' => 'alert-triangle',
                'sortOrder' => 5,
                'reference' => self::LETTRE_PLAINTE,
            ],
        ];

        foreach ($documentTypes as $data) {
            $docType = new DocumentType();
            $docType->setSlug($data['slug']);
            $docType->setName($data['name']);
            $docType->setDescription($data['description']);
            $docType->setCategory($data['category']);
            $docType->setIcon($data['icon']);
            $docType->setSortOrder($data['sortOrder']);
            $docType->setIsActive(true);

            $manager->persist($docType);
            $this->addReference($data['reference'], $docType);
        }

        $manager->flush();
    }
}
EOF
```

### Fichier à créer: `src/DataFixtures/TemplateFixtures.php`

```bash
cat > src/DataFixtures/TemplateFixtures.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Country;
use App\Entity\DocumentType;
use App\Entity\Template;
use App\Enum\ToneLevel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TemplateFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var Country $senegal */
        $senegal = $this->getReference(CountryFixtures::SENEGAL_REFERENCE, Country::class);
        
        /** @var DocumentType $demandeStage */
        $demandeStage = $this->getReference(DocumentTypeFixtures::DEMANDE_STAGE, DocumentType::class);

        // Template: Demande de stage - Sénégal - Formel
        $template = new Template();
        $template->setCountry($senegal);
        $template->setDocumentType($demandeStage);
        $template->setToneLevel(ToneLevel::FORMAL);
        $template->setVersion(1);
        $template->setSystemPrompt($this->getSystemPrompt('SN'));
        $template->setUserPromptTemplate($this->getUserPromptTemplate());
        $template->setFieldsSchema($this->getFieldsSchema());
        $template->setIsActive(true);

        $manager->persist($template);
        $manager->flush();
    }

    private function getSystemPrompt(string $countryCode): string
    {
        return <<<PROMPT
Tu es un assistant de rédaction administrative pour le {$countryCode}.
Tu génères UNIQUEMENT du contenu formel et professionnel en français.
Tu ne donnes JAMAIS de conseils juridiques.
Tu respectes les conventions administratives locales.

Règles de rédaction:
- Utilise un ton formel et respectueux
- Inclus les formules de politesse appropriées
- Structure le document de manière claire
- Adapte le style au contexte administratif africain francophone

Format de sortie:
- Lieu et date en haut à droite
- Coordonnées de l'expéditeur
- Coordonnées du destinataire
- Objet en gras
- Corps de la lettre
- Formule de politesse
- Signature
PROMPT;
    }

    private function getUserPromptTemplate(): string
    {
        return <<<PROMPT
Génère une demande de stage avec les informations suivantes:

EXPÉDITEUR:
- Nom complet: {sender_name}
- Adresse: {sender_address}
- Téléphone: {sender_phone}
- Email: {sender_email}

DESTINATAIRE:
- Nom/Titre: {recipient_name}
- Entreprise: {recipient_company}
- Adresse: {recipient_address}

DÉTAILS DU STAGE:
- Objet: {subject}
- Durée souhaitée: {duration}
- Période: {period}
- Domaine: {field}

CONTEXTE:
{context}

MOTIVATION:
{motivation}

Génère une lettre de demande de stage professionnelle et convaincante.
PROMPT;
    }

    private function getFieldsSchema(): array
    {
        return [
            'sender_name' => [
                'type' => 'string',
                'label' => 'Votre nom complet',
                'required' => true,
                'placeholder' => 'Aminata Diallo',
            ],
            'sender_address' => [
                'type' => 'string',
                'label' => 'Votre adresse',
                'required' => true,
                'placeholder' => 'Dakar, Sénégal',
            ],
            'sender_phone' => [
                'type' => 'string',
                'label' => 'Votre téléphone',
                'required' => true,
                'placeholder' => '+221 77 123 45 67',
            ],
            'sender_email' => [
                'type' => 'email',
                'label' => 'Votre email',
                'required' => true,
                'placeholder' => 'aminata.diallo@email.com',
            ],
            'recipient_name' => [
                'type' => 'string',
                'label' => 'Nom du destinataire',
                'required' => true,
                'placeholder' => 'Monsieur le Directeur des Ressources Humaines',
            ],
            'recipient_company' => [
                'type' => 'string',
                'label' => 'Entreprise',
                'required' => true,
                'placeholder' => 'Société ABC',
            ],
            'recipient_address' => [
                'type' => 'string',
                'label' => 'Adresse de l\'entreprise',
                'required' => true,
                'placeholder' => 'Dakar, Sénégal',
            ],
            'subject' => [
                'type' => 'string',
                'label' => 'Objet de la demande',
                'required' => true,
                'placeholder' => 'Demande de stage en marketing digital',
            ],
            'duration' => [
                'type' => 'string',
                'label' => 'Durée du stage',
                'required' => true,
                'placeholder' => '3 mois',
            ],
            'period' => [
                'type' => 'string',
                'label' => 'Période souhaitée',
                'required' => true,
                'placeholder' => 'Juillet à Septembre 2026',
            ],
            'field' => [
                'type' => 'string',
                'label' => 'Domaine',
                'required' => true,
                'placeholder' => 'Marketing digital',
            ],
            'context' => [
                'type' => 'text',
                'label' => 'Contexte (formation, situation)',
                'required' => true,
                'placeholder' => 'Étudiante en 3ème année de licence en Marketing...',
            ],
            'motivation' => [
                'type' => 'text',
                'label' => 'Motivation',
                'required' => true,
                'placeholder' => 'Je souhaite acquérir une expérience pratique...',
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            CountryFixtures::class,
            DocumentTypeFixtures::class,
        ];
    }
}
EOF
```

### Fichier à créer: `src/DataFixtures/UserFixtures.php`

```bash
cat > src/DataFixtures/UserFixtures.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Country;
use App\Entity\User;
use App\Enum\SubscriptionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_TEST = 'user-test';
    public const USER_PREMIUM = 'user-premium';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        /** @var Country $senegal */
        $senegal = $this->getReference(CountryFixtures::SENEGAL_REFERENCE, Country::class);

        // Utilisateur de test gratuit
        $user = new User();
        $user->setEmail('test@docgen.africa');
        $user->setName('Utilisateur Test');
        $user->setPhone('+221771234567');
        $user->setCountry($senegal);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
        $user->setSubscriptionType(SubscriptionType::FREE);

        $manager->persist($user);
        $this->addReference(self::USER_TEST, $user);

        // Utilisateur premium
        $premiumUser = new User();
        $premiumUser->setEmail('premium@docgen.africa');
        $premiumUser->setName('Utilisateur Premium');
        $premiumUser->setPhone('+221779876543');
        $premiumUser->setCountry($senegal);
        $premiumUser->setPassword($this->passwordHasher->hashPassword($premiumUser, 'password123'));
        $premiumUser->setSubscriptionType(SubscriptionType::PRO);
        $premiumUser->setSubscriptionEndsAt(new \DateTimeImmutable('+1 year'));

        $manager->persist($premiumUser);
        $this->addReference(self::USER_PREMIUM, $premiumUser);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CountryFixtures::class,
        ];
    }
}
EOF
```

### Charger les fixtures

```bash
# Charger les fixtures
php bin/console doctrine:fixtures:load --no-interaction
```

### Validation
```bash
php bin/console doctrine:query:sql "SELECT * FROM country"
php bin/console doctrine:query:sql "SELECT * FROM document_type"
php bin/console doctrine:query:sql "SELECT email, subscription_type FROM \"user\""
# Doit afficher les données de test
```

---

# PHASE 6: TESTS

## TASK 6.1: Configurer les tests

### Objectif
Configurer PHPUnit et l'environnement de test

### Fichier à créer: `phpunit.xml.dist`

```bash
cat > phpunit.xml.dist << 'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         colors="true"
         bootstrap="tests/bootstrap.php"
         cacheDirectory=".phpunit.cache"
>
    <php>
        <ini name="display_errors" value="1"/>
        <ini name="error_reporting" value="-1"/>
        <server name="APP_ENV" value="test" force="true"/>
        <server name="SHELL_VERBOSITY" value="-1"/>
        <server name="SYMFONY_PHPUNIT_REMOVE" value=""/>
        <server name="SYMFONY_PHPUNIT_VERSION" value="10.5"/>
    </php>

    <testsuites>
        <testsuite name="Project Test Suite">
            <directory>tests</directory>
        </testsuite>
    </testsuites>

    <coverage>
        <report>
            <html outputDirectory="coverage"/>
        </report>
    </coverage>

    <source>
        <include>
            <directory suffix=".php">src</directory>
        </include>
    </source>

    <extensions>
        <bootstrap class="DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension"/>
    </extensions>
</phpunit>
EOF
```

### Fichier à créer: `.env.test`

```bash
cat > .env.test << 'EOF'
KERNEL_CLASS='App\Kernel'
APP_SECRET='test_secret_$ecr3t'
SYMFONY_DEPRECATIONS_HELPER=999999
DATABASE_URL="postgresql://docgen:docgen_password@127.0.0.1:5432/docgen_test?charset=utf8"
ANTHROPIC_API_KEY=test_api_key
EOF
```

### Fichier à créer: `tests/bootstrap.php`

```bash
mkdir -p tests

cat > tests/bootstrap.php << 'EOF'
<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}
EOF
```

### Créer la base de données de test

```bash
php bin/console doctrine:database:create --env=test --if-not-exists
php bin/console doctrine:migrations:migrate --env=test --no-interaction
php bin/console doctrine:fixtures:load --env=test --no-interaction
```

### Validation
```bash
php bin/phpunit --version
```

---

## TASK 6.2: Créer les tests API

### Objectif
Créer les tests d'intégration pour l'API

### Fichier à créer: `tests/Api/AuthenticationTest.php`

```bash
mkdir -p tests/Api

cat > tests/Api/AuthenticationTest.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;

class AuthenticationTest extends ApiTestCase
{
    public function testLoginSuccess(): void
    {
        $client = static::createClient();

        $response = $client->request('POST', '/api/auth/login', [
            'json' => [
                'email' => 'test@docgen.africa',
                'password' => 'password123',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['token' => true]);
        
        $data = $response->toArray();
        $this->assertArrayHasKey('token', $data);
    }

    public function testLoginFailure(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/auth/login', [
            'json' => [
                'email' => 'test@docgen.africa',
                'password' => 'wrong_password',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testMeWithoutToken(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/auth/me');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testMeWithToken(): void
    {
        $client = static::createClient();

        // Login first
        $response = $client->request('POST', '/api/auth/login', [
            'json' => [
                'email' => 'test@docgen.africa',
                'password' => 'password123',
            ],
        ]);
        
        $token = $response->toArray()['token'];

        // Get profile
        $response = $client->request('GET', '/api/auth/me', [
            'auth_bearer' => $token,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'email' => 'test@docgen.africa',
        ]);
    }
}
EOF
```

### Fichier à créer: `tests/Api/DocumentTest.php`

```bash
cat > tests/Api/DocumentTest.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class DocumentTest extends ApiTestCase
{
    private string $token;

    protected function setUp(): void
    {
        $client = static::createClient();
        
        $response = $client->request('POST', '/api/auth/login', [
            'json' => [
                'email' => 'test@docgen.africa',
                'password' => 'password123',
            ],
        ]);
        
        $this->token = $response->toArray()['token'];
    }

    public function testGetDocumentsWithoutAuth(): void
    {
        $client = static::createClient();
        
        $client->request('GET', '/api/documents');
        
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetDocumentsWithAuth(): void
    {
        $client = static::createClient();
        
        $response = $client->request('GET', '/api/documents', [
            'auth_bearer' => $this->token,
        ]);
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testGetCountries(): void
    {
        $client = static::createClient();
        
        $response = $client->request('GET', '/api/countries');
        
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Country',
        ]);
    }

    public function testGetDocumentTypes(): void
    {
        $client = static::createClient();
        
        $response = $client->request('GET', '/api/document_types');
        
        $this->assertResponseIsSuccessful();
    }

    public function testGetTemplates(): void
    {
        $client = static::createClient();
        
        $response = $client->request('GET', '/api/templates');
        
        $this->assertResponseIsSuccessful();
    }
}
EOF
```

### Exécuter les tests

```bash
php bin/phpunit
```

### Validation
```bash
php bin/phpunit --testdox
# Doit afficher tous les tests passés
```

---

# PHASE 7: FINALISATION

## TASK 7.1: Vérification finale

### Objectif
Vérifier que tout fonctionne correctement

### Commandes de vérification

```bash
# Vérifier le container
php bin/console lint:container

# Vérifier les templates Twig
php bin/console lint:twig templates/

# Vérifier le schéma de base de données
php bin/console doctrine:schema:validate

# Vérifier les routes
php bin/console debug:router

# Vérifier les services
php bin/console debug:autowiring

# Lancer les tests
php bin/phpunit

# Vérifier la documentation API
php bin/console api:openapi:export --format=json > openapi.json
```

### Validation finale
```bash
# Démarrer le serveur
symfony serve

# Tester l'API (dans un autre terminal)
curl http://127.0.0.1:8000/api/docs

# Doit afficher la documentation Swagger
```

---

## TASK 7.2: Résumé des commandes de démarrage

### Pour démarrer le projet après installation

```bash
# 1. Base de données
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction

# 2. Démarrer le serveur
symfony serve -d

# 3. Démarrer le worker Messenger (dans un autre terminal)
php bin/console messenger:consume async -vv

# 4. Accéder à l'API
# Documentation: http://127.0.0.1:8000/api/docs
# API: http://127.0.0.1:8000/api
```

---

## RÉCAPITULATIF DES ENDPOINTS API

| Méthode | Endpoint | Description | Auth |
|---------|----------|-------------|------|
| POST | /api/auth/register | Inscription | Non |
| POST | /api/auth/login | Connexion | Non |
| GET | /api/auth/me | Profil utilisateur | Oui |
| GET | /api/countries | Liste des pays | Non |
| GET | /api/document_types | Types de documents | Non |
| GET | /api/templates | Templates disponibles | Non |
| GET | /api/templates/{id} | Détail template | Non |
| GET | /api/documents | Liste documents utilisateur | Oui |
| POST | /api/documents | Créer un document | Oui |
| GET | /api/documents/{id} | Détail document | Oui |
| PUT | /api/documents/{id} | Modifier document | Oui |
| DELETE | /api/documents/{id} | Supprimer document | Oui |
| POST | /api/documents/{id}/generate | Générer contenu | Oui |
| GET | /api/documents/{id}/export/pdf | Export PDF | Oui |
| GET | /api/documents/{id}/export/docx | Export DOCX | Oui (Premium) |

---

# FIN DU GUIDE D'EXÉCUTION
