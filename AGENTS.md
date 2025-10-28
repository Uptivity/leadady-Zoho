# Repository Guidelines

## Project Structure & Module Organization
- Controllers sit in `app/Http`, shared services in `app/Services`, and data scaffolding (migrations, factories, seeders) in `database/`; plan dedicated seeders such as `LeadsSeeder` and `DropdownOptionsSeeder`.
- Views, scripts, and styles reside in `resources/`, with Vite building to `public/`. Specs and hand-off briefs land in `from gemini/` until archived to `docs/`.
- Tests mirror production modules under `tests/Feature` and `tests/Unit`; group new suites by domain (e.g., `Leads`, `Contacts`).

## Build, Test, and Development Commands
- `composer install && cp .env.example .env && php artisan key:generate` bootstraps dependencies and configuration.
- `php artisan migrate --seed` hydrates schema and dev data (omit `--seed` outside local).
- `npm ci && npm run dev` compiles Vite assets; `npm run build` produces production bundles.
- `php artisan serve` runs the application locally; feature tests live under `php artisan test`.

## Coding Style & Naming Conventions
- Follow PSR-12 semantics: 4-space indentation, StudlyCase classes, camelCase methods, snake_case tables and columns.
- Blade components use snake_case filenames and slot syntax; keep UI logic minimal inside templates.
- Lint before commits with `./vendor/bin/pint` for PHP and `npm run lint` for JS; avoid mixing tabs and spaces.

## Security & Configuration Tips
- Never commit `.env`; configure `CRM_USERNAME` and `CRM_PASSWORD` in deployment secrets (see `.env.example`).
- Sensitive attributes in Eloquent models should live under `$hidden`; use helpers in `app/Support/Logging` to redact PII.
- Rotate API credentials each release and update `docs/security.md` with the change log.

## Implementation Sequence & References
- Specs sit in `from gemini/` and must be implemented in order: Authentication → Lead Filtering → Lead Display → Lead Tagging → Process Leads → Dropdown Admin → Contact Management.
- Authentication (`FEATURE_Authentication.md`) is complete: env-backed guard, session middleware alias in `bootstrap/app.php`, routes in `routes/web.php`, UI in `resources/views/auth/login.blade.php`, and coverage in `tests/Feature/Auth/EnvAuthTest.php`.
- Lead filtering + display + tagging (`FEATURE_Lead_Filtering.md`, `FEATURE_Lead_Display.md`, `FEATURE_Lead_Tagging.md`) are next: build an AJAX count dashboard, paginated results (`->paginate(100)`), and an endpoint that flips `leads.status` to `tagged`. Use `dashboard.blade.php` as the starting canvas.
- Processing + dropdown admin (`FEATURE_Process_Leads.md`, `FEATURE_Dropdown_Admin.md`) follow: create `crm_contacts` migration using `from gemini/database schema.txt`, implement the transactional processor, then scaffold `dropdown_options` CRUD with seeders.
- Contact management (`FEATURE_Contact_Management.md`) concludes the flow: list, edit, and activity logging for `crm_contacts` driven by dropdown options.
- A task-by-task continuation checklist is maintained in `TODO.md`; keep it updated as each milestone lands.
