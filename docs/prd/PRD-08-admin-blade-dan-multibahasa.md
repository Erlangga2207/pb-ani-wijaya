# PRD-08 — Admin Panel Blade (gaya Deepsea) + Website Multi-bahasa

**Branch:** `feature/prd-08-<fase>` (satu branch per fase) · **Bergantung pada:** PRD-01 s/d PRD-07 yang sudah jalan

## Tujuan
1. **Admin:** ganti tampilan Filament dengan admin panel Blade custom yang meniru tampilan & pola UX admin **Deepsea Florist** (project lama Erlan), supaya mudah dipahami pemilik pabrik & staff.
2. **Website:** company profile tersedia dalam **5 bahasa**: Indonesia (default), English, 中文 (Mandarin, Simplified), 日本語, 한국어. Tujuannya promosi ke pembeli luar negeri.

Yang diganti di admin hanya **lapisan tampilan**. Model, migration, Enum, Policy, dan semua `app/Services/*` dipakai ulang tanpa mengubah logikanya.

---

## A. Prinsip umum
1. **Referensi visual admin = project Deepsea Florist** di `[PATH_DEEPSEA]`. Hanya baca, **jangan ubah apa pun di folder itu**. Warna & font ikut brand PB. Ani Wijaya (Modern Heritage: merah, emas, charcoal; EB Garamond + Manrope). Struktur layout, komponen, dan alur ikut Deepsea.
2. **Migrasi bertahap.** Admin baru di `/panel`, Filament tetap hidup di `/admin` sampai fase terakhir.
3. Controller tipis: Form Request → Policy → Service → redirect + flash message. Tidak ada logika bisnis di controller atau Blade.
4. **Desain company profile tetap.** Perubahan yang diizinkan di Blade publik hanya:
   (a) mengganti teks hardcoded dengan `__()` / data terjemahan,
   (b) menambah *language switcher* di navbar dan menu mobile,
   (c) atribut `lang`, `hreflang`, dan meta SEO di `<head>`.
   Tidak boleh mengubah class, layout, warna, atau urutan section.

## B. Keamanan admin (tidak bisa ditawar)
- Semua route admin: `middleware(['auth', 'active', 'role:super_admin|owner|staff'])`. Buat middleware `EnsureUserIsActive`.
- **Setiap** aksi controller wajib `$this->authorize()` atau `->can()` di route. Pakai ulang Policy yang ada, dengan cek permission Shield diganti cek role eksplisit.
- Validasi pakai Form Request + `$request->validated()`. Jangan pernah `$request->all()`.
- Blade pakai `{{ }}`. Dilarang `{!! !!}` untuk data dari user/admin.
- Login: `throttle:5,1`, `session()->regenerate()`, logout via POST.
- Data keuangan (HPP, laba, avg cost, biaya, laporan keuangan) disembunyikan dari `staff` di **controller/query**, bukan cuma di Blade.
- Upload: validasi `mimes` + `max`, disk `public`, tampilkan dengan `asset('storage/'.$path)`.

## C. Spesifikasi Admin Blade
Struktur (sesuaikan dengan pola Deepsea bila berbeda):
```
routes/admin.php                              # prefix /panel, name admin.*
app/Http/Controllers/Admin/*Controller.php
app/Http/Requests/Admin/*Request.php
resources/views/admin/layouts/app.blade.php   # sidebar, topbar, flash message
resources/views/admin/components/*            # tabel, badge, modal konfirmasi, stat card, field, tab bahasa
resources/views/admin/<modul>/{index,create,show,edit}.blade.php
```
- Tabel: `paginate(15)->withQueryString()`, cari & filter lewat query string, nyaman di HP (lebar 375px).
- Admin panel **hanya Bahasa Indonesia** (tidak perlu multi-bahasa).

| Grup menu | Halaman | Catatan |
|---|---|---|
| Dashboard | ringkasan | widget sesuai role (PRD-06), chart ikut library Deepsea atau Chart.js |
| Transaksi | Pembelian Gabah, Produksi/Giling, Penjualan | create + show + void (modal alasan), **tanpa edit/delete** |
| Stok | Kartu Stok, Stock Opname | kartu stok read-only |
| Keuangan | Utang Petani, Piutang Pembeli, Pembayaran, Biaya Operasional, Laporan | owner+ (staff hanya laporan stok & rendemen tanpa rupiah) |
| Master Data | Barang, Petani/Pemasok, Pembeli | delete ditolak jika sudah dipakai transaksi |
| Website | Galeri, Katalog Produk, Pengaturan Website, Pesan & Calon Pembeli | owner+, dengan **tab bahasa** (bagian D) |
| Pengaturan | Pengguna | aturan super_admin PRD-01 tetap berlaku. **Menu Peran (Shield) dihapus**, role dikelola lewat seeder |

