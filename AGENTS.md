# AGENTS.md — maryame-erp

ERP Content Calendar untuk Maryamé — Laravel 13, Livewire 4.3, Flux Pro 2.14, Tailwind v4, PostgreSQL (dev uses SQLite).

## Quick commands

| Command | What |
|---|---|
| `composer test` | `config:clear` → `php artisan test` (SQLite in-memory; PostgreSQL not needed) |
| `php artisan test --filter=ExampleTest` | Single test class |
| `./vendor/bin/pint` | Laravel Pint (PSR-12 defaults, no custom config) |
| `composer dev` | Concurrent: serve + queue:listen + pail (logs) + vite |
| `composer setup` | `composer install` → `.env` → key → migrate → `npm install` → `npm run build` |

## PHP env quirks

- `source ~/.bashrc` (or fresh terminal) needed before `composer`/`artisan` — sets `PHP_INI_SCAN_DIR="/etc/php/8.4/cli/conf.d:/tmp"`
- PHP 8.4.22 with `pdo_sqlite`, `pdo_pgsql`, `pgsql`, `intl` loaded from manually extracted `.so` in `/tmp/`
- `.npmrc` has `ignore-scripts=true` — `npm run build` works, postinstall hooks never run

## Architecture

```
app/Content/            Content domain: Enums/, Models/, Observers/, Services/ (ContentCodeGenerator)
app/Livewire/           All UI (no controllers except GcsProxyController)
  ├─ Auth/Login, Dashboard/, Content/, MasterData/, Production/, ApprovalPipeline/,
  │  AssetManagement/, PublishingReporting/, RoleGuide.php
  └─ Content/Traits/    WithApprovalPipeline, WithPublishingReporting, WithAssetManagement, WithCapacityPlanning
app/MasterData/         DOES NOT EXIST — CRUD lives in app/Livewire/MasterData/ (+ HasMasterDataPermissions trait)
app/Enums/              Global enums (ApprovalStatus, AdjustmentType)
app/Models/             Platform, Product, Campaign, User (Spatie HasRoles), UserCapacitySetting
app/Helpers/            StorageHelper (GCS upload/delete/url)
app/Http/Controllers/   Only GcsProxyController (streams private GCS files)
```

Key routes:

| Route | Component |
|---|---|
| `/` `/login` | auto-redirect `/dashboard` (if authed) or `Login` |
| `/dashboard` | `Dashboard` |
| `/contents` | `ContentCalendar` — kanban/table/calendar + Fast-Track |
| `/my-tasks` | `MyTasks` |
| `/calendar` | `CalendarManagement` |
| `/approval-inbox` | `ApprovalInbox` |
| `/mix-tracker` | `MixTracker` |
| `/production-schedule` | `ProductionSchedule` |
| `/capacity` | `Capacity` (standalone) |
| `/qc/{contentId}` | `TikTokQcManager` |
| `/approval/{contentId}/{stage}` | `ApprovalWorkflow` |
| `/publish/{contentId}` | `PublishingManager` |
| `/adjustment/{adjustmentId}` | `AdjustmentManager` |
| `/assets/{contentId}` | `AssetManager` |
| `/storage/gcs/{path}` | `GcsProxyController` (view file URLs, authed) |
| `/role-guide` | `RoleGuide` |
| `/master-data/{platforms,products,campaigns,users}` | respective CRUD |

## GCS storage (default disk)

- `FILESYSTEM_DISK=gcs`; disk `gcs` in `config/filesystems.php` (private, UBLA visibility). Local SQLite dev still works — GCS only touched when files are uploaded.
- Credentials: `GOOGLE_CLOUD_KEY_FILE` → `storage/app/gcs-credentials.json` (NOT committed; `.env` and credential file are gitignored).
- Use `App\Helpers\StorageHelper::upload()/delete()/url()` for file ops — **do not call `Storage::disk('gcs')->url()`** (returns `storage_api_uri` which is null); `StorageHelper::url()` returns `/storage/gcs/{path}` proxy route instead.
- **Avatar profil = pengecualian**: stored on local `public` disk (`storage/app/public/avatars/`, symlink `public/storage`), URL via `Storage::disk('public')->url()` in `User::avatarUrl()`. Do NOT use StorageHelper for avatars (GCS 401 in dev). This was a deliberate choice — keep it local.
- Livewire temp uploads use `LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK=local` (not gcs).

## Content model enums

| Enum | Values |
|---|---|
| `ContentStatus` | `draft`, `in_production`, `ready_review`, `approved`, `scheduled`, `published` |
| `ContentPriority` | `rutin`, `campaign`, `spontan` |
| `ContentType` | `edukasi`, `jualan`, `testimoni`, `trending`, `ugc`, `campaign` |
| `ContentFormat` | `video`, `carousel`, `photo`, `stories`, `listing`, `blog`, `broadcast` |
| `TiktokSubtype` | `kk_interaktif`, `kk_soft_selling`, `non_kk` |
| `CalendarEntryStatus` | in `app/Content/Enums/` |

`app/Enums/`: `ApprovalStatus` (`pending`/`approved`/`revision`), `AdjustmentType` (`minor`/`major`/`reactive`).

Content codes: `{PLATFORM-CODE}-{YYYY}-{MM}-{NNN}` — generated in `ContentObserver::creating`, immutable (blocked in `updating`), `ContentCodeGenerator` uses `withTrashed()+lockForUpdate()` transaction.

## Approval pipeline

```
draft → in_production → ready_review → CW approve → CSP approve → SMS approve → (RnD if claim) → (Legal if sensitive) → approved
                                          ↓ (revision → in_production)
```

