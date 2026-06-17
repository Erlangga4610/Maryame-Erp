# Implementasi Alur — Maryamé ERP

---

## 1. Login

Buka browser ke URL website, akan muncul halaman login.

### Akun tersedia:

| Email | Password | Role |
|---|---|---|
| admin@maryame.com | password123 | Super Admin + CSP |
| csp@maryame.com | password123 | CSP |
| sms@maryame.com | password123 | SMS |
| cw@maryame.com | password123 | CW |
| gvd@maryame.com | password123 | GVD |
| cc@maryame.com | password123 | CC |
| vg@maryame.com | password123 | VG |
| asm@maryame.com | password123 | ASM |
| rnd@maryame.com | password123 | RnD |
| legal@maryame.com | password123 | Legal |
| mc_bm@maryame.com | password123 | MC/BM |

Cara: isi email + password, klik **Sign in**. Setelah login masuk ke halaman Dashboard.

---

## 2. Dashboard

Halaman pertama setelah login. Menampilkan:
- **7 kartu statistik** — jumlah konten per status (Draft, In Production, Ready for Review, Approved, Scheduled, Published)
- **Konten per Platform** — diagram batang distribusi konten
- **Jadwal Mendatang** — konten yang sudah dijadwalkan (approved/scheduled)
- **Konten Terbaru** — 8 konten terakhir dibuat

Navigasi utama ada di sidebar kiri (desktop) atau hamburger menu (mobile).

---

## 3. Master Data (Setting Awal — Sekali Saja)

**Akses:** Sidebar → Master Data → klik sub-menu

> Dilakukan sekali di awal oleh Admin/Super Admin.

### 3a. Platforms

- Klik **Master Data → Platforms**
- Klik **`[+ Tambah]`** (hanya untuk role Tier 1: CSP/SMS)
- Isi: *Nama Platform* (contoh: "TikTok maryame"), *Kode* (contoh: "TKM"), *Originality Strict* (centang jika konten harus original)
- Klik **`[Simpan]`**

Platform yang perlu dibuat: TikTok (TKM), IG Feed (IGF), IG Reels (IGR), IG Story (IGS), Facebook (FB), Shopee (SHP), Lazada (LZD), Website (WEB), Blog (BLG), WhatsApp (WA).

### 3b. Products/SKU

- **Master Data → Products** → **`[+ Tambah]`**
- Isi: *SKU* (contoh: "SKU-NIAC-30ML"), *Nama Produk*
- Klik **`[Simpan]`**

### 3c. Campaigns

- **Master Data → Campaigns** → **`[+ Tambah]`**
- Isi: *Nama Campaign* (contoh: "Payday Sale 5 Mei"), *Tipe* (seasonal/launching/tactical), *Tanggal Mulai - Selesai*
- Klik **`[Simpan]`**

### 3d. Users

- **Master Data → Users** → **`[+ Tambah]`**
- Isi: *Nama, Email, Password, Employee ID, Posisi, Departemen*
- Pilih *Role* dari dropdown (memberikan akses sesuai tier)
- Klik **`[Simpan]`**

---

## 4. Content Calendar (Alur 1 — Perencanaan Bulanan)

**Akses:** Sidebar → **Content Calendar**
**Login sebagai:** CSP (atau Super Admin)

### Melihat Konten

Ada 3 mode tampilan:
- **Kanban** — 4 kolom: To Do / In Progress / In Review / Done
- **Table** — tabel dengan sorting, search, pagination
- **Calendar** — kalender grid + panel konten belum terjadwal

Ganti mode dengan tombol di toolbar atas.

### Membuat Konten Baru

1. Klik **`[+ Konten Baru]`** (atau **Fast-Track** untuk konten spontan)
2. Isi tab **Detail**:
   - *Platform* (wajib) → sistem auto-generate kode konten
   - *Priority* (Rutin/Campaign/Spontan)
   - *Tema* (wajib), *Caption*
   - *Tanggal Publish, Deadline Produksi*
   - *Product, Campaign, Content Group* (opsional)
   - *PIC Copy, PIC Visual, PIC Video*
   - *Estimasi Jam* (copy/visual/video) — untuk capacity planning
3. Isi tab **Strategic** (CSP only):
   - *Angle, Positioning, Target Audience, Key Message, Tone, Copy Brief*
4. Isi tab **Technical** (SMS only):
   - *Aspect Ratio, Resolution, Durasi, Format File, Hashtag, Audio Guidance, Originality Instruction, Thumbnail Note, Visual/Video Direction*
5. Isi tab **Asset** (upload file nanti)
6. Klik **`[Simpan]`** → konten lahir status **Draft**

### Action per Konten

