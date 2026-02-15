# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

DocGen Africa is a full-stack document generation platform using AI (Claude API) to help users generate administrative documents. Built with Symfony 7.4 backend + React 19 SPA frontend.

## Commands

### Backend (PHP/Symfony)
```bash
composer install                                    # Install PHP dependencies
php bin/console cache:clear                         # Clear Symfony cache
php bin/console doctrine:migrations:migrate         # Run pending migrations
php bin/console doctrine:migrations:diff            # Generate migration from entity changes
php bin/console doctrine:fixtures:load              # Load test data fixtures
php bin/console messenger:consume async             # Start async message worker
```

### Frontend (JavaScript)
```bash
npm install          # Install JS dependencies
npm run dev          # Development build (single pass)
npm run watch        # Watch mode for development
npm run build        # Production build
npm run dev-server   # Webpack dev server with hot reload
```

### Testing
```bash
./vendor/bin/phpunit                  # Run all tests
./vendor/bin/phpunit tests/Api/       # Run API tests only
./vendor/bin/phpunit tests/Service/   # Run service tests only
./vendor/bin/phpstan analyse          # Static analysis
./vendor/bin/php-cs-fixer fix         # Fix code style
```

### Docker
```bash
docker-compose up -d    # Start PostgreSQL service
docker-compose down     # Stop services
```

## Architecture

### Backend (src/)

**Symfony 7.4 + API Platform 4.2 REST API**

- `src/Controller/` — HTTP controllers. `SpaController` is a catch-all (lowest priority) that returns the React SPA template for all non-API routes.
- `src/Entity/` — Doctrine ORM entities (UUID PKs). Core entities: `User`, `Document`, `Template`, `Country`, `DocumentType`, `UsageQuota`.
- `src/Service/` — Business logic. Key services:
  - `AI/ClaudeService.php` — Claude API HTTP calls for content generation
  - `AI/PromptBuilder.php` — Builds prompts from templates + user input
  - `DocumentContentGenerator.php` — Orchestrates AI + document assembly
  - `PdfGeneratorService.php` — KNP Snappy (wkhtmltopdf) PDF export
  - `QuotaService.php` — Enforces monthly document limits by subscription tier
- `src/State/` — API Platform state processors (e.g., `DocumentPostProcessor` for create workflow)
- `src/Message/` + `src/MessageHandler/` — Symfony Messenger async processing (PDF generation)
- `src/Enum/` — `SubscriptionType` (FREE/CITIZEN/PRO/ENTERPRISE), `DocumentStatus` (DRAFT/GENERATED/EXPORTED), `ToneLevel`

**API Endpoints** (config/routes.yaml + API Platform auto-generation):
- `POST /api/auth/login` — JWT login
- `POST /api/auth/register` — Registration
- `GET /api/me` — Current user profile
- `GET /api/documents/{id}/download` — Export PDF/DOCX
- API Platform generates CRUD at `/api/documents`, `/api/templates`, etc.

**Authentication**: JWT (LexikJWT) with refresh tokens. `config/packages/security.yaml` defines the `api` firewall (stateless JWT) and `main` firewall (form login for EasyAdmin).

### Frontend (assets/react/)

**React 19 SPA served from single Twig template** (`templates/spa.html.twig`)

- `assets/react/index.jsx` — React root, wraps app in: `QueryClientProvider`, `BrowserRouter`, `AuthProvider`, `NotificationProvider`
- `assets/react/App.jsx` — Route definitions with lazy-loaded pages and protected route wrappers
- `assets/react/pages/` — Page components (Home, Login, Register, Dashboard, Documents, Profile)
- `assets/react/contexts/` — `AuthContext` (user state + JWT management), `NotificationContext` (UI alerts)
- `assets/react/api/` — Axios client with JWT injection interceptor and automatic token refresh on 401
- `assets/react/hooks/` — Custom React hooks
- `assets/react/components/` — Reusable components (common/, layout/)

**State management**: React Context API for global state + TanStack React Query for server state/caching. Forms: React Hook Form + Zod validation.

### Key Configuration Files

| File | Purpose |
|------|---------|
| `config/services.yaml` | DI configuration, Claude API key injection |
| `config/packages/security.yaml` | JWT firewall, access control, roles |
| `config/packages/api_platform.yaml` | REST API defaults, pagination, formats |
| `.env` | `DATABASE_URL`, `JWT_SECRET_KEY`, `ANTHROPIC_API_KEY`, `CORS_ALLOW_ORIGIN` |
| `webpack.config.js` | Webpack Encore with React + Tailwind |
| `compose.yaml` | PostgreSQL 16 Docker service |

### Subscription Tiers & Quota

- `FREE`: 3 documents/month
- `CITIZEN`: 15 documents/month
- `PRO`/`ENTERPRISE`: unlimited

`QuotaService` enforces limits; `CheckQuota` is a custom Symfony Validator constraint used at the API level.

### Database

PostgreSQL 16. Entities use Ramsey UUID (v4) as primary keys. Doctrine migrations in `migrations/`. Test fixtures via Foundry (`src/DataFixtures/`, `src/Factory/`).