- Stages created sequentially: `cw`, `csp`, `sms`, `rnd` (if claim), `legal` (if sensitive)
- MC/BM does NOT approve content — approves Major Adjustment only

## QC TikTok

- Available for platform code `TKM` in status `in_production`/`ready_review`/`approved`
- 6 criteria (k1–k6) with pass/fail/na + optional shopping cart checkbox
- Auto-suggested subtype: KK Interaktif (all pass + cart), KK Soft Selling (cart, not all pass), Non-KK (no cart)
- "Turunkan ke Non-KK" button available when revision infeasible
- Stored in `tiktok_qc` table, one-per-content (`updateOrCreate`); per-criteria rows in `qc_criteria_results`

## Master Data CRUD + RBAC

- `HasMasterDataPermissions` trait: `canEdit()` = Super Admin or `rbac_tier === 1`
- Roles: Super Admin (tier 0, bypasses all gates), CSP/SMS (tier 1, edit), others (tier 2–3, view/comment)
- Users CRUD uses Spatie `syncRoles()`; cannot delete own account

## Login (dev)

| Email | Role |
|---|---|
| `admin@maryame.com` | **Super Admin** + CSP |
| `{role}@maryame.com` | role-specific (SMS, CW, GVD, CC, VG, ASM, RnD, Legal, MC_BM) |

All passwords: `password123`. Auth auto-redirects `/` and `/login` → `/dashboard`.

## Flux Pro 2.14 quirks

These components do **not** exist — use alternatives:
- `flux:table.header` / `flux:table.body` → use `<flux:table.columns>` + `<flux:table.rows>`
- `flux:subnav` → use `flux:navlist`
- `flux:dropdown.trigger` / `flux:dropdown.content` / `flux:dropdown.item` → use `flux:dropdown` + `flux:menu`
- `flux:dropdown.separator` → use `flux:menu.separator`
- `flux:radio` → use native `<input type="radio">`
- `flux:icon.database` → use another icon
- `wire:show` removed in v4 — use `x-show` (current codebase uses `x-show`)

## Key packages

- `livewire/flux` + `flux-pro` 2.14 — UI components; **flux-pro is a private composer repo** (`https://composer.fluxui.dev` in `composer.json` `repositories`) — requires auth token if reinstalled
- `php-flasher/flasher-notyf-laravel` 2.6 — `flash()->success()`/`->error()`/`->warning()`; view `<x-flasher />`
- `spatie/laravel-permission` 8.0 — RBAC; roles have `rbac_tier` column
- `spatie/laravel-google-cloud-storage` — `gcs` disk
- `spatie/laravel-activitylog` 5.0 — audit logging
- `spatie/laravel-model-states` 2.14 — installed, not actively used
- `sortablejs` 1.15 (npm) — Drag-drop dep (bundled via Vite, not used directly in views)
- `laravel/pao` (dev) — asset optimization

## Validation

- All fields in `flux:field` / `flux:label` / `flux:error`
- No HTML5 `required` — server-side only; `$validationAttributes` for readable names
- Validation reset on modal close via `closeModal()` + `updatedShowModal()` hook

## Testing

- `phpunit.xml` targets SQLite `:memory:` — PostgreSQL not required
- `composer test` runs `config:clear` first
- 2 test files (4 tests, 8 assertions): ExampleTest (route checks) + ContentVersionTest (model existence/relations)
- SQLite (`pdo_sqlite`) may be unavailable in this env — only route/model-existence tests work

## ContentCalendar traits

Component at `app/Livewire/Content/ContentCalendar.php` uses 4 traits in `app/Livewire/Content/Traits/`:
- `WithApprovalPipeline` — QC + approve/revision + downgradeToNonKk
- `WithPublishingReporting` — schedule, publish, checklist, adjustment
- `WithAssetManagement` — version modal, file upload properties (needs `$existingFinalAsset`, `$existingThumbnail`, `$finalAsset`, `$thumbnail`)
- `WithCapacityPlanning` — 5-step capacity resolution, `confirmed_by`

Adjustment validation: reason only required when `$this->adjustmentType !== 'minor'`.

## Taste Skills (UI Quality)

Installed in `.agents/skills/` — portable agent skills from [taste-skill](https://github.com/Leonxlnx/taste-skill) (MIT). Mention the install name in a prompt to activate, e.g. "follow taste-skill: buat landing page dengan VARIANCE 7, MOTION 6, DENSITY 4".

| Skill | Install name | Use for |
|---|---|---|
| `taste-skill` | `design-taste-frontend` | Default — strong layout, sharp typography, anti-boilerplate |
| `gpt-tasteskill` | `gpt-taste` | Stricter variant for GPT/Codex |
| `image-to-code-skill` | `image-to-code` | Image → analysis → frontend implementation |
| `redesign-skill` | `redesign-existing-projects` | Audit + fix existing UI |
| `soft-skill` | `high-end-visual-design` | Premium calm UI, soft contrast, whitespace |
| `minimalist-skill` | `minimalist-ui` | Editorial UI (Notion/Linear vibes) |
| `brutalist-skill` | `industrial-brutalist-ui` | Swiss type, sharp contrast, experimental |
| `imagegen-frontend-web` / `-mobile` | — | Generate design reference images |
| `brandkit` | — | Moodboard brand kit images |
| `output-skill` | `full-output-enforcement` | Force full output, no placeholders |
| `stitch-skill` | `stitch-design-taste` | Google Stitch-compatible rules |