Di kanban card atau table row, ada tombol aksi:
- **👁️ QC** (hanya TikTok) — buka modal QC 6 kriteria
- **✅ Approve** — approval per stage
- **📅 Schedule** — atur jadwal publish
- **🚀 Publish** — tandai sudah tayang
- **📋 Checklist** — post-publish checklist
- **📝 Adjustment** — buat perubahan setelah final
- **📎 Assets** — upload asset/lihat versi
- **🗑️ Hapus** — soft delete

---

## 5. Capacity Planning (Alur 2)

**Akses:** Sidebar → **Capacity** (atau tab Capacity di Content Calendar)
**Login sebagai:** CSP

1. Sistem otomatis hitung total estimasi jam per tim per minggu
2. Untuk setiap PIC, lihat perbandingan: estimasi jam vs kapasitas (max hours)
   - Jika ≤ kapasitas → status **OK**
   - Jika > kapasitas → status **⚠️ Over**
3. Klik **edit** pada user → ubah *Max Hours* jika perlu
4. Jika Over → isi **tindakan 5-step**:
   - Step 1: Redistribusi beban internal
   - Step 2: Prioritas ulang (campaign tetap jalan)
   - Step 3: Sederhanakan format
   - Step 4: Freelancer/vendor
   - Step 5: Eskalasi ke MC
5. Klik **`[Confirm]`** untuk konfirmasi kapasitas oleh tiap tim
6. Kalender tidak bisa `Submit for Review` jika masih ada yang Over belum di-confirm

---

## 6. Production Schedule (Alur 3 — Jadwal Mingguan)

**Akses:** Sidebar → **Production Schedule**
**Login sebagai:** CSP

1. Pilih minggu dengan navigasi **`[< Prev]`** / **`[Next >]`**
2. Lihat semua konten yang deadline produksinya di minggu itu
3. **Toggle Briefed** — tandai konten sudah dibrief ke tim produksi
4. **Toggle Blocked** — tandai konten terblokir (isi alasan blocker)
5. Filter berdasarkan platform atau status

---

## 7. My Tasks (Alur 5 — Produksi)

**Akses:** Sidebar → **My Tasks**
**Login sebagai:** CC / VG / GVD / CW (PIC konten)

1. Lihat konten yang di-assign ke saya, diurutkan berdasarkan status
2. Klik **`[Mulai Produksi]`** → status berubah **In Production**
3. Upload hasil:
   - Tab **Asset** → upload *Final Asset* (file video/gambar) dan *Thumbnail*
   - File tersimpan di `storage/app/public/assets/` dan `thumbnails/`
4. Klik **`[Submit for Review]`** → status **Ready for Review**
   - ⚠️ **Minimal 1 asset harus sudah diupload** atau tombol tidak bisa diklik

---

## 8. QC TikTok (Alur 6 — Khusus Platform TikTok)

**Akses:** Dari Content Calendar → klik tombol **👁️ QC** di kanban card (hanya konten TKM)
Atau: buka URL `/qc/{contentId}`
**Login sebagai:** SMS

1. Buka konten TikTok berstatus `In Production` / `Ready for Review` / `Approved`
2. Nilai 6 kriteria (pilih Pass / Fail / N/A):
   1. Voice/Audio original
   2. Demo penggunaan produk
   3. Produk visible jelas
   4. Manfaat/benefit disebut verbal
   5. Kriteria tambahan #5
   6. Kriteria tambahan #6
3. Centang **Keranjang Kuning** jika ada TikTok Shop cart
4. Sistem otomatis menyarankan **Sub-tipe**:
   - ✅ **KK Interaktif** — semua pass + ada keranjang
   - ✅ **KK Soft Selling** — ada keranjang, tidak semua pass
   - ✅ **Non-KK** — tanpa keranjang
5. Klik **`[Simpan QC]`**
6. Jika ada kriteria Fail yang tidak bisa diperbaiki → klik **`[Turunkan ke Non-KK]`** (memicu Adjustment minor)

---

## 9. Approval (Alur 7 — 3 Pihak)

**Akses:** Sidebar → **Approval Inbox**
**Login sesuai stage:**

### Urutan Approval:

1. **CW** (Copywriter) — review copy/key message
2. **CSP** — review strategic & angle
3. **SMS** — review spec teknis & kesiapan publish
4. **RnD** (tambahan, jika `has_claim = true`) — validasi klaim
5. **Legal** (tambahan, jika konten sensitive)

### Cara:

1. Buka Approval Inbox → lihat konten yang pending untuk role saya
2. Klik konten → review detail
3. Klik **`[Approve]`** atau **`[Request Revision]`** + isi feedback
4. Jika diminta revisi → konten balik ke `In Production`, PIC dinotifikasi
5. Setelah semua approve → status otomatis **Approved**

---

## 10. Schedule & Publish (Alur 8)

**Akses:** Content Calendar → klik **📅 Schedule** atau **🚀 Publish** (atau `/publish/{contentId}`)
**Login sebagai:** SMS / ASM

