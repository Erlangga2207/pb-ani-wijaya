# Rencana Migrasi Admin: Filament → Blade (PRD-08, Fase 0)

Status: **disetujui Erlan 2026-10-12** (lihat bagian keputusan).
Referensi tampilan: `C:\laragon\www\deepsea_florist` (hanya dibaca).

---

## 1. Audit admin Deepsea Florist

### Stack
| Hal | Deepsea | Catatan untuk PB. Ani Wijaya |
|---|---|---|
| Laravel | 12, PHP ^8.2 | Kita Laravel 13 — pola tetap sama |
| CSS | **Bootstrap 5.3.3 via CDN** + satu file `public/css/app.css` (330 baris, token di `:root` dipetakan ke variabel `--bs-*`) | Tidak ada Vite/npm build |
| JS | `bootstrap.bundle.min.js` via CDN + `<script>` vanilla per halaman | Tidak ada Alpine/Livewire di admin |
| Font | Google Fonts (Fraunces + Source Sans 3) | Ganti EB Garamond + Manrope |
| Chart | **Tidak ada** | Pakai Chart.js via CDN jsDelivr (sesuai PRD) |
| Auth | Breeze-style `AuthenticatedSessionController` + `LoginRequest` (rate limit), logout POST | Ditiru |
| PWA | manifest + service worker untuk `/admin` | **Tidak ditiru** (YAGNI) |

### Layout (`layouts/admin.blade.php`)
- Desktop: sidebar kiri (`.admin-sidebar`) berisi brand + menu berkelompok; topbar berisi nama user · peran + tombol **Keluar** (form POST).
- HP (< lg): **bottom nav** 4 pintasan + tombol "Menu" yang membuka **offcanvas bawah** berisi menu lengkap + user + Keluar.
- Flash message: `session('sukses')` → `.alert-success`, `session('gagal')` → `.alert-danger`, di atas `@yield('konten')`.
- `<meta name="robots" content="noindex">`, `@section('judul')` untuk `<title>`.

### Menu
- **Satu sumber**: `config/admin-menu.php` (grup → item: `label`, `route`, `ikon` = path SVG inline, `role`, `badge`).
- View composer di `AppServiceProvider` menyaring item per role + `Route::has()`, membuang grup kosong, mengisi angka badge.
- Item aktif: `request()->routeIs('modul.*')`.

### Komponen Blade (`resources/views/components`)
| Komponen | Fungsi |
|---|---|
| `x-panel` | kartu dengan header (judul + slot `aksi` / tautan "Lihat semua") |
| `x-field` | label + input + hint + error (`(boleh kosong)` bila tidak wajib); slot untuk select/textarea |
| `x-pill` | badge status (titik + teks, warna: rose/hijau/amber/merah/neutral) |
| `x-tile` | stat card (label, nilai, sub, nada) |
| `x-alert` | kotak peringatan (warn/bad/ok) |

### Pola halaman
- **Index**: `.judul-halaman` (h1 + tombol utama) → form GET filter (`onchange="this.form.submit()"`) → `x-panel` berisi `table` → `{{ $x->links() }}`. Controller `paginate(n)->withQueryString()`.
- **Tabel mobile**: class `.tabel-kartu` — di < md setiap baris jadi kartu (`.kartu-judul`, `.kartu-status`, `.kartu-sebaris`, `.kartu-aksi`, `td[data-label]`). Baris bisa diklik via `.baris-link` + `stretched-link` tanpa JS.
- **Form**: satu view `form.blade.php` untuk create & edit (`$model->exists`), `max-width:560px`, tombol Simpan + Batal.
- **Baris dinamis** (repeater): `<template>` + JS vanilla (lihat `produk/form.blade.php`).
- **Tab**: `.tab-garis` berbasis link/query string.
- **Konfirmasi hapus**: `onsubmit="return confirm(...)"` — untuk **void dengan alasan** kita pakai modal Bootstrap (bundle JS sudah dimuat).
- Helper Blade: `@rupiah(x)`, `@angka(x)`; `Paginator::useBootstrapFive()`.
- Controller: Form Request → `$request->safe()` / `validated()` → model → `redirect()->with('sukses', ...)`.