Nota penjualan & bukti pembelian: pakai ulang route/view print yang sudah ada.

## D. Spesifikasi Multi-bahasa (website publik)

### D1. Bahasa & URL
| Kode | Bahasa | URL contoh |
|---|---|---|
| `id` | Indonesia (default, fallback) | `/produk` |
| `en` | English | `/en/produk` |
| `zh` | 中文 (Simplified) | `/zh/produk` |
| `ja` | 日本語 | `/ja/produk` |
| `ko` | 한국어 | `/ko/produk` |

- Daftar bahasa di `config/locales.php` (kode, nama asli, label bendera/teks) supaya bahasa baru cukup ditambah di satu tempat.
- **URL prefix** (bukan session) supaya tiap bahasa bisa diindeks Google. Path tetap pakai slug Indonesia untuk semua bahasa (KISS).
- Middleware `SetLocale`: set `app()->setLocale()` dan `Carbon::setLocale()` dari prefix URL; kode tidak dikenal → 404.
- Helper `localized_route('products')` yang menghasilkan URL sesuai bahasa aktif. Semua link internal di Blade publik pakai helper ini.
- **Tidak ada redirect otomatis berdasarkan bahasa browser** (Google crawler bisa salah index). Cukup tampilkan switcher.
- Route `POST /kontak` tetap satu, redirect kembali ke halaman kontak sesuai bahasa asal.

### D2. Teks statis (yang sekarang hardcoded di Blade)
- Ekstrak **semua** teks hardcoded di `resources/views/public/**`, `components/**`, `layouts/public.blade.php` ke `lang/{id,en,zh,ja,ko}.json` (key = kalimat Indonesia). Termasuk: menu, tombol, label form, placeholder, flash message, label kategori galeri, teks WhatsApp prefill (`?text=` di-`urlencode`).
- Pesan validasi form kontak tersedia di 5 bahasa (`lang/{locale}/validation.php`, hanya rule yang dipakai).
- Draf terjemahan en/zh/ja/ko boleh dibuat oleh Claude Code, tapi tandai di `docs/translation-review.md` sebagai **perlu dicek penutur asli** sebelum go-live (terutama zh/ja/ko, karena ini teks promosi).

### D3. Konten dinamis (diisi admin)
- Gunakan `spatie/laravel-translatable` (kolom JSON). **Cek dulu kompatibilitasnya dengan Laravel 13** di composer. Kalau belum kompatibel, buat trait sederhana `HasTranslations` dengan cast JSON + fallback; jangan paksa versi.
- Field yang diterjemahkan:
  - `products`: `name`, `description`
  - `galleries`: `title`, `description`
  - `site_settings.value` **hanya** untuk type `text`/`textarea` (tambah kolom `is_translatable` boolean di seeder). Phone, email, url, image, angka tidak diterjemahkan.
- Migration data: pindahkan nilai lama ke key `id` di JSON (tidak boleh ada data hilang). Tulis migration `down()` yang benar.
- **Fallback:** kalau terjemahan kosong → tampil versi Indonesia. Halaman tidak boleh menampilkan string kosong.
- `SiteSetting::get($key)` tetap dengan signature yang sama, otomatis mengembalikan nilai sesuai locale aktif. Cache key menjadi `setting.{locale}.{key}` dan di-forget untuk semua locale saat disimpan.
- **Slug produk tidak diterjemahkan.**

### D4. Admin: tab bahasa
- Field yang bisa diterjemahkan tampil sebagai **tab** `ID | EN | 中文 | 日本語 | 한국어`. ID wajib, yang lain opsional.
- Di halaman index Produk/Galeri/Pengaturan ada indikator kelengkapan per bahasa (mis. badge "EN ✓ 中文 ✗").
- Tidak ada auto-translate (keputusan Erlan: terjemahan diisi manual).

### D5. Switcher & tampilan
- Dropdown bahasa kecil di navbar desktop & menu mobile, mengikuti gaya navbar yang ada. Menampilkan nama asli bahasa (English, 中文, 日本語, 한국어). Saat ganti bahasa, tetap di halaman yang sama.
- Font: EB Garamond & Manrope **tidak punya huruf CJK**. Untuk `zh`/`ja`/`ko`, tambahkan fallback (Noto Serif/Sans SC, JP, KR) yang **hanya dimuat pada locale tersebut** supaya halaman Indonesia/Inggris tidak lebih berat.
- Korea: `word-break: keep-all` pada teks paragraf. Cek tidak ada teks CJK yang terpotong atau overflow di lebar 375px.

