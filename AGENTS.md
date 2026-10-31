# Repository Guidelines

## Project Structure & Module Organization
- `src/` holds the Symfony 7.4 backend (controllers, services, entities).
- `assets/react/` contains the React 19 SPA sources, bundled via Webpack Encore.
- `templates/` stores Twig templates (SPA shell in `templates/spa.html.twig`).
- `config/` is Symfony configuration; `migrations/` is Doctrine migrations.
- `tests/` contains PHPUnit tests; `public/` is the web root.

## Build, Test, and Development Commands
- `composer install` installs PHP dependencies.
- `php bin/console cache:clear` clears Symfony cache.
- `php bin/console doctrine:migrations:migrate` applies pending migrations.
- `php bin/console messenger:consume async` runs async workers.
- `npm install` installs frontend dependencies.
- `npm run dev` builds frontend assets once; `npm run watch` rebuilds on change.
- `npm run dev-server` starts the Webpack dev server; `npm run build` creates a production build.
- `docker-compose up -d` starts PostgreSQL (see `compose.yaml`).

## Coding Style & Naming Conventions
- Indentation: 4 spaces, LF line endings (`.editorconfig`).
- PHP: follow Symfony conventions; format with `./vendor/bin/php-cs-fixer fix`.
- Static analysis: `./vendor/bin/phpstan analyse`.
- Naming: classes in `src/` use PascalCase; services and files use descriptive, domain-driven names.
- JS/React: components use PascalCase (e.g., `DocumentList.jsx`); hooks use `use*`.

## Testing Guidelines
- PHPUnit configuration in `phpunit.xml.dist`; tests live in `tests/`.
- Run all tests: `./vendor/bin/phpunit`.
- Run a suite: `./vendor/bin/phpunit tests/Api` or `tests/Service`.
- Favor integration tests for API Platform flows and service behavior.

## Commit & Pull Request Guidelines
- Commit messages follow Conventional Commits (examples: `feat(api): ...`, `fix(routing): ...`).
- PRs should describe behavior changes, list affected endpoints/pages, and note any new env vars.
- Add screenshots for UI changes (SPA) and link related issues when applicable.

## Security & Configuration Tips
- Environment config lives in `.env`, `.env.local`, `.env.test`; never commit real secrets.
- JWT, database, and Claude API keys must be set for local runs.