### Schedule:

1. Buka konten berstatus **Approved**
2. Klik **📅 Schedule**
3. Isi *Tanggal Publish* dan *Jam Publish*
4. Klik **`[Schedule]`** → status **Scheduled**

### Publish:

1. Buka konten berstatus **Scheduled**
2. Klik **🚀 Publish**
3. Isi *Live URL* (link konten yang sudah tayang)
4. Klik **`[Publish]`** → status **Published**, `published_at` tercatat

### Post-Publish Checklist:

1. Klik **📋 Checklist**
2. Centang 8 item verifikasi:
   - Link works, Thumbnail visible, Caption accurate
   - Hashtags included, CTA functional, Product tagged
   - No typo, Audio sync
3. Klik **`[Simpan]`**

---

## 11. Adjustment (Alur 9 — Perubahan Setelah Final)

**Akses:** Content Calendar → klik **📝 Adjustment**
**Login sebagai:** CSP

### Ada 3 jenis:

| Jenis | Alur | Approval |
|---|---|---|
| **Minor** | Edit → Save langsung | CSP execute langsung |
| **Major** | Submit → MC/BM Approve → Execute | Wajib MC/BM approve |
| **Reactive** | Fast-Track → Publish → Update | CSP + SMS approve cepat |

### Cara Minor:

1. Edit konten (field berubah)
2. Pilih **Minor** di radio button
3. Klik **`[Simpan]`** → langsung tereksekusi, version +1

### Cara Major:

1. Edit konten
2. Pilih **Major** → isi *Alasan*
3. Klik **`[Simpan]`** → masuk ke pending adjustment
4. MC/BM buka **Approval Inbox** → approve adjustment → tereksekusi

### Cara Reactive (Fast-Track):

1. Di toolbar Content Calendar, klik **`[Fast-Track]`**
2. Isi form cepat (minimal: platform, tema)
3. Otomatis: priority=Spontan, status=In Production, approval CW dibuat
4. Produksi cepat → QC → Approval CSP+SMS → Publish (target 4-6 jam)

---

## 12. Assets & Version (Alur 10)

**Akses:** Content Calendar → klik **📎 Assets** (kanban card) atau `/assets/{contentId}`
**Login sebagai:** GVD (atau PIC konten)

1. Lihat daftar asset yang sudah diupload
2. Klik **`[Upload Final Asset]`** atau **`[Upload Thumbnail]`**
3. File disimpan, tercatat di tabel `assets` dengan version number
4. **Version History** — lihat riwayat versi konten (siapa, kapan, perubahan apa)
5. Versi lama tidak dihapus (soft delete) — bisa dilihat di modal Version History

---

## 13. Calendar Management

**Akses:** Sidebar → **Calendar**
**Login sebagai:** CSP

### Status Kalender:

```
Draft → In Review → Approved → Distributed → Archived
```

1. **Draft** — tambah/hapus konten di kalender
2. **`[Submit for Review]`** → In Review (cek kapasitas dulu)
3. MC/BM **`[Approve]`** → Approved
4. CSP **`[Distribute]`** → Distributed (bagikan ke tim)
5. CSP **`[Archive]`** → Archived (akhir bulan)

### Cara:

- Pilih bulan dengan navigasi **`[< Prev]`** / **`[Next >]`**
- Di hari tertentu, klik **`[+]`** → cari konten → tambahkan
- Drag & drop entry untuk pindah tanggal
- Klik status badge untuk ubah status entry

---

## 14. Mix Tracker

**Akses:** Sidebar → **Mix Tracker**
**Login sebagai:** SMS / CSP

- Menampilkan rasio KK Interaktif vs KK Soft Selling vs Non-KK per minggu
- **Warning** otomatis jika KK Soft Selling melebihi limit (default 2/minggu)
- Berguna untuk menjaga kepatuhan aturan TikTok

---

## Ringkasan Menu per Role

| Role | Menu Utama |
|---|---|
| **Super Admin** | Semua menu + bisa edit apapun |
| **CSP** | Content Calendar, Capacity, Production Schedule, Calendar, My Tasks, Mix Tracker, Adjustment, Approval Inbox (stage csp) |
| **SMS** | Content Calendar (technical brief), QC TikTok, Approval Inbox (stage sms), Publishing, Mix Tracker |
| **CW** | My Tasks, Approval Inbox (stage cw) |
| **GVD/CC/VG** | My Tasks (upload asset) |
| **MC/BM** | Calendar (approve kalender), Approval Inbox (approve adjustment Major) |
| **RnD** | Approval Inbox (stage rnd) — hanya konten ber-klaim |
| **Legal** | Approval Inbox (stage legal) — hanya konten sensitif |
| **ASM** | Publishing (publish konten) |
