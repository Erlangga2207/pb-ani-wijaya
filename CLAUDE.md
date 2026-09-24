# CLAUDE.md — pb-ani-wijaya

Konteks proyek untuk Claude Code. Taruh file ini di root repo. PRD ada di folder `docs/prd/`.

## Tentang proyek
Website company profile + sistem internal pabrik penggilingan **beras ketan** PB. Ani Wijaya (Losarang, Indramayu). Proses: beli gabah dari petani → giling → jual beras ketan + hasil samping (menir, dedak, sekam). Pengguna admin: Owner + 1–2 staff.

## Stack
- PHP 8.3, Laravel 13, Filament v5 (panel `admin`, path `/admin`), Filament Shield + spatie/laravel-permission
- MySQL (Laragon lokal, Hostinger produksi), Tailwind + Alpine di halaman publik
- Struktur resource Filament: `app/Filament/Admin/Resources/<Nama>/{<Nama>Resource.php, Schemas/, Tables/, Pages/}`

## Aturan WAJIB
1. **Jangan ubah desain halaman publik.** File berikut hanya boleh diubah bila PRD menyebutnya secara eksplisit, dan hanya untuk binding data (bukan markup/class/style):
   `resources/views/public/**`, `resources/views/components/**`, `resources/views/layouts/public.blade.php`, `public/images/**`.
2. **API `SiteSetting::get($key)` dan helper `$setting()` harus tetap bekerja**, semua key yang ada di `SiteSettingSeeder` tetap ada.
3. **Filament v5**: jangan menebak namespace. Cek kode yang sudah ada / vendor sebelum memakai class baru. Pola yang dipakai repo ini: `Filament\Schemas\Schema`, `Filament\Schemas\Components\{Section,Grid,Utilities\Get,Utilities\Set}`, `Filament\Forms\Components\*`, `Filament\Actions\*`, `Filament\Support\Icons\Heroicon`.
4. **Logika bisnis di `app/Services`, bukan di Filament page/form.** Filament hanya memanggil service (mis. override `handleRecordCreation()`).
5. Semua perubahan stok **hanya lewat `StockService`**, di dalam `DB::transaction()` dengan `lockForUpdate()` pada baris item. Stok tidak boleh minus.
6. Dokumen transaksi (pembelian, produksi, penjualan, opname) **tidak boleh di-edit atau di-delete** setelah disimpan. Koreksi = **void** (alasan wajib) yang membuat mutasi balik. Tidak ada `DeleteBulkAction` pada resource transaksi.
7. Angka: berat `decimal(12,2)` dalam **kg**; uang `decimal(15,2)` dalam Rupiah; persen `decimal(5,2)`. Gunakan cast `decimal:2`. Hitung uang di PHP dengan `round(..., 2)`.
8. Enum PHP di `app/Enums` (backed string, implement `HasLabel`/`HasColor` Filament bila tampil di UI). Kolom DB pakai `string`, bukan `enum` MySQL.
9. Otorisasi lewat Policy/Shield + cek role (`super_admin`, `owner`, `staff`). Data keuangan (HPP, laba, biaya) **tidak boleh terlihat oleh `staff`** — termasuk kolom tabel, widget, dan infolist.
10. Label UI Bahasa Indonesia. Kode (nama class, method, kolom) Bahasa Inggris.
11. Kode sederhana & mudah dibaca pemula. Komentar hanya untuk hal penting (rumus, alasan keputusan).
12. Setiap PRD wajib punya feature test (PHPUnit) untuk service-nya. Jalankan `php artisan test` sebelum bilang selesai.

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
php artisan shield:generate --all --panel=admin   # setelah menambah resource
./vendor/bin/pint                                  # format kode
```
