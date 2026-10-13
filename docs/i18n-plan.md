# Rencana Multi-bahasa Website (PRD-08 bagian D, Fase 0)

Status: **disetujui Erlan 2026-10-12** (lihat bagian keputusan).
Bahasa: `id` (default, tanpa prefix), `en`, `zh` (Simplified, atribut `zh-Hans`), `ja`, `ko`.

---

## 1. Inventaris teks hardcoded (Blade publik)

Hitungan otomatis (text node + atribut `alt`/`placeholder`/`title`/`aria-label`/`content` + string di `@php`/`<script>`), **perkiraan ±10%**; angka pasti keluar saat ekstraksi Fase 5.

| File | Baris | Teks hardcoded | Sudah dari `$setting()` | Catatan |
|---|---:|---:|---:|---|
| `public/beranda.blade.php` | 317 | ~55 | 7 | fallback deskripsi produk unggulan hardcoded |
| `public/tentang-kami.blade.php` | 332 | ~59 | 6 | |
| `public/produk.blade.php` | 454 | ~110 | 5 | **katalog seluruhnya hardcoded** (keputusan 2), 2 link WA `?text=` sudah ter-encode manual |
| `public/galeri.blade.php` | 429 | ~55 | 1 | label kategori di JS Alpine + tombol filter |
| `public/kontak.blade.php` | 466 | ~84 | 17 | label/placeholder form, 2 link WA `?text=`, flash sukses |
| `components/navbar.blade.php` | 86 | ~8 | 2 | `$menuItems` di `@php` (5 label) |
| `components/footer.blade.php` | 77 | ~18 | 12 | |
| `components/whatsapp-float.blade.php` | 29 | ~2 | 2 | pesan WA via `urlencode()` |
| `layouts/public.blade.php` | 112 | ~4 | 3 | `<html lang="id">`, meta description, `<title>` |
| **Total** | | **~395** | | |

Teks di luar Blade yang ikut diterjemahkan:
- `HomeController::submitKontak` — flash "Pesan Anda berhasil dikirim! …" dan validasi (`required`, `email`, `max`, `string`) → `lang/{locale}/validation.php` (hanya rule ini + nama atribut `name/email/subject/message`).
- `Gallery::getCategoryLabelAttribute()` — 7 label kategori (sekarang Inggris + emoji) → `__()`.
- Judul halaman `@section('title', ...)` di tiap view.

Saat ini **belum ada** folder `lang/` dan tidak ada satu pun `__()` di view publik.

---

## 2. Kompatibilitas `spatie/laravel-translatable`

Dicek lewat `composer show --all` (tidak di-install):
- Versi terbaru **6.14.1**, require `php ^8.3`, `illuminate/database|support ^11|^12|^13`, `spatie/laravel-package-tools ^1.93` (sudah terpasang 1.93.1).
- Proyek: PHP 8.3.13, Laravel 13.11.2 → **kompatibel**. Rencana: `composer require spatie/laravel-translatable:^6.14` di Fase 6. Trait buatan sendiri tidak perlu.
- Fallback: `config/translatable.php` → `fallback_locale = 'id'`. Perilaku spatie untuk string kosong (`''`) belum diverifikasi — akan dipastikan dengan test di Fase 6; kalau tidak fallback, input kosong dari form disimpan sebagai `null`/dibuang sebelum disimpan.

---

## 3. Rancangan teknis

### Routing (Fase 5)
```
config/locales.php          ['id' => ['name' => 'Indonesia', 'hreflang' => 'id'], 'en' => [...], 'zh' => ['name' => '中文', 'hreflang' => 'zh-Hans'], ...]
app/Http/Middleware/SetLocale.php     app()->setLocale + Carbon::setLocale dari segmen {locale}; kode tak dikenal → 404
app/helpers.php (autoload files)      localized_route($name, $params = [], $locale = null)
```
- `routes/web.php`: daftar route publik dibungkus satu closure, didaftarkan **2 kali**:
  1. tanpa prefix → `id`, nama tetap `home`, `about`, `products`, `gallery`, `contact` (route lama tidak rusak);
  2. `prefix('{locale}')->where('locale', 'en|zh|ja|ko')` (dari config) → nama `localized.home`, dst.