### Yang **tidak** kita tiru dari Deepsea
- Controller Deepsea tidak memanggil `authorize()` — kita **wajib** (PRD B).
- Role Deepsea = kolom `role`; kita pakai spatie role (`super_admin|owner|staff`) + Policy yang sudah ada.
- Logika bisnis di controller Deepsea — kita tetap lewat `app/Services`.

---

## 2. Rancangan admin baru

```
routes/admin.php                         prefix /panel, name admin.*, didaftarkan di bootstrap/app.php
app/Http/Middleware/EnsureUserIsActive.php   alias 'active'
app/Http/Controllers/Admin/*Controller.php
app/Http/Controllers/Admin/Auth/LoginController.php
app/Http/Requests/Admin/*Request.php
config/admin-menu.php                    (pola Deepsea)
public/css/admin.css                     (port bagian admin dari app.css Deepsea, token warna PB)
resources/views/admin/layouts/app.blade.php   (+ menu.blade.php, aset.blade.php)
resources/views/admin/auth/login.blade.php
resources/views/admin/components/{panel,field,pill,tile,alert,void-modal,lang-tabs,filter}.blade.php
resources/views/admin/<modul>/{index,create,show,form}.blade.php
```

- Middleware grup: `['web', 'auth', 'active', 'role:super_admin|owner|staff']` (alias `role` dari spatie sudah terdaftar).
- `Gate::before` yang ada sudah menolak user nonaktif/tanpa role di semua policy → tetap dipakai.
- Token warna (Modern Heritage, dari `layouts/public.blade.php`): primary `#ba0013`, emas, charcoal → dipetakan ke `--bs-*` seperti Deepsea.
- Komponen admin ditaruh di `resources/views/admin/components` dan dipanggil `<x-admin::...>` atau `@include` — **tidak** di `resources/views/components` (folder itu milik halaman publik, dilarang diubah).
- Helper `@rupiah` / `@angka` didaftarkan di `AppServiceProvider` (tidak menyentuh Filament).

---

## 3. Pemetaan Filament → controller/route/view

Legenda aksi: I=index, C=create/store, S=show, E=edit/update, D=destroy, V=void.
Semua route di bawah prefix `/panel`, nama `admin.*`.

### Dashboard
| Filament | Baru | Akses |
|---|---|---|
| `Dashboard` + widget `StockStatsWidget`, `LowStockAlertWidget`, `TodayActivityWidget`, `YieldTrendChart` | `DashboardController` → `admin/dashboard.blade.php`, `x-tile` + Chart.js | semua |
| widget `FinanceStatsWidget`, `OverdueWidget`, `SalesTrendChart` | bagian yang sama, **query hanya dijalankan bila `isOwnerOrAbove()`** | owner+ |

### Transaksi (tanpa edit/delete)
| Filament | Controller / route | Aksi | Catatan |
|---|---|---|---|
| `PurchaseResource` (+ `PaymentsRelationManager`, `RecordPaymentAction`, `VoidAction`, aksi `receipt`) | `PurchaseController` · `pembelian` | I C S V | Filter: tanggal, status, status bayar, petani, desa. Form: buat petani baru inline (`createOptionForm`) → di Blade jadi field "petani baru" opsional atau link ke master. Bukti → route `purchases.receipt` yang ada |
| `ProductionResource` | `ProductionController` · `produksi` | I C S V | Hitung rendemen live → JS vanilla, nilai final tetap dari `ProductionService`. HPP di show hanya owner |
| `SaleResource` (+ payments, void, `invoice`, `print`) | `SaleController` · `penjualan` | I C S V | Repeater `items` → `<template>` + JS (pola Deepsea). Nota → route `sales.invoice` yang ada. Kolom HPP/laba di tabel & show hanya owner |
| `PaymentsRelationManager` + `RecordPaymentAction` | `PaymentController` · `pembayaran` | C (dari show pembelian/penjualan/utang/piutang), V | Tanpa menu sendiri (keputusan 4) |

