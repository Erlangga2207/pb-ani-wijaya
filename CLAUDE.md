# CLAUDE.md — pb-ani-wijaya

Konteks proyek untuk Claude Code. Taruh file ini di root repo. PRD ada di folder `docs/prd/`.

## Tentang proyek
Website company profile + sistem internal pabrik penggilingan **beras ketan** PB. Ani Wijaya (Losarang, Indramayu). Proses: beli gabah dari petani → giling → jual beras ketan + hasil samping (menir, dedak, sekam). Pengguna admin: Owner + 1–2 staff.

## Stack
- PHP 8.3, Laravel 13, spatie/laravel-permission (hanya role), spatie/laravel-translatable
- MySQL (Laragon lokal, Hostinger produksi), Tailwind + Alpine di halaman publik
- **Admin panel Blade** di `/panel` (route `admin.*`, `routes/admin.php`): Bootstrap 5.3.3 dari CDN (versi dikunci + SRI) + `public/css/admin.css`, JS vanilla, Chart.js. Tidak ada Filament/Livewire. `/admin` hanya redirect 301 ke `/panel`.
- Struktur admin: `app/Http/Controllers/Admin/<Nama>Controller.php`, `app/Http/Requests/Admin/<Nama>Request.php`, `resources/views/admin/<modul>/{index,create,show,form}.blade.php`, komponen `resources/views/admin/components/*` dipanggil `<x-admin::nama>`, menu di `config/admin-menu.php`.
- Website multi-bahasa: `id` (default, tanpa prefix), `en`, `zh`, `ja`, `ko` (URL `/en/produk`, dst.).

## Aturan WAJIB
1. **Jangan ubah desain halaman publik.** File berikut hanya boleh diubah bila PRD menyebutnya secara eksplisit, dan hanya untuk binding data (bukan markup/class/style):
   `resources/views/public/**`, `resources/views/components/**`, `resources/views/layouts/public.blade.php`, `public/images/**`.
   Pengecualian (PRD-08): teks boleh diganti `__()` / data terjemahan, language switcher, dan atribut `lang`/`hreflang`/meta SEO di `<head>`.
2. **API `SiteSetting::get($key)` dan helper `$setting()` harus tetap bekerja** (signature sama, nilai sesuai bahasa aktif), semua key yang ada di `SiteSettingSeeder` tetap ada.
3. **Admin Blade (`/panel`)** — alur controller tipis: Form Request → Policy → Service → `redirect()->with('sukses'|'gagal', ...)`.
   - Semua route di grup `['auth', 'active', 'role:super_admin|owner|staff']`. **Setiap** aksi wajib `$this->authorize()`; aksi yang memakai Form Request juga diberi middleware `can:` di route supaya user tanpa hak mendapat 403, bukan pesan validasi.
   - Validasi lewat Form Request + `$request->validated()`, **jangan** `$request->all()`. Blade pakai `{{ }}`, dilarang `{!! !!}` untuk data user/admin.
   - Index: `paginate(15)->withQueryString()`, filter lewat query string, tabel `.tabel-kartu` (nyaman di 375px). Satu `form.blade.php` untuk create & edit.
   - Pakai ulang komponen `<x-admin::panel|field|pill|tile|alert|void-modal|payment-modal|lang-tabs|lang-status>` sebelum membuat yang baru. Gaya tambahan hanya di `public/css/admin.css`. Admin panel hanya Bahasa Indonesia.
   - Upload: validasi `mimes` + `max`, disk `public`, tampilkan dengan `asset('storage/'.$path)`.