- `localized_route('products')` → pilih nama sesuai `app()->getLocale()`. Switcher memakai `localized_route(Route::currentRouteName-tanpa-prefix, [], $kode)` sehingga tetap di halaman yang sama.
- `POST /kontak` tetap satu; form mengirim hidden `locale`, redirect ke `localized_route('contact', locale: ...)`. Pesan flash & validasi memakai locale itu.
- Tidak ada deteksi bahasa browser.
- `GET /sitemap.xml` → 5 halaman × 5 bahasa, dengan `xhtml:link hreflang`.

### Teks statis (Fase 5)
- `lang/{id,en,zh,ja,ko}.json`, key = kalimat Indonesia. `id.json` boleh kosong/identik (fallback ke key).
- Blade: teks diganti `{{ __('...') }}` — **tanpa mengubah class, markup, urutan section**.
- Link WA: `?text={{ urlencode(__('Halo PB. Ani Wijaya, ...')) }}`.
- `docs/translation-review.md`: daftar semua key en/zh/ja/ko berstatus "draf Claude — perlu dicek penutur asli".

### Head & font (Fase 5)
- `<html lang="{{ hreflang locale }}">`, `<link rel="canonical">`, 5× `<link rel="alternate" hreflang>` + `x-default` → versi `id`. Partial baru di `layouts/public.blade.php` (diizinkan PRD A4c).
- Font CJK hanya dimuat bila locale `zh|ja|ko`: tambahan `<link>` Google Fonts Noto Sans/Serif SC|JP|KR + override `font-family` (EB Garamond/Manrope tetap di depan, Noto sebagai fallback).
- `ko`: `word-break: keep-all` pada paragraf, lewat `<style>` kecil yang hanya dirender untuk `ko`.
- Switcher: dropdown kecil di navbar desktop + menu mobile, memakai class yang sudah ada di navbar (Alpine `x-data` sudah ada).

### Konten dinamis (Fase 6)
| Tabel | Kolom jadi JSON | Migrasi data |
|---|---|---|
| `products` | `name`, `description` | nilai lama → `{"id": "..."}` |
| `galleries` | `title`, `description` | idem |
| `site_settings` | `value` (hanya baris `is_translatable = true`) | kolom baru `is_translatable` boolean; nilai lama → `{"id": ...}` hanya untuk baris translatable |

- Migration `up()`: tambah kolom JSON sementara → salin → hapus lama → rename (aman di MySQL & SQLite test). `down()`: ambil `id` kembali ke string.
- `SiteSetting::get($key, $default)` — signature sama; cache key `setting.{locale}.{key}`; saat simpan `forget` untuk semua locale di `config/locales.php`.
- `site_settings.value` tidak bisa langsung pakai `HasTranslations` karena hanya sebagian baris yang translatable → di `get()`: bila `is_translatable`, decode JSON, ambil locale aktif, fallback `id`. Model lain (Product, Gallery) pakai trait spatie.
- Slug produk tidak diterjemahkan.
- Admin `/panel` (Website): komponen `lang-tabs` `ID | EN | 中文 | 日本語 | 한국어` (ID wajib), badge kelengkapan di index Produk/Galeri/Pengaturan.

Kenapa Fase 6 harus sekaligus memindahkan modul Website ke `/panel`: begitu kolom jadi JSON, form Filament Produk/Galeri/Pengaturan rusak. Setelah Fase 6, menu Website di Filament disembunyikan.

---

## 4. Keputusan Fase 0 (disetujui Erlan, 2026-10-12)

1. **`is_translatable` per key (daftar eksplisit).** Daftar final:

   | Key | Diterjemahkan |
   |---|---|
   | `hero_badge`, `hero_title`, `hero_subtitle`, `hero_description` | ✓ |
   | `company_tagline` | ✓ |
   | `about_title`, `about_paragraph_1`, `about_paragraph_2` | ✓ |
   | `seo_meta_title`, `seo_meta_description` (baru) | ✓ |
   | `company_name`, `company_nib`, `years_experience`, `partners_count` | ✗ |
   | semua `contact_*` (`contact_phone`, `contact_email`, `contact_address`, `contact_short_address`, `contact_maps_link`) | ✗ |
   | semua `social_*` (`social_whatsapp`, `social_instagram`, `social_facebook`) | ✗ |
   | `maps_embed_url` | ✗ |

