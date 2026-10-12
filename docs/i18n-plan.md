# Rencana Multi-bahasa Website (PRD-08 bagian D, Fase 0)

Status: **draf, menunggu persetujuan Erlan**. Belum ada kode aplikasi yang diubah.
Bahasa: `id` (default, tanpa prefix), `en`, `zh` (Simplified, atribut `zh-Hans`), `ja`, `ko`.

---

## 1. Inventaris teks hardcoded (Blade publik)

Hitungan otomatis (text node + atribut `alt`/`placeholder`/`title`/`aria-label`/`content` + string di `@php`/`<script>`), **perkiraan ±10%**; angka pasti keluar saat ekstraksi Fase 5.

| File | Baris | Teks hardcoded | Sudah dari `$setting()` | Catatan |
|---|---:|---:|---:|---|
| `public/beranda.blade.php` | 317 | ~55 | 7 | fallback deskripsi produk unggulan hardcoded |
| `public/tentang-kami.blade.php` | 332 | ~59 | 6 | |
| `public/produk.blade.php` | 454 | ~110 | 5 | **katalog seluruhnya hardcoded** (lihat Q2), 2 link WA `?text=` sudah ter-encode manual |
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

## 4. Temuan & keputusan yang perlu disetujui

**Q1. `is_translatable` per key, bukan per type.** PRD bilang "type text/textarea", tapi beberapa setting bertipe `text` sebenarnya angka/ID: `company_name`, `company_nib`, `years_experience`, `partners_count`, `social_whatsapp`. Usul daftar translatable:
`hero_badge`, `hero_title`, `hero_subtitle`, `hero_description`, `company_tagline`, `about_title`, `about_paragraph_1`, `about_paragraph_2`, `contact_address`, `contact_short_address` (+ meta baru di Q3). Sisanya tidak diterjemahkan.

**Q2. Halaman `/produk` tidak memakai data `products` dari database.** `HomeController::produk` mengirim `$products` tapi view tidak memakainya — katalog ditulis langsung di Blade. Akibatnya terjemahan kolom `products.name/description` hanya terlihat di **produk unggulan Beranda**. Pilihan:
- (a) teks katalog di `produk.blade.php` masuk `lang/*.json` seperti teks statis lain (**usul, sesuai aturan "desain publik tetap"**);
- (b) binding `produk.blade.php` ke tabel `products` — butuh izin eksplisit karena menyentuh struktur view.

**Q3. Meta SEO per bahasa.** Belum ada setting `meta_title`/`meta_description`; meta description sekarang dirangkai dari `company_tagline`. Usul: tambah 2 key translatable `seo_meta_title`, `seo_meta_description` di `SiteSettingSeeder` (key lama tetap ada). Judul per halaman dari `__()`.

**Q4. Nilai "Indonesia" di seeder sebagian berbahasa Inggris** (`hero_title` "Heritage of Excellence", `about_paragraph_*`, `company_tagline`, label kategori galeri). Saat migrasi, nilai lama masuk key `id` apa adanya. Apakah:
- (a) biarkan apa adanya (nanti diedit lewat admin), atau
- (b) di Fase 6 seeder diberi isi `id` Bahasa Indonesia + `en` dari teks Inggris yang sekarang?

**Q5. Perubahan belum di-commit di halaman publik.** Working tree punya modifikasi di `beranda`, `produk`, `tentang-kami` + 2 gambar baru (`foto_beras.jpeg`, `foto_pabrik.webp`). Fase 5 butuh baseline screenshot "sebelum". Mohon di-commit (atau dibuang) dulu sebelum Fase 5 dimulai.

**Q6. Cache `SiteSetting::get` menyimpan `$default`** bila key tidak ada di DB (default panggilan pertama ikut di-cache 1 jam). Usul diperbaiki sekalian di Fase 6 (cache hanya nilai DB, default diterapkan di luar cache). Signature tetap.

**Catatan lain**
- Tailwind di halaman publik via CDN play script — tidak diubah.
- Kolom `galleries.category` masih `enum` MySQL (melanggar aturan 8 CLAUDE.md) — di luar cakupan PRD-08, tidak disentuh kecuali disuruh.
- Test i18n (Fase 5/6): 5 route × 5 locale → 200, locale tak dikenal → 404, switcher, fallback ID, `SiteSetting::get` per locale, hreflang lengkap.
