# Progres PRD-08 Fase 6 — i18n konten dinamis + modul Website di /panel

Branch: `feature/prd-08-fase6-i18n-dinamis` · Dicek ulang: 2026-10-13

**Kondisi git saat orientasi:** tidak ada commit "wip"; HEAD = `84b8a37` (akhir Fase 5), pekerjaan Fase 6 belum di-commit.
`php artisan test` (SQLite in-memory): **172/172 hijau** (171 + `AuthorizationBeforeValidationTest`).

**Keputusan Erlan (2026-10-13):**
1. File Filament Website boleh dihapus; `/admin` tidak boleh menyisakan link ke halaman yang dihapus; test yang dipindah dicatat di log.
2. "Identik" = desain/layout, bukan isi teks. `hero_badge` & `hero_title` versi `id` tetap teks brand Inggris
   ("INDRAMAYU'S FINEST", "Heritage of Excellence"). `hero_description`, `about_title`, `about_paragraph_*`: `id` = draf Indonesia,
   `en` = teks Inggris lama. Semua draf masuk `docs/translation-review.md`.
3. Uji migration hanya di database `pb_ani_wijaya_fase6_tmp`, dihapus setelah selesai.

## Log langkah
| Langkah | Status | Commit |
|---|---|---|
| Keputusan 2: `id` hero_badge/hero_title = teks brand; test disesuaikan (172/172 hijau) | ✅ | `45eadbd` |
| 2a. Uji migration pada **salinan** `database/database.sqlite` (DB `.env` = SQLite, bukan MySQL) + data contoh (2 produk, 1 galeri, `about_title` diedit): migrate → rollback → bandingkan snapshot → migrate → `migrate:fresh --seed`. Ditemukan & diperbaiki: `down()` mengembalikan draf ID, bukan teks lama, untuk setting default → kini roundtrip identik. File asli tidak berubah (sha1 sama). | ✅ | `ec47478` |
| 3. `docs/translation-review.md` bagian 3: 10 key pengaturan × (teks lama, id, en, zh, ja, ko), dibuat otomatis dari file data | ✅ | `debb34c` |
| 4. Log 8 method test di `admin-migration-plan.md` bagian 6 + "Hasil Fase 6" di `i18n-plan.md` | ✅ | `2440da7` |
| 5. Pint (file Fase 6) + `php artisan test` 172/172 hijau | ✅ | style: commit terpisah |
| 1. Commit pekerjaan yang sudah ada (4 commit) | ✅ | `4c9b428` feat(i18n), `bac39da` feat(admin), `19ca515` refactor(admin) (+ grup nav "Website" Filament dihapus), `b696170` fix(admin) |
| `company_tagline` versi `id` = "Premium Rice Producer" (teks brand); test: teks brand tetap + SEO versi ID memuat "beras ketan" | ✅ | `a2ef787` |
| 2b. Uji di MySQL 8.0.30 `pb_ani_wijaya_fase6_tmp` (target diverifikasi sebelum tiap perintah): skema Fase 5 (migrate + rollback 2) + seeder pengaturan versi Fase 5 (dari git `84b8a37`) + data contoh (2 produk incl. teks Unicode, 1 galeri, `about_title` diedit) → **migrate** (kolom jadi `json`, data utuh, fallback jalan) → **rollback** (isi, label & tipe kolom **identik** dengan snapshot Fase 5) → **migrate lagi** ✅ → **`migrate:fresh --seed`** ✅ (23 setting) → **`php artisan test` di MySQL**: awalnya 3 gagal (masalah test, bukan aplikasi), diperbaiki → **173/173**. SQLite juga 173/173. Database tmp **dihapus**; `pb_ani_wijaya` & `database.sqlite` tidak disentuh. | ✅ | `32a1366` |
| Langkah pindah `.env` lokal SQLite → MySQL `pb_ani_wijaya` (ditulis, **tidak dijalankan**) | ✅ | bagian "Pindah lokal ke MySQL" di bawah |