### D6. SEO
- `<html lang="{{ app()->getLocale() }}">` (pakai `zh-Hans` untuk Mandarin di atribut lang/hreflang).
- Di setiap halaman: `<link rel="alternate" hreflang="...">` untuk 5 bahasa + `x-default` → versi Indonesia, plus `<link rel="canonical">`.
- Meta title & description per bahasa (diambil dari pengaturan website yang bisa diterjemahkan).
- Route `/sitemap.xml` berisi semua halaman × 5 bahasa.

---

## E. Fase (satu sesi Claude Code per fase, berhenti di akhir tiap fase)
| Fase | Isi | Selesai jika |
|---|---|---|
| 0 | **Audit, tanpa coding.** (1) Baca admin Deepsea: stack, layout, komponen, CSS/JS, pola tabel/form/modal/notifikasi. (2) Pemetaan semua resource Filament → controller/route/view. (3) Inventaris teks hardcoded di Blade publik (jumlah per file). (4) Cek kompatibilitas `spatie/laravel-translatable`. Hasilnya ditulis di `docs/admin-migration-plan.md` + `docs/i18n-plan.md` | Erlan menyetujui kedua rencana |
| 1 | Layout admin, login/logout, middleware, dashboard | login `/panel` jalan, sidebar sesuai role |
| 2 | Master Data + Pengguna | CRUD + otorisasi + test |
| 3 | Pembelian, Produksi, Penjualan, Pembayaran, Stok | acceptance criteria PRD-03/04/05 lolos lewat UI baru |
| 4 | Biaya Operasional & Laporan (cetak + CSV) | angka identik dengan versi Filament |
| 5 | **i18n website bagian statis:** `config/locales.php`, routing prefix, `SetLocale`, helper `localized_route`, ekstraksi teks ke `lang/*.json`, switcher, font CJK, hreflang/canonical/sitemap | 5 bahasa bisa dibuka, teks statis berganti, tampilan Indonesia identik dengan sebelumnya |
| 6 | **i18n konten dinamis + modul Website di admin baru:** kolom translatable + migrasi data, `SiteSetting::get` per locale, halaman Galeri/Produk/Pengaturan/Lead di `/panel` dengan tab bahasa | edit EN di admin langsung tampil di `/en`; field kosong fallback ke ID |
| 7 | **Hapus Filament:** resource, provider, `filament/*`, `filament-shield`; redirect `/admin` → `/panel`; update `CLAUDE.md` (aturan Filament diganti aturan admin Blade + aturan i18n) | `composer install` bersih, semua test hijau |

Catatan urutan: konten dinamis (fase 6) sengaja digabung dengan pembuatan modul Website di admin baru. Kalau kolom diubah ke JSON saat form Filament masih dipakai, form Filament akan rusak.

## F. Test (wajib per fase)
- Admin: tamu → redirect login; staff → 403 di halaman owner (termasuk akses URL langsung); owner → 200; validasi gagal → error di session.
- i18n: setiap route publik × 5 locale → 200; locale tidak dikenal → 404; teks switcher sesuai; fallback ke ID saat terjemahan kosong; `SiteSetting::get` mengembalikan nilai sesuai locale; hreflang lengkap.
- Test Service yang sudah ada **harus tetap hijau tanpa diubah**. Kalau ada yang merah, berarti logika bisnis tersentuh: berhenti dan laporkan.

## G. Acceptance criteria akhir
- [ ] Semua fitur Filament tersedia di `/panel` dengan gaya Deepsea; tidak ada dependency `filament/*`.
- [ ] Staff tidak bisa membuka URL keuangan walau mengetik langsung (403).
- [ ] Website bisa dibuka di `/`, `/en`, `/zh`, `/ja`, `/ko`; semua teks (statis & dinamis) berganti bahasa; tidak ada string kosong.
- [ ] Tampilan versi Indonesia identik dengan sebelum PRD ini (bandingkan screenshot desktop & 375px kelima halaman).
- [ ] Teks CJK tampil dengan font yang benar dan tidak overflow di HP.
- [ ] `docs/translation-review.md` berisi daftar teks yang perlu dicek penutur asli.
- [ ] `php artisan test` hijau.