### Stok
| Filament | Controller / route | Aksi | Catatan |
|---|---|---|---|
| `StockMovementResource` (Kartu Stok) | `StockMovementController` · `kartu-stok` | I | read-only; kolom nilai (avg cost) hanya owner |
| `StockAdjustmentResource` (Stock Opname) | `StockAdjustmentController` · `opname` | I C S V | owner+ (policy) |

### Keuangan (owner+, kecuali Laporan)
| Filament | Controller / route | Catatan |
|---|---|---|
| `Payables` (Utang ke Petani) | `PayableController@index` · `utang` | dari `Purchase` belum lunas; tombol "Catat Pembayaran" |
| `Receivables` (Piutang Pembeli) | `ReceivableController@index` · `piutang` | + tombol "Ingatkan via WA" |
| `ExpenseResource` (Biaya Operasional) | `ExpenseController` · `biaya` | I C S V |
| `Reports` (tab laporan) | `ReportController` (Admin) · `laporan?tab=&preset=` | Pakai `ReportService::sections()` + Gate `view-report`; staff hanya tab stok & rendemen. Cetak/CSV pakai route `reports.print` / `reports.csv` yang sudah ada |

### Master Data
| Filament | Controller / route | Aksi | Catatan |
|---|---|---|---|
| `ItemResource` (+ `StockMovementsRelationManager`) | `ItemController` · `barang` | I C S E D | show = info + kartu stok barang. Delete ditolak policy bila ada mutasi → pesan + saran nonaktifkan (keputusan 5) |
| `SupplierResource` | `SupplierController` · `petani` | I C E D | delete ditolak bila sudah dipakai pembelian → pesan + saran nonaktifkan (keputusan 5) |
| `BuyerResource` | `BuyerController` · `pembeli` | I C E D | delete ditolak bila sudah dipakai penjualan → pesan + saran nonaktifkan (keputusan 5) |

### Website (owner+) — dikerjakan di **Fase 6** bersama i18n
| Filament | Controller / route | Catatan |
|---|---|---|
| `GalleryResource` | `GalleryController` · `galeri` | I C E D, upload `mimes`+`max`, toggle aktif, urutan (`order`) via input angka (bukan drag) |
| `ProductResource` | `ProductController` · `produk` | I C E D, auto slug |
| `WebsiteSettings` page | `SiteSettingController` · `pengaturan-website` (edit/update) | field dari tabel `site_settings`, tab bahasa |
| `ContactMessageResource` | `ContactMessageController` · `pesan` | I S + aksi: balas WA, balas email, **Jadikan Pembeli** (`LeadService::convertToBuyer`), **Tandai Spam** (`LeadService::markAsSpam`), ubah status |

### Pengaturan
| Filament | Baru | Catatan |
|---|---|---|
| `UserResource` | `UserController` · `pengguna` | I C E D; aturan `UserPolicy` (owner tak bisa sentuh super_admin, tak bisa hapus diri sendiri / user yang punya transaksi), `assignableRoles()` |
| Shield `RoleResource` | **dihapus** | role dari seeder |

### Yang dipakai ulang tanpa diubah
`app/Models/*`, `app/Enums/*`, `app/Services/*`, `app/Policies/*` (sudah cek role, bukan permission Shield), `SaleInvoiceController`, `PurchaseReceiptController`, `ReportController` (cetak/CSV), view `print/*`, `sales/invoice`, `purchases/receipt`, `reports/*`.

---

## 4. Urutan fase (ringkas)
| Fase | Isi | Branch |
|---|---|---|
| 1 | layout, aset CSS, menu config, login/logout (`throttle:5,1`), `EnsureUserIsActive`, dashboard | `feature/prd-08-fase1-layout` |
| 2 | Barang, Petani, Pembeli, Pengguna | `feature/prd-08-fase2-master` |
| 3 | Pembelian, Produksi, Penjualan, Pembayaran, Kartu Stok, Opname, Utang, Piutang | `feature/prd-08-fase3-transaksi` |
| 4 | Biaya Operasional, Laporan | `feature/prd-08-fase4-keuangan` |
| 6 | modul Website (lihat `docs/i18n-plan.md`) | `feature/prd-08-fase6-...` |
| 7 | hapus Filament + Shield, redirect `/admin` → `/panel` | `feature/prd-08-fase7-hapus-filament` |

