# AGENTS.md — maryame-erp

ERP Content Calendar untuk Maryamé — Laravel 13.15, Livewire 4.3, Flux Pro 2.14, Tailwind v4, PostgreSQL.

## Quick commands

| Command | What it does |
|---|---|
| `composer test` | `config:clear` → `php artisan test` (SQLite in-memory) |
| `composer dev` | Concurrent: `serve`, `queue:listen`, `pail` (logs), `vite` |
| `composer setup` | Fresh bootstrap: install → `.env` → key → migrate → `npm install` → `npm run build` |
| `./vendor/bin/pint` | Laravel Pint (no custom config, PSR-12 defaults) |
| `php artisan test --filter=ExampleTest` | Single test class |

## PHP env quirks

- `source ~/.bashrc` (or fresh terminal) needed before `composer` / `artisan` — sets `PHP_INI_SCAN_DIR="/etc/php/8.4/cli/conf.d:/tmp"`
- `ext-intl`, `pdo_sqlite`, `sqlite3`, `pdo_pgsql`, `pgsql` loaded from manually extracted `.so` files in `/tmp/`
- `.npmrc` has `ignore-scripts=true` — `npm run build` works, postinstall hooks never run
- PHP 8.4.22, not 8.3

## Architecture (domain-based)

```
app/Content/          Content Calendar, approval, QC TikTok
app/Auth/             Login Livewire component
app/Dashboard/        Dashboard stats/charts
app/MasterData/       Platforms, Products, Campaigns, Users CRUD
app/Enums/            Global enums (ApprovalStatus)
app/Models/           Platform, Product, Campaign, User (Spatie HasRoles)
```

Routes all point to Livewire components (no controllers):

| Route | Component |
|---|---|
| `/dashboard` | `Dashboard` |
| `/contents` | `ContentCalendar` (kanban/table/calendar) |
| `/approval-inbox` | `ApprovalInbox` |
| `/master-data/{platforms,products,campaigns,users}` | `Platforms`, `Products`, `Campaigns`, `Users` |
| `/login` | `Login` |

Layout: `resources/views/layouts/admin.blade.php` (Flux Pro pattern — header navbar + sidebar mobile + desktop navlist). Guest: `layouts/guest.blade.php`.

## Views structure

```
resources/views/content/
├── content-calendar.blade.php          # @include only
├── approval-inbox.blade.php
└── partials/
    ├── toolbar.blade.php               # view mode buttons + filters
    ├── kanban.blade.php                # kanban board + cards
    ├── table.blade.php                 # table view + pagination
    ├── calendar.blade.php              # unscheduled panel + calendar grid
    └── modals/
        ├── create-edit.blade.php       # form modal (5 tabs: Detail/Copy/Visual/Video/Asset)
        ├── approve.blade.php           # approval modal
        ├── tiktok-qc.blade.php         # QC TikTok checklist
        ├── version-history.blade.php   # version history (riwayat versi)
        ├── adjustment-log.blade.php    # per-field change log
        └── delete.blade.php            # delete confirmation
```

## Key packages

| Package | Version | Notes |
|---|---|---|
| `livewire/flux` + `flux-pro` | 2.14 | UI components; `wire:show` removed in v4 — use `x-show` |
| `php-flasher/flasher-notyf-laravel` | 2.6 | Toast library; call `flash()->success()` / `->error()` |
| `spatie/laravel-permission` | 8.0 | RBAC; roles have `rbac_tier` column (1=edit, 2=comment, 3=view) |
| `spatie/laravel-model-states` | 2.14 | State machine (installed but not actively used yet) |
| `sortablejs` (npm) | 1.15 | Drag-drop dependency (bundled via Vite, not used directly in views) |

## Flux Pro quirks

The following components do **not** exist in Flux 2.14 — use alternatives:
- `flux:table.header` / `flux:table.header.cell` / `flux:table.body` → use `<flux:table.columns>` + `<flux:table.rows>`
- `flux:subnav` → use `flux:navlist`
- `flux:dropdown.trigger` / `flux:dropdown.content` / `flux:dropdown.item` → use `flux:dropdown` + `flux:menu`
- `flux:dropdown.separator` → use `flux:menu.separator`
- `flux:icon.database` → use another icon

## Login / Auth

- Login: `admin@maryame.com` / `password123` (role: CSP)
- Per-role test users: `{role}@maryame.com` / `password123` (SMS, CW, GVD, CC, VG, ASM, RnD, Legal, MC_BM)
- Authenticated users auto-redirect from `/` and `/login` to `/dashboard`
- Logout: POST `/logout` (CSRF-protected form in profile dropdown)

## Content Calendar

- 3 view modes: **kanban** (default, `grid grid-cols-4`), **table** (paginated), **calendar** (Jira-like with unscheduled drag panel)
- Kanban columns: To Do → In Progress → In Review → Done (mapped from content statuses)
- Calendar drag-drop uses native HTML5 Drag & Drop API (not SortableJS)
- Content codes auto-generated: `{PLATFORM-CODE}-{YYYY}-{MM}-{NNN}` (immutable via Observer, uses `withTrashed()` + `lockForUpdate()` in transaction)