Legenda: ✅ selesai · 🟡 sebagian · ⬜ belum

## D3 — Konten dinamis
| Item | Status | Bukti |
|---|---|---|
| `spatie/laravel-translatable ^6.14` terpasang | ✅ | `composer.json`, `composer.lock` |
| `products.name/description`, `galleries.title/description` → JSON, nilai lama → `id`, `down()` | ✅ | migration `2026_10_12_000001_…`, model `HasTranslations`, `TranslatableMigrationTest` |
| `site_settings.is_translatable` + JSON hanya untuk key keputusan no. 1 | ✅ | migration `2026_10_12_000002_…`, `database/data/site_setting_translations.php` |
| Key baru `seo_meta_title`, `seo_meta_description` + dipakai di `<head>`/judul beranda | ✅ | seeder, `layouts/public.blade.php` (meta), `beranda.blade.php` (`@section('title')`) — binding saja |
| Fallback ke ID bila terjemahan kosong | ✅ | `Translatable::fallback()` di `AppServiceProvider`, `SiteSetting::valueFor()`, test fallback |
| Slug produk tidak diterjemahkan | ✅ | slug tetap kolom string |
| Seeder mengisi draf 5 bahasa untuk database baru; semua key lama tetap ada | ✅ | `SiteSettingSeeder` (23 key = 21 lama + 2 SEO) |

## Fix cache `SiteSetting::get`
| Item | Status | Bukti |
|---|---|---|
| Signature tetap, cache key `setting.{locale}.{key}` | ✅ | `app/Models/SiteSetting.php` |
| `$default` tidak ikut di-cache (bug lama) | ✅ | `test_missing_key_default_is_not_cached` |
| Forget semua locale saat saved/deleted | ✅ | `booted()` |

## Migration pemindahan teks en/id (keputusan no. 4)
| Item | Status | Bukti |
|---|---|---|
| Nilai = default seeder lama → teks Inggris ke `en`, `id` diisi draf Indonesia (+ zh/ja/ko) | ✅ | migration 000002 + `TranslatableMigrationTest::test_site_settings_defaults_get_drafts_and_edited_values_are_kept` |
| Nilai yang sudah diedit admin → hanya dibungkus ke `id` | ✅ | test yang sama |
| Label setting → Bahasa Indonesia | ✅ | migration 000002 + seeder |
| Draf ID/en/zh/ja/ko setting masuk `docs/translation-review.md` | ✅ | bagian 3 |
| Uji migration di database nyata: salinan SQLite ✅, MySQL ✅ (lihat log langkah 2b) | ✅ | |

## D4 — Admin: tab bahasa + modul Website di /panel
| Item | Status | Bukti |
|---|---|---|
| Komponen `lang-tabs` (ID wajib, tab error aktif otomatis, titik "terisi") | ✅ | `resources/views/admin/components/lang-tabs.blade.php` |
| Badge kelengkapan `lang-status` di index Produk, Galeri, Pengaturan | ✅ | `lang-status.blade.php` dipakai di 3 halaman |
| Galeri (I C E D, upload mimes+max, file lama dihapus) | ✅ | `GalleryController`, `GalleryRequest`, views, test |
| Katalog Produk (I C E D, auto slug) | ✅ | `ProductController`, `ProductRequest`, `ProductFormTest` |
| Pengaturan Website (edit/update, tab bahasa per field translatable) | 🟡 | kode + test hijau; **tampilan tab (JS Bootstrap) belum dicek di browser** |
| Pesan & Calon Pembeli (I S, tindak lanjut, Jadikan Pembeli, Spam, hapus) | ✅ | `ContactMessageController`, `LeadService::updateFollowUp`, `LeadConversionTest` |
| Menu Website di `config/admin-menu.php` (owner) | ✅ | sudah ada sejak Fase 1 |
| Otorisasi: `can:manage-website` di route + policy di controller; staff 403 sebelum validasi | ✅ | `routes/admin.php`, `AuthorizationBeforeValidationTest`, `WebsiteModuleTest` |
| Menu Website di Filament dihilangkan | ✅ | resource Galeri/Produk/Pesan + page WebsiteSettings dihapus (keputusan 1), grup nav "Website" dihapus, tidak ada referensi tersisa (`grep`) |
| `ItemForm` Filament & `ItemController` tetap jalan dengan `products.name` JSON | ✅ | label produk diambil lewat model |