Test per fase: tamu → redirect login; staff → 403 di URL owner (akses langsung); owner → 200; validasi gagal → error session.

---

## 5. Keputusan Fase 0 (disetujui Erlan, 2026-10-12)

1. **CSS admin:** Bootstrap 5.3.3 dari CDN jsDelivr, versi dikunci + atribut `integrity` (SRI) + `crossorigin`. Gaya tambahan di `public/css/admin.css`. Chart.js juga dikunci versinya + SRI.
2. **Test Filament:** test `/panel` ditulis di fase tiap modul; test yang memanggil Filament baru dihapus/diganti di Fase 7. Assertion angka & stok tidak boleh berubah. Setiap method test yang diubah dicatat di bagian 6.
3. **Redirect tamu → `/panel/login` di Fase 1** (`redirectGuestsTo`). Login Filament punya redirect sendiri, jadi `/admin` tetap aman.
4. **Pembayaran:** tidak ada menu terpisah. Dicatat & di-void dari detail Pembelian/Penjualan dan dari halaman Utang/Piutang.
5. **Hapus master data yang sudah dipakai:** `SupplierPolicy`, `BuyerPolicy`, dan `ItemPolicy` menolak delete bila sudah dipakai transaksi/mutasi. Controller menampilkan pesan jelas + saran **nonaktifkan** (`is_active` sudah ada di ketiga tabel). Dikerjakan di Fase 2.
6. **PWA tidak dibawa.** Bottom nav HP tetap: Dashboard, Pembelian, Penjualan, Stok, Menu.

---

## 6. Log perubahan test lama

Format: `File::method` — fase — alasan — penggantinya.

| Test | Fase | Perubahan | Pengganti |
|---|---|---|---|
| `PurchaseServiceTest::test_pages_and_receipt_render` | 1 | baris terakhir: tamu di `/pembelian/{id}/bukti` kini diarahkan ke `/panel/login` (bukan `/admin/login`) — keputusan 3. Assertion lain tidak berubah | — |
| `SaleInvoiceTest::test_invoice_access_rules` | 1 | assertion redirect tamu `/admin/login` → `/panel/login` — keputusan 3 | — |
| `AdminPagesTest::test_owner_pages_render` | 6 | 5 URL Filament Website (`website-settings`, `contact-messages`, `galleries`, `products`, `products/create`) dibuang dari daftar — halamannya dihapus (keputusan Erlan 2026-10-13). URL Filament lain tetap | `Admin\WebsiteModuleTest::test_owner_can_open_all_website_pages` |
| `GalleryCaptionTest::test_owner_gallery_pages_render` | 6 | `/admin/galleries(/create)` → `/panel/galeri(/create)` | — |
| `LeadConversionTest::test_convert_lead_to_buyer_via_action` | 6 | aksi Livewire `convertToBuyer` → `GET /panel/pesan`, `GET /panel/pesan/{id}`, `POST /panel/pesan/{id}/jadikan-pembeli`. Assertion data pembeli tidak berubah | — |
| `LeadConversionTest::test_mark_as_spam` | 6 | aksi Livewire `spam` → `POST /panel/pesan/{id}/spam`. Assertion status tidak berubah | — |
| `ProductFormTest::test_slug_is_generated_from_name` | 6 | form Livewire → `POST /panel/katalog` dengan `name[id]`. Assertion slug tidak berubah | — |
| `SiteSettingsPageTest::test_owner_updates_value_and_cache_is_reset` | 6 | form Livewire → `PUT /panel/pengaturan-website`; jumlah key 21 → 23 (+2 key SEO, keputusan Fase 0 no. 3). Assertion nilai & reset cache tidak berubah | — |
| `SiteSettingsPageTest::test_invalid_url_is_rejected` | 6 | form Livewire → `PUT /panel/pengaturan-website`; error di `settings.social_instagram` | — |
| `SiteSettingsPageTest::test_staff_cannot_access_website_menu` | 6 | 4 URL `/admin/...` → `/panel/pengaturan-website`, `/panel/pesan`, `/panel/galeri`, `/panel/katalog`. Tetap 403 | — |
