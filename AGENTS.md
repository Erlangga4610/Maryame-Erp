# AGENTS.md — maryame-erp

This is a **fresh Laravel 13.x skeleton** (stock scaffold). No custom business logic yet.

## Quick commands

| Command | What it does |
|---|---|
| `composer test` | Runs `config:clear` then `php artisan test` (PHPUnit, SQLite in-memory) |
| `composer setup` | Full bootstrap: `composer install` → copy `.env` → `key:generate` → `migrate` → `npm install` → `npm run build` |
| `composer dev` | Concurrently runs `php artisan serve`, `queue:listen`, `pail` (logs), and `vite` dev server |
| `./vendor/bin/pint` | Laravel Pint (PSR-12 code style); no custom config file exists |
| `php artisan test --filter=ExampleTest` | Run a single test class |

## Key config (non-default)

- **DB**: PostgreSQL (`pgsql` driver, database `Mryame-Erp`, user `root`)
- **Session/Cache/Queue**: All default to `database` driver
- **`.npmrc`** sets `ignore-scripts=true` — npm postinstall scripts never run
- **PHP 8.3+**, **Laravel 13.8**, **Tailwind CSS v4** via Vite
- **Flux v2.14** UI library (`livewire/flux`) + **FluxPro** (`livewire/flux-pro`)
- **Livewire v4.3** (`livewire/livewire`)

## PHP quirks

- `ext-intl`, `pdo_sqlite`, `sqlite3`, `pdo_pgsql`, and `pgsql` not installed system-wide; loaded from manually extracted `.so` files in `/tmp/`
- Composer commands need `PHP_INI_SCAN_DIR="/etc/php/8.4/cli/conf.d:/tmp"` (already set in `~/.bashrc` and `~/.profile`)
- A new terminal session (or `source ~/.bashrc`) is needed before running `composer` / `artisan` commands

## Code style

- `./vendor/bin/pint` for linting (Laravel Pint, PSR-12). No custom config — uses Pint defaults.
- Run `pint` before committing; format-on-save is not configured.

## Testing quirks

- Tests run against SQLite `:memory:` (see `phpunit.xml`). No external DB needed.
- `composer test` clears config before running — never call `phpunit` directly without `config:clear` first.
- `TestCase.php` is empty (extends base Laravel test case).

## Architecture

- `routes/web.php`: single `/` route returning the welcome view
- `routes/console.php`: only the `inspire` command
- `app/Models/User.php`: default User with `HasFactory`, `Notifiable`, attributes on `Fillable`/`Hidden`
- `app/Http/Controllers/Controller.php`: empty base controller
- No auth scaffolding, no custom middleware, no service providers beyond `AppServiceProvider`
- Flux entrypoint: `resources/css/app.css` imports `flux.css`; Vite entry: `resources/js/app.js`
- Admin layout: `resources/views/layouts/admin.blade.php` (Flux sidebar + header)
- Composer name still `laravel/laravel` — rename if deploying
