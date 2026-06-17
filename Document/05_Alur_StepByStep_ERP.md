# Alur Step-by-Step di ERP Laravel — Apa yang Diisi di Tiap Layar

**Versi:** v0.1
**Fungsi:** menerjemahkan 10 alur kerja (lihat `04_Alur_Kerja_Content_Creative.md`) jadi langkah konkret pemakaian aplikasi — siapa login, buka menu apa, isi field apa, klik tombol apa, dan apa hasilnya di sistem. Berguna sebagai acuan build UI sekaligus panduan training user.

**Notasi:** *(field)* = data yang diisi user · `[Tombol]` = aksi · → = hasil/perubahan status.

---

## Alur 0 — Persiapan Master Data (sekali di awal)

**Menu:** Settings / Master Data · **Login:** IT & Data / Admin.

1. **Users & Roles** — tambah user: *(nama, email)*, pilih *(role: CSP/CW/SMS/GVD/CC/VG/ASM/MC/BM/RnD/Legal)*. Role membawa `rbac_tier` (1 edit / 2 comment / 3 view). → akun siap login.
2. **Platforms** — tambah tiap akun: *(nama: "TikTok maryame", kode: "TKM", originality_strict: ya/tidak)*. Ulangi untuk IGF, IGR, IGS, FB, SHP, LZD, WEB, BLG, WA, dll.
3. **Products/SKU** — *(sku: "SKU-NIAC-30ML", nama produk)*.
4. **Campaigns** — *(nama: "Payday Sale 5 Mei", tipe: seasonal/launching/tactical, tanggal mulai–selesai)*.

→ Tanpa master data ini, kalender tidak bisa diisi (semua field pilihan menariknya dari sini).

---

# A. PERENCANAAN

## Alur 1 — Penyusunan Kalender Konten Bulanan

**Menu:** Content Calendar → `[+ Kalender Baru]` · **Login:** CSP.

1. Buat header kalender: *(periode bulan, versi)* → kalender berstatus `Draft`.
2. `[+ Tambah Konten]`, pilih *(platform)* → **sistem auto-generate kode konten** (mis. `TKM-2026-05-001`).
3. Isi entry: *(tanggal & waktu publish, tema, tipe konten, format, sub-tipe TikTok jika TikTok, angle kreatif, key message, produk highlight, klaim Y/N, campaign ref, PIC copy/visual/video, deadline brief, deadline produksi, priority, originality note, notes)* → konten berstatus `Draft`.
4. Ulangi langkah 2–3 untuk tiap platform secara terpisah (Cross-Platform Originality — tidak boleh 1 entry untuk banyak platform).
5. Buka tab Capacity → jalankan **Alur 2**.
6. `[Submit for Review]` → kalender `In Review`. CSP + SMS bahas di review.
7. MC/BM `[Approve]` → kalender `Approved` → `[Distribute]` membagikan ke tim.
8. Akhir bulan: `[Archive]` / export PDF → kalender `Archived`.

→ Output: banyak record `contents` berstatus `Draft`, terikat ke 1 kalender.

---

## Alur 2 — Konfirmasi Kapasitas Produksi

**Menu:** Content Calendar → tab `Capacity` · **Login:** CSP.

1. Sistem auto-tampilkan *(total entry bulan ini per tim)*.
2. CSP isi *(estimasi jam produksi)* dan *(kapasitas tersedia per tim: CW, GVD, CC, VG)*.
3. Sistem hitung *(status auto: OK / Over)*.
4. Jika **Over** → CSP catat tindakan 5-step di *(catatan)* dan sesuaikan kalender (redistribusi → prioritas ulang → sederhanakan format → freelancer (butuh approval MC+Finance) → eskalasi MC). Field *(Confirmed by)* diisi tiap tim.

→ Kalender hanya boleh `Submit for Review` jika status kapasitas = OK (atau over sudah ditandai teratasi).

---

## Alur 3 — Jadwal Produksi Mingguan

**Menu:** Production Schedule (filter minggu) · **Login:** CSP.

1. Pilih *(minggu)* → sistem tampilkan semua konten dengan deadline di minggu itu (Senin–Minggu).
2. CSP atur *(priority)* tiap item & catat *(blocker)* bila ada (Jumat sore).
3. Senin pagi: `[Mark Briefed]` → tim produksi mulai eksekusi.
4. Harian: status item di-update via Alur 5 (terlihat di board mingguan).

→ Tidak ada tabel baru — ini view turunan dari `contents`.

---

# B. PRODUKSI

## Alur 4 — Brief Konten

**Menu:** detail Konten → tab `Brief` · **Login:** CSP lalu SMS.

1. **CSP** isi bagian Strategic: *(theme, angle, positioning, target audience, key message, tone, campaign ref)* → `[Save]`.
2. **SMS** isi bagian Spec Teknis: *(aspect ratio, resolusi, durasi, format file, caption, hashtag, thumbnail, audio guidance, instruksi originality)* → `[Save]`.
3. Isi Schedule & Ownership: *(deadline produksi, PIC produksi, rencana publish)*.
4. `[Brief Final]` → konten boleh transisi ke produksi.

→ 1 record `briefs` per konten; field strategic hanya editable CSP, teknis hanya SMS.

---

## Alur 5 — Produksi & Update Status

