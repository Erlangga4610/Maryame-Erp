# Alur Kerja — Modul Content Calendar & Creative Ops (ERP Maryamé)

**Versi:** v0.1 (draft untuk project ERP baru)
**Fungsi dokumen:** SOP alur kerja sekaligus spesifikasi fungsional. Tiap alur jadi acuan implementasi (state machine, rule otorisasi, validasi) di Laravel.
**Sumber:** jobdesk CSP, SMS, GVD + template ContentCalendar_Maryame v3.

---

## Legenda

**Role:** CSP = Creative Strategist & Planner · CW = Copywriter · SMS = Social Media Specialist · GVD = Graphic & Visual Designer · CC = Content Creator · VG = Videografer · ASM = Admin Sosial Media · MC = Manajer Commercial · BM = Brand Manager · RnD = Manajer RnD · Legal = Legal Officer.

**Status konten (state machine):** `Draft → In Production → Ready for Review → Approved → Scheduled → Published`.

**Aturan non-negotiable:** Cross-Platform Originality (TT/IG Feed/IG Reels terpisah) · Approval 3-pihak wajib sebelum `Approved` · Konten ber-klaim wajib approval RnD · Kode konten unik & immutable.

---

# A. PERENCANAAN

## Alur 1 — Penyusunan Kalender Konten Bulanan

**Tujuan:** menghasilkan kalender konten bulanan final per platform.
**Trigger:** H-7 sebelum bulan baru (jangka menengah H-14). PIC: CSP.

**Langkah:**
1. **Pengumpulan input** — CSP kumpulkan dari Annual Strategy, Quarterly Update, Campaign Calendar, dan input stakeholder (RnD = launching produk, Marketplace = big day, KOL Leader = campaign KOL, Live Commerce = jadwal live, tren/momentum).
2. **Draft kalender** — CSP buat draft per platform terpisah (Cross-Platform Originality). Tiap entry mengisi field inti (tema, tipe, format, angle, key message, produk, klaim, PIC, deadline).
3. **Konfirmasi kapasitas** — jalankan **Alur 2**. Wajib lolos sebelum lanjut.
4. **Internal review** — CSP + SMS review draft (di Monthly Strategic Sync atau meeting terjadwal).
5. **Approval** — MC + BM (saat onboard) approve.
6. **Distribusi** — kalender final dibagikan ke tim produksi (CW, GVD, CC, VG), SMS, ASM, dan stakeholder terkait.
7. **Arsip** — simpan versi final, retention 2 tahun.

**Status sistem:** entry baru lahir berstatus `Draft`. Kalender punya status dokumen sendiri: `Draft → In Review → Approved → Distributed → Archived`.

**Catatan implementasi:** kalender = entitas dengan banyak `contents`. Approval kalender ≠ approval konten (Alur 7). Versi kalender ter-log (version history). Auto-generate kode konten per platform saat entry dibuat.

---

## Alur 2 — Konfirmasi Kapasitas Produksi

**Tujuan:** memastikan kalender realistis terhadap kapasitas tim.
**Trigger:** Step 3 Alur 1, sebelum kalender difinalkan. PIC: CSP.

**Langkah:**
1. Hitung total entry & estimasi jam produksi per tim (CW, GVD, CC, VG).
2. Bandingkan dengan kapasitas tersedia tiap tim.
3. Jika **dalam kapasitas** → lanjut ke internal review.
4. Jika **over kapasitas** → jalankan 5-step berurutan sampai teratasi:
   - Step 1: Redistribusi beban internal.
   - Step 2: Prioritas ulang (campaign tetap jalan, konten rutin digeser).
   - Step 3: Sederhanakan format (mis. 3 platform → 2 platform).
   - Step 4: Freelancer/vendor (perlu approval MC + Finance).
   - Step 5: Eskalasi ke MC untuk tambah tim permanen.

**Catatan implementasi:** tabel `capacity` per tim per periode; field `status (auto)` = OK / Over dihitung dari (estimasi jam vs kapasitas). Tampilkan warning otomatis bila over.