## Penutupan fase
| Item | Status |
|---|---|
| Log perubahan test lama di `docs/admin-migration-plan.md` bagian 6 | ✅ |
| Bagian "Hasil Fase 6" di `docs/i18n-plan.md` | ✅ |
| `migrate:fresh --seed` sukses di database **sementara** (bukan DB `.env`) | ✅ salinan SQLite · ✅ MySQL |
| `php artisan test` di SQLite **dan** MySQL | ✅ 173/173 keduanya |
| Pint | ✅ |
| Commit bertahap (Conventional Commits) | ✅ |
| Cek manual di browser (oleh Erlan) | ⬜ |

## Rencana sisa pekerjaan (disetujui 2026-10-13 — selesai kecuali 2b MySQL & cek browser)
1. Commit pekerjaan yang sudah ada, dipecah: (a) `feat(i18n)` translatable kolom + migration + `SiteSetting::get` per locale/fix cache;
   (b) `feat(admin)` modul Website di /panel + tab bahasa; (c) `refactor(admin)` hapus resource Filament Website + test pindah ke /panel;
   (d) `test(admin)` otorisasi sebelum validasi.
2. Uji migration di MySQL lewat database salinan sementara `pb_ani_wijaya_fase6_tmp` (dibuat & dihapus sendiri; DB `.env` tidak disentuh):
   `migrate` dari skema Fase 5 + data → cek → `migrate:rollback` → cek; lalu `migrate:fresh --seed` di DB sementara itu.
3. `docs/translation-review.md`: tambah bagian draf pengaturan website (5 bahasa).
4. `docs/admin-migration-plan.md` bagian 6 + `docs/i18n-plan.md` "Hasil Fase 6".
5. Pint, `php artisan test`, ringkasan + daftar cek browser.

## Pertanyaan
Sudah dijawab — lihat "Keputusan Erlan" di atas.

## Pindah lokal ke MySQL (dijalankan Erlan, bukan Claude)

Kondisi saat dicek (2026-10-13, hanya baca):
- `database/database.sqlite` (DB `.env` sekarang): 3 user test `@pbaniwijaya.test` (super_admin, owner, staff), 5 barang dari `ItemSeeder`,
  21 pengaturan **tidak ada yang diedit**, 0 produk/galeri/pesan/transaksi → **tidak ada data yang perlu dipindah**; cukup `migrate:fresh --seed`.
- MySQL `pb_ani_wijaya` **sudah ada tapi basi**: 21 tabel dari migration awal saja (tabel transaksi belum ada), 2 user. Akan ditimpa → di-backup dulu.
- `.env` belum punya `ADMIN_EMAIL` / `ADMIN_PASSWORD` → tanpa itu seeder **tidak membuat super admin**.

Perintah untuk **PowerShell** di root proyek (`C:\laragon\www\pb-ani-wijaya`), MySQL Laragon menyala:

```powershell
# 1. Backup (di luar folder repo supaya tidak ter-commit)
$bak = "C:\laragon\backup-pb-ani-wijaya\$(Get-Date -Format yyyyMMdd-HHmm)"
New-Item -ItemType Directory -Force $bak
Copy-Item database\database.sqlite "$bak\database.sqlite"
Copy-Item .env "$bak\.env.sqlite"
# --result-file, bukan ">": di PowerShell 5.1 ">" menulis UTF-16 dan file SQL jadi rusak
& "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe" -uroot --result-file="$bak\pb_ani_wijaya-lama.sql" pb_ani_wijaya
Get-ChildItem $bak    # pastikan 3 file ada dan ukurannya > 0

# 2. Catat user lokal (untuk dibuat ulang di langkah 6)
php artisan tinker --execute="App\Models\User::with('roles')->get()->each(fn(`$u) => print `$u->name.' | '.`$u->email.' | '.`$u->roles->pluck('name')->join(',').PHP_EOL);"
```

