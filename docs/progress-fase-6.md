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
| 4. Log 8 method test di `admin-migration-plan.md` bagian 6 + "Hasil Fase 6" di `i18n-plan.md` | ✅ | (commit berikut) |
| 2b. Uji yang sama di MySQL `pb_ani_wijaya_fase6_tmp` | ⏸ | MySQL Laragon mati (port 3306) — menunggu Erlan menyalakan |
| 1. Commit pekerjaan yang sudah ada (4 commit) | ✅ | `4c9b428` feat(i18n), `bac39da` feat(admin), `19ca515` refactor(admin) (+ grup nav "Website" Filament dihapus), `b696170` fix(admin) |

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
| Uji migration di database nyata: salinan SQLite ✅, MySQL ⏸ (lihat log langkah 2b) | 🟡 | |

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
| `migrate:fresh --seed` sukses di database **sementara** (bukan DB `.env`) | ✅ salinan SQLite · ⏸ MySQL |
| Pint | ⬜ |
| Commit bertahap (Conventional Commits) | ✅ |
| Cek manual di browser (oleh Erlan) | ⬜ |

## Rencana sisa pekerjaan (menunggu persetujuan)
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