## Approval pipeline (5 tahap)

```
draft → submit → ready_review → CW approve → CSP approve → SMS approve → (RnD if claim) → (Legal if sensitive) → approved
                                    ↓ (revision)
                                in_production
```

- Approvals stored in `approvals` table (`content_id` + `stage` unique)
- Stages: `cw`, `csp`, `sms`, `rnd` (if claim), `legal` (if sensitive) — created sequentially
- Revision at any stage returns content to `in_production`
- Approval Inbox (`/approval-inbox`) shows pending items per role
- MC/BM does NOT approve content — they approve Major Adjustment (Alur 9)

## QC TikTok

- Available only for platform code `TKM` in status `in_production` / `ready_review` / `approved`
- 9-item boolean checklist; auto-status `passed` (all checked) or `need_revision`
- Stored in `tiktok_qc` table, one-per-content (updateOrCreate)
- Accessible via button on kanban card or table row

## Asset + Version (Riwayat Versi)

- **Asset tab** in create-edit modal: upload final asset (image/video) and thumbnail via `Livewire\WithFileUploads`
- Files stored in `storage/app/public/assets/` and `storage/app/public/thumbnails/`
- `final_asset_link` and `thumbnail_link` columns already existed in `contents` table but were unused
- On content edit, if tracked fields changed, version is incremented and a `ContentVersion` snapshot is created in `content_versions` table (JSON snapshot of all content fields)
- Version number shown on kanban card (`v{N}`) and table row — clickable to open Riwayat Versi modal
- `ContentVersion` model in `App\Content\Models\`
- Relation: `Content::versions()` hasMany

## Adjustment Log

- Per-field change tracking recorded in `adjustment_logs` table
- Created automatically when ContentCalendar `save()` detects field changes (compares old vs new values)
- Tracks all brief fields, priority, dates, PIC assignments, asset links
- Displayed in modal with before/after diff (red/green cards)
- `AdjustmentLog` model in `App\Content\Models\`
- Relation: `Content::adjustmentLogs()` hasMany

## Master Data CRUD + RBAC

- `HasMasterDataPermissions` trait: `canEdit()` = `rbac_tier === 1` (CSP, SMS)
- Tier 1: full CRUD (create/edit/delete buttons + server-side guard)
- Tier 2-3: view only (actions hidden)
- Users CRUD includes role assignment via Spatie `syncRoles()`
- Cannot delete own user account

## Content model enums (all under `App\Content\Enums\`)

| Enum | Values | Cast on Content? |
|---|---|---|
| `ContentStatus` | `draft`, `in_production`, `ready_review`, `approved`, `scheduled`, `published` | Yes |
| `ContentPriority` | `high`, `medium`, `low` | Yes |
| `ContentType` | `feed`, `reels`, `story`, `carousel` | Yes |
| `ContentFormat` | — (installed, values pending) | Yes |
| `TiktokSubtype` | `kk_interaktif`, `kk_soft_selling`, `non_kk` | Yes |

Status flow: `draft → in_production → ready_review → approved → scheduled → published`

## Flask toast (php-flasher)

- `flash()->success('msg')` — green notyf toast
- `flash()->error('msg')` — red notyf toast
- `flash()->warning('msg')` — amber notyf toast
- View: `<x-flasher />` in both admin + guest layouts
- Config: `config/flasher.php` (default adapter `notyf`, bottom-right, dismissible)

## Validation

- All form fields wrapped in `flux:field` / `flux:label` / `flux:error`
- `$validationAttributes` for readable field names (Platform, Tema, Priority, etc.)
- Validation reset on modal close via `closeModal()` + `updatedShowModal()` hook
- No HTML5 `required` — server-side only

## Testing

- `phpunit.xml` targets SQLite `:memory:` — no PostgreSQL needed
- Always run `config:clear` first (handled by `composer test`)
- Current test count: 5 (ExampleTest — 2 route tests; ContentVersionTest — model existence, relations)
- `TestCase.php` is empty (extends base Laravel)

## Super Admin

- Role `Super Admin` (rbac_tier=0) bypasses ALL role/permission gates
- `Auth::user()->isSuperAdmin()` helper on User model
- Can create/edit/delete any content, approve any stage, access all master data CRUD
- Assigned to `admin@maryame.com` alongside CSP role

## Login credentials (dev)

| Email | Password | Role |
|---|---|---|
| `admin@maryame.com` | `password123` | **Super Admin** (bypass all role gates) + CSP |
| `mc_bm@maryame.com` | `password123` | MC/BM (Tier 3 — view) |
| `legal@maryame.com` | `password123` | Legal (Tier 2 — comment) |
| `csp@maryame.com` | `password123` | CSP |
| `sms@maryame.com` | `password123` | SMS (Tier 1 — edit) |
