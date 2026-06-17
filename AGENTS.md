# AGENTS.md — maryame-erp

ERP Content Calendar untuk Maryamé — Laravel 13, Livewire 4.3, Flux Pro 2.14, Tailwind v4, PostgreSQL.

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
app/Content/           Content Calendar, approval, QC TikTok, capacity
app/Content/Enums/     ContentStatus, ContentType, ContentPriority, ContentFormat, TiktokSubtype
app/Content/Models/    Content, Approval, TiktokQc, ContentVersion, AdjustmentLog, Brief, Adjustment
app/Livewire/          All Livewire components (no controllers)
app/MasterData/        Platforms, Products, Campaigns, Users CRUD
app/Enums/             Global enums (ApprovalStatus)
app/Models/            Platform, Product, Campaign, User (Spatie HasRoles)
```

All routes point to Livewire components (no controllers). Key routes:

| Route | Component |
|---|---|
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
| `/master-data/{platforms,products,campaigns,users}` | respective CRUD |

## Content model enums

| Enum | Values |
|---|---|
| `ContentStatus` | `draft`, `in_production`, `ready_review`, `approved`, `scheduled`, `published` |
| `ContentPriority` | `rutin`, `campaign`, `spontan` |
| `ContentType` | `edukasi`, `jualan`, `testimoni`, `trending`, `ugc`, `campaign` |
| `ContentFormat` | `video`, `carousel`, `photo`, `stories`, `listing`, `blog`, `broadcast` |
| `TiktokSubtype` | `kk_interaktif`, `kk_soft_selling`, `non_kk` |

Content codes: `{PLATFORM-CODE}-{YYYY}-{MM}-{NNN}` (immutable via Observer, `withTrashed()+lockForUpdate()` transaction).

## Approval pipeline (5 tahap)

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
- Stored in `tiktok_qc` table, one-per-content (`updateOrCreate`)

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

- `livewire/flux` + `flux-pro` 2.14 — UI components
- `php-flasher/flasher-notyf-laravel` 2.6 — `flash()->success()`/`->error()`/`->warning()`; view `<x-flasher />`
- `spatie/laravel-permission` 8.0 — RBAC; roles have `rbac_tier` column
- `spatie/laravel-model-states` 2.14 — installed, not actively used
- `sortablejs` 1.15 (npm) — Drag-drop dep (bundled via Vite, not used directly in views)

## Validation

- All fields in `flux:field` / `flux:label` / `flux:error`
- No HTML5 `required` — server-side only; `$validationAttributes` for readable names
- Validation reset on modal close via `closeModal()` + `updatedShowModal()` hook

## Testing

- `phpunit.xml` targets SQLite `:memory:` — PostgreSQL not required
- `composer test` runs `config:clear` first
- 2 test files (4 tests, 8 assertions): ExampleTest (route checks) + ContentVersionTest (model existence/relations)
- SQLite (`pdo_sqlite`) may be unavailable in this env — only route/model-existence tests work

## Views structure

```
resources/views/livewire/content/
├── content-calendar.blade.php          # @include only
├── approval-inbox.blade.php
├── my-tasks.blade.php
├── mix-tracker.blade.php
├── calendar-management.blade.php
└── partials/
    ├── toolbar.blade.php               # view mode + filters + Fast-Track
    ├── kanban.blade.php                # 4-column board (To Do / In Progress / In Review / Done)
    ├── table.blade.php                 # paginated table
    ├── calendar.blade.php              # unscheduled drag panel + calendar grid
    ├── capacity.blade.php              # capacity planning view
    └── modals/
        ├── create-edit.blade.php       # 4-tab form (detail/strategic/technical/asset)
        ├── approve.blade.php
        ├── tiktok-qc.blade.php
        ├── version-history.blade.php
        ├── adjustment-log.blade.php
        ├── delete.blade.php
        ├── schedule.blade.php
        ├── publish.blade.php
        ├── post-publish-checklist.blade.php
        ├── brief.blade.php
        └── _adjustment.blade.php       # Minor/Major/Reactive adjustment form

resources/views/livewire/approval-pipeline/
├── tiktok-qc-manager.blade.php
└── approval-workflow.blade.php

resources/views/livewire/asset-management/
└── asset-manager.blade.php

resources/views/livewire/publishing-reporting/
├── publishing-manager.blade.php
└── adjustment-manager.blade.php
```

## ContentCalendar traits

Component at `app/Livewire/Content/ContentCalendar.php` uses 4 traits:
- `WithApprovalPipeline` — QC + approve/revision + downgradeToNonKk
- `WithPublishingReporting` — schedule, publish, checklist, adjustment
- `WithAssetManagement` — version modal, file upload properties (needs `$existingFinalAsset`, `$existingThumbnail`, `$finalAsset`, `$thumbnail`)
- `WithCapacityPlanning` — 5-step capacity resolution, `confirmed_by`

Adjustment validation: reason only required when `$this->adjustmentType !== 'minor'`.

## Taste Skills (UI Quality)

Terinstal di `.agents/skills/` — koleksi *portable agent skills* untuk meningkatkan kualitas output UI/UX. Dari repo [taste-skill](https://github.com/Leonxlnx/taste-skill) (MIT).

| Skill | Install name | Fungsi |
|---|---|---|
| `taste-skill` | `design-taste-frontend` | Default — layout kuat, tipografi tajam, anti boilerplate. 3 dial: VARIANCE/MOTION/DENSITY |
| `gpt-tasteskill` | `gpt-taste` | Varian stricter untuk GPT/Codex |
| `image-to-code-skill` | `image-to-code` | Pipeline image → analisis → implementasi frontend |
| `redesign-skill` | `redesign-existing-projects` | Audit UI existing, lalu perbaiki layout/spacing/hierarchy |
| `soft-skill` | `high-end-visual-design` | UI premium kalem, kontras lembut, whitespace luas |
| `minimalist-skill` | `minimalist-ui` | Editorial UI (Notion/Linear vibes) |
| `brutalist-skill` | `industrial-brutalist-ui` | Swiss type, sharp contrast, experimental layout |
| `imagegen-frontend-web` | — | Generate gambar referensi website (hero, landing) |
| `imagegen-frontend-mobile` | — | Generate gambar referensi mobile flow |
| `brandkit` | — | Generate moodboard brand kit |
| `output-skill` | `full-output-enforcement` | Paksa agent output penuh, no placeholder |
| `stitch-skill` | `stitch-design-taste` | Google Stitch-compatible rules |

Cara pakai: mention skill name di prompt, misal "follow taste-skill: buat landing page dengan VARIANCE 7, MOTION 6, DENSITY 4".
