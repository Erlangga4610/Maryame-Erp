# Data Model / ERD — Modul Content Calendar & Creative Ops

**Catatan:** entitas diturunkan langsung dari template kalender v3 + jobdesk CSP/SMS/GVD. Siap jadi acuan migration Laravel. Nama tabel saran dalam `snake_case` jamak.

---

## Diagram

```mermaid
erDiagram
    users ||--o{ contents : "PIC"
    roles ||--o{ users : has
    platforms ||--o{ contents : on
    products ||--o{ contents : highlights
    campaigns ||--o{ contents : references
    contents ||--|| briefs : has
    contents ||--o{ approvals : "goes through"
    contents ||--o| qc_checks : "QC (TikTok)"
    contents ||--o{ assets : has
    contents ||--o{ adjustment_logs : "changed by"
    qc_checks ||--o{ qc_criteria_results : details

    users {
        id pk
        string name
        string email
        fk role_id
    }
    roles {
        id pk
        string name "CSP, Copywriter, SMS, GVD, ContentCreator, ..."
        int rbac_tier "1=edit 2=comment 3=view"
    }
    platforms {
        id pk
        string name "TikTok maryame, IG Feed, Shopee, ..."
        string code "TKM, IGF, SHP, ..."
        bool originality_strict "true utk TT/IG Feed/IG Reels"
    }
    products {
        id pk
        string sku "SKU-NIAC-30ML"
        string name
    }
    campaigns {
        id pk
        string name "Payday Sale 5 Mei"
        string type "seasonal | launching | tactical"
        date start_date
        date end_date
    }
    contents {
        id pk
        string content_code uk "TKM-2026-05-001"
        fk platform_id
        date publish_date
        time publish_time
        int week_no
        string tema
        string tipe "edukasi|jualan|testimoni|trending|ugc|campaign"
        string format "video|carousel|photo|stories|listing|blog|broadcast"
        string tiktok_subtype "KK Interaktif|KK Soft Selling|Non-KK|null"
        text angle
        text key_message
        fk product_id
        bool has_claim
        fk campaign_id
        fk pic_copy_id
        fk pic_visual_id
        fk pic_video_id
        datetime deadline_brief
        datetime deadline_produksi
        string status "draft|in_production|ready_review|approved|scheduled|published"
        text originality_note
        string priority "rutin|campaign|spontan"
        text notes
    }
    briefs {
        id pk
        fk content_id uk
        text strategic_section "theme, angle, audience, tone (CSP)"
        text technical_section "aspect ratio, durasi, caption, hashtag (SMS)"
        text schedule_ownership
    }
    approvals {
        id pk
        fk content_id
        int step "1=Copywriter 2=CSP 3=SMS (+RnD/Legal)"
        fk approver_id
        string status "pending|approved|revision"
        text feedback
        datetime acted_at
        string version
    }
    qc_checks {
        id pk
        fk content_id uk
        string overall_status "pending|pass|fail|na"
        fk validator_id
        text notes
    }
    qc_criteria_results {
        id pk
        fk qc_check_id
        int criteria_no "1..6"
        string result "pass|fail|na"
        text note
    }
    assets {
        id pk
        fk content_id
        string type "brief|visual|video|final"
        string link_or_path
        string version
        string review_status
    }
    adjustment_logs {
        id pk
        fk content_id
        string jenis "minor|major|reactive"
        text detail
        text alasan
        fk request_by
        fk approve_by
        text impact
        string status
        datetime adjusted_at
    }
```

---

## Catatan implementasi Laravel

- **Status & QC** → gunakan PHP enum + state pattern (mis. package `spatie/laravel-model-states`) untuk kontrol transisi.
- **RBAC** → `spatie/laravel-permission`; map `roles.rbac_tier` ke policy per modul.
- **Kode konten** → generate via observer saat `creating`, kombinasi `platform.code + YYYY-MM + counter`.
- **Cross-platform originality** → 1 ide konten bisa punya banyak baris `contents` (satu per platform); kalau perlu, tambah `content_groups` untuk mengikat varian lintas-platform dari ide yang sama.
- **Klaim** → validasi: `has_claim = true` butuh approval RnD sebelum boleh `approved`.
- **Audit/version** → `spatie/laravel-activitylog` untuk version log otomatis.