2. **Katalog `/produk` → `lang/*.json`** seperti teks statis lain. Catatan penting: **nama & deskripsi produk yang diisi di admin (tabel `products`) hanya tampil di produk unggulan Beranda**, tidak di halaman `/produk`.
3. **Meta SEO:** 2 key baru translatable `seo_meta_title`, `seo_meta_description` di `SiteSettingSeeder`.
4. **Isi awal bahasa (lewat migration, bukan seeder).** Di migration Fase 6: untuk setting translatable yang nilainya **masih sama persis** dengan default seeder lama, teks Inggris dipindah ke `en` dan `id` diisi draf Bahasa Indonesia. Setting yang sudah diedit admin **tidak disentuh** (nilai lama → `id` saja). Draf ID masuk `docs/translation-review.md`.
5. Perubahan halaman publik sudah di-commit (`d4c05d1`) — baseline screenshot diambil dari commit ini.
6. Bug cache default `SiteSetting::get` diperbaiki di Fase 6, signature tetap.

**Catatan lain**
- Tailwind di halaman publik via CDN play script — tidak diubah.
- Kolom `galleries.category` masih `enum` MySQL (melanggar aturan 8 CLAUDE.md) — di luar cakupan PRD-08, tidak disentuh kecuali disuruh.
- Test i18n (Fase 5/6): 5 route × 5 locale → 200, locale tak dikenal → 404, switcher, fallback ID, `SiteSetting::get` per locale, hreflang lengkap.

---

## 5. Hasil Fase 5 (i18n statis)

- Key terjemahan = teks Blade **apa adanya** (sebagian memang berbahasa Inggris, mis. "Why Choose Us") supaya halaman Indonesia tidak berubah. `lang/id.json` = daftar kanonik 252 key; `en/zh/ja/ko` = draf → `docs/translation-review.md`.
- Bukti tampilan Indonesia identik: HTML kelima halaman sebelum vs sesudah dibandingkan. Selisihnya hanya tambahan yang diizinkan PRD (canonical, hreflang, pemilih bahasa, field tersembunyi `locale` di form kontak) dan escaping yang tampil sama (`"` → `&quot;`, `&` → `&amp;`, koma di link WhatsApp → `%2C`).
- Lebar 375px: 5 halaman × 5 bahasa dicek di browser, tidak ada teks yang keluar layar. Font Noto SC/JP/KR hanya dimuat di zh/ja/ko; `word-break: keep-all` hanya di ko.
- Kalimat yang dipotong `<span>`/`<br>` di desain diterjemahkan per potongan (markup tidak boleh diubah) — urutan kata CJK perlu dicek, daftar khusus ada di bagian 1 `docs/translation-review.md`.
- Pesan validasi form kontak memakai nama field yang diterjemahkan (mis. "Nama Lengkap wajib diisi.").

---

## 6. Hasil Fase 6 (konten dinamis + modul Website di /panel)

- `spatie/laravel-translatable` 6.14: `products.name/description`, `galleries.title/description` jadi JSON; nilai lama → `id`. Terjemahan kosong → versi `id` (`Translatable::fallback`).
- `site_settings.is_translatable` hanya untuk 10 key keputusan no. 1 + 2 key SEO baru. `SiteSetting::get($key, $default)` signature sama, cache `setting.{locale}.{key}`, `$default` tidak lagi ikut di-cache (bug lama), forget semua bahasa saat simpan.
- Isi awal: `database/data/site_setting_translations.php` (dipakai migration & seeder). Keputusan Erlan 2026-10-13: `hero_badge`/`hero_title` versi `id` tetap tagline brand Inggris; teks lain `id` = draf Indonesia, `en` = teks lama. Semua draf di bagian 3 `docs/translation-review.md`.
- Migration diuji di salinan database lokal (SQLite) dengan data contoh: migrate → rollback menghasilkan snapshot identik; nilai yang sudah diedit admin tidak disentuh. `down()` mengembalikan teks lama untuk setting yang belum diedit.
- Admin `/panel` grup Website (owner+): Galeri, Katalog Produk, Pengaturan Website, Pesan & Calon Pembeli. Tab bahasa `ID | EN | 中文 | 日本語 | 한국어` (`admin/components/lang-tabs`), badge kelengkapan (`lang-status`). Resource/page Filament Website dihapus.
- Route Website dan aksi owner lain memakai middleware `can:` sehingga staff mendapat 403 sebelum validasi.