3. Edit `.env` (ganti baris `DB_CONNECTION=sqlite`, tambahkan sisanya; hapus baris `DB_*` lain yang dikomentari agar tidak bingung):
   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=pb_ani_wijaya
   DB_USERNAME=root
   DB_PASSWORD=

   ADMIN_EMAIL=email-super-admin-kamu
   ADMIN_PASSWORD=password-kuat-kamu
   ```

```powershell
# 4. Muat ulang config & pastikan targetnya benar SEBELUM menghapus apa pun
php artisan config:clear
php artisan db:show          # harus: MySQL, database pb_ani_wijaya. Kalau masih SQLite → berhenti, cek .env

# 5. Buat ulang semua tabel + data awal (MENGHAPUS isi pb_ani_wijaya lama — sudah di-backup di langkah 1)
php artisan migrate:fresh --seed
php artisan migrate:status   # semua "Ran", termasuk 2026_10_12_000001 & 000002
php artisan cache:clear
```

6. Login `/panel/login` dengan `ADMIN_EMAIL`, buat ulang user owner/staff di **Pengaturan › Pengguna** (sesi login lama otomatis keluar karena tabel `sessions` baru).
7. Buka `/` dan `/en` — teks hero, tagline, judul SEO tampil.
8. `php artisan test` tetap memakai SQLite in-memory (`phpunit.xml`), tidak menyentuh `pb_ani_wijaya`.

**Kembali ke SQLite** bila ada masalah: salin `"$bak\.env.sqlite"` ke `.env`, lalu `php artisan config:clear`. `database\database.sqlite` tidak diubah oleh langkah di atas.
Database `pb_ani_wijaya_test` (sudah ada di MySQL) tidak dipakai oleh proyek ini.

## Cek manual di browser (Erlan)
Persiapan: jalankan langkah "Pindah lokal ke MySQL" di atas (atau, bila tetap SQLite: backup `database/database.sqlite` lalu `php artisan migrate`). Login owner di `/panel`.

1. **Pengaturan Website** `/panel/pengaturan-website`: klik tab ID / EN / 中文 / 日本語 / 한국어 di beberapa field — isi berganti, tab aktif bergaris merah, titik hijau muncul pada bahasa yang terisi. Badge kelengkapan di atas form.
2. Kosongkan tab ID sebuah field → simpan → error tampil dan tab ID otomatis aktif.
3. Ubah `hero_description` tab EN → simpan → buka `/en`: teks baru tampil; `/` tetap versi Indonesia. Kosongkan EN → `/en` menampilkan versi Indonesia (tidak kosong).
4. Beranda `/`: lencana "INDRAMAYU'S FINEST" & judul "Heritage of Excellence" tetap; deskripsi hero berbahasa Indonesia. Judul tab browser = judul SEO.
5. **Katalog** `/panel/katalog`: tambah produk dengan nama ID + EN → produk unggulan di `/` dan `/en` sesuai bahasa; badge "EN ✓ 中文 ✗" di daftar.
6. **Galeri** `/panel/galeri`: unggah foto, isi judul ID saja → tampil di `/galeri` dan `/ja/galeri` (fallback ID).
7. **Pesan** `/panel/pesan`: buka pesan, ubah status, coba "Jadikan Pembeli".
8. Semua halaman di atas di lebar **375px**: tab bahasa bisa digeser, tidak ada yang keluar layar.
9. Login **staff**: menu Website tidak tampil; buka `/panel/galeri` langsung → 403.
10. `/admin` (Filament): sidebar tidak punya grup/link Website.