---

## Alur 3 — Penyusunan Jadwal Produksi Mingguan

**Tujuan:** breakdown kalender bulanan jadi jadwal eksekusi mingguan.
**Trigger:** tiap Jumat sore (untuk minggu depan). PIC: CSP.

**Langkah:**
1. **Draft (Jumat sore)** — CSP breakdown kalender bulanan ke daftar item per hari (Senin–Minggu).
2. **Konfirmasi tim (Jumat sore)** — cek cepat blocker dengan tim produksi.
3. **Finalize & distribute (Senin pagi)** — briefkan jadwal final ke tim.
4. **Tracking (Senin–Jumat)** — update status di daily morning sync (15 menit).

**Catatan implementasi:** jadwal mingguan = view turunan dari `contents` (filter minggu), bukan tabel baru — cukup tampilan + field priority & blocker.

---

# B. PRODUKSI

## Alur 4 — Brief Konten

**Tujuan:** menghasilkan brief lengkap per konten sebelum produksi.
**Trigger:** konten masuk kalender; min 7 hari sebelum deadline produksi.

**Langkah:**
1. **Bagian Strategic (CSP)** — theme, angle, positioning, target audience, key message, tone, campaign ref.
2. **Bagian Spec Teknis (SMS)** — platform & aspect ratio, resolusi, durasi, format file, caption & hashtag, thumbnail, audio, instruksi originality.
3. **Bagian Schedule & Ownership** — deadline produksi, PIC (GVD/CC/VG), rencana publish.
4. Brief final → konten siap masuk produksi (status `In Production`).

**Catatan implementasi:** `briefs` relasi 1-1 ke `contents`. Bagian strategic editable oleh CSP, bagian teknis oleh SMS (field-level authorization).

---

## Alur 5 — Produksi Konten & Update Status

**Tujuan:** eksekusi produksi dan tracking progres real-time.
**Trigger:** brief final. PIC: CC/VG (video/foto), GVD (visual), CW (copy).

**Langkah:**
1. Konten `In Production` — tim produksi kerjakan sesuai brief.
2. PIC update status real-time; blocker diangkat di daily sync.
3. Hasil produksi diunggah/di-link sebagai asset → status `Ready for Review`.
4. Lanjut ke QC (Alur 6, khusus TikTok) dan/atau Approval (Alur 7).

**Catatan implementasi:** transisi `In Production → Ready for Review` hanya boleh oleh PIC item. Wajib ada minimal 1 asset ter-link sebelum boleh `Ready for Review`.

---

# C. QUALITY & APPROVAL

## Alur 6 — QC Konten TikTok (6 Kriteria Interaktif)

**Tujuan:** menentukan kelayakan & sub-tipe konten TikTok (mengacu aturan TikTok 19 Jan 2026).
**Trigger:** konten TikTok `Ready for Review`. PIC: SMS.

**6 kriteria** (detail penuh di tab QC_Detail template):
1. Voice/Audio original (ada narasi/voice-over; dilarang hanya musik trending tanpa narasi).
2. Demo penggunaan (tunjukkan cara pakai; dilarang cuma pegang & goyang produk).
3. Produk visible jelas (lighting bagus, produk tajam; dilarang buram/gelap).
4. Manfaat/benefit disebut verbal.
5–6. Kriteria tambahan sesuai QC_Detail.

**Langkah:**
1. SMS cek tiap kriteria → Pass / Fail / N/A.
2. Tentukan sub-tipe:
   - **KK Interaktif** — semua 6 kriteria pass + ada keranjang kuning.
   - **KK Soft Selling** — ada keranjang kuning, soft sell.
   - **Non-KK** — tanpa keranjang kuning.
3. Jika gagal kriteria & revisi tidak feasible → turunkan ke Non-KK (jalur Adjustment minor, lihat Alur 9).
4. Update Mix Tracker: jaga rasio KK vs Non-KK per minggu (warning bila mendekati limit KK non-interaktif/7 hari).