**Menu:** My Tasks (konten yang di-assign ke saya) · **Login:** CC/VG/GVD/CW.

1. PIC buka konten → `[Mulai Produksi]` → status `In Production`.
2. Setelah hasil siap: tab Assets `[+ Tambah Asset]` → *(tipe: visual/video/final, link/file, version)*.
3. `[Submit for Review]` → status `Ready for Review`.

→ **Guard:** tombol `Submit for Review` terkunci sampai minimal 1 asset ter-link. Hanya PIC item yang bisa transisi.

---

# C. QUALITY & APPROVAL

## Alur 6 — QC Konten TikTok

**Menu:** QC Queue (konten TikTok `Ready for Review`) · **Login:** SMS.

1. Buka konten → tab `QC`.
2. Centang hasil tiap kriteria 1–6: *(Pass / Fail / N/A)* + *(catatan per kriteria)*.
3. Sistem hitung overall & menyarankan sub-tipe; SMS set *(sub-tipe: KK Interaktif / KK Soft Selling / Non-KK)*.
4. Jika Fail & revisi tak feasible → `[Turunkan ke Non-KK]` (memicu Adjustment minor, Alur 9).
5. Mix Tracker mingguan auto-update (warning bila mendekati limit KK non-interaktif/7 hari).

→ 1 `qc_check` + 6 `qc_criteria_results` per konten TikTok.

---

## Alur 7 — Approval 3-Pihak

**Menu:** Approval Inbox (per role) · **Login:** CW → CSP → SMS (berurutan).

1. **CW** buka konten → review copy → `[Approve]` atau `[Request Revision]` + *(feedback)*.
2. **CSP** (setelah CW approve) → review strategic/angle → `[Approve]` / `[Request Revision]`.
3. **SMS** (setelah CSP approve) → review spec teknis → `[Approve]` / `[Request Revision]`.
4. Jika *(klaim = Y)* → muncul step **RnD** (validasi klaim). Jika konten sensitif → step **Legal**.
5. `[Request Revision]` di langkah mana pun → konten balik `In Production`, PIC dinotifikasi, siklus diulang.
6. Semua step `Approve` → status `Approved`.

→ Tiap aksi membuat record `approvals` *(step, approver, status, feedback, version, tanggal)*. **Guard:** status tak bisa `Approved` selama ada step pending/revision; `klaim=Y` wajib approval RnD.

---

# D. PUBLISH & MAINTENANCE

## Alur 8 — Scheduling & Publish

**Menu:** Publishing (konten `Approved`) · **Login:** SMS / ASM.

1. Buka konten → *(set tanggal & jam publish)* → `[Schedule]` → status `Scheduled` + simpan konfirmasi jadwal.
2. Setelah tayang → `[Mark Published]` → isi *(tanggal/jam publish aktual, link konten live)* → status `Published`.
3. Post-publish check: centang *(verifikasi caption/settings)* + catat *(issue bila ada)*.

→ MVP **tanpa** integrasi API platform: scheduling = pencatatan + reminder, publish dikonfirmasi manual.

---

## Alur 9 — Adjustment Kalender

**Menu:** detail Konten → `[Buat Adjustment]` (aktif setelah kalender `Approved`) · **Login:** CSP.

1. Pilih *(jenis: Minor / Major / Reactive)*.
2. Isi *(kode konten terkait, detail perubahan, alasan, impact)*.
3. **Minor** → CSP `[Save & Execute]` langsung; tim terdampak dinotifikasi.
4. **Major** → `[Submit]` → MC/BM `[Approve]` dulu → baru bisa execute.
5. **Reactive (trending)** → `[Buat Konten Fast-Track]` (kode baru) → brief cepat → approval cepat CSP+SMS → publish (maks 4–6 jam) → update kalender post-publish.

→ Record `adjustment_logs` *(jenis, kode konten, detail, alasan, request_by, approve_by, impact, status)* + version log kalender.

---

## Alur 10 — Arsip Asset & Version

**Menu:** detail Konten → tab `Assets` (atau Asset Library) · **Login:** GVD.

1. `[+ Tambah Asset]` final → *(tipe: Final, link/file, version, review_status)*.
2. Sistem catat version history otomatis (siapa/kapan/perubahan).
3. Versi lama → `[Archive]` (bukan dihapus); retention sesuai policy.

→ Banyak record `assets` per konten + activity log untuk version history.

---

## Ringkasan: peta menu ERP

| Modul/Menu | Alur terkait | Role utama |
|------------|--------------|------------|
| Settings / Master Data | 0 | IT & Data |
| Content Calendar (+ tab Capacity) | 1, 2 | CSP |
| Production Schedule | 3 | CSP |
| Detail Konten → Brief | 4 | CSP, SMS |
| My Tasks | 5 | CC/VG/GVD/CW |
| QC Queue | 6 | SMS |
| Approval Inbox | 7 | CW, CSP, SMS, RnD, Legal |
| Publishing | 8 | SMS, ASM |
| Adjustment | 9 | CSP, MC/BM |
| Assets / Asset Library | 10 | GVD |

Acuan field & entitas: `02_Data_Model_ERD.md`. Aturan & otorisasi tiap alur: `04_Alur_Kerja_Content_Creative.md`.