4. **Logika bisnis di `app/Services`, bukan di controller atau Blade.** Controller hanya memanggil service.
5. Semua perubahan stok **hanya lewat `StockService`**, di dalam `DB::transaction()` dengan `lockForUpdate()` pada baris item. Stok tidak boleh minus.
6. Dokumen transaksi (pembelian, produksi, penjualan, opname) **tidak boleh di-edit atau di-delete** setelah disimpan. Koreksi = **void** (alasan wajib, `x-admin::void-modal` + route `*.void`) yang membuat mutasi balik. Route transaksi hanya `index, create, store, show` + `void` — tidak ada edit/update/destroy.
7. Angka: berat `decimal(12,2)` dalam **kg**; uang `decimal(15,2)` dalam Rupiah; persen `decimal(5,2)`. Gunakan cast `decimal:2`. Hitung uang di PHP dengan `round(..., 2)`.
8. Enum PHP di `app/Enums` (backed string). Bila tampil di UI, beri method `getLabel()` (Bahasa Indonesia) dan `getColor()` (`success|warning|danger|info|gray`, dipakai `x-admin::pill`). Kolom DB pakai `string`, bukan `enum` MySQL.
9. Otorisasi lewat Policy (`app/Policies`) + cek role (`super_admin`, `owner`, `staff`) — tidak memakai permission. Role dibuat oleh `RoleSeeder`. Data keuangan (HPP, laba, avg cost, biaya, laporan keuangan) **tidak boleh terlihat oleh `staff`** — disembunyikan di **controller/query** (jangan di-query sama sekali), bukan hanya di Blade.
10. Label UI Bahasa Indonesia. Kode (nama class, method, kolom) Bahasa Inggris.
11. Kode sederhana & mudah dibaca pemula. Komentar hanya untuk hal penting (rumus, alasan keputusan).
12. Setiap PRD wajib punya feature test (PHPUnit) untuk service-nya. Jalankan `php artisan test` sebelum bilang selesai. Untuk halaman `/panel`: tamu → redirect login, staff → 403 lewat URL langsung, owner → 200, validasi gagal → error di session.
13. **Multi-bahasa website (i18n)**:
   - Daftar bahasa hanya di `config/locales.php` (kunci pertama = default `id`). Bahasa baru = tambah di config + `lang/<kode>.json` + `lang/<kode>/validation.php`.
   - Teks statis Blade publik: `{{ __('Teks Indonesia apa adanya') }}`, key = teks Indonesia, terjemahan di `lang/{en,zh,ja,ko}.json`. Link internal publik pakai `localized_route('nama')`. Link WA: `urlencode(__('...'))`.
   - Konten dinamis: `products.name/description` dan `galleries.title/description` = kolom JSON dengan trait `Spatie\Translatable\HasTranslations` (`$translatable`). `site_settings` hanya baris `is_translatable = true` yang JSON; isi awalnya di `database/data/site_setting_translations.php`.
   - **Jangan menulis teks biasa ke kolom translatable** lewat `DB::table()`, seeder, atau factory — MySQL menolak teks non-JSON. Tulis lewat model atau `json_encode(['id' => ...])`.
   - Terjemahan kosong → tampil versi `id` (tidak boleh ada string kosong di halaman). Slug produk tidak diterjemahkan.
   - Form admin untuk field translatable: `<x-admin::lang-tabs>` (ID wajib, bahasa lain opsional) + aturan dari `App\Http\Requests\Admin\Concerns\TranslatableRules`; index menampilkan `<x-admin::lang-status>`.
   - Draf terjemahan buatan Claude dicatat di `docs/translation-review.md` sebagai "perlu dicek penutur asli". Tidak ada auto-translate dan tidak ada redirect berdasarkan bahasa browser.

## Cara kerja per PRD
1. Baca PRD sampai habis, lalu buat rencana (plan mode) — tunjukkan daftar file yang akan dibuat/diubah.
2. Buat branch: `feature/prd-0X-<slug>`.
3. Kerjakan bertahap, commit per langkah logis dengan Conventional Commits (`feat(sales): ...`, `fix(auth): ...`, `test(stock): ...`). Jangan satu commit raksasa.
4. Setelah selesai: `php artisan migrate:fresh --seed` harus sukses, `php artisan test` hijau, lalu ringkas apa yang berubah + cara uji manual.
5. Kalau PRD ambigu atau bertentangan dengan kode yang ada: **berhenti dan tanya**, jangan mengarang.

## Perintah
```bash
php artisan migrate:fresh --seed
php artisan test
./vendor/bin/pint            # format kode
```