**Catatan implementasi:** `qc_checks` 1-1 ke konten TikTok + `qc_criteria_results` (6 baris). Sub-tipe dihitung dari hasil kriteria. Mix tracker = agregasi mingguan.

---

## Alur 7 — Approval 3-Pihak

**Tujuan:** menyetujui konten sebelum dijadwalkan.
**Trigger:** konten `Ready for Review` (lolos QC bila TikTok). Wajib sebelum `Approved`.

**Langkah (berurutan):**
1. **Step 1 — Copywriter** review copy/key message → approve / minta revisi.
2. **Step 2 — CSP** review kesesuaian strategic & angle → approve / minta revisi.
3. **Step 3 — SMS** review spec teknis & kesiapan publish → approve / minta revisi.
4. **Konten ber-klaim** → tambah approval **RnD** (validasi klaim) sebelum final.
5. **Konten sensitif** → tambah approval **Legal**.
6. Bila ada langkah "minta revisi" → konten balik ke produksi (`In Production`), siklus diulang dari step terkait.
7. Semua step lolos → status `Approved`.

**Catatan implementasi:** `approvals` (many per konten), field `step`, `approver_id`, `status`, `feedback`, `version`. Guard: status tidak boleh `Approved` selama ada step `pending`/`revision`; `has_claim = true` mensyaratkan approval RnD.

---

# D. PUBLISH & MAINTENANCE

## Alur 8 — Scheduling & Publish

**Tujuan:** menjadwalkan dan menayangkan konten yang sudah approved.
**Trigger:** konten `Approved`. PIC: SMS / ASM.

**Langkah:**
1. SMS/ASM set jadwal publish → status `Scheduled` + simpan konfirmasi jadwal.
2. Konten tayang (manual/terjadwal) → status `Published`, catat tanggal/jam aktual + link live.
3. Post-publish check — verifikasi tayang benar (caption, settings); catat issue bila ada.

**Catatan implementasi:** MVP **tanpa** integrasi API platform — scheduling = pencatatan internal + reminder. Field publish aktual vs rencana untuk audit.

---

## Alur 9 — Adjustment Kalender (Post-Finalized)

**Tujuan:** mengelola perubahan kalender setelah final, dengan otorisasi sesuai dampak.
**Trigger:** kebutuhan perubahan. PIC: CSP.

**Tiga jalur:**
- **Minor** — CSP identifikasi → CSP langsung eksekusi → notify tim terdampak → update + version log.
- **Major** — CSP identifikasi → keputusan **BM + MC** → CSP eksekusi → notify semua tim → update + version log.
- **Reactive (trending)** — CSP identifikasi tren → brief cepat → produksi cepat (maks 4–6 jam) → approval cepat CSP + SMS → publish → update kalender post-publish.

**Catatan implementasi:** `adjustment_logs` (jenis, kode konten, detail, alasan, request_by, approve_by, impact, status). Otorisasi eksekusi berbeda per jenis (major mensyaratkan approval MC/BM).

---

## Alur 10 — Arsip Asset & Version

**Tujuan:** menyimpan asset final terstruktur dengan riwayat versi.
**Trigger:** konten `Approved`/`Published`. PIC: GVD.

**Langkah:**
1. Asset final disimpan/di-link sesuai struktur & naming convention.
2. Update version history (siapa, kapan, perubahan).
3. Versi lama di-archive (bukan dihapus), retention sesuai policy.

**Catatan implementasi:** `assets` (banyak per konten), field type/version/review_status. Gunakan activity log untuk version history otomatis.

---

## Pemetaan ke implementasi (ringkas)

| Alur | Bentuk teknis utama |
|------|---------------------|
| 1, 3 | Workflow + view turunan + version log |
| 2 | Perhitungan + warning otomatis |
| 4 | Form 3-bagian dengan field-level authorization |
| 5, 7, 8 | **State machine** status konten |
| 6 | Checklist + aturan derivasi sub-tipe |
| 9 | Otorisasi berjenjang per jenis adjustment |
| 10 | Versioning + activity log |

Acuan entitas: lihat `02_Data_Model_ERD.md`.
